<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/coach1')]
final class Coach1Controller extends AbstractController
{
    #[Route(name: 'app_coach1_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        // Récupération des paramètres de tri
        $sort = $request->query->get('sort', 'nom'); // Tri par défaut : nom
        $direction = $request->query->get('direction', 'ASC'); // Direction par défaut : croissant

        // On récupère uniquement les coachs
        $users = $userRepository->findBy(
            ['role' => 'coach'],
            [$sort => $direction]
        );

        return $this->render('coach1/index.html.twig', [
            'users' => $users,
        ]);
    }



    #[Route('/new', name: 'app_coach1_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_coach1_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('coach1/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_coach1_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('coach1/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_coach1_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_coach1_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('coach1/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_coach1_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_coach1_index', [], Response::HTTP_SEE_OTHER);
    }

    
}
