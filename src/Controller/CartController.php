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
            return $this->redirectToRoute('app_home_catalogue');
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

        return $this->redirectToRoute('app_home_catalogue');
    }

   #[Route('/cart/augmenter/{id}', name: 'app_cart_increase')]
    public function increase(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            // Récupération du produit pour vérifier son stock
            $product = $this->productRepository->find($id);

            if ($product) {
                // Extraction de la quantité disponible (ou 0 si l'entité Stock n'existe pas)
                $availableStock = $product->getStock() ? $product->getStock()->getQuantity() : 0;

                // On vérifie si la future quantité dépasse le stock disponible
                if ($cart[$id] + 1 > $availableStock) {
                    $this->addFlash('warning', sprintf('Désolé, il ne reste que %d exemplaire(s) en stock.', $availableStock));
                    // On bloque le panier à la valeur maximale du stock
                    $cart[$id] = $availableStock; 
                } else {
                    // L'incrémentation ne doit se faire QUE si le stock le permet
                    $cart[$id]++; 
                }
            }
        }

        $session->set('cart', $cart);
        return $this->redirectToRoute('app_cart');
    }
    

    #[Route('/cart/diminuer/{id}', name: 'app_cart_decrease')]
    public function decrease(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if(!empty($cart[$id])) {
            if($cart[$id] > 1){
                $cart[$id]--; //Decrementation
            }else{
                unset($cart[$id]); //suppression du produit si la quantité est =0 
            }
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