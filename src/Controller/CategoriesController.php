<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/categories')]
final class CategoriesController extends AbstractController
{
    #[Route('/', name: 'app_categories', methods: ['GET'])]
    public function index(
        CategoriesRepository $categoriesRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        //Preparation de la requ^ete  Querybuilder
        $query = $categoriesRepository->createQueryBuilder('c')
        ->orderBy ('c.name', 'ASC')
        ->getQuery();
        //  Sécurisation de la page : toujours >= 1
        $page = max(1, $request->query->getInt('page', 1));
        //Pagination des categories 10 par pages
        $categories = $paginator->paginate(
            $query,
            $page,
           10
            //Pour eviter l'erreur Invalid page number. Page: 0: $page must be positive non-zero integer
            //U
            //$request->query->getInt('page , 1'), 10 nombre defini par pages

        );
        // $categories = $categoriesRepository->findAll(); si on veut tout afficher sans pagination

        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    // 1. La route statique "/new" est placée AVANT la route dynamique
    #[Route('/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    // #[IsGranted('ROLE_ADMIN')] //  Seuls les admins peuvent entrer ici
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

    #[Route('/{id}/edit', name: 'app_categories_edit', methods: ['GET', 'POST'])]
    public function edit(Categories $category, EntityManagerInterface $entityManager, Request $request, SluggerInterface $slugger): Response
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

        return $this->render('categories/edit.html.twig', [
            'form' => $form,
            'category' => $category
        ]);
    }

    #[Route('/{id}/delete', name: 'app_categories_delete', methods: ['POST'])]
    public function delete(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
            
            $this->addFlash('success', 'La catégorie a été supprimée avec succès !');
        }

        return $this->redirectToRoute('app_categories', [], Response::HTTP_SEE_OTHER);
    }

    // 2. La route dynamique avec {slug} est placée tout à la fin
    // Correction : Utilisation explicite du Repository pour contourner le problème d'autowiring
    #[Route('/{slug}', name: 'app_categories_show', methods: ['GET'])]
    public function show(string $slug, CategoriesRepository $categoriesRepository): Response
    {
        $category = $categoriesRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie n'existe pas.");
        }

        return $this->render('categories/show.html.twig', [
            'category' => $category,
          
        ]);
    }
}