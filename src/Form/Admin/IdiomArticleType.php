<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\IdiomArticle;
use App\Entity\IdiomCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IdiomArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('description', TextareaType::class)
            ->add('content', TextareaType::class, [
                'attr' => [
                    'data-fulleditor' => 'Titre 1=h2; Titre 2=h3; Titre 3=h4; Titre 4=h5; Titre 5=h6;',
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => IdiomCategory::class,
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
            'data_class' => IdiomArticle::class,
        ]);
    }
}
