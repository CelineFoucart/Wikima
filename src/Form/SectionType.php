<?php

namespace App\Form;

use App\Entity\Section;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class SectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class, [
                'attr' => [
                    'data-editor' => 'editor'
                ]
            ])
            ->add('referencedArticles', null, [
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices'
                ]
            ])
            ->add('referencedTimelines', null, [
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices'
                ]
            ])
            ->add('referencedPersons', null, [
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices'
                ]
            ])
            ->add('referencedPlaces', null, [
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
