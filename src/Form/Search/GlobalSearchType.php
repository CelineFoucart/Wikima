<?php

namespace App\Form\Search;

use App\Entity\Data\SearchData;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GlobalSearchType extends AdvancedSearchType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('query', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Search...',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                    ]),
                ]
            ])
            ->add('fields', ChoiceType::class, [
                'multiple' => true,
                'label' => false,
                'attr' => [
                    'data-choices' => 'choices',
                ],
                'choices' => [
                    'name' => 'name',
                    'description' => 'description',
                    'tags' => 'tags'
                ]
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