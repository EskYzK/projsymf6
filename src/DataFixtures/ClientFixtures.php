<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($j = 1; $j <= 15; $j++) {
            $client = new Client();
            $client->setName('Client ' . $j);
            $client->setEmail('client' . $j . '@entreprise.com');
            $client->setCompany('Société ' . $j);
            $client->setPhone('06000000' . str_pad((string)$j, 2, '0', STR_PAD_LEFT));
            
            $manager->persist($client);
        }

        $manager->flush();
    }
}