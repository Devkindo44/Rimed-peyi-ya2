<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/ Category')]
final class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category')]
    public function index(): Response
    {
         return $this->render('category/index.html.twig');
    }

   

    #[Route('/infusions', name:'app_category/infusions')]
    public function infusion(): Response

    {
        return $this->render('Category/infusions.html.twig');
    }


    #[Route('/sirops', name:'app_category/sirops')]
    public function sirop(): Response

    {
        return $this->render('Category/sirops.html.twig');
    }

    #[Route('/plantes', name:'app_category/plantes')]
    public function plantes(): Response

    {
        return $this->render('app_category/plantes.html.twig');
    }
    

    #[Route('/recettes', name:'app_category/recettes')]
    public function recettes(): Response

    {
        return $this->render('category/recettes.html.twig');
    }
}
