<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FooterController extends AbstractController
{
   
    #[Route('/mentions-legales', name: 'footer/app_legal_notices', methods: ['GET', 'POST'])]
    public function index(): Response
    {
        return $this->render('footer/mentions-legales.html.twig', []);
    }

    #[Route('/cgv', name: 'footer/app_cgv', methods: ['GET', 'POST'])]
    public function cgv(): Response
    {
        return $this->render('footer/cgv.html.twig', []);
    }

    #[Route('/nous-rejoindre', name: 'footer/app-rejoindre', methods: ['GET', 'POST'])]
    public function rejoindre(): Response
    {
        return $this->render('footer/nous-rejoindre.html.twig', []);
    }

   
}
