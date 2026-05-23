<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

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
               'help'=>'Nombre de caractères maximal : <span class="text-custom-brown">40 charactères max.</span>',
               'help_html'=>true,
               'help_attr'=> [
                'class'=> 'text-success'
               ],
               'row_attr' => [
               'class'=>'shadow rounded bg-success-custom-brown-f  p-4 m-4'
               ],
               'required'=> false

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
               'class'=>'shadow rounded bg-success-custom-brown-f  p-4 m-4'
               ],
               'required'=> false
                
                
            ])

            //class FileType pour ajouter les images
            ->add('illustration',FileType::class,[
                'label' => 'illustration<span class="text-danger">*</span>',
                'label_html' => true,
                'label_attr'=>[
                'class'=>'text-success',
               ],
               'row_attr' => [
               'class'=>'shadow rounded p-4 m-4 bg-success-custom-brown-f '
               ],
                'help'=>'Veuillez uploader une image valide : <span class="text-custom-brown"> (jpg,png,webp)de 2MO max. </span>',
                'help_html'=>true,
                'help_attr'=> [
                'class'=> 'text-success'
               ],
               
                   

                //pour gerer le fichier manuellement
                'mapped'=> false,

                //pour modifier le produit sans re-telecharger l'image
                'required'=> false,
                'constraints'=> [
                    new File([
                        'maxSize'=>'2M',//format maximale de la photo 2MO
                        'mimeTypes'=> [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            ],
                            'mimeTypesMessage'=> 'Veuillez uploader une image valide (jpg,png,webp).',
                            
                            
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
                'help'=>'Le prix du produit : <span class="text-custom-brown">TTC.</span>',
                'help_html'=>true,
                'help_attr'=> [
                    'class'=> 'text-success'
                ],
                'row_attr' => [
                    'class'=>'shadow rounded bg-success-custom-brown-f p-4 m-4'
                ],
                'required'=> false
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
