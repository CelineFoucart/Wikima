<?php

namespace App\Form\Admin;

use App\Entity\Person;
use App\Entity\Portal;
use App\Entity\Category;
use App\Entity\PersonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PersonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => ['data-type' => 'firstname'],
            ])
            ->add('lastname', TextType::class, [
                'attr' => ['data-type' => 'lastname'],
            ])
            ->add('fullname', TextType::class, [
                'attr' => [ 'data-type' => 'fullname'],
            ])
            ->add('slug', TextType::class)
            ->add('nationality', TextType::class, ['required' => false])
            ->add('job', TextType::class, [ 'required' => false ])
            ->add('birthday', TextType::class, ['required' => false])
            ->add('birthdayPlace', TextType::class, ['required' => false ])
            ->add('deathDate', TextType::class, [ 'required' => false ])
            ->add('deathPlace', TextType::class, ['required' => false])
            ->add('parents', TextType::class, [ 'required' => false ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [ 'style' => 'height: 73px' ],
                'help' => 'help_description',
            ])
            ->add('presentation', TextareaType::class)
            ->add('biography', TextareaType::class, [ 'required' => false])
            ->add('personality', TextareaType::class, ['required' => false ])
            ->add('isSticky', null, ['required' => false ])
            ->add('children', TextType::class, ['required' => false])
            ->add('siblings', TextType::class, ['required' => false])
            ->add('partner', TextType::class, [ 'required' => false])
            ->add('physicalDescription', TextareaType::class, [
                'required' => false,
                'attr' => ['style' => 'height: 73px' ]
            ])
            ->add('species', TextType::class, [ 'required' => false ])
            ->add('gender', TextType::class, ['required' => false])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => ['data-choices' => 'choices']
            ])
            ->add('portals', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'multiple' => true,
                'attr' => ['data-choices' => 'choices']
            ])
            ->add('type', EntityType::class, [
                'class' => PersonType::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'attr' => ['data-choices' => 'choices']
            ]) 
            ->add('isArchived', null, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Person::class,
        ]);
    }
}
