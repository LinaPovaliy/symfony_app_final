<?php

namespace App\Command;

use App\Repository\AdvertisementRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendNotificationCommand extends Command
{
    public function __construct(AdvertisementRepository $advertisementRepository, MailerInterface $mailer)
    {
        parent::__construct();

        $this->advertisementRepository = $advertisementRepository;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this->setName('orders:send_notification')
            ->setDescription('Send notification email about the number of advertisements in a specified status')
            ->addArgument('status', InputArgument::REQUIRED, 'Status of advertisements')
            ->addArgument('email', InputArgument::REQUIRED, 'Email address to send notification');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = $input->getArgument('status');
        $emailAddress = $input->getArgument('email');

        $io = new SymfonyStyle($input, $output);

        $totalOrders = $this->advertisementRepository->getTotalByStatus($status);

        $subject = 'Advertisement Notification';
        $message = sprintf("Total orders in status '%s': %d", $status, $totalOrders);

        $email = (new Email())
            ->from('adexpress@mail.ru')
            ->to($emailAddress)
            ->subject($subject)
            ->text($message);

        $this->mailer->send($email);

        $io->success(sprintf("Notification email sent to %s", $emailAddress));
        $io->section('Total:');
        $io->listing([$totalOrders]);

        return Command::SUCCESS;
    }
}