<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            ['email' => 'admin@test.com', 'role' => ['ROLE_ADMIN'], 'firstname' => 'Super', 'lastname' => 'Admin'],
            ['email' => 'manager@test.com', 'role' => ['ROLE_MANAGER'], 'firstname' => 'John', 'lastname' => 'Manager'],
            ['email' => 'user@test.com', 'role' => ['ROLE_USER'], 'firstname' => 'Alice', 'lastname' => 'User'],
        ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setFirstname($userData['firstname']);
            $user->setLastname($userData['lastname']);
            $user->setRoles($userData['role']);

            $password = $this->hasher->hashPassword($user, 'password');
            $user->setPassword($password);

            $manager->persist($user);
        }

        // 2. Création de Produits factices
        for ($i = 1; $i <= 20; $i++) {
            $product = new Product();
            $product->setName('Produit ' . $i);
            $product->setDescription('Une description incroyable pour le produit numéro ' . $i);
            // Prix aléatoire entre 10 et 100
            $product->setPrice(mt_rand(1000, 10000) / 100); 

            $manager->persist($product);
        }

        $manager->flush();
    }
}