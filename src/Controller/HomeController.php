<?php

namespace App\Controller;

use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Attribute\IsUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user) {
            if ($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_MANAGER')) {
                return $this->redirectToRoute('app_dashboard');
            }

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('home/index.html.twig');
    }
}