<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire; 

#[AsCommand(
    name: 'app:import-products',
    description: 'Importe des produits depuis un fichier CSV situé dans /public',
)]
class ImportProductsCommand extends Command
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')] private string $projectDir,
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $file = $this->projectDir . '/public/import.csv';

        if (!file_exists($file)) {
            $io->error('Le fichier public/import.csv n\'existe pas.');
            return Command::FAILURE;
        }

        $io->title('Importation des produits...');

        if (($handle = fopen($file, "r")) !== false) {
            fgetcsv($handle, 1000, ";");

            $count = 0;
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                if (count($data) < 3) continue;

                $product = new Product();
                $product->setName($data[0]);
                $product->setDescription($data[1]);
                $product->setPrice((float) str_replace(',', '.', $data[2]));
                $product->setType($data[3]);
                if($data[3]=="physique" || $data[3]=="physique"){$product->setStock($data[4]);}
                

                $this->entityManager->persist($product);
                $count++;
            }
            fclose($handle);

            $this->entityManager->flush();
            $io->success("$count produits importés avec succès !");
            return Command::SUCCESS;
        }

        $io->error('Impossible de lire le fichier.');
        return Command::FAILURE;
    }
}