<?php

namespace App\Controller;

use App\Entity\Gym;
use App\Form\GymType;
use App\Repository\GymRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/gym')]
class GymController extends AbstractController
{
    #[Route('/', name: 'app_gym_index', methods: ['GET'])]
    public function index(GymRepository $gymRepository): Response
    {
        $user = $this->getUser();
        return $this->render('gym/index.html.twig', [
            'gyms' => $gymRepository->findAll(),
            'user' => $user,
        ]);
    }

    #[Route('/grille', name: 'app_gym_grille', methods: ['GET'])]
    public function grille(GymRepository $gymRepository): Response
    {
        $gyms = $gymRepository->findAll();

        return $this->render('gym/grille.html.twig', [
            'gyms' => $gyms,
        ]);
    }

    #[Route('/new', name: 'app_gym_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gym = new Gym();
        $form = $this->createForm(GymType::class, $gym);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                
                try {
                    $imageFile->move(
                        $this->getParameter('gym_images_directory'),
                        $newFilename
                    );
                    $gym->setPhoto($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image');
                }
            }

            $entityManager->persist($gym);
            $entityManager->flush();

            return $this->redirectToRoute('app_gym_index', [], Response::HTTP_SEE_OTHER);
        }

        $formView = $form->createView();
        return $this->render('gym/new.html.twig', [
            'gym' => $gym,
            'form' => $formView,
            'initial_lat' => 36.8065,
            'initial_lng' => 10.1815,
            'latitude_id' => $formView->children['latitude']->vars['id'],
            'longitude_id' => $formView->children['longitude']->vars['id']
        ]);
    }

    #[Route('/{id}', name: 'app_gym_show', methods: ['GET'])]
    public function show(Gym $gym): Response
    {
        return $this->render('gym/show.html.twig', [
            'gym' => $gym,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gym_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Gym $gym, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GymType::class, $gym);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                
                try {
                    if ($gym->getPhoto()) {
                        $oldImage = $this->getParameter('gym_images_directory').'/'.$gym->getPhoto();
                        if (file_exists($oldImage)) {
                            unlink($oldImage);
                        }
                    }
                    
                    $imageFile->move(
                        $this->getParameter('gym_images_directory'),
                        $newFilename
                    );
                    $gym->setPhoto($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image');
                }
            }

            $entityManager->flush();
            return $this->redirectToRoute('app_gym_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gym/edit.html.twig', [
            'gym' => $gym,
            'form' => $form->createView(),
            'initial_lat' => $gym->getLatitude() ?? 36.8065,
            'initial_lng' => $gym->getLongitude() ?? 10.1815
        ]);
    }

    #[Route('/{id}', name: 'app_gym_delete', methods: ['POST'])]
    public function delete(Request $request, Gym $gym, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gym->getId(), $request->request->get('_token'))) {
            if ($gym->getPhoto()) {
                $imagePath = $this->getParameter('gym_images_directory').'/'.$gym->getPhoto();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $entityManager->remove($gym);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gym_index', [], Response::HTTP_SEE_OTHER);
    }
}
