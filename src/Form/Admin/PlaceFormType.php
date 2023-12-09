<?php

namespace App\Form\Admin;

use App\Entity\Place;
use App\Entity\Portal;
use App\Entity\Category;
use App\Entity\PlaceType;
use App\Entity\ImageGroup;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PlaceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $id = $options['data']->getId();

        $builder
            ->add('title', TextType::class)
            ->add('slug', TextType::class)
            ->add('situation', TextareaType::class, [
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('presentation', TextareaType::class, [
                'required' => false,
            ])
            ->add('history', TextareaType::class, [
                'required' => false,
            ])
            ->add('dominatedBy', TextType::class, [
                'required' => false,
            ])
            ->add('capitaleCity', TextType::class, [
                'required' => false,
            ])
            ->add('population', TextType::class, [
                'required' => false,
            ])
            ->add('languages', TextType::class, [
                'required' => false,
            ])
            ->add('size', TextType::class, [
                'required' => false,
            ])
            ->add('imageMap', VichImageType::class, [
                'constraints' => [
                    new Image([
                        'minWidth' => 200,
                        'maxWidth' => 300,
                    ]),
                    ],
                    'help' => 'map_help',
                    'required' => false,
            ])
            ->add('isInhabitable', TextType::class, [
                'required' => false,
            ])
            ->add('isSticky', null, [
                'required' => false,
            ])
            ->add('localisations', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'query_builder' => function (EntityRepository $er) use ($id) {
                    $query = $er->createQueryBuilder('p') ->orderBy('p.title', 'ASC');

                    if ($id !== null) {
                        $query->andWhere('p.id != :id')->setParameter('id', $id);
                    }
                    
                    return $query;
                },
            ])
            ->add('types', EntityType::class, [
                'class' => PlaceType::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices'
                ]
            ])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices'
                ]
            ])
            ->add('portals', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'data-choices' => 'choices'
                ]
            ])
            ->add('imageGroup', EntityType::class, [
                'class' => ImageGroup::class,
                'choice_label' => 'title',
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'required' => false,
            ])
            ->add('isArchived', null, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
