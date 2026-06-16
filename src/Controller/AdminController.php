<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
    // 1. On change l'URL ici pour /admin/users
    #[Route('/admin/users', name: 'app_admin_user_index')]
    public function index(UserRepository $userRepository): Response
    {
        $lesUtilisateurs = $userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $lesUtilisateurs, // Bien au pluriel pour correspondre à ton Twig
        ]);
    }

    // 2. On change l'URL ici pour /admin/categories
    #[Route('/admin/categories', name: 'app_admin_categories_index')]
    public function indexCategories(): Response
    {
        // Attention : On utilise un template différent pour ne pas mélanger les utilisateurs et les catégories !
        return $this->render('admin/categories_index.html.twig', []);
    }
}