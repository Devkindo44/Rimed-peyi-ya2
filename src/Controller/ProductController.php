<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
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
    #[Route('/produit/afficher', name: 'app_product_index')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    // Ajout d'un produit
    #[Route('/produit/ajouter', name: 'app_product_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $illustration = $form->get('illustration')->getData();

            if ($illustration) {
                $originalName = pathinfo($illustration->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalName);
                // ajout d'image
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $illustration->guessExtension();

                // CORRECTION 2 : Déplacement physique du fichier dans public/images/
                $illustration->move(
                    $this->getParameter('kernel.project_dir') . '/public/images',
                    $newFileName
                );

                // CORRECTION 3 : Liaison du nom généré à l'objet Product
                $product->setIllustration($newFileName);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit a bien été ajouté');

            // CORRECTION 4 : Majuscule à redirectToRoute
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', [
            'formProduct' => $form->createView()
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
        // On réutilise le même formulaire configuré pour le produit existant
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            // Pas besoin de persist() lors d'une modification, l'entité est déjà connue de Doctrine
            $entityManager->flush();

            $this->addFlash('success', 'Le produit a bien été modifié');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formProduct' => $form->createView()
        ]);
    }
}