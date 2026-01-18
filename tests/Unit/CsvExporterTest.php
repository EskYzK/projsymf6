<?php

namespace App\Tests\Unit;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CsvExporter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporterTest extends TestCase
{
    public function testExportProductsReturnsStreamedResponseWithContent()
    {
        $productRepositoryMock = $this->createMock(ProductRepository::class);

        $p1 = (new Product())->setName('Produit A')->setPrice(10)->setDescription('Desc A');
        $p2 = (new Product())->setName('Produit B')->setPrice(20)->setDescription('Desc B');

        $productRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with([], ['name' => 'ASC'])
            ->willReturn([$p1, $p2]);

        $exporter = new CsvExporter($productRepositoryMock);

        $response = $exporter->exportProducts();

        $this->assertInstanceOf(StreamedResponse::class, $response);
        $this->assertEquals('text/csv; charset=utf-8', $response->headers->get('Content-Type'));

        ob_start();
        $response->sendContent();
        $content = ob_get_clean(); 

        $this->assertStringContainsString('Nom;Prix', $content);
        
        $this->assertStringContainsString('"Produit A"', $content); 
        $this->assertStringContainsString('10', $content);
    }

    public function testExportProductsWithCustomSort()
    {
        $productRepositoryMock = $this->createMock(ProductRepository::class);

        $productRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with([], ['price' => 'DESC'])
            ->willReturn([]);

        $exporter = new CsvExporter($productRepositoryMock);

        $exporter->exportProducts(['price' => 'DESC']);
    }
}