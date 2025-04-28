<?php


namespace App\Controller;

use App\Entity\Notification1;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notifications')]
class Notification1Controller extends AbstractController
{
    #[Route('', name: 'app_notifications', methods: ['GET'])]
    public function notifications(EntityManagerInterface $entityManager): Response
    {
        // Récupérer les notifications de l'utilisateur actuel
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir vos notifications.');
        }

        // Utilisez le bon repository ici
        $notifications = $entityManager->getRepository(Notification1::class)->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('notifications/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
    
    // Ajouter une méthode pour marquer les notifications comme lues (facultatif)
    #[Route('/mark-as-read/{id}', name: 'app_notification_mark_as_read', methods: ['POST'])]
    public function markAsRead(Notification1 $notification, EntityManagerInterface $entityManager): Response
    {
        // Optionnel : Vérifiez que l'utilisateur est le propriétaire de la notification
        $user = $this->getUser();
        if ($notification->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas voir cette notification.');
        }

        // Marquer la notification comme lue
        $notification->setIsRead(true); // Assurez-vous de gérer ce champ dans votre entité
        $entityManager->flush();

        return $this->redirectToRoute('app_notifications');
    }
}