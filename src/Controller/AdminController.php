<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\AdresseDeLivraisonRepository; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// Préfixe magique qui s'applique à TOUT le contrôleur :
#[Route('/admin', name: 'app_admin_')]
#[IsGranted('ROLE_ADMIN')] // Sécurise tout le back-office d'un coup
final class AdminController extends AbstractController
{
    // URL finale : /admin/users
    // Nom final : app_admin_user_dashboard
    #[Route('/users', name: 'user_dashboard')]
    public function index(
        UserRepository $userRepository, 
        ProductRepository $productRepository, 
        CategoriesRepository $categoriesRepository,
        CommandeRepository $commandesRepository,
    ): Response {   
        $lesUtilisateurs = $userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $lesUtilisateurs,
            'totalUsers' => $userRepository->count([]),
            'totalProducts' => $productRepository->count([]),
            'totalCategories' => $categoriesRepository->count([]),
            'totalCommandes' => $commandesRepository->count([])
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

    

    // URL finale : /admin/commandes
    // Nom final : app_admin_commande_dashboard
    #[Route('/commandes', name: 'commande_dashboard')]
    public function indexCommandes(CommandeRepository $commandeRepository): Response
    {
        return $this->render('admin/commande_dashboard.html.twig', [
            'commandes' => $commandeRepository->findBy([], ['date' => 'DESC'])
        ]);
    }

    // NOUVEAUTÉ 
    // URL finale : /admin/adresses
    // Nom final : app_admin_adresse_de_livraison_index
    #[Route('/adresses', name: 'adresse_de_livraison_index')]
    public function indexAdresses(AdresseDeLivraisonRepository $adresseRepository): Response
    {
        return $this->render('admin/adresse_dashboard.html.twig', [
            'adresses' => $adresseRepository->findAll()
        ]);
    }
}