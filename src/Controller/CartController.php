<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    #[Route('/cart', name: 'app_cart', methods: ['GET'])]
    public function index(SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        $cartWithData = [];
        
        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if ($product) {
                // association du produit à sa quantité 
                $cartWithData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            }
        }
            // calcule le total en une ligne  prix*quantité
        $total = array_sum(array_map(function ($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_new', methods: ['GET', 'POST'])]
    public function addToCart(int $id, Request $request, SessionInterface $session): Response 
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            $this->addFlash('danger', 'Produit introuvable.');
            return $this->redirectToRoute('app_home_catalogue');
        }
            //recuperation du panier ou un tableau vide si pas d'article
        $cart = $session->get('cart', []);
            // recuperation de la quantité demandé en get ou post
        $rawQuantity = $request->isMethod('POST') 
            ? $request->request->get('quantity') 
            : $request->query->get('quantity');

            //Validation de la quantité
        if ($rawQuantity === null || $rawQuantity === '' || !is_numeric($rawQuantity)) {
            $quantityRequested = 1;
        } else {
            $quantityRequested = (int)$rawQuantity;
        }

        if ($quantityRequested < 1) {
            $quantityRequested = 1;
        }
            //calcul de la quantité total
        $currentQuantityInCart = !empty($cart[$id]) ? $cart[$id] : 0;
        $totalRequested = $currentQuantityInCart + $quantityRequested;

        // verifiaction et sécurisation du stock : empêche les nombres négatifs
        $rawStock = $product->getStock() ? $product->getStock()->getQuantity() : 0;
        $availableStock = max(0, $rawStock);

        if ($availableStock === 0) {
            $this->addFlash('warning', 'Désolé, ce produit est en rupture de stock.');
        } elseif ($totalRequested > $availableStock) {
            $this->addFlash('warning', sprintf('Désolé, il ne reste que %d exemplaire(s) disponible. Votre panier a été ajouté', $availableStock));
            $cart[$id] = $availableStock;
            $session->set('cart', $cart);
        } else {
            $cart[$id] = $totalRequested;
            $session->set('cart', $cart);

            $this->addFlash('success', 'Le produit a bien été ajouté à votre panier !');
        }

        return $this->redirectToRoute('app_home_catalogue');
    }

    #[Route('/cart/augmenter/{id}', name: 'app_cart_increase', methods: ['GET'])]
    public function increase(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $product = $this->productRepository->find($id);

            if ($product) {
                // Sécurisation du stock : empêche les nombres négatifs
                $rawStock = $product->getStock() ? $product->getStock()->getQuantity() : 0;
                $availableStock = max(0, $rawStock);

                if ($cart[$id] + 1 > $availableStock) {
                    if ($availableStock === 0) {
                        $this->addFlash('warning', 'Désolé, ce produit est en rupture de stock.');
                        unset($cart[$id]); // Optionnel : supprime le produit du panier s'il n'y en a plus du tout en BDD
                    } else {
                        $this->addFlash('warning', sprintf('Désolé, il ne reste que %d exemplaire(s) disponible(s).', $availableStock));
                        $cart[$id] = $availableStock; 
                    }
                } else {
                    $cart[$id]++; 
                }
            }
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/diminuer/{id}', name: 'app_cart_decrease', methods: ['GET'])]
    public function decrease(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            if ($cart[$id] > 1) {
                $cart[$id]--;
            } else {
                unset($cart[$id]); // Supprime du panier si la quantité tombe à 0
            }
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_product_remove', methods: ['GET'])]
    public function removeToCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(Request $request, SessionInterface $session): Response
    {
        if ($this->isCsrfTokenValid('clear_cart', $request->getPayload()->getString('_token'))){
            $session->set('cart', []);
            $this->addFlash('success', 'votre panier a été vidé avec succès.');
        }else{
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }
        
        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
    }
}