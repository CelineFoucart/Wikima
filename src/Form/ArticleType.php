<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Portal;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
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
            ->add('keywords', TextType::class)
            ->add('portals', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'multiple' => true,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => '3',
                ],
            ])
            ->add('content', CKEditorType::class, [
                'config' => ['toolbar' => 'full'],
            ])
            ->add('isDraft', CheckboxType::class, [
                'label' => 'draft',
                'required' => false,
            ])
            ->add('isPrivate', CheckboxType::class, [
                'label' => 'private',
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
