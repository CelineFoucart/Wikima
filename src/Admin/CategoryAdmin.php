<?php

declare(strict_types=1);

namespace App\Admin;

use DateTime;
use DateTimeImmutable;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Form\Type\TemplateType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\Image;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;

final class CategoryAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Content', ['class' => 'col-md-9'])
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
                ->add('presentation', CKEditorType::class, [
                    'config' => ['toolbar' => 'full', 'format_tags' => 'p;h3;h4;h5;h6;pre'],
                ])
                ->add('preview', TemplateType::class, [
                    'template' => 'Admin/components/_preview.html.twig',
                    'label' => false,
                ])
            ->end()
            ->with('Informations', ['class' => 'col-md-3'])
                ->add('keywords', TextType::class)
                ->add('description', TextareaType::class, [
                    'help' => 'help_description',
                ])
            ->end() 
            ->with('Illustration', ['class' => 'col-md-3'])
                ->add('imageBanner', VichImageType::class, [
                    'constraints' => [
                        new Image([
                            'minWidth' => 800,
                            'maxWidth' => 1320,
                            'minHeight' => 200,
                            'maxHeight' => 300,
                        ])
                        ],
                        'help' => "banner_help",
                        'required' => false,
                ])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('title')
            ->add('keywords')
            ->add('description')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('title')
            ->add('slug')
            ->add('createdAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('updatedAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'read' => ['template' => 'Admin/show.html.twig'],
                    'sortable' => ['template' => 'Admin/components/_sortable_btn.html.twig'],
                    'edit' => [],
                    'show' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Category', ['class' => 'col-md-9'])
                ->add('title', null, [
                    'template' => 'Admin/components/_show_title.html.twig',
                ])
                ->add('slug')
                ->add('keywords')
                ->add('description')
                ->add('presentation', null, [
                    'safe' => true,
                ])
                ->add('portals', null, [
                    'template' => 'Admin/components/_sortable_link.html.twig',
                ])
            ->end()
            ->with('Meta data', ['class' => 'col-md-3'])
                ->add('createdAt', null, [
                    'format' => 'd/m/Y à H:i',
                ])
                ->add('updatedAt', null, [
                    'format' => 'd/m/Y à H:i',
                ])
            ->end()
            ->with('Banner', ['class' => 'col-md-9'])
                ->add('banner', null, [
                    'template' => 'Admin/components/_banner_show.html.twig',
                ])
            ->end()
        ;
    }

    public function preUpdate(object $category): void
    {
        $category->setUpdatedAt(new DateTime());
    }

    public function prePersist(object $category): void
    {
        $category->setCreatedAt(new DateTimeImmutable());
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('sortable', $this->getRouterIdParameter().'/sortable');
    }
}
