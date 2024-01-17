<?php

namespace App\DataFixtures;

use App\Entity\Advertisement;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdvertisementFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $category = new Category();
        $category->setName("category");
        $dateTimeString = '2022-01-01 12:00:00';
        $dateTimeImmutable = new \DateTimeImmutable($dateTimeString);
        $manager->persist($category);
        for ($i = 1; $i <= 10; $i++) {
            $advertisement = new Advertisement();
            $advertisement->setName("Advertisement $i");
            $advertisement->setCategory($category);
            $advertisement->setStatus('published');
            $advertisement->setCreatedAt($dateTimeImmutable);

            $manager->persist($advertisement);
        }

        $manager->flush();
    }
}
