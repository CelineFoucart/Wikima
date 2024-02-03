<?php

namespace App\Form\Admin;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\ImageGroup;
use App\Entity\ImageTag;
use App\Entity\Portal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

#[UniqueEntity('slug')]
class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $id = $options['data']->getId();

        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('keywords', TextType::class)
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => '3',
                ],
                'help' => 'help_description',
            ])
            ->add('tags', EntityType::class, [
                'class' => ImageTag::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
            ->add('portals', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
            ->add('imageGroups', EntityType::class, [
                'class' => ImageGroup::class,
                'choice_label' => 'title',
                'attr' => [
                    'data-choices' => 'choices',
                ],
                'required' => false,
                'multiple' => true,
                'by_reference' => false,
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => (null === $id) ? true : false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/gif',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Seuls sont autorisés les fichiers jpeg, jpg, gif et png.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
