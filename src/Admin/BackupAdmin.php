<?php

declare(strict_types=1);

namespace App\Admin;

use App\Service\BackupService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;

final class BackupAdmin extends AbstractAdmin
{
    public function __construct(
        private BackupService $backupService
    ) {  
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('filename')
            ->add('createdAt')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('filename')
            ->add('createdAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'download' => [
                        'template' => 'Admin/download.html.twig'
                    ],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('id')
            ->add('filename')
            ->add('createdAt')
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('filename')
            ->add('createdAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
        ;
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('edit')
            ->add('download', $this->getRouterIdParameter().'/download');
        ;
    }

    public function preRemove(object $object): void
    {
        $path = $this->backupService->getBackupFolder() . DIRECTORY_SEPARATOR . $object->getFilename();

        if (file_exists($path)) {
            unlink($path);
        }
    }
}
