<?php

namespace App\Form\Admin;

use App\Entity\Portal;
use App\Entity\Scenario;
use App\Entity\Timeline;
use App\Entity\ScenarioCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ScenarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('pitch', TextareaType::class, [
                'help' => "Courte présentation du projet de moins de 3000 caractères.",
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('categories', EntityType::class, [
                'class' => ScenarioCategory::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'required' => false,
            ])
            ->add('portals', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'required' => false,
            ])
            ->add('timelines', EntityType::class, [
                'class' => Timeline::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scenario::class,
        ]);
    }
}
