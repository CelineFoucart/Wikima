<?php

namespace App\Form\Admin;

use App\Entity\Article;
use App\Form\Admin\ArticleFormType as AdminArticleFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SectionConvertType extends AdminArticleFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $builder->add('actions', ChoiceType::class, [
            'required' => true,
            'expanded' => true,
            'mapped' => false,
            'label' => false,
            'choices'  => [
                'Supprimer la section' => 0,
                'Modifier la section' => 1,
                'Garder la section' => 2,
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
