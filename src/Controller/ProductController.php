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
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;


// Definition des rôles admin au cas par cas
final class ProductController extends AbstractController
{
    
    #[Route('/admin/produit', name: 'app_product_index')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    // /admin/produit/ajouter
    #[Route('/ajouter', name: 'app_product_new')]
    #[IsGranted('ROLE_ADMIN')]
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

                $entityManager->persist($product); //prepare le produit pour l'envoi à la BD
                $entityManager->flush();// Envoi le produit, le stock et l'image en BD

                $this->addFlash('success', 'Le produit et son stock ont bien été ajoutés');

                return $this->redirectToRoute('app_product_index'); 
            } else {
                $this->addFlash('danger', 'Le formulaire contient des erreurs. Veuillez les corriger.');
            }
        }

        return $this->render('product/new.html.twig', [
            'formProduct' => $form->createView(),
            'categories'=> $categories,
        ], new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY)); 
    }

    // Accessible aux USERS connectés
    #[Route('/fiche/{id}', name: 'app_product_show')]
     #[IsGranted('ROLE_USER')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    // L'URL devient : /admin/produit/modifier/{id}
    #[Route('/modifier/{id}', name: 'app_product_edit')] 
    #[IsGranted('ROLE_ADMIN')]
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

            return $this->redirectToRoute('app_product_index'); // <-- Redirection
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formProduct' => $form->createView()
        ], new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    // L'URL devient : /admin/produit/supprimer/{id}
    #[Route('/supprimer/{id}', name: 'app_product_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        //verification token CSRF transmis dans la requête POST
        if($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))){
            if ($product->getStock()) {
                $entityManager->remove($product->getStock());
            }

            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit a bien été supprimé.');
        }else{
            $this->addFlash('error', 'Jeton CSRF invalide.');
        }
            

            return $this->redirectToRoute('app_product_index'); // <- Redirection 
        }
}