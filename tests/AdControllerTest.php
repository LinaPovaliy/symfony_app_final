<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/order/1');

        $this->assertResponseStatusCodeSame(200);

        $advertisementRepository = $client->getResponse()->getContent();

        $this->assertNotEmpty($advertisementRepository, "Response content is empty");

        $advertisement = $advertisementRepository->find(1);

        $this->assertArrayHasKey('name', $advertisement->getName());
        $this->assertArrayHasKey('category', ['name' => $advertisement->getCategory()->getName()]);
        $this->assertArrayHasKey('status', $advertisement->getStatus());
        $this->assertArrayHasKey('hash', $advertisement->getHash());
        $this->assertArrayHasKey('created_at', $advertisement->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertArrayHasKey('public_url', $this->generateUrl('category', [
            'slug' => $advertisement->getCategory()->getSlug(),
            'order_hash' => $advertisement->getHash(),
        ], true));
    }
}
