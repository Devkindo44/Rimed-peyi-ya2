<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FooterController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_mentions_legales', methods: ['GET'])]
    public function mentionsLegales(): Response
    {
        return $this->render('footer/mentions-legales.html.twig');
    }

    #[Route('/cgv', name: 'app_cgv', methods: ['GET'])]
    public function cgv(): Response
    {
        return $this->render('footer/cgv.html.twig');
    }

    #[Route('/cgu', name: 'app_cgu', methods: ['GET'])]
    public function cgu(): Response
    {
        
        return $this->render('footer/cgu.html.twig');
    }

    #[Route('/apropos', name: 'app_a_propos', methods: ['GET'])]
    public function apropos(): Response
    {
        
        return $this->render('footer/a-propos.html.twig');
    }

    // On garde 'POST' ici au cas où la page "Nous rejoindre" contient un formulaire de candidature
    #[Route('/nous-rejoindre', name: 'app_rejoindre', methods: ['GET', 'POST'])]
    public function rejoindre(): Response
    {
        return $this->render('footer/nous-rejoindre.html.twig');
    }
}