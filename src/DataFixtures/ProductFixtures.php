<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 20; $i++) {
            $product = new Product();
            $product->setName('Produit ' . $i);
            $product->setDescription('Description du produit ' . $i);
            $product->setPrice(mt_rand(1000, 10000) / 100);

            // Aléatoire entre Physique et Numérique
            if (mt_rand(0, 1)) {
                $product->setType('physique');
                $product->setStock(mt_rand(0, 100));
            } else {
                $product->setType('numerique');
                $product->setLicense('LICENSE-' . strtoupper(uniqid()));
            }

            $manager->persist($product);
        }

        $manager->flush();
    }
}