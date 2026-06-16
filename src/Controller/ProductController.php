<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Stock; // <-- IMPORT DE L'ENTITÉ STOCK INDISPENSABLE
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

        if ($form->isSubmitted() && $form->isValid()) {
            
            // --- GESTION DU STOCK  ---
            // On récupère la valeur entière tapée dans le champ 'stock' (qui est mapped => false)
            $quantity = $form->get('stock')->getData();
            
            // On instancie un nouvel objet Stock, on lui donne la quantité, et on le lie au produit
            $stock = new Stock();
            $stock->setQuantity($quantity); 
            $product->setStock($stock);
            
            // On demande à Doctrine de persister le Stock en premier
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
        }

        return $this->render('product/new.html.twig', [
            'formProduct' => $form->createView(),
            'categories'=> $categories,
        ]);
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
        // --- PRÉ-REMPLISSAGE DU CHAMP STOCK POUR LA MODIFICATION (NOUVEAU) ---
        $form = $this->createForm(ProductType::class, $product);
        
        // Si le produit a déjà un stock, on pré-remplit le champ non-mappé dans le formulaire
        if ($product->getStock()) {
            $form->get('stock')->setData($product->getStock()->getQuantity());
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            // --- MISE A JOUR DU STOCK EN MODIFICATION (NOUVEAU) ---
            $quantity = $form->get('stock')->getData();
            
            if ($product->getStock()) {
                // Si le stock existe déjà, on met juste à jour sa quantité
                $product->getStock()->setQuantity($quantity);
            } else {
                // Cas de secours au cas où un produit en base n'aurait pas de stock
                $stock = new Stock();
                $stock->setQuantity($quantity);
                $product->setStock($stock);
                $entityManager->persist($stock);
            }
            // ------------------------------------------------------

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

            $entityManager->flush();

            $this->addFlash('success', 'Le produit a bien été modifié');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formProduct' => $form->createView()
        ]);
        
    }
    // Suppression d'un produit spécifique
    #[Route('/produit/supprimer/{id}', name: 'app_product_delete', methods: ['POST', 'GET'])]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
        // Suppression du Stock associé s'il existe
        if ($product->getStock()) {
            $entityManager->remove($product->getStock());
        }

        // Suppression du produit
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit a bien été supprimé.');

        return $this->redirectToRoute('app_product_index');
    }
}