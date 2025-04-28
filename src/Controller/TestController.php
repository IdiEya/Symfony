<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test-smtp-only', name: 'test_smtp_only')]
    public function testSmtpOnly(MailerInterface $mailer): Response
    {
        try {
            $email = (new Email())
                ->from('trabelsi.montaha123@gmail.com') // Doit correspondre à l'email Gmail configuré
                ->to('trabelsi.montaha123@gmail.com') // Votre email de test
                ->subject('Test SMTP')
                ->text('Ceci est un test technique');
            
            $mailer->send($email);
            return new Response("Email envoyé! Vérifiez votre boîte mail (y compris les spams)");
            
        } catch (\Exception $e) {
            return new Response("Échec SMTP: " . $e->getMessage());
        }
    }
}