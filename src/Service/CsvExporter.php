<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvExporter
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    public function exportProducts(array $orderBy = ['name' => 'ASC']): StreamedResponse
    {
        $products = $this->productRepository->findBy([], $orderBy);

        $response = new StreamedResponse(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Nom', 'Prix', 'Type & Stock', 'Description'], ';');

            foreach ($products as $product) {
                if ($product->getType() === 'numerique') {
                    $stockInfo = 'Licence Numérique';
                } else {
                    $stockInfo = sprintf('Physique (Stock: %d)', $product->getStock());
                }

                fputcsv($handle, [
                    $product->getId(),
                    $product->getName(),
                    $product->getPrice(),
                    $stockInfo,
                    $product->getDescription()
                ], ';');
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="produits.csv"');

        return $response;
    }
}