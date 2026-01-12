<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function exportProducts(): StreamedResponse
    {
        $products = $this->productRepository->findAllOrderedByPrice();

        $response = new StreamedResponse(function () use ($products) {
            $handle = fopen('php://output', 'w+');

            fputs($handle, "\xEF\xBB\xBF");
            
            fputcsv($handle, ['name', 'description', 'price'], ';');

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->getName(),
                    $product->getDescription(),
                    $product->getPrice()
                ], ';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="produits.csv"');

        return $response;
    }
}