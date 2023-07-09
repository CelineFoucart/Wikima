<?php

namespace App\Form\Admin;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('keywords', TextType::class)
            ->add('description', TextareaType::class, [
                'help' => 'help_description',
            ])
            ->add('presentation', TextareaType::class)
            ->add('imageBanner', VichImageType::class, [
                'constraints' => [
                    new Image([
                        'minWidth' => 800,
                        'maxWidth' => 1320,
                        'minHeight' => 200,
                        'maxHeight' => 300,
                    ])
                ],
                'help' => "banner_help",
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
