<?php

namespace App\Controller;
use App\Entity\Statut;
use App\Repository\ParticipationRepository;
use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Entity\User;
use App\Entity\UserEventVote;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/evenement')]
final class EvenementController extends AbstractController
{
    #[Route(name: 'app_evenement_index', methods: ['GET'])]
    public function index(EvenementRepository $evenementRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
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
        ]);
    }
  
private function updateEventStatus(Evenement $evenement, EntityManagerInterface $entityManager): void
{
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
    ]);
}


    #[Route('/{id}', name: 'app_evenement_show', methods: ['GET'])]
public function show(Evenement $evenement): Response
{
    // Mettre à jour le statut avant affichage
    $evenement->updateStatut();
    
    return $this->render('evenement/show.html.twig', [
        'evenement' => $evenement,
        'places_reservees' => $evenement->getPlacesReservees(),
        'places_disponibles' => $evenement->getNombreDePlaces() - $evenement->getPlacesReservees()
    ]);
}
    #[Route('/{id}/edit', name: 'app_evenement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
{
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
    #[Route('/client/events', name: 'app_client_events', methods: ['GET'])]
    public function clientEvents(EvenementRepository $evenementRepository): Response
    {
        // Récupérer tous les événements sans filtrage
        $evenements = $evenementRepository->findAll();
    
        // Met à jour le statut de chaque événement
        foreach ($evenements as $evenement) {
            $evenement->updateStatut();
        }
    
        return $this->render('evenement/client_events.html.twig', [
            'evenements' => $evenements,
        ]);
    }
    #[Route('/client/events/search', name: 'app_client_events_search', methods: ['GET'])]
    public function clientEventsSearch(EvenementRepository $evenementRepository, Request $request): Response
    {
        $query = $request->query->get('query', '');
        
        if (empty($query)) {
            return $this->json([]);
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
    
        return $this->json($eventsArray);
    }
   
    #[Route('/search/events', name: 'app_evenement_search_index', methods: ['GET'])]
    public function searchIndex(EvenementRepository $evenementRepository, Request $request): Response
    {
        $query = $request->query->get('query', '');
        
        if (empty($query)) {
            return $this->json([]);
        }
    
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
    
        return $this->json($eventsArray);
    }
private function getCurrentUser(EntityManagerInterface $entityManager): User
{
    // Check if a user with the specified email already exists
    $existingUser = $entityManager->getRepository(User::class)->findOneBy([
        'email' => 'idieya56@gmail.com' // Checking against the email to avoid duplicates
    ]);

    if ($existingUser) {
        // If the user exists, return that user
        return $existingUser;
    }

    // If no user exists, create a new one
    $user = new User();
    $user->setNom('Utilisateur Test');
    $user->setPrenom('Test');
    $user->setEmail('idieya56@gmail.com');
    $user->setPassword(password_hash('motdepasse', PASSWORD_DEFAULT)); // Hash the password
    $user->setRole('sportif');
    $user->setTelephone('0123456789'); // Assuming other fields are not required for this example

    // Persist the new user
    $entityManager->persist($user);
    $entityManager->flush();

    return $user;
}

#[Route('/{id}/like', name: 'app_evenement_like', methods: ['POST'])]
public function like(
    Evenement $evenement, 
    EntityManagerInterface $entityManager,
    Request $request
): Response {
    $user = $this->getCurrentUser($entityManager);
    
    if ($this->isCsrfTokenValid('like'.$evenement->getId(), $request->request->get('_token'))) {
        $voteRepository = $entityManager->getRepository(UserEventVote::class);
        $existingVote = $voteRepository->findOneByUserAndEvent($user, $evenement);

        if (!$existingVote) {
            // Nouveau vote like
            $vote = new UserEventVote();
            $vote->setUser($user);
            $vote->setEvent($evenement);
            $vote->setVote(1);
            $entityManager->persist($vote);
            $evenement->incrementLikes();
        } elseif ($existingVote->getVote() === -1) {
            // Changement de dislike à like
            $existingVote->setVote(1);
            $evenement->incrementLikes();
            $evenement->decrementDislikes();
        }
        // Si déjà like, ne rien faire

        $entityManager->flush();
    }

    return $this->redirectToRoute('app_client_events');
}

#[Route('/{id}/dislike', name: 'app_evenement_dislike', methods: ['POST'])]
public function dislike(
    Evenement $evenement, 
    EntityManagerInterface $entityManager,
    Request $request
): Response {
    $user = $this->getCurrentUser($entityManager);
    
    if ($this->isCsrfTokenValid('dislike'.$evenement->getId(), $request->request->get('_token'))) {
        $voteRepository = $entityManager->getRepository(UserEventVote::class);
        $existingVote = $voteRepository->findOneByUserAndEvent($user, $evenement);

        if (!$existingVote) {
            // Nouveau vote dislike
            $vote = new UserEventVote();
            $vote->setUser($user);
            $vote->setEvent($evenement);
            $vote->setVote(-1);
            $entityManager->persist($vote);
            $evenement->incrementDislikes();
        } elseif ($existingVote->getVote() === 1) {
            // Changement de like à dislike
            $existingVote->setVote(-1);
            $evenement->incrementDislikes();
            $evenement->decrementLikes();
        }
        // Si déjà dislike, ne rien faire

        $entityManager->flush();
    }

    return $this->redirectToRoute('app_client_events');
}


}