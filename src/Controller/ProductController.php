<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{

    // route produit\afficher pour pour l'affichage des produits en BD

    #[Route('/produit/afficher', name: 'app_product_index')]
    public function index(ProductRepository $productRepository): Response

    // $products= $productRepository->findAll();=>requête pour tous les produits en BD (pas de repository =pas d'objets)
    {

        //syntaxe: class=$products $objet =$productRepository(on peut donner le nom qu'on souhaite)
        //findAll() equivqaut à SELECT * FROM product

        $products= $productRepository->findAll();



        return $this->render('product/index.html.twig',['products'=>$products,]);
       
    }

    #[Route('/produit/ajouter', name: 'app_product_new')]
    public function new():Response
    {
        // creation d'un objet de la classe Product
        $product = new Product();
        // dump($product);

       $form = $this->createForm(ProductType::class, $product);
        // dd($form->createView());
        return $this->render('product/new.html.twig', [

            //createView pas obligatoire depuis symfony 7
            'formProduct'=> $form->createView()
        ]);

        

    }
}
