<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Category;
use App\Entity\Portal;
use DateTime;
use DateTimeImmutable;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

final class NoteAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('title')
            ->add('message')
            ->add('isProcessed')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('category')
            ->add('portal')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('title')
            ->add('isProcessed', null, [
                'editable' => true
            ])
            ->add('createdAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('updatedAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('category')
            ->add('portal')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title')
            ->add('message', CKEditorType::class, [
                'config' => ['toolbar' => 'basic'],
                'required' => false,
            ])
            ->add('portal', EntityType::class, [
                'class' => Portal::class,
                'choice_label' => 'title',
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title',
                'required' => false,
            ])
            ->add('isProcessed', null, [
                'required' => false,
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('title')
            ->add('message', null, [
                'safe' => true,
            ])
            ->add('createdAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('updatedAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('category', TemplateType::class, [
                'template' => 'Admin/note/_category.html.twig',
            ])
            ->add('portal', TemplateType::class, [
                'template' => 'Admin/note/_portal.html.twig',
            ])
            ->add('isProcessed', null, [
                'editable' => true
            ])
        ;
    }

    public function preUpdate(object $note): void
    {
        $note->setUpdatedAt(new DateTime());
    }
    
    public function prePersist(object $note): void
    {
        $note->setCreatedAt(new DateTimeImmutable())->setIsProcessed(false);
    }
}
