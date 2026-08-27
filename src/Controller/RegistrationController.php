<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encryptage du mot de passe par UserPasswordHasherInterface
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            
            // Génération d'une URL signée et l'envoie par e-mail à l'utilisateur.
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@rimedpeyiya.com', 'No reply RimedPeyiYa'))
                    ->to((string) $user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // Après confirmation du mail le user devient verifié (isVerified=>true)
            //le lien a une durée de validité, si expiré refaire le process

        $this->addFlash('success', 'Votre compte a été créé. Un e-mail de confirmation vous a été envoyé.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Security $security, Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
             $this->addFlash('verify_email_error', 'Lien de confirmation invalide ou compte introuvable.');
            return $this->redirectToRoute('app_register');
           
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            $this->addFlash('verify_email_error', 'Lien de confirmation invalide ou compte introuvable.');
            return $this->redirectToRoute('app_register');
            
        }

        // confirmer le lien de validation, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);

            //gestion des erreurs : si le lien est expiré user redirigé vers le lien d'inscription

        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_login');
        }

        // connexion du user automatique après validation du lien dans l'email
        $security->login($user, EmailAuthenticator::class, 'main');
      
        $this->addFlash('success', 'Votre email est validée, vous êtes maintenant connecté !.');

        //Redirection vers la page de connexion pouse connecter
        return $this->redirectToRoute('app_home');
    }
}
