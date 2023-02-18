<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class ArticleTypeAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('title')
            ->add('description')
            ->add('icon')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('title')
            ->add('slug')
            ->add('icon', null, [
                'template' => 'Admin/components/_icon_list.html.twig',
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'read' => ['template' => 'Admin/show.html.twig'],
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
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
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('icon', TextType::class, [
                'help' => 'help_icon',
                'help_html' => true,
                'required' => false,
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('title', null, [
                'template' => 'Admin/components/_show_title.html.twig',
            ])
            ->add('slug')
            ->add('description')
            ->add('icon', null, [
                'template' => 'Admin/components/_icon_show.html.twig',
            ])
            ;
    }
}
