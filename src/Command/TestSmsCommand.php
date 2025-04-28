<?php
// src/Command/TestSmsCommand.php
namespace App\Command;

use App\Service\SmsNotifier;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:test-sms')]
class TestSmsCommand extends Command
{
    private $smsNotifier;

    public function __construct(SmsNotifier $smsNotifier)
    {
        parent::__construct();
        $this->smsNotifier = $smsNotifier;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->smsNotifier->sendSms('+21623374707', 'Test SMS depuis Symfony');
        $output->writeln('SMS envoyé avec succès !');
        return Command::SUCCESS;
    }
}
