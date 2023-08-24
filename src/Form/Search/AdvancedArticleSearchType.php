<?php

namespace App\Form\Search;

use App\Entity\ArticleType;
use App\Entity\Data\SearchData;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvancedArticleSearchType extends SearchPortalType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('tags', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => ArticleType::class,
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'allow_extra_fields ' => true,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
