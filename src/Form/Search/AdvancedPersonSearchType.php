<?php

namespace App\Form\Search;

use App\Entity\Data\SearchData;
use App\Entity\PersonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvancedPersonSearchType extends AdvancedSearchType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('tags', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => PersonType::class,
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
