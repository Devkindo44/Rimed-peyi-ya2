<?php

namespace App\Controller;


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

        
    #[Route('/catalogue', name:'app_catalogue', methods: ['GET', 'POST'])]
    public function boutique(): Response
    {
        return $this->render('home/catalogue.html.twig');
    }

        

}
