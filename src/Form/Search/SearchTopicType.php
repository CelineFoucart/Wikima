<?php

declare(strict_types=1);

namespace App\Form\Search;

use App\Entity\Forum;
use App\Entity\ForumCategory;
use App\Entity\Data\SearchForumData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

final class SearchTopicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userRoles = $options['user_roles'];

        $builder
            ->add('query', TextType::class, [
                'label' => 'Mot clé',
                'attr' => [
                    'placeholder' => 'Search...',
                ]
            ])
            ->add('categories', EntityType::class, [
                'required' => false,
                'class' => ForumCategory::class,
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'query_builder' => function (EntityRepository $er) use ($userRoles) {
                    return $er->createQueryBuilder('c')
                        ->leftJoin('c.groupAccess', 'cg')
                        ->andWhere('cg IN (:groups)')
                        ->setParameter('groups', $userRoles);
                }
            ])
            ->add('forums', EntityType::class, [
                'required' => false,
                'class' => Forum::class,
                'multiple' => true,
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'query_builder' => function (EntityRepository $er) use ($userRoles) {
                    return $er->createQueryBuilder('f')
                        ->leftJoin('f.groupAccess', 'fg')
                        ->leftJoin('f.category', 'c')
                        ->leftJoin('c.groupAccess', 'cg')
                        ->andWhere('fg IN (:groups)')
                        ->andWhere('cg IN (:groups)')
                        ->setParameter('groups', $userRoles);
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchForumData::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'user_roles' => [],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
