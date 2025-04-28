<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/categorie')]
final class CategorieController extends AbstractController
{
    
    #[Route(name: 'app_categorie_index', methods: ['GET'])]

    public function index(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager
            ->getRepository(Categorie::class)
            ->findAll();

        return $this->render('categorie/index.html.twig', [
            'categories' => $categories
        ]);
    }
    #[Route('/new', name: 'app_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();
    
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }
    
            return $this->redirectToRoute('app_categorie_index');
        }
    
        $response = $this->render('categorie/new.html.twig', [
            'form' => $form->createView(),
        ]);
    
        // Force le Content-Type pour les requêtes AJAX
        if ($request->isXmlHttpRequest()) {
            $response->headers->set('Content-Type', 'text/html');
        }
    
        return $response;
    }

    #[Route('/{id}', name: 'app_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{id}/edit-modal', name: 'categorie_modal_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }
    
            return $this->redirectToRoute('app_categorie_index');
        }
    
        $template = $request->isXmlHttpRequest() 
            ? 'categorie/_edit_modal_content.html.twig' 
            : 'categorie/edit.html.twig';
    
        return $this->render($template, [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ], new Response(
            null,
            $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK
        ));
    }
      

    #[Route('/{id}', name: 'app_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
