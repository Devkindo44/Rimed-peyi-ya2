<?php

namespace App\Form;

use App\Entity\AdresseDeLivraison;
use App\Entity\Commande;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
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
        /** @var User|null $user */
        $user = $options['user'];

        $builder
            ->add('date', DateTimeType::class, [
                'attr' => ['style' => 'display:none;'],
                'label' => false,
            ])
            
            ->add('montant_total', HiddenType::class)
            ->add('frais_port', HiddenType::class)
            ->add('transporteur_nom', HiddenType::class)
            
            ->add('adressedeLivraison', EntityType::class, [
                'class' => AdresseDeLivraison::class,
                'label' => 'Choisissez votre adresse de livraison',
                'query_builder' => function (EntityRepository $er) use ($user) {
                
                    return $er->createQueryBuilder('a')
                        ->where('a.user = :user')
                        ->setParameter('user', $user);
                },
                'choice_label' => function (AdresseDeLivraison $adresse) {
                    $affichage = sprintf(
                        '%s, %s %s', 
                        $adresse->getAdressePostale(), 
                        $adresse->getCodePostale(), 
                        $adresse->getVille()
                    );
                    
                    // Utilisation du getter de l'adresse
                    $u = $adresse->getUser(); 
                    if ($u && $u->getNumeroDeTelephone()) {
                        $affichage .= sprintf(' (Tel: %s)', $u->getNumeroDeTelephone());
                    }
                    
                    return $affichage;
                },
                'expanded' => true, // Boutons radio
                'multiple' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'user' => null, // Permet de recevoir 'user' depuis le controller
        ]);

        // Valide que 'user' est bien une instance de User ou null
        $resolver->setAllowedTypes('user', [User::class, 'null']);
    }
}