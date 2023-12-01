<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Image;

/**
 * Class AccountType
 * 
 * AccountType represents an form for updating username and email.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class)
            ->add('rank', TextType::class, [
                'required' => false,
            ])
            ->add('localisation', TextType::class, [
                'required' => false,
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/gif',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Seuls sont autorisés les fichiers jpeg, jpg, gif et png.',
                    ]),
                    new Image([
                        'maxWidth' => 300,
                        'maxHeight' => 300,
                    ])
                ],
                'help' => "Seuls sont autorisés les fichiers jpeg, jpg, gif et png d'une largeur et d'une hauteur maximale de 300 pixels."
            ])
            ->add('email', EmailType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
