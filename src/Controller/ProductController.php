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
    public function new(Request $request, EntityManagerInterface $entityManager):Response
    {
        // creation d'un objet de la classe Product (entity)
        $product = new Product();
        // dump($product);

       $form = $this->createForm(ProductType::class, $product);
        // dd($form->createView());

        //traitement du formulaire
        $form->handleRequest($request);
         

        /*
            Si le form a été soumis et si validé (click sur le bouton valider=>"ajouter le produit")
            par defaut is valid= true si la contrainte n'est pas respecté // isValid passe à faulse
            click ok + respect des contraintes=> ok pour l'enregistrement en BD
        */
        if($form->isSubmitted() && $form->isValid()){

        //    dump($form->isValid());
            // dd($product);

            //Enregistrement l'objet que l'on veut envoyer en BD
            $entityManager->persist($product);

            //flush permet l'execution pour envoi en BD (info (contrainte respecté) validé pour l'envoi)
            $entityManager->flush();

            // dd($product);   

            //notification du 
            $this->addFlash('success', 'Le produit à bien été ajouté');

         

            //redirection
            //equivalent à la fonction twig path()
            return $this->redirectToroute('app_product_index');
        }

        return $this->render('product/new.html.twig', [

            //createView pas obligatoire depuis symfony 7
            'formProduct'=> $form->createView()
            
        ]);

        

    }

    #[Route('/produit/fiche/{id}', name: 'app_product_show')]
    public function show(Product $product): Response
    {
        // dump($product);

    return $this->render('product/show.html.twig',[
        'product'=> $product
    ]);
        
    }
     // Modification d'un produit spécifique
    #[Route('/produit/modifier', name: 'app_product_edit')] 
    public function edit(): Response
    {
       
        

        return $this->render('product/edit.html.twig', [
            // 'product' => $product,
            // 'formProduct' => $form->createView() // Pensez à afficher ce formulaire dans votre vue edit.html.twig
        ]);
    }

}