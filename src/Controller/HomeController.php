<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('home/index.html.twig',[
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/contact', name:'app_contact', methods: ['GET', 'POST'])]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }

    #[Route('/home/catalogue', name: 'app_home_catalogue')]
public function catalogue(
    Request $request, 
    ProductRepository $productRepository, 
    CategoriesRepository $categoriesRepository,
    PaginatorInterface $paginator // Injecte le service ici
): Response {    
    // Récupération de l'id de la catégorie depuis l'url si présent
    $categoryId = $request->query->get('category');

    // On récupère le dernier produit ajouté
    $lastProduct = $productRepository->findOneBy([], ['id' => 'DESC']);

    if ($categoryId) {
        // On crée le QueryBuilder filtré, mais SANS le ->getResult() à la fin
        $query = $productRepository->createQueryBuilder('p')
            ->join('p.categories', 'c')
            ->where('c.id = :catId')
            ->setParameter('catId', $categoryId)
            ->orderBy('p.id', 'DESC') // Optionnel : trier du plus récent au plus ancien
            ->getQuery(); // On s'arrête à ->getQuery()
    } else {
        // Au lieu de findAll(), on fait une requête de base pour tout récupérer sous forme de Query
        $query = $productRepository->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC')
            ->getQuery();
    }

    // On applique la pagination sur la requête choisie
    $products = $paginator->paginate(
        $query,                              // La requête SQL (filtrée ou totale)
        $request->query->getInt('page', 1),  // Le numéro de la page en cours (1 par défaut)
        8                                  // Nombre de produits par page
    );

    // Renvoi à la vue Twig (le reste ne change pas !)
    return $this->render('home/catalogue.html.twig', [
        'products' => $products, 
        'categories' => $categoriesRepository->findAll(),
        'lastProduct' => $lastProduct,
    ]);
    }
}