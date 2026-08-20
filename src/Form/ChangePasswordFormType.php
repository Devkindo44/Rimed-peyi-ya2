<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class' => 'form-contol',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank(
                            message: 'Veuillez saisir un mot de passe',
                        ),
                        new Length(
                            min: 12,
                            minMessage: 'Votre mot de passe doit contenir au moins  {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            max: 4096,
                        ),
                        new PasswordStrength( message: 'Le mot de passe est trop faible. Veuillez utiliser mot de passe plus robuste.',),
                           
                        new NotCompromisedPassword(),
                    ],
                    'label' =>'<span class="text-custom-green-l">Nouveau mot de passe</span>' . '<span class="text-danger m-1">*</span>',
                    'label_html' => true,
                    'label_attr' => ['class'=> 'form-label text-white'],
                    'attr' => [
                        'placeholder' => 'Votre nouveau mot de passe',
                    ],
                ],
                'second_options' => [
                    'label' =>'<span class="text-custom-green-l">Confirmation du mot de passe</span>' . '<span class="text-danger m-1">*</span>',
                    'label_html' => true,
                    'label_attr' => ['class' => 'form-label text-white'],
                    'attr' => [
                        'placeholder' => 'Répétez le mot de passe',
                    ],
                ],
                'invalid_message' => 'Les deux mots de passes doivent être identiques.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
