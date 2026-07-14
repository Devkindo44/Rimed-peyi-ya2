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
#[IsGranted('ROLE_USER')] // Sécurise tout le contrôleur pour les utilisateurs connectés
final class AdresseDeLivraisonController extends AbstractController
{
    #[Route(name: 'app_adresse_de_livraison_index', methods: ['GET'])]
    public function index(AdresseDeLivraisonRepository $adresseDeLivraisonRepository): Response
    {
        // Optionnel :Afficher que les adresses de l'utilisateur connecté
        return $this->render('adresse_de_livraison/index.html.twig', [
            'adresse_de_livraisons' => $adresseDeLivraisonRepository->findBy(['user' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'app_adresse_de_livraison_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Verfification si l'utilisateur possède déjà une adresse
        $adresseDeLivraison = $user->getAdresseDeLivraisons()->first();

        // 2. S'il n'en a aucune, ALORS on instancie un nouvel objet et on lui associe le User
        if (!$adresseDeLivraison) {
            $adresseDeLivraison = new AdresseDeLivraison();
            $adresseDeLivraison->setUser($user); // Lie l'utilisateur à son adrresse
        }

        // 3. Si l'adresse existe, remplissage automatique du formulaire avec les infos
        $form = $this->createForm(AdresseDeLivraisonType::class, $adresseDeLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On utilise persist dans l'entité uniquement si l'adresse est nouvelle en BDD
            if (!$entityManager->contains($adresseDeLivraison)) {
                $entityManager->persist($adresseDeLivraison);
            }
            
            $entityManager->flush();

            $this->addFlash('success', 'Votre adresse de livraison a bien été mise à jour.');

            // Redirection logique vers la finalisation de la commande !
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
        if ($adresseDeLivraison->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Cette adresse ne vous appartient pas.");
        }

        return $this->render('adresse_de_livraison/show.html.twig', [
            'adresse_de_livraison' => $adresseDeLivraison,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_adresse_de_livraison_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdresseDeLivraison $adresseDeLivraison, EntityManagerInterface $entityManager): Response
    {
        if ($adresseDeLivraison->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas modifier cette adresse.");
        }

        $form = $this->createForm(AdresseDeLivraisonType::class, $adresseDeLivraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_creer');
        }

        return $this->render('adresse_de_livraison/edit.html.twig', [
            'adresse_de_livraison' => $adresseDeLivraison,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_adresse_de_livraison_delete', methods: ['POST'])]
    public function delete(Request $request, AdresseDeLivraison $adresseDeLivraison, EntityManagerInterface $entityManager): Response
    {
        if ($adresseDeLivraison->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas supprimer cette adresse.");
        }

        if ($this->isCsrfTokenValid('delete'.$adresseDeLivraison->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($adresseDeLivraison);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_adresse_de_livraison_index', [], Response::HTTP_SEE_OTHER);
    }
}