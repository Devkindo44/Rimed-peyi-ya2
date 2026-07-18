<?php

namespace App\Form;

use App\Entity\AdresseDeLivraison;
use App\Entity\Commande;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Masqué en HTML pour l'objet DateTime
            ->add('date', DateTimeType::class, [
                'attr' => ['style' => 'display:none;'],
                'label' => false,
            ])
            
            // Masqués car ce sont des valeurs scalaires (chiffres/texte)
            ->add('montant_total', HiddenType::class)
            ->add('frais_port', HiddenType::class)
            ->add('transporteur_nom', HiddenType::class)
            
            // Seule l'adresse reste visible et sélectionnable
            ->add('adressedeLivraison', EntityType::class, [
                'class' => AdresseDeLivraison::class,
                'choice_label' => function (AdresseDeLivraison $adresse) {
                    // 1. On prépare la chaîne de caractères avec l'adresse
                    $affichage = sprintf(
                        '%s, %s %s', 
                        $adresse->getAdressePostale(), 
                        $adresse->getCodePostale(), 
                        $adresse->getVille()
                    );
                    
                    // 2. On récupère la relation User liée à cette adresse
                    $user = $adresse->getUser();
                    
                    // 3. Si l'utilisateur possède un numéro de téléphone, on l'ajoute à la suite
                    if ($user && $user->getNumeroDeTelephone()) {
                        $affichage .= sprintf(' (Tel: %s)', $user->getNumeroDeTelephone());
                    }
                    
                    return $affichage;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}