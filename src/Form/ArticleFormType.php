<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\ArticleType;
use App\Entity\Portal;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
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
            ->add('title', TextType::class, [
                'attr' => [
                    'data-action' => 'slug',
                ],
            ])
            ->add('type', EntityType::class, [
                'class' => ArticleType::class,
                'choice_label' => 'title',
                'required' => false,
            ])
            ->add('slug', TextType::class, [
                'attr' => [
                    'data-target' => 'slug',
                ],
            ])
            ->add('keywords', TextType::class)
            ->add('portals', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'multiple' => true,
            ])
            ->add('description', TextareaType::class)
            ->add('content', CKEditorType::class, [
                'config' => ['toolbar' => 'full', 'format_tags' => 'p;h2;h3;h4;h5;h6;pre'],
            ])
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
