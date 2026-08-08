<?php

namespace App\Form;

use App\Entity\User;
use App\Form\AdresseDeLivraisonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
            ])
            ->add('numeroDeTelephone', TelType::class, [
                'label' => 'Téléphone principal',
                'required' => false,
            ])
            // Imbrication de la collection d'adresses de livraison
            // ->add('adresseDeLivraisons', CollectionType::class, [
            //     'entry_type' => AdresseDeLivraisonType::class,
            //     'entry_options' => ['label' => false],
            //     'allow_add' => true,
            //     'allow_delete' => true,
            //     'by_reference' => false,
            //     'label' => false,
            // ])
            // Champ mot de passe facultatif
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'first_options'  => ['label' => 'Nouveau mot de passe (laisser vide pour conserver l\'actuel)'],
                'second_options' => ['label' => 'Confirmer le nouveau mot de passe'],
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit faire au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}