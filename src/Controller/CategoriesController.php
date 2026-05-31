<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categories')]
final class CategoriesController extends AbstractController

{
    #[Route('/', name: 'app_categories')]
    public function index(): Response
    {
        return $this->render('categories/index.html.twig');
    }

    #[Route('/new', name:'app_categories/new')]
    public function addCategories(EntityManagerInterface $entityManager, Request $request): Response

    {
        $categories =new Categories();
        $form = $this->createForm(CategoriesType::class, $categories);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($categories);
            $entityManager->flush();
        } 
                return $this->render('Categories/new.html.twig', ['form'=>$form->createView()]);
    }


    #[Route('/{id}/update', name:'app_categories_update')]
    public function update(Categories $categories, EntityManagerInterface $entityManager, Request $request): Response
    {
         $form = $this->createForm(CategoriesType::class, $categories);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            
            $entityManager->flush();
        } 

        return $this->render('Categories/update.html.twig', ['form'=>$form->createView()]);
    }


   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
    #[Route('/infusions', name:'app_categories/infusions')]
    public function infusion(): Response

    {
        return $this->render('Categories/infusions.html.twig');
    }


    #[Route('/sirops', name:'app_categories/sirops')]
    public function sirop(): Response

    {
        return $this->render('Categories/sirops.html.twig');
    }

    #[Route('/plantes', name:'app_categories/plantes')]
    public function plantes(): Response

    {
        return $this->render('Categories/plantes.html.twig');
    }
    

    #[Route('/recettes', name:'app_categories/recettes')]
    public function recettes(): Response

    {
        return $this->render('Categories/recettes.html.twig');
    }
       
}
