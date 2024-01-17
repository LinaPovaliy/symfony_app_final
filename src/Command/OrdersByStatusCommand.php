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

class OrdersByStatusCommand extends Command
{
    private AdvertisementRepository $advertisementRepository;
    public function __construct(AdvertisementRepository $advertisementRepository)
    {
        parent::__construct();

        $this->advertisementRepository = $advertisementRepository;
    }

    protected function configure(): void
    {
        $this
            ->setName('orders:by_status')
            ->setDescription('Get a list of advertisements by status')
            ->addArgument('status', InputArgument::OPTIONAL, 'Filter by status');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $status = $input->getArgument('status');

        if ($status) {
            $advertisements = $this->advertisementRepository->findByStatus($status);
        } else {
            $advertisements = $this->advertisementRepository->findAll();
        }

        usort($advertisements, function ($a, $b) {
            return $a->getCreatedAt() <=> $b->getCreatedAt();
        });

        foreach ($advertisements as $advertisement) {
            $output->writeln(sprintf(
                '%s %s %s',
                $advertisement->getId(),
                $advertisement->getHash(),
                $advertisement->getCreatedAt()->format('Y-m-d H:i:s')
            ));
        }

        $totalCount = count($advertisements);
        $output->writeln("=========================");
        $output->writeln("Total: $totalCount");

        return Command::SUCCESS;
    }
}
