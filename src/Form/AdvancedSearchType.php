<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Data\SearchData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvancedSearchType extends SearchPortalType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('categories', EntityType::class, [
                'label' => false,
                'required' => false,
                'class' => Category::class,
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
            ->add('page', HiddenType::class, [ 'label' => false,'required' => false, 'mapped' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
