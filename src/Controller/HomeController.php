<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('home/index.html.twig',[
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request): Response
    {
        // Construction du formulaire de contact
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' => 'Nom / Pseudo',
                'attr' => ['class' => 'form-control border-brown', 'placeholder' => 'Votre nom']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => ['class' => 'form-control border-brown', 
                'placeholder' => 'votre@email.com']
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'attr' => ['class' => 'form-control border-brown',
                 'placeholder' => 'Ex: Proposition de remède, Question...']
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => ['class' => 'form-control border-brown', 
                'rows' => 6, 
                'placeholder' => 'Écrivez votre message ici...']
            ])
            ->getForm();

        // Analyse de la requête HTTP
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Ici, tu peux traiter les données :
            // 1. Envoyer un mail (via le service Mailer)
            // 2. Sauvegarder en base de données si nécessaire
            
            // Notification de succès pour l'utilisateur
            $this->addFlash('success', 'Votre message a bien été envoyé ! Merci pour votre contribution.');

            return $this->redirectToRoute('app_contact');
        }

        // Envoi du formulaire à ton template 'home/contact.html.twig'
        return $this->render('home/contact.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }

    #[Route('/home/catalogue', name: 'app_home_catalogue')]
    public function catalogue(
        Request $request, 
        ProductRepository $productRepository, 
        CategoriesRepository $categoriesRepository,
        PaginatorInterface $paginator
    ): Response {    
        $categoryId = $request->query->get('category');
        $lastProduct = $productRepository->findOneBy([], ['id' => 'DESC']);

        if ($categoryId) {
            $query = $productRepository->createQueryBuilder('p')
                ->join('p.categories', 'c')
                ->where('c.id = :catId')
                ->setParameter('catId', $categoryId)
                ->orderBy('p.id', 'DESC')
                ->getQuery();
        } else {
            $query = $productRepository->createQueryBuilder('p')
                ->orderBy('p.id', 'DESC')
                ->getQuery();
        }

        $products = $paginator->paginate(
            $query,                                
            $request->query->getInt('page', 1),  
            8                                    
        );

        return $this->render('home/catalogue.html.twig', [
            'products' => $products, 
            'categories' => $categoriesRepository->findAll(),
            'lastProduct' => $lastProduct,
        ]);
    }
}