<?php
// src/Command/TestMailerCommand.php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AsCommand(
    name: 'app:send-test-mail',
    description: 'Envoie un email de test',
)]
class TestMailerCommand extends Command
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('idieya504@gmail.com')
            ->to('idieya56@gmail.com')
            ->subject('Test d\'envoi')
            ->text('Ceci est un mail de test envoyé depuis Symfony');

        $this->mailer->send($email);

        $output->writeln('Email envoyé !');
        return Command::SUCCESS;
    }
}
