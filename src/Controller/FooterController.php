<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FooterController extends AbstractController
{
    #[Route('/mentions-legales', name: 'footer/app_legal_notices')]
    public function index(): Response
    {
        return $this->render('footer/mentions-legales.html.twig', []);
    }

    #[Route('/cgv', name: 'footer/app_cgv')]
    public function cgv(): Response
    {
        return $this->render('footer/cgv.html.twig', []);
    }

    #[Route('/nous-rejoindre', name: 'app_nous_rejoindre')]
    public function rejoindre(): Response
    {
        return $this->render('footer/index.html.twig', []);
    }

   
}
