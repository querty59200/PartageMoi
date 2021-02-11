<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('marque')
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'label' => 'Catégorie',
                'multiple' => false,
                'choice_label' => 'nom'
            ])
            ->add('contenu', CKEditorType::class)
            ->add('images', FileType::class, [
                'label' => 'Image (PNG / JPG / JPEG file)',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'data_class' => null
            ])
            ->add('publier', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
