<?php

namespace App\Form\Admin;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserFormType extends AbstractType
{
    public function __construct(
        private UserService $userService,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $id = $options['data']->getId();

        $builder
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                'choices' => $this->userService->getAvailableRoles(),
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices'
                ]
            ])
            ->add('isVerified')
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
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
                'required' => (null === $id) ? true : false,
                'invalid_message' => 'Le mot de passe de confirmation ne concorde pas.',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
