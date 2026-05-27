<?php

namespace App\Controller;


use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }


    #[Route('/contact', name:'app_contact', methods: ['GET', 'POST'])]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }

        
    
    #[Route('/home/catalogue', name: 'app_home_catalogue')]
    // 1. On injecte le ProductRepository entre les parenthèses de la fonction
    public function catalogue(ProductRepository $productRepository): Response
    {
        // 2. On récupère tous les produits de la Base de Données
        $products = $productRepository->findAll();

        // 3. On injecte le tableau de produits dans le render pour que Twig y ait accès !
        return $this->render('home/catalogue.html.twig', [
            'products' => $products, // <-- C'est cette ligne exacte qui corrige votre erreur
        ]);
    }
}

        


