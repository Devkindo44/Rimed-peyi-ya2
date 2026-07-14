<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

// Préfixe magique qui s'applique à TOUT le contrôleur :
#[Route('/admin', name: 'app_admin_')]
final class AdminController extends AbstractController
{
    // URL finale : /admin/users
    // Nom final : app_admin_user_dashboard
    #[Route('/users', name: 'user_dashboard')]
    public function index(UserRepository $userRepository, ProductRepository $productRepository, CategoriesRepository $categoriesRepository): Response
    {   
        $lesUtilisateurs = $userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $lesUtilisateurs,
            'totalUsers' => $userRepository->count([]),
            'totalProducts' => $productRepository->count([]),
            'totalCategories' => $categoriesRepository->count([])
        ]);
    }

    // URL finale : /admin/categories
    // Nom final : app_admin_categories_dashboard
    #[Route('/categories', name: 'categories_dashboard')]
    public function indexCategories(CategoriesRepository $categoriesRepository): Response
    {
        $allCategories = $categoriesRepository->findAll();

        return $this->render('admin/categories_dashboard.html.twig', [
            'allCategories' => $allCategories
        ]);
    }
}