<?php
// src/Controller/SmsController.php
namespace App\Controller;
use Twilio\Rest\Client;

use App\Service\TwilioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SmsController extends AbstractController
{
    #[Route('/send-sms', name: 'send_sms')]
    public function sendSms(TwilioService $twilioService): Response
    {
        $twilioService->sendSms('+216xxxxxxxx', 'Bonjour depuis Twilio et Symfony !');

        return new Response('SMS envoyé avec succès');
    }
}
