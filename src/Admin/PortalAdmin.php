<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Category;
use DateTime;
use DateTimeImmutable;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\TemplateType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class PortalAdmin extends AbstractAdmin
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
            ->with('Informations et relations', ['class' => 'col-md-3'])
                ->add('keywords', TextType::class)
                ->add('description', TextareaType::class, [
                    'help' => 'help_description',
                ])
                ->add('categories', EntityType::class, [
                    'class' => Category::class,
                    'choice_label' => 'title',
                    'multiple' => true,
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
            ->add('slug')
            ->add('keywords')
            ->add('description')
            ->add('categories', null, [
                'field_type' => EntityType::class,
                'field_options' => [
                    'class' => Category::class,
                    'choice_label' => 'title',
                ],
            ])
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
            ->tab('Portal')
                ->with('Content', ['class' => 'col-md-9'])
                    ->add('title', null, [
                        'template' => 'Admin/components/_show_title.html.twig',
                    ])
                    ->add('slug')
                    ->add('keywords')
                    ->add('description')
                    ->add('presentation', null, [
                        'safe' => true,
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
            ->end()
            ->tab('Relations')
                ->with('Children', ['class' => 'col-md-6'])
                    ->add('articles')
                ->end()
                ->with('Parents', ['class' => 'col-md-6'])
                    ->add('categories')
                ->end()
            ->end()
        ;
    }

    public function preUpdate(object $portal): void
    {
        $portal->setUpdatedAt(new DateTime());
    }

    public function prePersist(object $portal): void
    {
        $portal->setCreatedAt(new DateTimeImmutable());
    }
}
