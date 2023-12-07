<?php

namespace App\Form\Admin;

use App\Entity\Scenario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ScenarioEpisodeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('episodes', CollectionType::class, [
                'entry_type' => EpisodeShortType::class,
                'allow_add' => false,
                'allow_delete' => false,
                'label' => false,
                'required' => false,
                'entry_options' => [
                    'label' => false,
                ],
            ])
        ;
    }

    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scenario::class,
        ]);
    }
}
