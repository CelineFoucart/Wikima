<?php

namespace App\Form\Admin;

use App\Entity\Place;
use App\Entity\MapPosition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MapPositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('color', TextType::class, [
                'required' => false,
            ])
            ->add('marker', TextType::class, [
                'help' => 'help_icon',
                'help_html' => true,
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'title',
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
            'data_class' => MapPosition::class,
        ]);
    }
}
