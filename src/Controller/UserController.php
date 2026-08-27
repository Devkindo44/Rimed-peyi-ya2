<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;



#[Route('/admin/user')]
final class UserController extends AbstractController
{
    #[Route('', name: 'app_admin_user', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [

            //Recuperation de tous les users
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $userPasswordHasher,
        MailerInterface $mailer
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // Récupération du mot de passe saisi en clair
            $plainPassword = $form->get('plainPassword')->getData();

            if ($plainPassword) {
                // Hachage et définition du mot de passe avant enregistrement en bdd
                $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }


            $entityManager->persist($user);
            $entityManager->flush();

            //Process d'envoi d'email de premiere connexion par l'admin
            $email = (new Email())
            ->from('admin@rimedpeyiya.com')
            ->to($user->getEmail())
            ->subject('Activation de votre compte')
            ->html('<p>Bonjour' . $user->getFirstName() . ', Votre compte a été créé !
            Vous pourrez le changer après votre connexion.
            <p>');

            $mailer->send($email);

            //Redirection vers tableau de bord admin
            return $this->redirectToRoute('app_admin_user_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
            //affichage d'un user précis
    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        User $user, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // la mise à jour du mot de passe se fait seulement si un nouveau a été saisi
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

                //Validation immediat si créé par admin
                $user->setIsVerified(true);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
            //Verification securite csrf avant la suppression
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            //Message flash de succès envoyé en session user
            $this->addFlash('success', 'L\'utilisateur a bien été supprimé.');
        }

        return $this->redirectToRoute('app_admin_user_dashboard', [], Response::HTTP_SEE_OTHER);
    }
}