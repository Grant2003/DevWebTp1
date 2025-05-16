<?php

namespace App\Form;
//-----------------------------------
//   Fichier : produitType.php
//   Par:      Anthony Grenier
//   Date :    2025-5-11
//-----------------------------------
use App\Entity\Produit;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => ['maxlength' => 50]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => ['maxlength' => 150]
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix',
                'required' => true,
                'currency' => 'CAD'
            ])
            ->add('qtte_stock', IntegerType::class, [
                'label' => 'Quantité en stock',
                'required' => true
            ])
            ->add('Qtte_seuil_min', IntegerType::class, [
                'label' => 'Seuil minimal',
                'required' => true
            ])
            ->add('idCategorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'required' => true,
                'placeholder' => 'Choisir une catégorie'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
