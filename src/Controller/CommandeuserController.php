<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/commandeuser')]
final class CommandeuserController extends AbstractController
{
    #[Route('/', name: 'app_commandeuser_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commandeuser/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    #[Route('/produit/{produitId}/new', name: 'app_commandeuser_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ProduitRepository $produitRepository,
        int $produitId
    ): Response {
        $produit = $produitRepository->find($produitId);

        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        $commande = new Commande();
        $commande->setDate(new \DateTime());

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantiteCommandee = $commande->getNombre();
            $ancienneQuantite = $produit->getQuantite();

            if ($quantiteCommandee > $ancienneQuantite) {
                $this->addFlash('error', 'Quantité insuffisante en stock !');
                return $this->redirectToRoute('app_produituser_index');
            }

            // Mise à jour de la quantité du produit
            $produit->setQuantite($ancienneQuantite - $quantiteCommandee);

            // Mise à jour de la commande
            $commande
                ->setTotal($produit->getPrix() * $quantiteCommandee)
                ->setPrix($produit->getPrix())
                ->setNomProduit($produit->getNom());

            $commande->setProduit($produit); // si relation ManyToOne

            $entityManager->persist($commande);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }

            return $this->redirectToRoute('commande_success');
        }

        $response = $this->render('commandeuser/new.html.twig', [
            'form' => $form->createView(),
              'produit' => $produit, // <-- Ajout ici
        ]);

        if ($request->isXmlHttpRequest()) {
            $response->headers->set('Content-Type', 'text/html');
        }

        return $response;
    }

    #[Route('/{id}', name: 'commandeuser_modal_show', methods: ['GET'])]
    public function showModal(Commande $commande): Response
    {
        return $this->render('commandeuser/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/success', name: 'commande_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->redirectToRoute('app_commandeuser_index');
    }

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

    #[Route('/{id}', name: 'app_commandeuser_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commandeuser_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/pdf/{id}', name: 'commandeuser_pdf', methods: ['GET'])]
    public function generatePdf($id, CommandeRepository $commandeRepository): Response
    {
        $commande = $commandeRepository->find($id);

        if (!$commande) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        $html = $this->renderView('commande/pdf.html.twig', [
            'commande' => $commande,
        ]);

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
                'Content-Disposition' => 'inline; filename="commande_' . $id . '.pdf"',
            ]
        );
    }
}
