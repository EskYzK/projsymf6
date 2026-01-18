<?php

namespace App\Tests\Unit;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductEntityTest extends KernelTestCase
{
    public function testProductEntityIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $validator = $container->get('validator');

        $product = new Product();
        $product->setName('Bon Produit')
            ->setPrice(100)
            ->setDescription('Une description valide')
            ->setType('physique')
            ->setStock(10);

        $errors = $validator->validate($product);

        $this->assertCount(0, $errors, 'Le produit devrait être valide');
    }

    public function testProductEntityIsInvalidWithNegativePrice(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $validator = $container->get('validator');

        $product = new Product();
        $product->setName('Produit Pas Cher')
            ->setPrice(-50)
            ->setType('physique');

        $errors = $validator->validate($product);

        $this->assertGreaterThan(0, count($errors), 'Le produit ne devrait pas accepter un prix négatif');
    }

    public function testProductEntityIsInvalidWithoutName(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $validator = $container->get('validator');

        $product = new Product();
        $product->setPrice(10);

        $errors = $validator->validate($product);

        $this->assertGreaterThan(0, count($errors), 'Le produit doit avoir un nom');
    }
}