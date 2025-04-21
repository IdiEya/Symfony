<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produituser')]
final class ProduituserController extends AbstractController
{
    #[Route('', name: 'app_produituser_index', methods: ['GET'])]
    public function index(Request $request, ProduitRepository $produitRepo, CategorieRepository $categorieRepo): Response
    {
        $categorieId = $request->query->get('categorie');
        $categories = $categorieRepo->findAll();

        if ($categorieId) {
            $produits = $produitRepo->findBy(['categorie' => $categorieId]);
        } else {
            $produits = $produitRepo->findAll();
        }

        return $this->render('produituser/index.html.twig', [
            'produits' => $produits,
            'categories' => $categories,
            'selectedCategorie' => $categorieId,
        ]);
    }

    #[Route('/new', name: 'app_produituser_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('app_produituser_index');
        }

        return $this->render('produituser/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'produituser_modal_show', methods: ['GET'])]
    public function showModal(Produit $produit): Response
    {
        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé');
        }

        return $this->render('produituser/_details.html.twig', [
            'produit' => $produit
        ]);
    }

    #[Route('/{id}/edit', name: 'app_produituser_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_produituser_index');
        }

        return $this->render('produituser/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_produituser_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produituser_index');
    }

    #[Route('/panier', name: 'app_panier_index')]
public function panier(): Response
{
    // Logique pour afficher les articles du panier
    return $this->render('panier/index.html.twig');
}

}
