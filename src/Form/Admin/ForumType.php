<?php

namespace App\Form\Admin;

use App\Entity\Forum;
use App\Entity\ForumCategory;
use App\Entity\ForumGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ForumType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('description', TextareaType::class, [
                'help' => 'help_description',
                'required' => false,
            ])
            ->add('groupAccess', EntityType::class, [
                'class' => ForumGroup::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'help' => "Ajoutez les groupes d'utilisateurs ayant accès à ce forum"
            ])
            ->add('category', EntityType::class, [
                'class' => ForumCategory::class,
                'choice_label' => 'title',
                'attr' => [
                    'data-choices' => 'choices'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Forum::class,
        ]);
    }
}
