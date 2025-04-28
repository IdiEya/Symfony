<?php

namespace App\Controller;

use App\Service\SmsNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SmsController extends AbstractController
{
    #[Route('/send-sms', name: 'send_sms')]
    public function sendSms(SmsNotifier $smsNotifier): Response
    {
        $smsNotifier->sendSms('+21623374707', 'Hello from Twilio!');
        return new Response('Message envoyé !');
    }
}
