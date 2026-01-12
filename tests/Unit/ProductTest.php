<?php

namespace App\Tests\Unit;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testProductEntity()
    {
        $product = new Product();
        $date = new \DateTimeImmutable();

        $product->setName('Vélo Rouge');
        $product->setPrice(150.50);
        $product->setType('physique');

        $this->assertSame('Vélo Rouge', $product->getName());
        $this->assertSame(150.50, $product->getPrice());
        $this->assertSame('physique', $product->getType());
        
        $this->assertNull($product->getStock());
    }
}