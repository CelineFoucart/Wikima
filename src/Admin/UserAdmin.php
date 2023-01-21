<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\User;
use App\Service\UserService;
use DateTimeImmutable;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class UserAdmin extends AbstractAdmin
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserService $userService,
        private TokenStorageInterface $tokenStorage
    ) {
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $subject = $this->getSubject();

        if ($subject instanceof User && in_array('ROLE_SUPER_ADMIN', $subject->getRoles())) {
            $this->canHandleFounderUser($subject);
        }

        $form
            ->with('General', ['class' => 'col-md-8'])
                ->add('username', TextType::class)
                ->add('email', EmailType::class)
            ->end()
            ->with('Status', ['class' => 'col-md-4'])
                ->add('roles', ChoiceType::class, [
                    'choices' => $this->userService->getAvailableRoles(),
                    'multiple' => true,
                ])
                ->add('isVerified', BooleanType::class)
            ->end()
        ;

        if (null === $subject->getId()) {
            $form
                ->with('Password', ['class' => 'col-md-8'])
                ->add('plainPassword', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'attr' => ['autocomplete' => 'new-password'],
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Please enter a password',
                            ]),
                            new Length([
                                'min' => 6,
                                'max' => 4096,
                            ]),
                        ],
                        'label' => 'New password',
                    ],
                    'second_options' => [
                        'attr' => ['autocomplete' => 'new-password'],
                        'label' => 'Repeat Password',
                    ],
                    'invalid_message' => 'The password fields must match.',
                    'mapped' => false,
                ])
                ->end()
            ;
        }

        $form->remove('_delete');
    }

    protected function configureDatagridFilters(DatagridMapper $datagrid): void
    {
        $datagrid
            ->add('username')
            ->add('email')
            ->add('isVerified')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('username', null, [
                'editable' => true,
            ])
            ->add('email', FieldDescriptionInterface::TYPE_EMAIL, [
                'editable' => true,
            ])
            ->add('isVerified', null, [
                'editable' => true,
            ])
            ->add('roles', FieldDescriptionInterface::TYPE_CHOICE, [
                'choices' => [
                    'ROLE_USER' => 'Membre',
                    'ROLE_EDITOR' => 'Contributeur',
                    'ROLE_ADMIN' => 'Administrateur',
                    'ROLE_SUPER_ADMIN' => 'Administrateur Fondateur',
                ],
                'multiple' => true,
                'editable' => true,
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => ['template' => 'Admin/user_list_edit.html.twig'],
                    'delete' => ['template' => 'Admin/user_list_delete.html.twig'],
                ],
            ])
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('Informations', ['class' => 'col-md-8'])
                ->add('username')
                ->add('email')
                ->add('createdAt', null, [
                    'format' => 'd/m/Y à H:i',
                ])
            ->end()
            ->with('Status', ['class' => 'col-md-4'])
                ->add('roles', FieldDescriptionInterface::TYPE_CHOICE, [
                    'choices' => [
                        'ROLE_USER' => 'Membre',
                        'ROLE_EDITOR' => 'Contributeur',
                        'ROLE_ADMIN' => 'Administrateur',
                        'ROLE_SUPER_ADMIN' => 'Administrateur Fondateur',
                    ],
                    'multiple' => true,
                ])
                ->add('isVerified', FieldDescriptionInterface::TYPE_BOOLEAN)
            ->end()
        ;
    }

    public function prePersist(object $user): void
    {
        $this->canHandleFounderUser($user);

        $plainPassword = $this->getForm()->get('plainPassword')->getData();
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );
        $user->setCreatedAt(new DateTimeImmutable());
    }

    public function preUpdate(object $user): void
    {
        $this->canHandleFounderUser($user);
    }

    public function preRemove(object $object): void
    {
        if (in_array('ROLE_ADMIN', $object->getRoles()) || in_array('ROLE_SUPER_ADMIN', $object->getRoles())) {
            throw new AccessDeniedException('Les administrateurs et les fondateurs ne peuvent être supprimés.');
        }
    }

    private function canHandleFounderUser(User $user): void
    {
        $currentUser = $this->tokenStorage->getToken()->getUser();

        if (!in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return;
        }
        if ($currentUser instanceof User && !in_array('ROLE_SUPER_ADMIN', $currentUser->getRoles())) {
            throw new AccessDeniedException('You cannot edit or create a Founder Administrator.');
        }
    }
}
