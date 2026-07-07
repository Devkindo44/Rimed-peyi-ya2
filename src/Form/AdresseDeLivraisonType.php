<?php

namespace App\Form;

use App\Entity\AdresseDeLivraison;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AdresseDeLivraisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse_postale', TextType::class, [
                'label' => 'Adresse postale <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: 8 Rue de la Paix'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "L'adresse postale ne peut pas être vide."
                    ]),
                    new Length([
                        'min' => 5,
                        'max' => 255,
                        'minMessage' => "L'adresse semble trop courte (minimum {{ limit }} caractères).",
                        'maxMessage' => "L'adresse est trop longue."
                    ])
                ]
            ])
            ->add('code_postale', TextType::class, [
                'label' => 'Code postal <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: 75009'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "Le code postal est obligatoire."
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]{5}$/',
                        'message' => "Le code postal doit être composé de exactement 5 chiffres."
                    ])
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville <span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Paris'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => "La ville est obligatoire."
                    ]),
                    new Length([
                        'max' => 50,
                        'maxMessage' => "Le nom de la ville est trop long."
                    ])
                ]
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdresseDeLivraison::class,
        ]);
    }
}