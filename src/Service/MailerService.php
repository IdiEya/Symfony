<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, string $content): void
    {
        $email = (new Email())
            ->from('trabelsi.montaha123@gmail.com') // Ton adresse Gmail (bien configurer)
            ->to($to)                     // Destinataire
            ->subject($subject)
            ->html($content);               // Contenu HTML autorisé

        $this->mailer->send($email);
    }
}