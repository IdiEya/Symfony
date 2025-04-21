<?php

namespace App\Controller;
use App\Entity\Statut;
use App\Repository\ParticipationRepository;
use App\Entity\Evenement;
use App\Form\EvenementType;
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
public function index(EvenementRepository $evenementRepository, EntityManagerInterface $entityManager): Response
{
    $evenements = $evenementRepository->findAll();
    
    foreach ($evenements as $evenement) {
        $this->updateEventStatus($evenement, $entityManager);
    }
    
    return $this->render('evenement/index.html.twig', [
        'evenements' => $evenements,
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
        $evenements = $evenementRepository->findByName($query);
        
        $eventsArray = array_map(function($event) {
            return [
                'id' => $event->getId(),
                'nom' => $event->getNom(),
                'dateDebut' => $event->getDateDebut()->format('c'),
                'dateFin' => $event->getDateFin()->format('c'),
                'localisation' => $event->getLocalisation(),
                'photo' => $event->getPhoto(),
                'statut' => $event->getStatut(),
                'nombreDePlaces' => $event->getNombreDePlaces(),
                'placesReservees' => $event->getPlacesReservees(),
            ];
        }, $evenements);
    
        return $this->json($eventsArray);
    }
   
    #[Route('/search/events', name: 'app_evenement_search_index', methods: ['GET'])]
public function searchIndex(EvenementRepository $evenementRepository, Request $request): Response
{
    $query = $request->query->get('query', '');
    $evenements = $evenementRepository->findByName($query);
    
    $eventsArray = array_map(function($event) {
        return [
            'id' => $event->getId(),
            'nom' => $event->getNom(),
            'description' => $event->getDescription(),
            'dateDebut' => $event->getDateDebut()->format('Y-m-d'),
            'dateFin' => $event->getDateFin()->format('Y-m-d'),
            'localisation' => $event->getLocalisation(),
            'frais' => $event->getFrais(),
            'nombreDePlaces' => $event->getNombreDePlaces(),
        ];
    }, $evenements);

    return $this->json($eventsArray);
}
}