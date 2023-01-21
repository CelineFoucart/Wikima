<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Category;
use App\Entity\Person;
use App\Entity\Portal;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\String\Slugger\SluggerInterface;

final class PersonAdmin extends AbstractAdmin
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('firstname')
            ->add('lastname')
            ->add('fullname')
            ->add('slug')
            ->add('nationality')
            ->add('job')
            ->add('birthday')
            ->add('birthdayPlace')
            ->add('deathDate')
            ->add('deathPlace')
            ->add('parents')
            ->add('description')
            ->add('presentation')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('firstname')
            ->add('lastname')
            ->add('nationality')
            ->add('birthday')
            ->add('deathDate')
            ->add('description')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'read' => ['template' => 'Admin/show.html.twig'],
                    'show' => [],
                    'image' => ['template' => 'Admin/person/image_action.html.twig'],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->tab('Generality')
                ->with('Name', ['class' => 'col-md-6'])
                    ->add('firstname', TextType::class, [
                        'attr' => [
                            'data-type' => 'firstname',
                        ],
                    ])
                    ->add('lastname', TextType::class, [
                        'attr' => [
                            'data-type' => 'lastname',
                        ],
                    ])
                    ->add('fullname', TextType::class, [
                        'attr' => [
                            'data-type' => 'fullname',
                        ],
                    ])
                    ->add('slug', TextType::class, [
                        'attr' => [
                            'data-target' => 'slug-character',
                        ],
                    ])
                ->end()
                ->with('Informations', ['class' => 'col-md-6'])
                    ->add('parents')
                    ->add('nationality')
                    ->add('job')
                ->end()
            ->end()

            ->tab('Biography')
                ->with('Full biography', ['class' => 'col-md-8'])
                    ->add('presentation', CKEditorType::class, [
                        'config' => ['toolbar' => 'full'],
                    ])
                    ->add('biography', CKEditorType::class, [
                        'config' => ['toolbar' => 'full'],
                        'required' => false,
                    ])
                ->end()
                ->with('In short', ['class' => 'col-md-4'])
                    ->add('birthday')
                    ->add('birthdayPlace')
                    ->add('deathDate')
                    ->add('deathPlace')
                ->end()
            ->end()

            ->tab('Personality')
                ->with('Personality', ['class' => 'hidden-header'])
                    ->add('personality', CKEditorType::class, [
                        'config' => ['toolbar' => 'full'],
                        'required' => false,
                    ])
                ->end()
            ->end()
            ->tab('Meta Data')
                ->with('Meta Data', ['class' => 'hidden-header'])
                    ->add('description', TextareaType::class, [
                        'required' => false,
                        'attr' => [
                            'style' => 'height:115px',
                        ],
                    ])
                    ->add('portals', EntityType::class, [
                        'class' => Portal::class,
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
                ->end()
            ->end()
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('In Short', ['class' => 'col-md-12'])
                ->add('id')
                ->add('firstname')
                ->add('lastname')
                ->add('fullname')
                ->add('slug')
                ->add('nationality')
                ->add('job')
                ->add('parents')
                ->add('birthday')
                ->add('birthdayPlace')
                ->add('deathDate')
                ->add('deathPlace')
                ->add('description')
                ->add('image', null, ['template' => 'Admin/person/image_show.html.twig'])
            ->end()
            ->with('Full Presentation', ['class' => 'col-md-12'])
                ->add('presentation', null, [
                    'safe' => true,
                ])
                ->add('biography', null, [
                    'safe' => true,
                ])
                ->add('personality', null, [
                    'safe' => true,
                ])
            ->end()
        ;
    }

    public function preUpdate(object $person): void
    {
        $person->setSlug($this->generateSlug($person));
    }

    public function prePersist(object $person): void
    {
        $person->setSlug($this->generateSlug($person));
    }

    private function generateSlug(Person $person): string
    {
        $firstname = $person->getFirstname();
        $lastname = $person->getLastname() ? ' '.$person->getLastname() : '';

        return (string) $this->slugger->slug(strtolower($firstname.$lastname));
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('image', $this->getRouterIdParameter().'/image');
    }
}
