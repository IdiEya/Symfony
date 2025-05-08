<?php

namespace App\Controller;
use App\Entity\Statut;
use App\Repository\ParticipationRepository;
use App\Entity\Evenement;
use App\Form\EvenementType;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Entity\UserEventVote;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/evenement')]
final class EvenementController extends AbstractController
{
    #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        $sortBy = $request->query->get('sort_by', 'nom');
        $direction = $request->query->get('direction', 'asc');
    
        $evenements = $evenementRepository->findAllSorted($sortBy, $direction);
        
        foreach ($evenements as $evenement) {
            $this->updateEventStatus($evenement, $entityManager);
        }
        
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
            'sort_by' => $sortBy,
            'direction' => $direction,
            'user' => $user,
        ]);
    }
  
private function updateEventStatus(Evenement $evenement, EntityManagerInterface $entityManager): void
{
    $user = $this->getUser();
    $now = new \DateTime();
    $newStatus = $evenement->getDateFin() < $now 
        ? Statut::TERMINE->value 
        : Statut::A_VENIR->value;
    
    if ($evenement->getStatut() !== $newStatus) {
        $evenement->setStatut($newStatus);
        $entityManager->persist($evenement);
        $entityManager->flush();
    }
}


#[Route('/new', name: 'app_evenement_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    $evenement = new Evenement();
    $evenement->setStatut(Statut::A_VENIR->value);
    $form = $this->createForm(EvenementType::class, $evenement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $file */
        $file = $form->get('photo')->getData();
        
        if ($file) {
            $filename = uniqid().'.'.$file->guessExtension();
            $file->move(
                $this->getParameter('images_directory'),
                $filename
            );
            $evenement->setPhoto($filename);
        } else {
            // Optionnel: définir une image par défaut si aucune n'est fournie
            $evenement->setPhoto('default-event.jpg');
        }

        $entityManager->persist($evenement);
        $entityManager->flush();

        $this->addFlash('success', 'Événement créé avec succès!');
        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('evenement/new.html.twig', [
        'evenement' => $evenement,
        'form' => $form,
        'user' => $user,
    ]);
}


    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
public function show(Evenement $evenement): Response
{
    $user = $this->getUser();
    // Mettre à jour le statut avant affichage
    $evenement->updateStatut();
    
    return $this->render('evenement/show.html.twig', [
        'evenement' => $evenement,
        'places_reservees' => $evenement->getPlacesReservees(),
        'places_disponibles' => $evenement->getNombreDePlaces() - $evenement->getPlacesReservees(),
        'user' => $user,
    ]);
}
    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    $form = $this->createForm(EvenementType::class, $evenement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        /** @var UploadedFile $file */
        $file = $form->get('photo')->getData();
        
        if ($file) {
            $filename = uniqid().'.'.$file->guessExtension();
            $file->move($this->getParameter('images_directory'), $filename);
            $evenement->setPhoto($filename);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('evenement/edit.html.twig', [
        'evenement' => $evenement,
        'form' => $form,
        'user' => $user,
    ]);}

    #[Route('/{id}', name: 'app_evenement_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Evenement $evenement, 
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }
    
        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }

   #[Route('/search/events', name: 'app_evenement_search_index', methods: ['GET'])]
public function searchIndex(EvenementRepository $evenementRepository, Request $request): JsonResponse
{
    $query = $request->query->get('query', '');
    
    if (empty($query)) {
        return $this->json(['events' => []]);
    }

    try {
        $evenements = $evenementRepository->advancedSearch($query);
        
        $eventsArray = array_map(function($event) {
            return [
                'id' => $event->getId(),
                'nom' => $event->getNom(),
                'description' => $event->getDescription(),
                'dateDebut' => $event->getDateDebut()->format('d/m/Y'),
                'dateFin' => $event->getDateFin()->format('d/m/Y'),
                'localisation' => $event->getLocalisation(),
                'frais' => $event->getFrais(),
                'nombreDePlaces' => $event->getNombreDePlaces(),
                'photo' => $event->getPhoto(),
                'statut' => $event->getStatut()
            ];
        }, $evenements);

        return $this->json([
            'success' => true,
            'events' => $eventsArray
        ]);
    } catch (\Exception $e) {
        return $this->json([
            'success' => false,
            'message' => 'Erreur lors de la recherche',
            'error' => $e->getMessage()
        ], 500);
    }
}

    #[Route('/client/events', name: 'app_client_events', methods: ['GET'])]
