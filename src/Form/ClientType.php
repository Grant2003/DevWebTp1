<?php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('utilisateur', TextType::class, [
                'label' => 'Utilisateur',
                'required' => true,
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('courriel', EmailType::class, [
                'label' => 'Courriel',
                'required' => true,
            ])
            ->add('genre', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Féminin' => 'Féminin',
                    'Masculin' => 'Masculin',
                    'Neutre' => 'neutre',
                ],
                'required' => true,
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'required' => true,
            ])
            ->add('codePostal', TextType::class, [
                'label' => 'Code Postal',
                'required' => true,
            ])
            ->add('province', ChoiceType::class, [
                'label' => 'Province',
                'choices' => [
                    'Alberta' => 'Alberta',
                    'British Columbia' => 'British Columbia',
                    'Manitoba' => 'Manitoba',
                    // Add other provinces and territories...
                ],
                'required' => true,
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => true,
            ])
            ->add('motDePasse', PasswordType::class, [
                'label' => 'Mot de Passe',
                'required' => true,
            ])
            ->add('confirmationMotDePasse', PasswordType::class, [
                'label' => 'Confirmer le Mot de Passe',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}