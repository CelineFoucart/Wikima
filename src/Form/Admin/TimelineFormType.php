<?php

namespace App\Form\Admin;

use App\Entity\Category;
use App\Entity\Portal;
use App\Entity\Timeline;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimelineFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
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
            ->add('portals', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
            ->add('previous', EntityType::class, [
                'class' => Timeline::class,
                'choice_label' => 'title',
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
            ->add('next', EntityType::class, [
                'class' => Timeline::class,
                'choice_label' => 'title',
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Timeline::class,
        ]);
    }
}
