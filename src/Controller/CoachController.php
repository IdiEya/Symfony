<?php
// src/Controller/CoachController.php
namespace App\Controller;

use App\Repository\CoachRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CoachController extends AbstractController
{
    #[Route('/coaches', name: 'app_coach_list')]
    public function list(CoachRepository $coachRepository): Response
    {
        // Récupère tous les coachs depuis la base de données
        $coaches = $coachRepository->findAll();

        // Passe les coachs à la vue pour les afficher
        return $this->render('coach/list.html.twig', [
            'coaches' => $coaches,
        ]);
    }

    #[Route('/coaches/reserve/{id}', name: 'app_reserve_coach')]
    public function reserve($id, CoachRepository $coachRepository, Request $request, SessionInterface $session): Response
    {
        // Récupère le coach par son ID
        $coach = $coachRepository->find($id);
        if (!$coach) {
            throw $this->createNotFoundException('Aucun coach trouvé pour l\'ID ' . $id);
        }

        // Générer des créneaux horaires disponibles pour 5 jours
        $disponibilites = [];
        $startDate = new \DateTime('tomorrow 08:00');
        $interval = new \DateInterval('PT2H');
        $periodsPerDay = 6; // 6 créneaux par jour : 08h, 10h, 12h, 14h, 16h, 18h

        for ($day = 0; $day < 5; $day++) {
            $date = clone $startDate;
            $date->modify("+$day days");
            for ($i = 0; $i < $periodsPerDay; $i++) {
                $slot = clone $date;
                $slot->modify("+".($i * 2)." hours");
                $disponibilites[] = $slot;
            }
        }

        // Charger les dates réservées depuis la session
        $reservedDates = $session->get('reserved_dates_' . $id, []);

        if ($request->isMethod('POST')) {
            // Récupérer la date sélectionnée
            $selectedDate = $request->request->get('date');

            // Vérifier si la date est déjà réservée
            if (in_array($selectedDate, $reservedDates)) {
                // Afficher un message que la date est déjà réservée
                $this->addFlash('error', 'Cette date est déjà réservée.');
                return $this->redirectToRoute('app_reserve_coach', ['id' => $id]);
            }

            // Ajouter la date à la liste des réservations
            $reservedDates[] = $selectedDate;
            $session->set('reserved_dates_' . $id, $reservedDates);

            $this->addFlash('success', 'Réservation confirmée pour le ' . $selectedDate);
            return $this->redirectToRoute('app_reserve_coach', ['id' => $id]);
        }

        return $this->render('coach/reservation_confirmation.html.twig', [
            'coach' => $coach,
            'disponibilites' => $disponibilites,
            'reservedDates' => $reservedDates,
        ]);
    }
}
