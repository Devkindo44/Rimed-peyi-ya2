<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    // meth:login charge le fichier twig et transmets les msges d'erreur d'authentification àla vue grace au service AuthenticationUtils
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        //redirection de l'utilisateur vers la paged'accueil s'il est deja connecté

         if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin_user_dashboard');
        }
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // affiche une erreur s'il y en a
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier email saisi par le user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    
    public function logout(): void
    {
        
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
