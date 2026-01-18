<?php

namespace App\Tests\Unit;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CsvExporter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporterTest extends TestCase
{
    public function testExportProductsReturnsStreamedResponse()
    {
        $productRepositoryMock = $this->createMock(ProductRepository::class);

        $productRepositoryMock->expects($this->once())
            ->method('findBy')
            ->with([], ['name' => 'ASC'])
            ->willReturn([
                (new Product())->setName('P1')->setPrice(10),
                (new Product())->setName('P2')->setPrice(20),
            ]);

        $exporter = new CsvExporter($productRepositoryMock);

        $response = $exporter->exportProducts();

        $this->assertInstanceOf(StreamedResponse::class, $response);
        
        $this->assertEquals('text/csv; charset=utf-8', $response->headers->get('Content-Type'));
    }
}