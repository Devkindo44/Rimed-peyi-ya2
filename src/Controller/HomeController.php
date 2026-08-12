<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mime\Email;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        // Construction du formulaire de contact
        $form = $this->createFormBuilder()
            ->add('name', TextType::class, [
                'label' => 'Nom / Pseudo',
                'attr' => ['class' => 'form-control border-brown', 'placeholder' => 'Votre nom']
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => [
                    'class' => 'form-control border-brown', 
                    'placeholder' => 'votre@email.com'
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'attr' => [
                    'class' => 'form-control border-brown',
                    'placeholder' => 'Ex: Proposition de remède, Question...'
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => [
                    'class' => 'form-control border-brown', 
                    'rows' => 6, 
                    'placeholder' => 'Écrivez votre message ici...'
                ]
            ])
            ->getForm();

        // Analyse de la requête HTTP
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            //process d'envoi d'email
            $email = (new Email())
                ->from($data['email'])// adresse saisie par le user
                ->to('contact@rimedpeyiya.fr') //adresse mail de reception
                ->subject('Nouveau message de contact : ' . $data['subject'])
                ->text(
                    "Nouveau message de : " .$data['name'] . "(" . $data['email'] . ")\n\n" .
                    "Sujet : " . $data['subject'] . "\n\n" .
                    "Message :\n" . $data['message'] 
                );

                //Envoi effectif de l'email
                $mailer->send($email);
               
             //Fin d'envoi 
            // Notification de succès pour l'utilisateur
            $this->addFlash('success', 'Votre message a bien été envoyé ! Merci pour votre contribution.');

            return $this->redirectToRoute('app_contact');
        }

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

        // Gestion de la pagination KnpPaginator
        $products = $paginator->paginate(
            $query,                                 // Requête Doctrine
            $request->query->getInt('page', 1),    // Numéro de la page courante
            8                                      // Nombre d'éléments par page
        );

        return $this->render('home/catalogue.html.twig', [
            'products' => $products, 
            'categories' => $categoriesRepository->findAll(),
            'lastProduct' => $lastProduct,
        ]);
    }
}