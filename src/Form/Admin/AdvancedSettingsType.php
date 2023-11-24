<?php

namespace App\Form\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Range;

class AdvancedSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('WIKI_NAME', TextType::class, [
                'label' => "Nom de l'application",
                "constraints" => [
                    new NotBlank(),
                    new Length([
                        'min' => 2,
                        'max' => 180
                    ])
                ]
            ])
            ->add('WIKI_DESCRIPTION', TextType::class, [
                'label' => "Description",
                'help' => "Description rapide de moins de 160 caractères, utilisé pour le référencement.",
                "constraints" => [
                    new NotBlank(),
                    new Length([
                        'min' => 10,
                        'max' => 160
                    ])
                ]
            ])
            ->add('faviconFile', FileType::class, [
                'label' => 'Favicon',
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 300,
                        'maxHeight' => 300,
                        'mimeTypes' => [
                            'image/png',
                        ],
                    ]),
                ],
                'help' => "Le favicon doit faire au maximum 300 pixels de large et 300 pixels de hauteur avec une extension en png, jpg ou jpeg.",
            ])
            ->add('bannerFile', FileType::class, [
                'label' => 'Bannière',
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxWidth' => 2000,
                        'minHeight' => 400,
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                    ])
                ],
                'help' => "La bannière doit faire au maximum 2000 pixels de large et au minimum 400 pixels de hauteur avec une extension en png.",
            ])
            ->add('BACKGROUND_COLOR', TextType::class, [
                'label' =>"Couleur de l'arrière plan du site",
                'help' => "Cette valeur définira l'arrière plan du body de la page (propriété CSS background).",
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
            ->add('PER_PAGE_EVEN_COLUMNS', IntegerType::class, [
                'label' => "Pagination des affichages avec 1, 2 et 4 colonnes",
                'help' => "Définissez le nombre d'éléments par page sur ce type d'affichage, de 10 à 100.",
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                    new Range([
                        'min' => 10,
                        'max' => 100
                    ])
                ]
            ])
            ->add('PER_PAGE_ODD_COLUMNS', IntegerType::class, [
                'label' => "Pagination des affichages avec 3 et 6 colonnes",
                'help' => "Définissez le nombre d'éléments par page sur ce type d'affichage, de 6 à 90.",
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                    new Range([
                        'min' => 6,
                        'max' => 90
                    ])
                ]
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
                'help' => "L'environnement de développement n'est à utiliser qu'en local, car il expose des données sensibles comme les identifiants de la base de données."
            ])
            ->add('DATE_FORMAT', TextType::class, [
                'label' =>"Format de la date",
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