public function clientEvents(EvenementRepository $evenementRepository, CsrfTokenManagerInterface $csrfTokenManager): Response
{
    $user = $this->getUser();
    // Récupérer tous les événements sans filtrage
    $evenements = $evenementRepository->findAll();

    // Met à jour le statut de chaque événement
    foreach ($evenements as $evenement) {
        // Assurez-vous que la méthode updateStatut() existe et fonctionne correctement dans votre entité Evenement
        $evenement->updateStatut();
    }

    // Générer les tokens CSRF
    $csrfLikeToken = $csrfTokenManager->getToken('like'); // Utilisez le même nom que dans le template
    $csrfDislikeToken = $csrfTokenManager->getToken('dislike'); // Utilisez le même nom que dans le template

    return $this->render('evenement/client_events.html.twig', [
        'evenements' => $evenements,
        'user' => $user,
        'csrf_like_token' => $csrfLikeToken,
        'csrf_dislike_token' => $csrfDislikeToken,
    ]);
}
    #[Route('/client/events/search', name: 'app_client_events_search', methods: ['GET'])]
public function clientEventsSearch(EvenementRepository $evenementRepository, Request $request): JsonResponse
{
    $query = $request->query->get('query', '');
    
    if (empty($query)) {
        return $this->json(['events' => []]);
    }

    $evenements = $evenementRepository->searchByNameAndDescription($query);
    
    $eventsArray = array_map(function($event) {
        return [
            'id' => $event->getId(),
            'nom' => $event->getNom(),
            'description' => $event->getDescription(),
            'dateDebut' => $event->getDateDebut()->format('c'),
            'dateFin' => $event->getDateFin()->format('c'),
            'localisation' => $event->getLocalisation(),
            'photo' => $event->getPhoto(),
            'statut' => $event->getStatut(),
            'nombreDePlaces' => $event->getNombreDePlaces(),
            'placesReservees' => $event->getPlacesReservees(),
            'likes' => $event->getLikes(),
            'dislikes' => $event->getDislikes()
        ];
    }, $evenements);

    return $this->json(['events' => $eventsArray]);
}

#[Route('/{id}/like', name: 'app_evenement_like', methods: ['POST'])]
public function like(Evenement $evenement, Request $request, EntityManagerInterface $em): JsonResponse
{
    return $this->handleVote($evenement, $request, $em, 1, 'like');
}

#[Route('/{id}/dislike', name: 'app_evenement_dislike', methods: ['POST'])]
public function dislike(Evenement $evenement, Request $request, EntityManagerInterface $em): JsonResponse
{
    return $this->handleVote($evenement, $request, $em, -1, 'dislike');
}

private function handleVote(Evenement $evenement, Request $request, EntityManagerInterface $em, int $voteValue, string $tokenName): JsonResponse
{
    $user = $this->getUser();
    
    if (!$user) {
        return $this->json(['error' => 'Not authenticated'], 401);
    }

    if (!$this->isCsrfTokenValid($tokenName, $request->request->get('_token'))) {
        return $this->json(['error' => 'Invalid CSRF token'], 403);
    }

    $voteRepo = $em->getRepository(UserEventVote::class);
    $existingVote = $voteRepo->findOneBy(['user' => $user, 'event' => $evenement]);

    if (!$existingVote) {
        // Nouveau vote
        $vote = new UserEventVote();
        $vote->setUser($user);
        $vote->setEvent($evenement);
        $vote->setVote($voteValue);
        $em->persist($vote);
        
        if ($voteValue === 1) {
            $evenement->incrementLikes();
        } else {
            $evenement->incrementDislikes();
        }
    } else {
        if ($existingVote->getVote() === $voteValue) {
            // Retirer le vote
            $em->remove($existingVote);
            if ($voteValue === 1) {
                $evenement->decrementLikes();
            } else {
                $evenement->decrementDislikes();
            }
        } else {
            // Changer de vote
            $existingVote->setVote($voteValue);
            if ($voteValue === 1) {
                $evenement->incrementLikes();
                $evenement->decrementDislikes();
            } else {
                $evenement->incrementDislikes();
                $evenement->decrementLikes();
            }
        }
    }

    $em->flush();

    return $this->json([
        'likes' => $evenement->getLikes(),
        'dislikes' => $evenement->getDislikes(),
        'userVote' => $existingVote ? $existingVote->getVote() : 0
    ]);
}
}