<?php

namespace App\MessageHandler;

use App\Message\AdvertisementMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Advertisement;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AdvertisementMessageHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {

    }

    public function __invoke(AdvertisementMessage $message)
    {
        $advertisement = $this->entityManager->getRepository(Advertisement::class)->find($message->getId());

        if (!$advertisement) {
            return;
        }

        $advertisement->setStatus('published');

        $this->entityManager->flush();
    }
}

