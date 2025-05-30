<?php

namespace App\Form\Admin;

use App\Entity\Scenario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class ScenarioNoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('notes', TextareaType::class, [
            'required' => false,
            'attr' => [
                'data-fulleditor' => 'Titre 1=h2; Titre 2=h3; Titre 3=h4; Titre 4=h5; Titre 5=h6;',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scenario::class,
        ]);
    }
}
