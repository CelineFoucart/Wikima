<?php

namespace App\Form\Admin;

use App\Entity\Place;
use App\Entity\Person;
use App\Entity\Portal;
use App\Entity\Scenario;
use App\Entity\Timeline;
use App\Entity\ImageGroup;
use App\Entity\ScenarioCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('defaultColor', TextType::class, [
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'help' => "Texte explicatif affiché sur l'interface de gestion des épisodes"
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
            ->add('imageGroup', EntityType::class, [
                'class' => ImageGroup::class,
                'choice_label' => 'title',
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'required' => false,
            ])
            ->add('places', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'required' => false,
            ])
            ->add('persons', EntityType::class, [
                'class' => Person::class,
                'choice_label' => 'fullname',
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'required' => false,
            ])
            ->add('public', CheckboxType::class, [
                'required' => false,
            ])
            ->add('archived', CheckboxType::class, [
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
