<?php
// src/Form/UtilisateurType.php
namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $requis = true;

        $builder
            ->add('utilisateur', TextType::class, ['label' => 'Nom d’utilisateur'])
            ->add('prenom', TextType::class, ['label' => 'Prénom'])
            ->add('nom', TextType::class, ['label' => 'Nom de famille'])
            ->add('genre', ChoiceType::class, [
                'choices' => ['Féminin' => 'Féminin', 'Masculin' => 'Masculin', 'Neutre' => 'Neutre']
            ])
            ->add('adresse', TextType::class, ['label' => 'Adresse'])
            ->add('ville', TextType::class, ['label' => 'Ville'])
            ->add('province', ChoiceType::class, [
                'choices' => [
                    'Québec' => 'Québec', 'Ontario' => 'Ontario', 'Manitoba' => 'Manitoba',
                    'Saskatchewan' => 'Saskatchewan', 'Alberta' => 'Alberta', 'Colombie-Britannique' => 'Colombie-Britannique',
                    'Nouvelle-Écosse' => 'Nouvelle-Écosse', 'Île-du-Prince-Édouard' => 'Île-du-Prince-Édouard',
                    'Nouveau-Brunswick' => 'Nouveau-Brunswick', 'Terre-Neuve-et-Labrador' => 'Terre-Neuve-et-Labrador',
                    'Territoires du Nord-Ouest' => 'Territoires du Nord-Ouest', 'Nunavut' => 'Nunavut', 'Yukon' => 'Yukon'
                ]
            ])
            ->add('codePostal', TextType::class, ['label' => 'Code Postal'])
            ->add('telephone', TextType::class, ['label' => 'Téléphone', 'required' => $requis])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('motDePasse', PasswordType::class, ['label' => 'Mot de passe'])
            ->add('confirmationMotDePasse', PasswordType::class, ['label' => 'Confirmez le mot de passe']);
            }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Client::class]);
    }
}