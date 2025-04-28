<?php
// src/Service/NotificationService.php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Entity\User;

class NotificationService
{
    private $entityManager;
    private $twilioSid;
    private $twilioToken;
    private $twilioPhoneNumber;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $twilioSid,
        string $twilioToken,
        string $twilioPhoneNumber
    ) {
        $this->entityManager = $entityManager;
        $this->twilioSid = $twilioSid;
        $this->twilioToken = $twilioToken;
        $this->twilioPhoneNumber = $twilioPhoneNumber;
    }

    public function sendNotificationToUsers(string $message)
    {
        // Récupérer tous les utilisateurs
        $users = $this->entityManager->getRepository(User::class)->findAll();

        // Boucle à travers tous les utilisateurs pour envoyer un message
        foreach ($users as $user) {
            $telephone = $user->getTelephone();
            if ($telephone) {
                // Utiliser un service d'envoi de SMS (ex. Twilio)
                $this->sendSms($telephone, $message);
            }
        }
    }

    private function sendSms(string $telephone, string $message)
    {
        // Code pour envoyer un SMS via Twilio
        // Exemple avec Twilio SDK
        $twilio = new \Twilio\Rest\Client($this->twilioSid, $this->twilioToken);
        $twilio->messages->create(
            $telephone,
            [
                'from' => $this->twilioPhoneNumber,
                'body' => $message
            ]
        );
    }
}
