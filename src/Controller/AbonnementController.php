<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use App\Repository\GymRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/abonnement')]
final class AbonnementController extends AbstractController
{
   

#[Route(name: 'app_abonnement_index', methods: ['GET'])]
public function index(Request $request, AbonnementRepository $abonnementRepository, GymRepository $gymRepository, PaginatorInterface $paginator): Response
{
    $searchTerm = $request->query->get('search', '');

    if ($searchTerm) {
        $abonnementsQuery = $abonnementRepository->searchByTerm($searchTerm);  
    } else {
        $abonnementsQuery = $abonnementRepository->findAllQuery(); 
    }

    $gyms = $gymRepository->findAll();

    $pagination = $paginator->paginate(
        $abonnementsQuery, // 
        $request->query->getInt('page', 1), 
    );

    return $this->render('abonnement/index.html.twig', [
        'pagination' => $pagination, // Assurez-vous de passer la variable pagination
        'abonnements' => $pagination, // Vous pouvez aussi la passer sous un autre nom si nécessaire
        'searchTerm' => $searchTerm,  // Passer le terme de recherche à la vue
        'gyms' => $gyms, // Passer les salles de sport à la vue
        'selectedGym' => $request->query->get('gym'), // Passer l'ID de la salle sélectionnée
        'sort' => $request->query->get('sort'), // Passer le tri sélectionné
    ]);
}


    #[Route('/new/{gymId}', name: 'app_abonnement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ParameterBagInterface $params, GymRepository $gymRepository, int $gymId): Response
    {
        $stripePublicKey = $params->get('STRIPE_PUBLIC_KEY');
        $stripeSecretKey = $params->get('STRIPE_SECRET_KEY');

        $abonnement = new Abonnement();
        $gym = $gymRepository->find($gymId);

        if (!$gym) {
            throw $this->createNotFoundException('Salle non trouvée.');
        }

        $abonnement->setGym($gym);
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $type = $abonnement->getType();

            $prix = null;
            if ($type) {
                $methodName = 'getPrix' . ucfirst($type->value);
                if (method_exists($gym, $methodName)) {
                    $prix = $gym->$methodName();
                    $abonnement->setPrix($prix);
                } else {
                    throw new \RuntimeException('Méthode de prix introuvable pour le type : ' . $type->value);
                }
            }

            if ($abonnement->getDateInitiale() && $type) {
                $dateExpiration = clone $abonnement->getDateInitiale();
                match ($type->value) {
                    'mensuel' => $dateExpiration->modify('+1 month'),
                    'trimestriel' => $dateExpiration->modify('+3 months'),
                    'semestriel' => $dateExpiration->modify('+6 months'),
                    'annuel' => $dateExpiration->modify('+1 year'),
                    default => throw new \InvalidArgumentException('Type d\'abonnement invalide.')
                };
                $abonnement->setDateExpiration($dateExpiration);
            }

            $entityManager->persist($abonnement);
            $entityManager->flush();

            if (!isset($prix)) {
                throw new \RuntimeException('Le prix de l\'abonnement est manquant. Impossible de créer une session de paiement.');
            }

            \Stripe\Stripe::setApiKey($stripeSecretKey);
            $checkoutSession = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Abonnement ' . $type->value,
                        ],
                        'unit_amount' => $prix * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->generateUrl('app_abonnement_qr_code_show', ['id' => $abonnement->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('app_abonnement_new', ['gymId' => $gymId], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

            return $this->redirect($checkoutSession->url);
        }

        return $this->render('abonnement/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
            'gyms' => [$gym],
            'stripe_public_key' => $stripePublicKey,
        ]);
    }

    #[Route('/show/{id}', name: 'app_abonnement_show', methods: ['GET'])]
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('abonnement/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_abonnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_abonnement_delete', methods: ['POST'])]
    public function delete(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnement->getId(), $request->get('_token'))) {
            $entityManager->remove($abonnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/create-checkout-session/{id}', name: 'create_checkout_session', methods: ['POST'])]
    public function createCheckoutSession(Request $request, AbonnementRepository $abonnementRepo, ParameterBagInterface $params, int $id = null): JsonResponse
    {
        $abonnement = $id ? $abonnementRepo->find($id) : null;
        $stripeSecretKey = $params->get('STRIPE_SECRET_KEY');
    
        \Stripe\Stripe::setApiKey($stripeSecretKey);
    
        // Création de la session de paiement
        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $abonnement ? ('Abonnement ' . $abonnement->getType()->value) : 'Abonnement',
                    ],
                    'unit_amount' => $abonnement ? ($abonnement->getPrix() * 100) : 1000,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_abonnement_qr_code_show', ['id' => $abonnement->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_abonnement_index', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    
        return new JsonResponse(['id' => $checkoutSession->id]);
    }

    #[Route('/qr-code/{id}', name: 'app_abonnement_qr_code_show', methods: ['GET'])]
    public function showQrCode(int $id, AbonnementRepository $abonnementRepository): Response
    {
        $abonnement = $abonnementRepository->find($id);

        if (!$abonnement) {
            throw $this->createNotFoundException('Abonnement non trouvé.');
        }

        return $this->render('abonnement/qr_code_show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    #[Route('/qr-code-generate/{id}/{format}', name: 'abonnement_qr_code', methods: ['GET'])]
    public function generateQrCode(int $id, string $format, AbonnementRepository $abonnementRepository): Response
    {
        return $this->qrCode($id, $format, $abonnementRepository);
    }
    
    private function qrCode(int $id, string $format, AbonnementRepository $abonnementRepository): Response
    {
        $abonnement = $abonnementRepository->find($id);
    
        if (!$abonnement) {
            throw $this->createNotFoundException('Abonnement non trouvé.');
        }
    
        $now = new \DateTime();
        $dateExpiration = $abonnement->getDateExpiration();
        $typeAbonnement = $abonnement->getType()?->value ?? 'Non précisé';
        $prix = $abonnement->getPrix();
    
        $interval = $now->diff($dateExpiration);
        $daysRemaining = (int) $interval->format('%r%a');
    
        $message = "=== ABONNEMENT ===\n";
        $message .= "🏋️ Type : {$typeAbonnement}\n";
        $message .= "💰 Prix : {$prix} TND\n";
        $message .= "🛑 Expiration : " . $dateExpiration->format('d/m/Y') . "\n";
        $message .= "-------------------------\n";
    
        if ($daysRemaining < 0) {
            $message .= "❌ Accès interdit\nVotre abonnement est expiré.\n🔄 Veuillez le renouveler.";
        } else {
            $message .= "✅ Accès autorisé\nBienvenue !\n⌛ Il vous reste {$daysRemaining} jours.";
            if ($daysRemaining <= 5) {
                $message .= "\n⚠️ Pensez à renouveler bientôt !";
            }
        }
    
        $builder = Builder::create()
            ->data($message)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10);
    
        switch ($format) {
            case 'svg':
                $builder->writer(new SvgWriter());
                break;
            default:
                $builder->writer(new PngWriter());
        }
    
        $result = $builder->build();
    
        return new Response(
            $result->getString(),
            200,
            [
                'Content-Type' => 'image/' . $format,
            ]
        );
    }
}