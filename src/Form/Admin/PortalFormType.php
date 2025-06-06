<?php

namespace App\Form\Admin;

use App\Entity\Category;
use App\Entity\Portal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PortalFormType extends AbstractType
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
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
            ->add('presentation', TextareaType::class, [
                'attr' => [
                    'data-fulleditor' => 'Titre 1=h3; Titre 2=h4; Titre 3=h5; Titre 4=h6;',
                ],
            ])
            ->add('imageBanner', VichImageType::class, [
                'constraints' => [
                    new Image([
                        'minWidth' => 900,
                        'minHeight' => 300,
                    ]),
                ],
                'help' => 'banner_help',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Portal::class,
        ]);
    }
}
