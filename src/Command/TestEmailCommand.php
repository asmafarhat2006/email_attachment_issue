<?php

declare(strict_types=1);

namespace App\Command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

#[AsCommand(name: 'app:test-email')]
class TestEmailCommand extends Command
{
    public function __construct(
        private MailerInterface $mailer
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $message = new Email();
        $message->from('asma.farhat2003@gmail.com');
        $message->to('asma.farhat2003@gmail.com');
        $message->html('<p>Test</p>');

        for ($i = 0; $i < 4; $i++) {
            $message->attach(fopen('./large_image.png', 'r'));
            // same issue with the below:
            // $message->attachFromPath('./large_image.png');
            // $message->attachPart(new DataPart(fopen('./large_image.png', 'r')));
        }

        $output->writeln('Memory: ' . memory_get_usage());

        try {
            $this->mailer->send($message);
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
        }

        $output->writeln('Memory: ' . memory_get_usage());
        $output->writeln('Done');

        return 0;
    }
}
