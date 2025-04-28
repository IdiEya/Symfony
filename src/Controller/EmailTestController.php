<?php
// src/Controller/EmailTestController.php
namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class EmailTestController
{
    /**
     * @Route("/send-email", name="send_email")
     */
    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(MailerInterface $mailer): Response
{
    try {
        $email = (new Email())
            ->from('idieya504@gmail.com')
            ->to('idieya56@gmail.com')
            ->subject('Test Email')
            ->text('This is a test.');

        $mailer->send($email);

        return new Response('Email sent!');
    } catch (\Exception $e) {
        return new Response('Failed to send email: '.$e->getMessage());
    }
}
}
