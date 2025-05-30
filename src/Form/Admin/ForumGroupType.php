<?php

namespace App\Form\Admin;

use App\Entity\Forum;
use App\Entity\ForumCategory;
use App\Entity\ForumGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForumGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isSymfonyRole = (bool) $options['data']->isSymfonyRole();

        $builder
            ->add('title', TextType::class, [
                'help' => 'Le nom qui sera affiché aux utilisateurs',
            ])
            ->add('roleName', TextType::class, [
                'help' => 'Le nom unique du groupe',
                'disabled' => $isSymfonyRole,
            ])
            ->add('colour', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'color-input',
                ],
            ])
            ->add('description', TextareaType::class, [
                'help' => 'help_description',
                'required' => false,
            ])
            ->add('forumCategories', EntityType::class, [
                'class' => ForumCategory::class,
                'required' => false,
                'multiple' => true,
                'choice_label' => 'title',
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
            ->add('forums', EntityType::class, [
                'class' => Forum::class,
                'required' => false,
                'multiple' => true,
                'choice_label' => 'title',
                'attr' => [
                    'data-choices' => 'choices',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ForumGroup::class,
        ]);
    }
}
