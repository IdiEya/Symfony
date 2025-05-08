<?php
namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationFormType;
use App\Service\TwilioService;
use App\Service\SmsNotifier;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\ReservationRepository;
use Psr\Log\LoggerInterface;

class ReservationController extends AbstractController
{
    private LoggerInterface $logger;
    private ManagerRegistry $doctrine;

    // Constructor with LoggerInterface and ManagerRegistry dependency injection
    public function __construct(LoggerInterface $logger, ManagerRegistry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    #[Route('/reserver', name: 'app_reserver')]
    public function reserver(Request $request, EntityManagerInterface $em, ReservationRepository $reservationRepository, SmsNotifier $smsNotifier): Response
    {
        $user = $this->getUser();
        $reservation = new Reservation();
        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reservation);
            $em->flush();

            // Récupération du numéro de téléphone de la réservation
            $phone = $reservation->getTel();
            $message = 'Votre réservation a été enregistrée avec succès. Merci !';

            // Vérification si le numéro de téléphone est valide
            if ($phone) {
                try {
                    // Log avant d'envoyer le SMS
                    $this->logger->info("Envoi SMS à $phone");
                    $smsNotifier->sendSms($phone, $message);
                    $this->logger->info("SMS envoyé à $phone");
                } catch (\Throwable $e) {
                    // Log de l'erreur en cas d'échec d'envoi
                    $this->logger->error("Échec de l'envoi du SMS", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Redirection après soumission réussie
            return $this->redirectToRoute('reservation_success');
        }

        return $this->render('reservation/reserver.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/reservation/success', name: 'reservation_success')]
    public function success(): Response
    {
        return $this->render('reservation/success.html.twig');
    }

    #[Route('/reservation', name: 'reservation_list')]
    public function showReservationsAction(): Response
    {
        $user = $this->getUser();
        $reservations = $this->doctrine->getRepository(Reservation::class)->findAll();

        return $this->render('reservation/list.html.twig', [
            'reservedDates' => $reservations,
            'user' => $user,
        ]);
    }

    #[Route('/reservation/new/{id}', name: 'app_reservation_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $reservation = new Reservation();
        $form = $this->createForm(ReservationFormType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($reservation);
            $em->flush();

            // Redirection après soumission
            return $this->redirectToRoute('reservation_success');
        }

        return $this->render('reservation/reserver.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
