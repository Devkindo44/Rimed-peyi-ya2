<?php

namespace App\Form;

use App\Entity\User;
use Composer\Semver\Constraint\Constraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', ChoiceType::class,[
                'choices' => [
                'Admin' => 'ROLE_ADMIN'
                ],
                'placeholder' => '--Sélectionner un rôle--',
                'multiple'=> true,
                'expanded'=> true
            ])
            ->add('email', EmailType::class,[
                'label' =>'Email<span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'attr' => [
                    'placeholder' =>'Saisir votre email'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre email'
                    ]),
                    new Email([
                        'message' => 'veuillez saisir un email conforme'
                    ])
                ]
            ])
            
            
            ->add('firstName', null,[
                'label' => 'Prénom<span class="text-danger">*</span>',
                'label_html' => true,
                'required' => false,
                'attr' =>[
                    'placeholder' => 'Saisir votre prenom'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre prénom'
                    ])
                ]
            ])
            ->add('lastName', null, [
                'label' => 'Nom<span class=" text-danger">*</span>',
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
            // ->add('isVerified')
            // ->add('adresse_mail')
            // ->add('numero_de_telephone')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
