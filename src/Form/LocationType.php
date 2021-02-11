<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('utilisateur', EntityType::class, [
                'class' => Utilisateur::class,
                'label' => 'locataire',
                'multiple' => false,
                'choice_label' => 'nom'
            ])
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'label' => 'Produit',
                'multiple' => false,
                'choice_label' => 'nom'
            ])
            ->add('dateDebut')
            ->add('dateFin')
            ->add('montantLocation')

            ->add('valider', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
