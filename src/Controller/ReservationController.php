<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationFormType;
use App\Service\TwilioService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reserver', name: 'app_reserver')]
    public function reserver(Request $request, TwilioService $twilioService): Response
{
    $reservation = new Reservation();
    $form = $this->createForm(ReservationFormType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Enregistrer la réservation dans la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reservation);
        $entityManager->flush();

        // Préparer le message pour Twilio
        $message = sprintf(
            "Votre réservation est confirmée pour le %s à %s. Adresse : %s, %s. Merci de nous avoir choisi !",
            $reservation->getDateReservation()->format('d/m/Y'),
            $reservation->getDateReservation()->format('H:i'),
            $reservation->getAdresse(),
            $reservation->getAdresseComplete()
        );

        // Envoi du SMS de confirmation
        $toPhone = $reservation->getTel(); // Numéro de téléphone du client
        $smsSent = $twilioService->sendSms($toPhone, $message);

        if ($smsSent) {
            $this->addFlash('success', 'Réservation réussie et SMS de confirmation envoyé.');
        } else {
            $this->addFlash('error', 'La réservation a échoué, mais nous n\'avons pas pu envoyer de SMS.');
        }

        return $this->redirectToRoute('reservation');
    }

    return $this->render('reservation/reserver.html.twig', [
        'form' => $form->createView(),
    ]);
}



    #[Route('/reservation/new/{id}', name: 'app_reservation_new')]
    public function new(Request $request, ManagerRegistry $doctrine, TwilioService $twilioService, int $id): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        $reservedDates = $doctrine->getRepository(Reservation::class)->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $date = $reservation->getDateReservation();
            $existing = $doctrine->getRepository(Reservation::class)->findOneBy(['date_reservation' => $date]);

            if ($existing) {
                $this->addFlash('error', 'Cette date est déjà réservée.');
            } else {
                $em = $doctrine->getManager();
                $em->persist($reservation);
                $em->flush();

                $to = $reservation->getTel();
                $message = "Bonjour {$reservation->getPrenom()} ! Votre réservation pour le {$reservation->getDateReservation()->format('d/m/Y')} a bien été enregistrée.";

                try {
                    $twilioService->sendSms($to, $message);
                    $this->addFlash('success', 'Réservation enregistrée avec succès ! SMS envoyé.');
                } catch (\Exception $e) {
                    $this->addFlash('error', "Réservation enregistrée, mais erreur lors de l'envoi du SMS : " . $e->getMessage());
                }

                return $this->redirectToRoute('app_reservation_new', ['id' => $id]);
            }
        }

        return $this->render('reservation/reserver.html.twig', [
            'form' => $form->createView(),
            'reservedDates' => $reservedDates,
        ]);
    }

    #[Route('/reservation', name: 'reservation_list')]
    public function showReservationsAction(ManagerRegistry $doctrine): Response
    {
        $reservations = $doctrine->getRepository(Reservation::class)->findAll();

        return $this->render('reservation/list.html.twig', [
            'reservedDates' => $reservations,
        ]);
    }
}
