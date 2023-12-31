<?php

namespace App\Form\Admin;

use App\Entity\Place;
use App\Entity\Person;
use App\Entity\Episode;
use App\Entity\Scenario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EpisodeShortType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
                'help' => "Petite description pour les cartes (elle ne sera pas visible sur la partie publique)",
            ])
            ->add('color', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'color-input'
                ]
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
            ->add('valid', CheckboxType::class, [
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
            'data_class' => Episode::class,
        ]);
    }
}
