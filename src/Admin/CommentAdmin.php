<?php

declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

final class CommentAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('content', TextareaType::class, [
                'attr' => [
                    'rows' => '3'
                ]
            ])
            ->add('author')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid->add('author')->add('createdAt');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('author')
            ->add('createdAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('article')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'edit' => [],
                    'show' => [],
                    'delete' => [],
                ]
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Comment', ['class' => 'col-md-8'])
                ->add('createdAt', null, [
                    'format' => 'd/m/Y à H:i',
                ])
                ->add('content')
            ->end()
            ->with('Meta data', ['class' => 'col-md-4'])
                ->add('author')
                ->add("article")
            ->end()
        ;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('create');
    }
}