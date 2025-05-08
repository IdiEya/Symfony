<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\User;
use App\Entity\Statut;
use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;
use App\Service\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/participation')]
class ParticipationController extends AbstractController
{
  
    private LoggerInterface $logger; 
    private MailerService $mailerService;
 
    #[Route('/', name: 'app_participation_index', methods: ['GET'])]
public function index(
    ParticipationRepository $participationRepository,
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    $filters = [
        'user' => $request->query->get('user'),
        'event' => $request->query->get('event'),
        'dateFrom' => $request->query->get('dateFrom'),
        'dateTo' => $request->query->get('dateTo'),
    ];

    $participations = $participationRepository->findWithFilters($filters);
    $user = $this->getUser();
    // Récupérer tous les utilisateurs et événements pour les filtres
    $users = $entityManager->getRepository(User::class)->findAll();
    $events = $entityManager->getRepository(Evenement::class)->findAll();

    return $this->render('participation/index.html.twig', [
        'participations' => $participations,
        'users' => $users,
        'events' => $events,
        'currentFilters' => $filters,
        'user' => $user,
    ]);
}


public function __construct(LoggerInterface $logger, MailerService $mailerService) // Injecter MailerService
{
    $this->logger = $logger;
    $this->mailerService = $mailerService; // Initialisation de votre service
}

#[Route('/new/{eventId}', name: 'app_participation_new', methods: ['GET', 'POST'])]

public function new(
    Request $request, 
    EntityManagerInterface $entityManager, 
    int $eventId,
    LoggerInterface $logger
): Response {
    /** @var User $user */
    $user = $this->getUser();
    
    // Vérification que l'utilisateur a bien un email
    if (!$user->getEmail()) {
        $this->addFlash('error', 'Votre profil utilisateur ne contient pas d\'email valide.');
        return $this->redirectToRoute('app_profile_edit');
    }

    $evenement = $entityManager->getRepository(Evenement::class)->find($eventId);
    
    if (!$evenement) {
        throw $this->createNotFoundException('Événement non trouvé');
    }

    $participation = new Participation();
    $participation->setUser($user);
    $participation->setEvenement($evenement);

    $form = $this->createForm(ParticipationType::class, $participation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        try {
            $placesDemandees = $participation->getNombreDePlacesReservees();
            $placesDisponibles = $evenement->getNombreDePlaces() - $evenement->getPlacesReservees();

            if ($placesDemandees > $placesDisponibles) {
                $this->addFlash('error', 'Pas assez de places disponibles. Il reste ' . $placesDisponibles . ' places.');
                return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
            }

            // Mise à jour des places
            $evenement->setNombreDePlaces($evenement->getNombreDePlaces() - $placesDemandees);

            if ($evenement->getNombreDePlaces() <= $evenement->getPlacesReservees()) {
                $evenement->setStatut(Statut::COMPLET->value);
            } else if ($evenement->getStatut() === Statut::COMPLET->value) {
                $evenement->setStatut(Statut::A_VENIR->value);
            }

            // Persister les entités
            $entityManager->persist($participation);
            $entityManager->persist($evenement);
            $entityManager->flush();

            // Envoi de l'email de confirmation
            try {
                $this->mailerService->sendEmail(
                    $user->getEmail(),
                    'Confirmation de participation - ' . $evenement->getNom(),
                    $this->renderView('emails/participation_confirmation.html.twig', [
                        'user' => $user,
                        'evenement' => $evenement,
                        'placesReservees' => $placesDemandees,
                        'date' => new \DateTime()
                    ])
                );
                $this->addFlash('success', 'Confirmation envoyée par email');
            } catch (\Exception $e) {
                $logger->error('Erreur envoi email: '.$e->getMessage());
                $this->addFlash('warning', 'Participation enregistrée mais échec d\'envoi du mail');
            }

            return $this->redirectToRoute('app_client_events', ['id' => $evenement->getId()]);
            
        } catch (\Exception $e) {
            $logger->error('Erreur participation: '.$e->getMessage());
            $this->addFlash('error', 'Une erreur technique est survenue');
        }
    }

    return $this->render('participation/new.html.twig', [
        'participation' => $participation,
        'form' => $form->createView(),
        'evenement' => $evenement,
        'user' => $user,
    ]);
}
   #[Route('/{id}/edit', name: 'app_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
            'user' => $user,
        ]);
    }
   

    #[Route('/{id}', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }

    
#[Route('/mes-participations', name: 'app_mes_participations', methods: ['GET'])]

public function mesParticipations(ParticipationRepository $participationRepository): Response
{
    $user = $this->getUser();
    $participations = $participationRepository->findBy([
        'user' => $this->getUser()
    ]);

    return $this->render('participation/mes_participations.html.twig', [
        'participations' => $participations ?? []
    ]);
}



}