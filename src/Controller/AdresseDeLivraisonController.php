<?php

namespace App\Controller;

use App\Entity\AdresseDeLivraison;
use App\Form\AdresseDeLivraisonType;
use App\Repository\AdresseDeLivraisonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/adresse/de/livraison')]
#[IsGranted('ROLE_USER')] // Sécurise le contrôleur pour tous les utilisateurs connectés
final class AdresseDeLivraisonController extends AbstractController
{
    #[Route(name: 'app_adresse_de_livraison_index', methods: ['GET'])]
    public function index(AdresseDeLivraisonRepository $adresseDeLivraisonRepository): Response
    {
        return $this->render('adresse_de_livraison/index.html.twig', [
            'adresse_de_livraisons' => $adresseDeLivraisonRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'app_adresse_de_livraison_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $adresseDeLivraison = $user->getAdresseDeLivraisons()->first();

        if (!$adresseDeLivraison) {
            $adresseDeLivraison = new AdresseDeLivraison();
            $adresseDeLivraison->setUser($user);
        }

        $form = $this->createForm(AdresseDeLivraisonType::class, $adresseDeLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$entityManager->contains($adresseDeLivraison)) {
                $entityManager->persist($adresseDeLivraison);
            }
            
            $entityManager->flush();
            $this->addFlash('success', 'L\'adresse de livraison a bien été mise à jour.');

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_admin_adresse_de_livraison_index');
            }

            return $this->redirectToRoute('app_commande_creer');
        }

        return $this->render('adresse_de_livraison/new.html.twig', [
            'adresse_de_livraison' => $adresseDeLivraison,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_adresse_de_livraison_show', methods: ['GET'])]
    public function show(AdresseDeLivraison $adresseDeLivraison): Response
    {
        if ($adresseDeLivraison->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Cette adresse ne vous appartient pas.");
        }

        return $this->render('adresse_de_livraison/show.html.twig', [
            'adresse_de_livraison' => $adresseDeLivraison,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_adresse_de_livraison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdresseDeLivraison $adresseDeLivraison, EntityManagerInterface $entityManager): Response
    {
        if ($adresseDeLivraison->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas modifier cette adresse.");
        }

        $form = $this->createForm(AdresseDeLivraisonType::class, $adresseDeLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'L\'adresse a été modifiée avec succès.');

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('app_admin_adresse_de_livraison_index');
            }

            return $this->redirectToRoute('app_commande_creer');
        }

        return $this->render('adresse_de_livraison/edit.html.twig', [
            'adresse_de_livraison' => $adresseDeLivraison,
            'form' => $form,
        ]);
    }

    /**
     * ROUTE UNIQUE DE SUPPRESSION (Sécurisée par méthode POST + Jeton CSRF)
     */
    #[Route('/{id}', name: 'app_adresse_de_livraison_delete', methods: ['POST'])]
    public function delete(Request $request, AdresseDeLivraison $adresseDeLivraison, EntityManagerInterface $entityManager): Response
    {
        // Vérification des droits d'accès (Propriétaire OU Admin)
        if ($adresseDeLivraison->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas supprimer cette adresse.");
        }

        // Validation du token CSRF
        if ($this->isCsrfTokenValid('delete'.$adresseDeLivraison->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($adresseDeLivraison);
            $entityManager->flush();
            $this->addFlash('danger', 'L\'adresse a été supprimée avec succès.');
        }

        // Redirection contextuelle selon le rôle
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin_adresse_de_livraison_index');
        }

        return $this->redirectToRoute('app_adresse_de_livraison_index');
    }
}