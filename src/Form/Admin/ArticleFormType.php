<?php

namespace App\Form\Admin;

use App\Entity\Article;
use App\Entity\ArticleType;
use App\Entity\Portal;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('type', EntityType::class, [
                'class' => ArticleType::class,
                'choice_label' => 'title',
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices'
                ]
            ])
            ->add('slug', TextType::class)
            ->add('keywords', TextType::class)
            ->add('portals', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices'
                ]
            ])
            ->add('description', TextareaType::class)
            ->add('content', TextareaType::class)
            ->add('isDraft', CheckboxType::class, [
                'label' => 'draft',
                'required' => false,
            ])
            ->add('isPrivate', CheckboxType::class, [
                'label' => 'private',
                'required' => false,
            ])
            ->add('isSticky', CheckboxType::class, [
                'required' => false,
            ])
            ->add('isArchived', null, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
