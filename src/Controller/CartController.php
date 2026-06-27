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
                $cartWithData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            }
        }
        
        $total = array_sum(array_map(function ($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_new', methods: ['GET','POST'])]
    public function addToCart(int $id, Request $request, SessionInterface $session): Response 
    {
        $product = $this->productRepository->find($id);

        // Si le produit n'existe pas, on redirige
        if (!$product) {
            $this->addFlash('danger', 'Produit introuvable.');
            return $this->redirectToRoute('app_cart');
        }

        $cart = $session->get('cart', []);
        $quantityRequested = $request->request->getInt('quantity', 1);

        // Sécurité de base
        if ($quantityRequested < 1) {
            $quantityRequested = 1;
        }

        // Calcul de la quantité totale voulue (panier actuel + nouvelle demande)
        $currentQuantityInCart = !empty($cart[$id]) ? $cart[$id] : 0;
        $totalRequested = $currentQuantityInCart + $quantityRequested;

        //VÉRIFICATION DU STOCK pour la coherence dde la commande
        // On récupère proprement la quantité numérique ou 0 si l'entité Stock est nulle
        $availableStock = $product->getStock() ? $product->getStock()->getQuantity() : 0;

        if ($totalRequested > $availableStock) {
            $this->addFlash('warning', sprintf('Désolé, il ne reste que %d exemplaire(s) en stock.', $availableStock));
            
            // On remplit le panier au ratio  maximum du stock disponible
            $cart[$id] = $availableStock;
        } else {
            $cart[$id] = $totalRequested;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_product_remove', methods: ['GET'])]
    public function removeToCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get(name: 'cart', default: []);

        if (!empty($cart[$id])) {
            // Suppression du produit du tableau
            unset($cart[$id]);
            
            // Sauvegarde + mise à jour du panier en session
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove', name: 'app_cart_remove', methods: ['GET'])]
    public function remove(SessionInterface $session): Response
    {
        // Vide proprement le panier
        $session->set('cart', []);
        return $this->redirectToRoute('app_cart');
    }
}