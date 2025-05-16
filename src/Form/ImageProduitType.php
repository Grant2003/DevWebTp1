<?php

namespace App\Form;
//-----------------------------------
//   Fichier : ImageProduitType.php
//   Par:      Anthony Grenier
//   Date :    2025-5-11
//-----------------------------------
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\{SubmitType, FileType};

class ImageProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageProduit', FileType::class)
            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}