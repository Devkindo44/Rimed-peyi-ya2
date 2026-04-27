<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categories')]
final class CategoriesController extends AbstractController

{
    #[Route('/', name: 'app_categories')]
    public function index(): Response
    {
        return $this->render('categories/index.html.twig');
    }

    #[Route('/infusions', name:'app_categories/infusions')]
    public function infusion(): Response

    {
        return $this->render('Categories/infusions.html.twig');
    }


    #[Route('/sirops', name:'app_categories/sirops')]
    public function sirop(): Response

    {
        return $this->render('Categories/sirops.html.twig');
    }

    #[Route('/plantes', name:'app_categories/plantes')]
    public function plantes(): Response

    {
        return $this->render('Categories/plantes.html.twig');
    }
    

    #[Route('/recettes', name:'app_categories/recettes')]
    public function recettes(): Response

    {
        return $this->render('Categories/recettes.html.twig');
    }
       
}
