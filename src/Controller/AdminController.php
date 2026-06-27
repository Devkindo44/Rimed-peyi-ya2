<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    // 1. On change l'URL ici pour /admin/users
    #[Route('/admin/users', name: 'app_admin_user_index')]
    public function index(UserRepository $userRepository, ProductRepository $productRepository, CategoriesRepository $categoriesRepository): Response
    {   
        $lesUtilisateurs = $userRepository->findAll();
        

        return $this->render('admin/index.html.twig', [
            'users' => $lesUtilisateurs, // Bien au pluriel pour correspondre à ton Twig
            'totalUsers' => $userRepository->count([]),
            'totalProducts' => $productRepository->count([]),
            'totalCategories' => $categoriesRepository->count([])
        ]);
    }

    // l'URL ici pour /admin/categories
    #[Route('/admin/categories', name: 'app_admin_categories_index')]
    public function indexCategories(CategoriesRepository $categoriesRepository): Response
    {
        // Paramètres pour recuperer toutes les catégories de la base de données

        $allCategories = $categoriesRepository->findAll();

        // Attention : On utilise un template différent pour ne pas mélanger les utilisateurs et les catégories !
        return $this->render('admin/categories_index.html.twig', [
            'allCategories' => $allCategories
        ]);
    }
}