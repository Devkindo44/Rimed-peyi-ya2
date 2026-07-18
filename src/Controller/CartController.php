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

    #[Route('/cart/add/{id}', name: 'app_cart_new', methods: ['GET', 'POST'])]
    public function addToCart(int $id, Request $request, SessionInterface $session): Response 
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            $this->addFlash('danger', 'Produit introuvable.');
            return $this->redirectToRoute('app_home_catalogue');
        }

        $cart = $session->get('cart', []);

        $rawQuantity = $request->isMethod('POST') 
            ? $request->request->get('quantity') 
            : $request->query->get('quantity');

        if ($rawQuantity === null || $rawQuantity === '' || !is_numeric($rawQuantity)) {
            $quantityRequested = 1;
        } else {
            $quantityRequested = (int)$rawQuantity;
        }

        if ($quantityRequested < 1) {
            $quantityRequested = 1;
        }

        $currentQuantityInCart = !empty($cart[$id]) ? $cart[$id] : 0;
        $totalRequested = $currentQuantityInCart + $quantityRequested;

        $availableStock = $product->getStock() ? $product->getStock()->getQuantity() : 0;

        if ($totalRequested > $availableStock) {
            $this->addFlash('warning', sprintf('Désolé, il ne reste que %d exemplaire(s) en stock.', $availableStock));
            $cart[$id] = $availableStock;
        } else {
            $cart[$id] = $totalRequested;
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('app_home_catalogue');
    }

    #[Route('/cart/augmenter/{id}', name: 'app_cart_increase', methods: ['GET'])]
    public function increase(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $product = $this->productRepository->find($id);

            if ($product) {
                $availableStock = $product->getStock() ? $product->getStock()->getQuantity() : 0;

                if ($cart[$id] + 1 > $availableStock) {
                    $this->addFlash('warning', sprintf('Désolé, il ne reste que %d exemplaire(s) en stock.', $availableStock));
                    $cart[$id] = $availableStock; 
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

    #[Route('/cart/remove', name: 'app_cart_remove', methods: ['GET'])]
    public function remove(SessionInterface $session): Response
    {
        $session->set('cart', []);
        return $this->redirectToRoute('app_cart');
    }
}