<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Place;
use App\Entity\Portal;
use App\Entity\Category;
use App\Entity\PlaceType;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Form\Type\TemplateType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class PlaceAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('dominatedBy')
            ->add('types')
            ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('types')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'read' => ['template' => 'Admin/show.html.twig'],
                    'show' => [],
                    'edit' => [],
                    'image' => ['template' => 'Admin/components/image_action.html.twig'],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Presentation', ['class' => 'col-md-8'])
                ->add('title', TextType::class, [
                    'attr' => [
                        'data-action' => 'slug',
                    ],
                ])
                ->add('slug', TextType::class, [
                    'attr' => [
                        'data-target' => 'slug',
                    ],
                ])
                ->add('description', CKEditorType::class, [
                    'config' => ['toolbar' => 'full', 'format_tags' => 'p;h2;h4;h5;h6;pre'],
                    'required' => false,
                ])
                ->add('presentation', CKEditorType::class, [
                    'config' => ['toolbar' => 'full', 'format_tags' => 'p;h3;h4;h5;h6;pre'],
                    'required' => false,
                ])
                ->add('history', CKEditorType::class, [
                    'config' => ['toolbar' => 'full', 'format_tags' => 'p;h3;h4;h5;h6;pre'],
                    'required' => false,
                ])
                ->add('isSticky', null, [
                    'required' => false,
                ])
                ->add('preview', TemplateType::class, [
                    'template' => 'Admin/components/_preview.html.twig',
                    'label' => false,
                ])
            ->end()
            ->with('In Short', ['class' => 'col-md-4'])
                ->add('situation', TextareaType::class, [
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
                ->add('isInhabitable', TextType::class, [
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
            ->end()
            ->with('Relations', ['class' => 'col-md-4'])
                ->add('localisations', EntityType::class, [
                    'class' => Place::class,
                    'choice_label' => 'title',
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('types', EntityType::class, [
                    'class' => PlaceType::class,
                    'choice_label' => 'title',
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('categories', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'title',
                    'multiple' => true,
                    'required' => false,
                ])
                ->add('portals', EntityType::class, [
                    'class' => Portal::class,
                    'choice_label' => 'title',
                    'multiple' => true,
                    'required' => false,
                ])
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Presentation', ['class' => 'col-md-8'])
                ->add('id')
                ->add('title', null, [
                    'template' => 'Admin/components/_show_title.html.twig',
                ])
                ->add('slug')
                ->add('description', null, [
                    'safe' => true,
                ])
                ->add('history', null, [
                    'safe' => true,
                ])
                ->add('presentation', null, [
                    'safe' => true,
                ])
            ->end()
            ->with('In Short', ['class' => 'col-md-4'])
                ->add('situation')
                ->add('dominatedBy')
                ->add('population')
                ->add('capitaleCity')
                ->add('localisations')
                ->add('categories')
                ->add('portals')
                ->add('types')
                ->add('languages')
                ->add('size')
                ->add('isInhabitable')
                ->add('illustration', null, ['template' => 'Admin/_place_illustration.html.twig'])
            ->end()
        ;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('image', $this->getRouterIdParameter().'/image');
    }
}
