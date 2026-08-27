<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; // <-- Ajout de l'import pour la liste d'options
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre<span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'text-success',
                ],
                'attr' => [
                    'placeholder' => 'Saisir un titre',
                    'class' => 'form-control bg-success-custom-brown-f shadow'
                ],
                'help' => 'Nombre de caractères maximal : <span class="text-custom-brown">30 charactères max.</span>',
                'help_html' => true,
                'help_attr' => [
                    'class' => 'text-success'
                ],
                'row_attr' => [
                    'class' => 'shadow bg-success-custom-brown-f p-4 m-4'
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le titre du produit'
                    ]),
                    new Length([
                        'max' => 30,
                        'maxMessage' => 'Veuillez saisir au maximum 30 caractères'
                    ])
                ]
            ])
            
            ->add('description', TextareaType::class, [
                'label' => 'Description<span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'text-success',
                ],
                'attr' => [
                    'placeholder' => 'Ajouter une description',
                    'rows' => 8,
                    'class' => 'form-control bg-success-custom-brown-f shadow'
                ],
                'help' => 'Ajouter une description pertinente du produit : <span class="text-custom-brown">2000 charactères max. </span>',
                'help_html' => true,
                'help_attr' => [
                    'class' => 'text-success'
                ],
                'row_attr' => [
                    'class' => 'shadow bg-success-custom-brown-f p-4 m-4'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir la description du produit.'
                    ]),
                    new Length([
                        'max' => 2000,
                        'maxMessage' => 'Veuillez saisir au maximum 2000 caractères'
                    ])
                ]
            ])

            ->add('illustration', FileType::class, [
                'label' => 'illustration du produit<span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'text-success',
                ],
                'row_attr' => [
                    'class' => 'shadow p-4 m-4 bg-success-custom-brown-f'
                ],
                'help' => 'Veuillez uploader une image valide : <span class="text-custom-brown"> (jpg,png,webp)de 2MO max. </span>',
                'help_html' => true,
                'help_attr' => [
                    'class' => 'text-success'
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide au format (jpg,jpeg,png,webp).',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control bg-success-custom-brown-f shadow',
                ],
            ])

            ->add('price', MoneyType::class, [
                'label' => 'Prix<span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'text-success',
                ],
                'attr' => [
                    'placeholder' => 'Ajouter un prix',
                    'class' => 'form-control bg-success-custom-brown-f shadow rounded'
                ],
                'help' => 'Prix du produit à afficher en : <span class="text-custom-brown">prix TTC.</span>',
                'help_html' => true,
                'help_attr' => [
                    'class' => 'text-success'
                ],
                'row_attr' => [
                    'class' => 'shadow bg-success-custom-brown-f p-4 m-4'
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir le prix du produit.'
                    ]),
                    new Positive([
                        'message' => 'Veuillez saisir un prix strictement superieur à 0.'
                    ])
                ]
            ])

            // --- AJOUT DU CHAMP CONTENANCE EN STYLE LISTE DÉROULANTE ---
            ->add('contenance', ChoiceType::class, [
                'label' => 'Format du Conditionnement<span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'text-success',
                ],
                'placeholder' => ' Choisir le conditionnement',
                'required' => true,
                'choices'  => [
                    'Paquet de 100g'    =>  'Paquet de 100g',
                    'Sachet de 100g'    =>  'Sachet de 100g',
                    'Sachet de 250g'    =>  'Sachet de 250g',
                    'Sachet de 500g'    =>  'Sachet de 500g',
                    'Bouteille de 25 cl' => 'Bouteille de 25 cl',
                    'Bouteille de 50 cl' => 'Bouteille de 50 cl',
                    'Bouteille de 75 cl' => 'Bouteille de 75 cl',
                    'Flacon de 10 ml'    => 'Flacon de 10 ml' ,
                    'Flacon de 50 ml'    => 'Flacon de 50 ml',
                    'Pot de 20 cm'       => 'Pot de 20 cm' 
                ],
                'attr' => [
                    'class' => 'form-select bg-success-custom-brown-f shadow rounded' // form-select s'adapte mieux au dropdown
                ],
                'help' => 'Sélectionnez le format du conditionnement : <span class="text-custom-brown">Sachet, bouteille, flacon, paquet.</span>',
                'help_html' => true,
                'help_attr' => [
                    'class' => 'text-success'
                ],
                'row_attr' => [
                    'class' => 'shadow bg-success-custom-brown-f p-4 m-4'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une contenance pour ce produit.'
                    ])
                ]
            ])
            // ------------------------------------------------------------

            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => ' <span class="text-success">Catégorie du produit </span> <span class="text-danger">*</span>',
                'label_html' => true,
                'multiple' => true,
                'expanded' => true,
                'attr' => [
                    'placeholder' => 'Ajouter la categorie du produit',
                    'class' => 'form-control bg-success-custom-brown-f shadow rounded'
                ],
                'row_attr' => [
                    'class' => 'shadow bg-success-custom-brown-f p-4 m-4'
                ],
                'help' => 'Cocher la catégorie du produit : <span class="text-custom-brown">Catégorie</span>',
                'help_html' => true,
                'help_attr' => [
                    'class' => 'text-success'
                ],
            ])
          
            ->add('stock', IntegerType::class, [
                'label' => 'Quantité en stock<span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'text-success',
                ],
                'attr' => [
                    'placeholder' => 'Ex: 10, 50, 100...',
                    'class' => 'form-control bg-success-custom-brown-f shadow rounded'
                ],
                'mapped' => false,
                'row_attr' => [
                    'class' => 'shadow bg-success-custom-brown-f p-4 m-4'
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une quantité pour le stock.'
                    ]),
                    new Positive([
                        'message' => 'Le stock doit être supérieur ou égal à 0.'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}