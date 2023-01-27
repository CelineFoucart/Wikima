<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Portal;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class ArticleAdmin extends AbstractAdmin
{
    public function __construct(
        private TokenStorageInterface $token
    ) {
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('Article body', ['class' => 'col-md-8'])
                ->add('content', CKEditorType::class, [
                    'config' => ['toolbar' => 'full', 'format_tags' => 'p;h2;h3;h4;h5;h6;pre'],
                ])
            ->end()
            ->with('Meta Data', ['class' => 'col-md-4'])
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
                ->add('keywords', TextType::class)
                ->add('description', TextareaType::class)
                ->add('portals', EntityType::class, [
                    'class' => Portal::class,
                    'choice_label' => 'title',
                    'multiple' => true,
                ])
                ->add('isDraft', CheckboxType::class, [
                    'label' => 'draft',
                    'required' => false,
                ])
                ->add('isPrivate', CheckboxType::class, [
                    'label' => 'private',
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
            ->add('author', null, [
                'field_type' => EntityType::class,
                'field_options' => [
                    'class' => User::class,
                    'choice_label' => 'username',
                ],
            ])
            ->add('portals', null, [
                'field_type' => EntityType::class,
                'field_options' => [
                    'class' => Portal::class,
                    'choice_label' => 'title',
                ],
            ])
            ->add('isDraft')
            ->add('isPrivate')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('title', null, [
                'template' => 'Admin/article/article_list_title.html.twig',
            ])
            ->add('keywords')
            ->add('createdAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('updatedAt', null, [
                'format' => 'd/m/Y à H:i',
            ])
            ->add('isDraft')
            ->add('isPrivate')
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'read' => ['template' => 'Admin/show.html.twig'],
                    'sections' => ['template' => 'Admin/article/article_edit_links.html.twig'],
                    'delete' => [],
                ],
            ])
        ;
    }

    public function preUpdate(object $article): void
    {
        $article->setUpdatedAt(new DateTime());
    }

    public function prePersist(object $article): void
    {
        $article->setCreatedAt(new DateTimeImmutable());
        $user = $this->token->getToken()->getUser();
        $article->setAuthor($user);
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('section', $this->getRouterIdParameter().'/section')
            ->add('gallery', $this->getRouterIdParameter().'/gallery');
    }
}
