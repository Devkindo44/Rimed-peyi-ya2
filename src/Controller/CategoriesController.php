<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/categories')]
final class CategoriesController extends AbstractController
{
    #[Route('/', name: 'app_categories', methods: ['GET'])]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findAll();
        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    public function addCategories(EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($category->getName())->lower();
            $category->setSlug($slug);

            $entityManager->persist($category);
            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été ajoutée avec succès !');

            return $this->redirectToRoute('app_categories');
        }

        return $this->render('categories/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/{id}/update', name: 'app_categories_update', methods: ['GET', 'POST'])]
    public function update(Categories $category, EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($category->getName())->lower();
            $category->setSlug($slug);

            $entityManager->flush();

            $this->addFlash('success', 'La catégorie a été modifiée avec succès !');

            return $this->redirectToRoute('app_categories');
        }

        return $this->render('categories/update.html.twig', [
            'form' => $form,
            'category' => $category
        ]);
    } // <-- CORRIGÉ : L'accolade manquante qui fermait la fonction "update" a été remise ici !

    #[Route('/{slug}', name: 'app_categories_show', methods: ['GET'])]
    public function show(string $slug, CategoriesRepository $categoriesRepository): Response
    {
        // On cherche la catégorie en BDD grâce au slug de l'URL
        $category = $categoriesRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie n'existe pas.");
        }

        // Renvoie vers un template unique et dynamique
        return $this->render('categories/show.html.twig', [
            'category' => $category,
        ]);
    } // <-- Ferme proprement la fonction show()
   

    
} // <-- Ferme proprement la classe CategoriesController
