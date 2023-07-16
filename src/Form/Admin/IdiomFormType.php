<?php

namespace App\Form\Admin;

use App\Entity\Idiom;
use App\Entity\Portal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;

class IdiomFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('translatedName', TextType::class)
            ->add('originalName', TextType::class, [
                'required' => false,
            ])
            ->add('slug', TextType::class)
            ->add('description', TextareaType::class, [
                'help' => 'help_description',
                'required' => false,
            ])
            ->add('presentation', TextareaType::class)
            ->add('imageBanner', VichImageType::class, [
                'constraints' => [
                    new Image([
                        'minWidth' => 800,
                        'maxWidth' => 1320,
                        'minHeight' => 200,
                        'maxHeight' => 300,
                    ]),
                ],
                'help' => 'banner_help',
                'required' => false,
            ])
            ->add('portals', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Idiom::class,
        ]);
    }
}
