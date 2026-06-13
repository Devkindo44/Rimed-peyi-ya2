<?php

namespace App\Form;

use App\Entity\Categories;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // 💡 On utilise un champ texte classique
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // securisation champ 'name' de l'entité Categories
            ->add('name', TextType::class, [
                'label' => 'Nom de la catégorie<span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr' => [
                    'class' => 'text-success font-weight-bold',
                ],
                'attr' => [
                    'placeholder' => 'Saisir catégorie (ex: Infusions, Graines...)',
                    'class' => 'form-control bg-success-custom-brown-f shadow'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner le nom de la catégorie.'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categories::class,
        ]);
    }
}