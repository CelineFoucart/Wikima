<?php

namespace App\Form\Admin;

use App\Entity\ArticleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'attr' => [
                    'data-action' => 'slug',
                ],
            ])
            ->add('slug', TextType::class, [
                'attr' => [
                    'data-target' => 'slug',
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('icon', TextType::class, [
                'help' => 'help_icon',
                'help_html' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ArticleType::class,
        ]);
    }
}
