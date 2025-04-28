<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class ReservationController extends AbstractController
{
    #[Route('/reserver', name: 'app_reserver')]
    public function reserver(Request $request, EntityManagerInterface $em, ManagerRegistry $doctrine): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationFormType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reservation);
            $em->flush();

            $this->addFlash('success', 'Réservation effectuée avec succès !');
            return $this->redirectToRoute('app_reserver');
        }

        $reservedDates = $doctrine->getRepository(Reservation::class)->findAll();

        return $this->render('reservation/reserver.html.twig', [
            'form' => $form->createView(),
            'reservedDates' => $reservedDates,
        ]);
    }

    #[Route('/reservation/new/{id}', name: 'app_reservation_new')]
    public function new(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        $reservedDates = $doctrine->getRepository(Reservation::class)->findAll(); // Ajout de cette ligne

        if ($form->isSubmitted() && $form->isValid()) {
            $date = $reservation->getDateReservation();

            $existing = $doctrine->getRepository(Reservation::class)->findOneBy(['date_reservation' => $date]);

            if ($existing) {
                $this->addFlash('error', 'Cette date est déjà réservée.');
            } else {
                $em = $doctrine->getManager();
                $em->persist($reservation);
                $em->flush();

                $this->addFlash('success', 'Réservation enregistrée avec succès !');
                return $this->redirectToRoute('app_reservation_new', ['id' => $id]);
            }
        }

        return $this->render('reservation/reserver.html.twig', [
            'form' => $form->createView(),
            'reservedDates' => $reservedDates,
        ]);
    }
    #[Route('/reservation', name: 'reservation_list')]
    public function showReservationsAction(ManagerRegistry $doctrine)
    {
        // Utilisation de l'injection de dépendance pour récupérer les réservations
        $reservations = $doctrine->getRepository(Reservation::class)->findAll();

        return $this->render('reservation/list.html.twig', [
            'reservedDates' => $reservations,
        ]);
    }
}