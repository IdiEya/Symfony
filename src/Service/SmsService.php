<?php
// src/Service/SmsService.php
// src/Service/SmsService.php
namespace App\Service;

use App\Repository\UserRepository;  // Importation de UserRepository
use Twilio\Rest\Client;

class SmsService
{
    private string $sid;
    private string $authToken;
    private string $twilioPhoneNumber;
    private Client $client;
    private UserRepository $userRepository;

    // Injecter UserRepository ainsi que les informations Twilio
    public function __construct(
        string $sid, 
        string $authToken, 
        string $twilioPhoneNumber,
        UserRepository $userRepository // Injecter UserRepository
    ) {
        $this->sid = $sid;
        $this->authToken = $authToken;
        $this->twilioPhoneNumber = $twilioPhoneNumber;
        $this->client = new Client($this->sid, $this->authToken);
        $this->userRepository = $userRepository; // Assignation de UserRepository
    }

    // Envoi de SMS à tous les utilisateurs
    public function sendSmsToAllUsers(string $message): void
    {
        $users = $this->userRepository->findAll();  // Récupérer tous les utilisateurs

        // Envoi du message à tous les utilisateurs
        foreach ($users as $user) {
            $this->sendSms($user->getPhoneNumber(), $message);
        }
    }

    // Envoi de SMS à un utilisateur spécifique
    public function sendSms(string $to, string $message): bool
    {
        try {
            // Utiliser Twilio pour envoyer le SMS
            $this->client->messages->create(
                $to,
                [
                    'from' => $this->twilioPhoneNumber,
                    'body' => $message
                ]
            );
            return true;
        } catch (\Exception $e) {
            // Gérer les erreurs (enregistrer dans les logs si nécessaire)
            return false;
        }
    }
}
