<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdvancedSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('WIKI_NAME', TextType::class, [
                'label' => "Nom de l'application",
            ])
            ->add('WIKI_DESCRIPTION', TextType::class, [
                'label' => "Description",
                'help' => "Description rapide de moins de 160 caractères, utilisé pour le référencement"
            ])
            ->add('faviconFile', FileType::class, [
                'label' => 'Favicon',
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 300,
                        'maxHeight' => 300,
                    ]),
                ],
                'help' => "Le favicon doit faire au maximum 300 pixels de large et 300 pixels de hauteur.",
            ])
            ->add('CONTACT_EMAIL', TextType::class, [
                'label' => "Email de contact",
            ])
            ->add('CONTACT_NAME', TextType::class, [
                'label' => 'Nom associé à l\'email de contact',
                'required' => false,
            ])
            ->add('SMTP_USER', TextType::class, [
                'label' => "Nom d'utilisateur du compte SMTP",
                'required' => false,
                'help' => "L'identifiant de votre compte SMTP"
            ])
            ->add('SMTP_PASSWORD', PasswordType::class, [
                'label' => "Mot de passe du compte SMTP",
                'attr' => ['autocomplete' => 'new-password'],
                'required' => false,
                'help' => "Le mot de passe de votre compte SMTP"
            ])
            ->add('SMTP_HOST', TextType::class, [
                'label' => "Service SMTP",
                'required' => false,
                'help' => "L'hôte du serveur SMTP",
            ])
            ->add('SMTP_PORT', IntegerType::class, [
                'label' => "Port utilisé pour la connexion SMTP",
                'required' => false,
                'help' => "Le port du serveur SMTP"
            ])
            ->add('ENABLE_REGISTRATION', CheckboxType::class, [
                'label' => "Autoriser la création de compte",
                'required' => false,
            ])
            ->add('ENABLE_CONTACT', CheckboxType::class, [
                'label' => "Activer la page de contact",
                'required' => false,
            ])
            ->add('APP_ENV', ChoiceType::class, [
                'choices' => [
                    'Développement' => 'dev',
                    'Production' => 'prod'
                ],
                'label' => "Environnement",
                'help' => "L'environnement de développement n'est à utiliser qu'en local, car il expose des données sensibles comme les identifiants de la base de données"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
