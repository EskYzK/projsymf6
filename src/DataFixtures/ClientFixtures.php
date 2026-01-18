<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($j = 1; $j <= 5; $j++) {
            $client = new Client();
            $client->setFirstname('Prénom' . $j);
            $client->setLastname('Nom' . $j);
            $client->setEmail('client' . $j . '@test.com');
            $client->setPhoneNumber('06000000' . str_pad((string)$j, 2, '0', STR_PAD_LEFT));
            $client->setAddress($j . ' rue de la Paix, 7500'.$j. 'Paris');
            
            $manager->persist($client);
        }
        $manager->flush();
    }
}