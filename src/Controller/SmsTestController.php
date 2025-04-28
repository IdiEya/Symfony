<?php

namespace App\Controller;

use App\Service\SmsNotifier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SmsTestController extends AbstractController
{
    #[Route('/test-sms', name: 'test_sms')]
    public function testSms(SmsNotifier $smsNotifier): Response
    {
        try {
            $smsNotifier->sendSms('+21623374707', 'Test SMS depuis Symfony avec Twilio');
            return new Response('✅ SMS envoyé avec succès');
        } catch (\Exception $e) {
            return new Response('❌ Erreur : ' . $e->getMessage());
        }
    }
}
