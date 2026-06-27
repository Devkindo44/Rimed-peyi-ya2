<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Stock;
use App\Form\ProductType;
use App\Repository\CategoriesRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ProductController extends AbstractController
{
    // Affichage des produits
    #[Route('/produit/index', name: 'app_product_index')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    // Ajout d'un produit
    #[Route('/produit/ajouter', name: 'app_product_new')]
    public function new(Request $request, CategoriesRepository $categoriesRepository, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {   
        $categories = $categoriesRepository->findAll();
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
       
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                
                // --- GESTION DU STOCK  ---
                $quantity = $form->get('stock')->getData();
                
                $stock = new Stock();
                $stock->setQuantity($quantity); 
                $product->setStock($stock);
                
                $entityManager->persist($stock);
              
                // Gestion de l'illustration
                $illustration = $form->get('illustration')->getData();
                if ($illustration) {
                    $originalName = pathinfo($illustration->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFileName = $slugger->slug($originalName);
                    $newFileName = $safeFileName . '-' . uniqid() . '.' . $illustration->guessExtension();

                    $illustration->move(
                        $this->getParameter('kernel.project_dir') . '/public/images',
                        $newFileName
                    );

                    $product->setIllustration($newFileName);
                }

                $entityManager->persist($product);
                $entityManager->flush();

                $this->addFlash('success', 'Le produit et son stock ont bien été ajoutés');

                return $this->redirectToRoute('app_product_index');
            } else {
                // Le formulaire a été soumis mais il contient des erreurs
                $this->addFlash('danger', 'Le formulaire contient des erreurs. Veuillez les corriger.');
            }
        }

        // Le render est maintenant en dehors des conditions de soumission, accessible au premier chargement
        return $this->render('product/new.html.twig', [
            'formProduct' => $form->createView(),
            'categories'=> $categories,
        ],new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)); 
    }

    // Fiche d'un produit spécifique
    #[Route('/produit/fiche/{id}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    // Modification d'un produit spécifique
    #[Route('/produit/modifier/{id}', name: 'app_product_edit')] 
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        
        if ($product->getStock()) {
            $form->get('stock')->setData($product->getStock()->getQuantity());
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $quantity = $form->get('stock')->getData();
            
            if ($product->getStock()) {
                $product->getStock()->setQuantity($quantity);
            } else {
                $stock = new Stock();
                $stock->setQuantity($quantity);
                $product->setStock($stock);
                $entityManager->persist($stock);
            }

            $illustration = $form->get('illustration')->getData();
            if ($illustration) {
                $originalName = pathinfo($illustration->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $illustration->guessExtension();

                $illustration->move(
                    $this->getParameter('kernel.project_dir') . '/public/images',
                    $newFileName
                );

                $product->setIllustration($newFileName);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le produit a bien été modifié');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formProduct' => $form->createView()
        ],new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    // Suppression d'un produit spécifique
    #[Route('/produit/supprimer/{id}', name: 'app_product_delete', methods: ['POST', 'GET'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($product->getStock()) {
            $entityManager->remove($product->getStock());
        }

        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit a bien été supprimé.');

        return $this->redirectToRoute('app_product_index');
    }
}