<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email<span class="text-danger">*</span>',
                'label_html' => true,
                'attr' => ['placeholder' => 'Saisir votre email'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre email.'])
                ]
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom<span class="text-danger">*</span>',
                'label_html' => true,
                'attr' => ['placeholder' => 'Saisir votre prénom'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre prénom.'])
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom<span class="text-danger">*</span>',
                'label_html' => true,
                'attr' => ['placeholder' => 'Saisir votre nom'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre nom.'])
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Accepter les conditions générales',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Veuillez accepter les conditions générales d\'utilisation.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'mapped' => false,
                'required' => false,
                'first_options'  => [
                    'label' => 'Mot de passe<span class="text-danger">*</span>',
                    'label_html' => true,
                    'attr' => [
                        'placeholder' => 'Saisir un mot de passe robuste',
                        'autocomplete' => 'new-password'
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe<span class="text-danger">*</span>',
                    'label_html' => true,
                    'attr' => [
                        'placeholder' => 'Ressaisir le mot de passe',
                        'autocomplete' => 'new-password'
                    ],
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                        'message' => 'Le mot de passe doit inclure au moins une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&).',
                    ]),
                ],
            ])
        ; // Le point-virgule manquant a été replacé ici pour fermer le $builder
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}