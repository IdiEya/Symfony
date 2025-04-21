<?php

namespace App\Controller;
use App\Entity\Evenement;
use App\Entity\User;
use App\Entity\Statut;
use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participation')]
class ParticipationController extends AbstractController
{
 
   #[Route('/', name: 'app_participation_index', methods: ['GET'])]
public function index(ParticipationRepository $participationRepository): Response
{
    $participations = $participationRepository->findAllWithUserAndEvent();

    return $this->render('participation/index.html.twig', [
        'participations' => $participations, 
        'evenement' => null
    ]);
}

#[Route('/new/{eventId}', name: 'app_participation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, int $eventId): Response
{
    $evenement = $entityManager->getRepository(Evenement::class)->find($eventId);
    
    if (!$evenement) {
        throw $this->createNotFoundException('Événement non trouvé');
    }

    $participation = new Participation();
    $user = $this->getUser() ?: $this->getCurrentUser($entityManager);
    $participation->setUtilisateur($user);
    $participation->setEvenement($evenement);

    $form = $this->createForm(ParticipationType::class, $participation);
    $form->handleRequest($request);


    if ($form->isSubmitted() && $form->isValid()) {
        $placesDemandees = $participation->getNombreDePlacesReservees();

        if ($evenement->getNombreDePlaces() !== null) {
            $placesDisponibles = $evenement->getNombreDePlaces() - $evenement->getPlacesReservees();

            if ($placesDemandees > $placesDisponibles) {
                $this->addFlash('error', 'Pas assez de places disponibles. Il reste '.$placesDisponibles.' places.');
                return $this->redirectToRoute('app_evenement_show', ['id' => $evenement->getId()]);
            }
            
            // Diminuer le nombre de places disponibles
            $evenement->setNombreDePlaces($evenement->getNombreDePlaces() - $placesDemandees);

            // Vérifier si le nombre de places est à zéro et changer le statut
            if ($evenement->getNombreDePlaces() <= 0) {
                $evenement->setStatut(Statut::COMPLET->value);
            } else {
                // S'il reste encore des places, réinitialiser à A_VENIR si applicable
                if ($evenement->getStatut() === Statut::COMPLET->value) {
                    $evenement->setStatut(Statut::A_VENIR->value);
                }
            }
        }

        // Persister la participation D'ABORD
        $entityManager->persist($participation);
        // Puis, persister l'événement pour mettre à jour le statut
        $entityManager->persist($evenement); 
        $entityManager->flush(); // Flushing une seule fois après cela
        $this->addFlash('success', 'Votre participation a été enregistrée avec succès');

        return $this->redirectToRoute('app_client_events', ['id' => $evenement->getId()]);
    }

    return $this->render('participation/new.html.twig', [
        'participation' => $participation,
        'form' => $form->createView(),
        'evenement' => $evenement
    ]);
}
  

    #[Route('/{id}/edit', name: 'app_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
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
#[Route('/mes-participations', name: 'app_mes_participations', methods: ['GET'])]
public function mesParticipations(ParticipationRepository $participationRepository, EntityManagerInterface $entityManager): Response
{
    // Obtenez l'utilisateur actuellement authentifié
    $user = $this->getUser();
    
    // Si l'utilisateur n'est pas authentifié, utilisez getCurrentUser() pour obtenir un utilisateur par défaut
    if (!$user) {
        $user = $this->getCurrentUser($entityManager);
    }

    // Récupérez les participations de l'utilisateur
    $participations = $participationRepository->findBy(['utilisateur' => $user]);

    // Vérifiez si l'utilisateur a des participations
    if (!$participations) {
        $this->addFlash('info', 'Aucune participation trouvée pour cet utilisateur.');
        return $this->redirectToRoute('app_participation_index'); // Redirigez ou renvoyez à une autre action
    }

    return $this->render('participation/mes_participations.html.twig', [
        'participations' => $participations,
    ]);
}
}
