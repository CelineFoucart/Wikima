<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class InstallationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('DB_NAME', TextType::class, [
                'label' => "Nom de la base de données",
                "constraints" => [ new NotBlank() ],
                "help" => "Nom de la base de donnée, celui qui que vous avez renseigné en la créant"
            ])
            ->add('DB_USER', TextType::class, [
                'label' => "Nom d'utilisateur",
                "constraints" => [ new NotBlank() ],
                "help" => "Utilisateur qui se connectera à la base de données. Ne jamais utiliser root"
            ])
            ->add('DB_PASSWORD', PasswordType::class, [
                'label' => "Mot de passe",
                "constraints" => [ new NotBlank() ],
                'attr' => ['autocomplete' => 'new-password'],
                "help" => "Mot de passe de l'utilisateur de la base de données"
            ])
            ->add('DB_HOST', TextType::class, [
                'label' => "Hôte",
                "constraints" => [ new NotBlank() ],
                "help" => "Hôte de la base de donnée, en général localhost",
            ])
            ->add('DB_PORT', IntegerType::class, [
                'label' => "Port",
                "constraints" => [ new NotBlank() ],
                'help' => "Port utilisé pour la base de données, en général 3306"
            ])
            ->add('serverVersion', TextType::class, [
                'label' => "Version de la base de données",
                'help' => "L'information se trouve sur la page d'accueil de phpMyAdmin"
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
