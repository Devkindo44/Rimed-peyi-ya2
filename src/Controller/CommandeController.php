<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneDeCommande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CommandeController extends AbstractController
{
    #[Route('/commandes', name: 'app_commande_index')]
    #[IsGranted('ROLE_USER')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        $commandes = $commandeRepository->findBy(
            ['utilisateur' => $this->getUser()],
            ['date' => 'DESC']
        );

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/commande/creer', name: 'app_commande_creer', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function creer(
        Request $request,
        SessionInterface $session, 
        ProductRepository $productRepository, 
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $cart = $session->get('cart', []);

        if (empty($cart)) {
            $this->addFlash('warning', 'Votre panier est vide, impossible de passer commande.');
            return $this->redirectToRoute('app_home_catalogue');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // VÉRIFICATION : Si l'utilisateur n'a aucune adresse de livraison, on le redirige vers le formulaire d'ajout
        if ($user->getAdresseDeLivraisons()->isEmpty()) {
            $this->addFlash('warning', 'Veuillez ajouter au moins une adresse de livraison avant de valider votre commande.');
            return $this->redirectToRoute('app_adresse_de_livraison_new');
        }

        // Initialisation de la commande avec les valeurs par défaut
        $commande = new Commande();
        $commande->setUtilisateur($user);
        $commande->setDate(new \DateTime());
        $commande->setFraisPort(0.0); 
        $commande->setTransporteurNom('Livraison Standard (Gratuite)');

        // Calcul du montant total
        $sousTotal = 0;
        foreach ($cart as $id => $cartValue) {
            $product = $productRepository->find($id);
            if ($product) {
                $quantity = is_array($cartValue) ? ($cartValue['quantity'] ?? $cartValue['quantite'] ?? 1) : $cartValue;
                $sousTotal += $product->getPrice() * (int)$quantity;
            }
        }
        $commande->setMontantTotal($sousTotal + $commande->getFraisPort());

        //  Création du formulaire EN PASSANT L'UTILISATEUR DANS LES OPTIONS user' => $user,
        $form = $this->createForm(CommandeType::class, $commande, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            //  Génération des lignes de commande et mise à jour des stocks
            foreach ($cart as $id => $cartValue) {
                $product = $productRepository->find($id);

                if ($product) {
                    $quantity = is_array($cartValue) ? ($cartValue['quantity'] ?? $cartValue['quantite'] ?? 1) : $cartValue;
                    $finalQuantity = max(1, (int)$quantity);

                    $ligne = new LigneDeCommande();
                    $ligne->setCommande($commande);
                    $ligne->setProduct($product);
                    $ligne->setQuantity($finalQuantity); 
                    $ligne->setPrixUnitaire($product->getPrice());

                    if ($product->getStock()) {
                        $currentStock = $product->getStock()->getQuantity();
                        $product->getStock()->setQuantity($currentStock - $finalQuantity);
                    }

                    $em->persist($ligne);
                }
            }

            $em->persist($commande);
            $em->flush();

            // Process envoi d'email
            $email = (new TemplatedEmail())
            ->from('contact@rimedpéyiya.fr')
            ->to($user->getEmail())
            ->subject('confirmation de votre commande n°' . $commande->getId() . '-Rimed Peyi ya')
            ->htmlTemplate('email/confirmation_commande.html.twig')
            ->context([
                'commande' => $commande,
                'user' => $user
            ]);

            //Envoi de l'email
            $mailer->send($email);

            //Vidage du panier
            $session->set('cart', []);

            $this->addFlash('success', 'Votre commande a été validée avec succès !');
            return $this->redirectToRoute('app_commande_recap', ['id' => $commande->getId()]);
        }

        return $this->render('commande/creer.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart
        ]);
    }

    #[Route('/commande/recap/{id}', name: 'app_commande_recap')]
    #[IsGranted('ROLE_USER')]
    public function recap(Commande $commande): Response
    {
        if ($commande->getUtilisateur() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette commande.');
        }

        return $this->render('commande/recap.html.twig', [
            'commande' => $commande
        ]);
    }

    #[Route('/test-email', name: 'app_test_email')]
public function testEmail(CommandeRepository $commandeRepository): Response
{
    // Récupère la toute dernière commande enregistrée pour avoir de vraies données
    $commande = $commandeRepository->findOneBy([], ['id' => 'DESC']);

    return $this->render('email/confirmation_commande.html.twig', [
        'commande' => $commande,
        'user' => $this->getUser(),
    ]);
}
}