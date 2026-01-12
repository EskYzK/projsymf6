<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\ExpressionLanguage\Expression;

#[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_MANAGER")'))]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        UserRepository $userRepo,
        ProductRepository $productRepo,
        ClientRepository $clientRepo
    ): Response {
        $stats = [
            'users' => $userRepo->count([]),
            'products' => $productRepo->count([]),
            'clients' => $clientRepo->count([]),
        ];

        $latestClients = $clientRepo->findBy([], ['id' => 'DESC'], 5);

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'latestClients' => $latestClients
        ]);
    }
}