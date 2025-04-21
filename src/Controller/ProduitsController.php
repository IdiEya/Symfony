<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\Produit1Type;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

#[Route('/produits')]
final class ProduitsController extends AbstractController
{ 
    #[Route('/', name: 'app_produits_index', methods: ['GET'])]
    public function index(
        ProduitRepository $produitRepository,
        NotificationRepository $notificationRepository
    ): Response {
        return $this->render('produits/index.html.twig', [
            'produits' => $produitRepository->findAll(),
            'notifications_unread' => $notificationRepository->findUnreadNotifications()
        ]);
    }

    #[Route('/new', name: 'app_produits_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(Produit1Type::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $photoFile */
            $photoFile = $form->get('photo')->getData();

            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move($this->getParameter('photos_directory'), $newFilename);
                $produit->setPhoto($newFilename);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            if ($request->isXmlHttpRequest()) {
                return new JsonResponse(['success' => true]);
            }

            return $this->redirectToRoute('app_produits_index');
        }

        $response = $this->render('produits/new.html.twig', [
            'form' => $form->createView(),
        ]);

        if ($request->isXmlHttpRequest()) {
            $response->headers->set('Content-Type', 'text/html');
        }

        return $response;
    }

    #[Route('/{id}/show-modal', name: 'produit_modal_show', methods: ['GET'])]
    public function showModal(Produit $produit): Response
    {
        return $this->render('produits/_show_modal_content.html.twig', [
            'produit' => $produit,
        ]);
    }

    
    #[Route('/{id}/edit-modal', name: 'produit_modal_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
{
    $originalPhoto = $produit->getPhoto();

    if ($originalPhoto) {
        try {
            $produit->setPhoto(
                new File($this->getParameter('photos_directory').'/'.$originalPhoto)
            );
        } catch (FileNotFoundException $e) {
            $produit->setPhoto(null);
        }
    }

    $form = $this->createForm(Produit1Type::class, $produit);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile|null $photoFile */
        $photoFile = $form->get('photo')->getData();

        if ($photoFile) {
            $newFilename = uniqid().'.'.$photoFile->guessExtension();
            $photoFile->move(
                $this->getParameter('photos_directory'),
                $newFilename
            );
            $produit->setPhoto($newFilename);
        } else {
            $produit->setPhoto($originalPhoto);
        }

        $entityManager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => true,
                'photoPath' => $produit->getPhoto() 
                    ? $this->getParameter('photos_directory_web').'/'.$produit->getPhoto().'?v='.uniqid()
                    : null,
                'fileName' => $produit->getPhoto() ?: 'Aucune image'
            ]);
        }

        return $this->redirectToRoute('app_produits_index');
    }

    return $this->render('produits/_edit_modal_content.html.twig', [
        'produit' => $produit,
        'form' => $form->createView(),
        'originalPhoto' => $originalPhoto,
        'photos_directory_web' => $this->getParameter('photos_directory_web')
    ]);
}
    #[Route('/{id}', name: 'app_produits_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
    }
}
