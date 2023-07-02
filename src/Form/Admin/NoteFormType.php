<?php

namespace App\Form\Admin;

use App\Entity\Note;
use App\Entity\Portal;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title')
        ->add('message', CKEditorType::class, [
            'config' => ['toolbar' => 'basic'],
            'required' => false,
        ])
        ->add('portal', EntityType::class, [
            'class' => Portal::class,
            'choice_label' => 'title',
            'required' => false,
            'attr' => [
                'data-choices' => 'choices'
            ]
        ])
        ->add('category', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'title',
            'required' => false,
            'attr' => [
                'data-choices' => 'choices'
            ]
        ])
        ->add('isProcessed', null, [
            'required' => false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
