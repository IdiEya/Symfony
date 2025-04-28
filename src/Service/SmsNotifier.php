<?php

namespace App\Service;

use Twilio\Rest\Client;

class SmsNotifier
{
    private $twilioClient;
    private $from;

    public function __construct(string $sid, string $token, string $from)
    {
        $this->twilioClient = new Client($sid, $token);
        $this->from = $from;
    }

    public function sendSms(string $to, string $message): void
    {
        // Formatage correct du numéro (optionnel selon ta base de données)
        if (!preg_match('/^\+\d{6,15}$/', $to)) {
            throw new \InvalidArgumentException("Numéro de téléphone invalide : $to");
        }

        $this->twilioClient->messages->create(
            $to,
            [
                'from' => $this->from,
                'body' => $message,
            ]
        );
    }
}
