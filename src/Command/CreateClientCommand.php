<?php

namespace App\Command;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-client',
    description: 'Crée un nouveau client via un assistant interactif',
)]
class CreateClientCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Assistant de création de Client');

        $firstname = $io->ask('Prénom du client', null, function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('Le prénom ne peut pas être vide.');
            }
            return $answer;
        });

        $lastname = $io->ask('Nom du client', null, function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('Le nom ne peut pas être vide.');
            }
            return $answer;
        });

        $email = $io->ask('Email', null, function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Format email invalide.');
            }

            $existingClient = $this->entityManager->getRepository(Client::class)->findOneBy(['email' => $answer]);
            if ($existingClient) {
                throw new \RuntimeException('Cet email est déjà utilisé par un autre client.');
            }

            return $answer;
        });

        $phone = $io->ask('Numéro de téléphone', null, function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('Le numéro est requis.');
            }
            return $answer;
        });

        $address = $io->ask('Adresse postale', null, function ($answer) {
            if (empty($answer)) {
                throw new \RuntimeException('L\'adresse est requise.');
            }
            return $answer;
        });

        $io->section('Enregistrement en cours...');

        $client = new Client();
        $client->setFirstname($firstname);
        $client->setLastname($lastname);
        $client->setEmail($email);
        $client->setPhoneNumber($phone);
        $client->setAddress($address);

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        $io->success(sprintf('Le client "%s %s" a été créé avec succès !', $firstname, $lastname));

        return Command::SUCCESS;
    }
}