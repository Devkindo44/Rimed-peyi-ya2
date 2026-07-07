<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneDeCommande;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CommandeController extends AbstractController
{
    #[Route('/commande/creer', name: 'app_commande_creer')]
    #[IsGranted('ROLE_USER')]
    public function creer(
        SessionInterface $session, 
        ProductRepository $productRepository, 
        EntityManagerInterface $em
    ): Response {
        $cart = $session->get('cart', []);

        if (empty($cart)) {
            $this->addFlash('warning', 'Votre panier est vide, impossible de passer commande.');
            return $this->redirectToRoute('app_home_catalogue');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // --- RÉCUPÉRATION ET VÉRIFICATION DE L'ADRESSE ---
        $adresse = $user->getAdresseDeLivraisons()->first();

        if (!$adresse) {
            $this->addFlash('danger', 'Veuillez configurer une adresse de livraison dans votre profil avant de commander.');
            return $this->redirectToRoute('app_adresse_de_livraison_new'); // Ou redirige vers ta page profil / formulaire adresse
        }
        // -------------------------------------------------

        $commande = new Commande();
        $commande->setUtilisateur($user);
        $commande->setAdressedeLivraison($adresse); // Assigne l'adresse de livraison obligatoire !
        $commande->setDate(new \DateTime());
        
        $commande->setFraisPort(0.0);
        $commande->setTransporteurNom('Livraison Standard (Gratuite)');

        $sousTotal = 0;

        foreach ($cart as $id => $cartValue) {
            $product = $productRepository->find($id);

            if ($product) {
                $quantity = 1;

                if (is_array($cartValue)) {
                    if (isset($cartValue['quantity'])) {
                        $quantity = $cartValue['quantity'];
                    } elseif (isset($cartValue['quantite'])) {
                        $quantity = $cartValue['quantite'];
                    }
                } 
                elseif (is_numeric($cartValue) || is_string($cartValue)) {
                    $quantity = $cartValue;
                }

                $finalQuantity = (int)$quantity;
                if ($finalQuantity < 1) {
                    $finalQuantity = 1;
                }

                $ligne = new LigneDeCommande();
                $ligne->setCommande($commande);
                $ligne->setProduct($product);
                $ligne->setQuantity($finalQuantity); 
                $ligne->setPrixUnitaire($product->getPrice());

                $sousTotal += $product->getPrice() * $finalQuantity;

                if ($product->getStock()) {
                    $currentStock = $product->getStock()->getQuantity();
                    $product->getStock()->setQuantity($currentStock - $finalQuantity);
                }

                $em->persist($ligne);
            }
        }

        $commande->setMontantTotal($sousTotal + $commande->getFraisPort());

        $em->persist($commande);
        $em->flush();

        $session->set('cart', []);

        $this->addFlash('success', 'Votre commande a été validée avec succès !');

        return $this->redirectToRoute('app_commande_recap', ['id' => $commande->getId()]);
    }

    #[Route('/commande/recap/{id}', name: 'app_commande_recap')]
    #[IsGranted('ROLE_USER')]
    public function recap(Commande $commande): Response
    {
        if ($commande->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette commande.');
        }

        return $this->render('commande/recap.html.twig', [
            'commande' => $commande
        ]);
    }
}