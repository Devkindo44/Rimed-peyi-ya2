<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe s'il est renseigné
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            // Persistance de l'utilisateur
            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/profile/delete', name: 'app_account_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function deleteAccount(
        Request $request, 
        EntityManagerInterface $entityManager, 
        TokenStorageInterface $tokenStorage
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete_account_' . $user->getId(), $request->request->get('_token'))) {
            
            // 1. Déconnexion de l'utilisateur
            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();

            // 2. Suppression de l'utilisateur en BDD
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été supprimé avec succès.');

            return $this->redirectToRoute('app_home'); // Assure-toi que la route 'app_home' existe pour la redirection
        }

        $this->addFlash('danger', 'Jeton CSRF invalide.');

        return $this->redirectToRoute('app_profile');
    }
}