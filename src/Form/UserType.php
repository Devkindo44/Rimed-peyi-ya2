<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Admin' => 'ROLE_ADMIN'
                ],
                'placeholder' => '--Sélectionner un rôle--',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email<span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisir votre email'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre email'
                    ]),
                    new Email([
                        'message' => 'Veuillez saisir un email conforme'
                    ])
                ]
            ])
            ->add('firstName', null, [
                'label' => 'Prénom<span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisir votre prénom'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre prénom'
                    ])
                ]
            ])
            ->add('lastName', null, [
                'label' => 'Nom<span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisir votre nom'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom'
                    ])
                ]
            ])
            ->add('numero_de_telephone', null, [
                'label' => 'Numéro de téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Saisir votre numéro de téléphone'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => false,
                'invalid_message' => 'Les champs du mot de passe doivent correspondre.',
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => ['placeholder' => 'Saisir un mot de passe']
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => ['placeholder' => 'Répéter le mot de passe']
                ],
                'constraints' => [
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/',
                        'message' => 'Le mot de passe doit inclure au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&).',
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