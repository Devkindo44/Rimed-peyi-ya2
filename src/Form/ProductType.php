<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('title',TextType::class,[
               // key =>value
               'label' => 'Titre<span class="text-danger">*</span>',
               'label_html' => true,
               'label_attr'=>[
                'class'=>'text-success',
               ],
               'attr' =>[
                    'placeholder'=> 'Saisir un titre',
                    'class'=>'form-control bg-success-custom-brown-f  shadow'
               ],
               'help'=>'Nombre de caractères maximal : <span class="text-custom-brown">30 charactères max.</span>',
               'help_html'=>true,
               'help_attr'=> [
                'class'=> 'text-success'
               ],
               'row_attr' => [
               'class'=>'shadow  bg-success-custom-brown-f  p-4 m-4'
               ],
               'required'=> false,
               'constraints'=>[
                    new NotBlank([
                        'message'=> 'Veuillez saisir le titre du produit'
                    ]),
                    new Length([
                        'max'=>30,
                        'maxMessage'=>'Veuillez saisir au maximum 30 caractères'

                    ])

               ]

            ])
            ->add('Description',TextareaType::class, [
                'label' => 'Description<span class="text-danger">*</span>',
               'label_html' => true,
               'label_attr'=>[
                'class'=>'text-success',
               ],
               'attr' =>[
                    'placeholder'=> 'Ajouter une description',
                    'rows'=>8,
                    'class'=>'form-control   bg-success-custom-brown-f shadow'
               ],
               'help'=>'Ajouter une description  pertinente du produit : <span class="text-custom-brown">200 charactères max. </span>',
               'help_html'=>true,
               'help_attr'=> [
                'class'=> 'text-success'
               ],
               'row_attr' => [
               'class'=>'shadow  bg-success-custom-brown-f  p-4 m-4'
               ],
               'required'=> false,
               'constraints'=>[
                 new NotBlank([
                        'message'=> 'Veuillez saisir la description du produit.'
                    ]),
                 new Length([
                        
                        'max'=>200,
                        'maxMessage'=>'Veuillez saisir au maximum 200 caractères'

                    ])
               ]
                
            ])

            //class FileType pour ajouter les images
            ->add('illustration',FileType::class,[
                'label' => 'illustration du produit<span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr'=>[
                'class'=>'text-success',
               ],
               'row_attr' => [
               'class'=>'shadow  p-4 m-4 bg-success-custom-brown-f '
               ],
                'help'=>'Veuillez uploader une image valide : <span class="text-custom-brown"> (jpg,png,webp)de 2MO max. </span>',
                'help_html'=>true,
                'help_attr'=> [
                'class'=> 'text-success'
               ],
               
                   //pour gerer le fichier manuellement
                'mapped'=> false,

                //pour modifier le produit sans re-telecharger l'image
                'required'=> true,
                'constraints'=> [
                    //  new NotBlank([
                    //     'message'=> 'Veuillez uploader une image.'
                    // ]),
                    new File([
                        'maxSize'=>'2M',//format maximale de la photo 2MO
                        'mimeTypes'=> [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            ],
                            'mimeTypesMessage'=> 'Veuillez uploader une image valide au format (jpg,jpeg,png,webp).',
                            
                            
            ])
                ],
                 'attr'=>[
                        'class'=>'form-control bg-success-custom-brown-f  shadow',
                ],
            ])
                 //classe MoneyType pour le prix et currency pour la devise
            ->add('price',MoneyType::class,[

            //  'currency'pour changer la devise ici (HTG pour la gourde haïtienne) => 'USD',ou autres devise 
            //config pour appliquer une devise par defaut => config/packages/framework.yaml
            // framework:
            // default_locale: en_US # Mettre 'en' ou changer la locale adapte les devises par défaut
    
                    'label' => 'Prix<span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr'=>[
                    'class'=>'text-success',
                ],
                'attr' =>[
                        'placeholder'=> 'Ajouter un prix',
                        'class'=>'form-control  bg-success-custom-brown-f shadow rounded '
                        
                ],
                'help'=>'Prix du produit à afficher en : <span class="text-custom-brown">prix TTC.</span>',
                'help_html'=>true,
                'help_attr'=> [
                    'class'=> 'text-success'
                ],
                'row_attr' => [
                    'class'=>'shadow  bg-success-custom-brown-f p-4 m-4'
                ],
                'required'=> false,

                //Contraintes pour securiser les champs pour l'enregistrement en BD
                'constraints'=> [
                    new NotBlank([
                        'message'=> 'Veuillez saisir le prix du produit.'
                    ]),
                    new Positive([
                        'message'=>'Veuillez saisir un prix strictement superieur à 0.'
                    ])
                ]
        
            ])
            ->add('categories',EntityType::class,[
                'class' => Categories ::class,
                'choice_label'=>'name',
                'multiple'=>true,
                'attr' =>[
                        'placeholder'=> 'Ajouter un prix',
                        'class'=>'form-control  bg-success-custom-brown-f shadow rounded '],
           ])
           

                // ->add('Ajouter',SubmitType::class) //creation bouton formulaire
        ;
    }
    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }

}
