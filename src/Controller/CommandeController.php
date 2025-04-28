<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;  // Annotation correcte
use App\Repository\ProduitRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Service\CommandeMailService;
 
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;



#[Route('/commande')]
final class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    // Route pour créer une nouvelle commande avec le produit spécifique
    #[Route('/produit/{produitId}/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ProduitRepository $produitRepository,
        CommandeMailService $commandeMailService,
        int $produitId
    ): Response {
        $produit = $produitRepository->find($produitId);
        
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $commande = new Commande();
        $commande->setDate(new \DateTime());

        // Créer le formulaire pour la commande
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantiteCommandee = $commande->getNombre();
            $ancienneQuantite = $produit->getQuantite();

            // Vérifier la disponibilité du stock
            if ($quantiteCommandee > $ancienneQuantite) {
                $this->addFlash('error', 'Quantité insuffisante en stock !');
                return $this->redirectToRoute('app_produituser_index');
            }

            // Mise à jour de la quantité du produit
            $nouvelleQuantite = $ancienneQuantite - $quantiteCommandee;
            $produit->setQuantite($nouvelleQuantite);

            // Calcul du total de la commande
            $commande
                ->setTotal($produit->getPrix() * $quantiteCommandee)
                ->setPrix($produit->getPrix())
                ->setNomProduit($produit->getNom());

            // Persister la commande
            $entityManager->persist($commande);
            $entityManager->flush();
            $commandeMailService->sendCommandeEmail($commande);
            return $this->redirectToRoute('app_commandeuser_index');
        }

        return $this->render('commande/new.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    // Affichage du modal de commande
    #[Route('/{id}/show-modal', name: 'commande_modal_show', methods: ['GET'])]
    public function showModal(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    // Route de succès après la soumission de la commande
    #[Route('/success', name: 'commande_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->redirectToRoute('app_commandeuser_index');
    }

    // Modifier une commande existante
    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
        ]);
    }

    // Supprimer une commande
    #[Route('/{id}', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

    // Générer un PDF pour la commande



   
    
   

    #[Route('/pdf/{id}', name: 'commande_pdf', methods: ['GET'])]
    public function generatePdf($id, CommandeRepository $commandeRepository): Response
    {
        // Récupérer la commande par son ID
        $commande = $commandeRepository->find($id);
    
        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }
    
        // Créer le contenu du QR Code avec toutes les informations de la commande
        $qrContent = sprintf(
            "Commande ID: %s\nNom du produit: %s\nDate: %s\nLocalisation: %s\nTéléphone: %s\nMail: %s\nNombre: %s\nPrix: %s\nTotal: %s",
            $commande->getId(),
            $commande->getNomProduit(),
            $commande->getDate()->format('Y-m-d H:i:s'), // Assurez-vous que la date soit bien formatée
            $commande->getLocalisation(),
            $commande->getTelephone(),
            $commande->getMail(),
            $commande->getNombre(),
            $commande->getPrix(),
            $commande->getTotal()
        );
    
        // Créer le QR Code avec toutes les informations
        $qrCode = new QrCode($qrContent);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $qrCodeBase64 = base64_encode($result->getString()); // Générer le QR code en base64
    
        // Générer le HTML pour le PDF
        $html = $this->renderView('commande/pdf.html.twig', [
            'commande' => $commande,
            'qrCodeBase64' => $qrCodeBase64, // Passer la variable qrCodeBase64 à la vue
        ]);
    
        // Initialiser Dompdf pour générer le PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
    
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="commande_' . $id . '.pdf"'
            ]
        );
    }
}