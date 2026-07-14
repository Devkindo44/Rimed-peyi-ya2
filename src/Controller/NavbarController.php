<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class NavbarController extends AbstractController
{
    #[Route('/navbar', name: 'app_navbar')]
    public function renderNavbar(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('_include/_nav.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }
}
