<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Forum;
use App\Entity\Topic;
use App\Service\ForumHelper;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TopicType extends AbstractType
{
    public function __construct(private Security $security, private ForumHelper $forumHelper)
    {
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groups = $this->forumHelper->getCurrentUserRoles($this->security->getToken()->getUser());
        
        $builder
            ->add('title')
            ->add('forum', EntityType::class, [
                'class' => Forum::class,
                'required' => true,
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'query_builder' => function (EntityRepository $er) use($groups) {
                    
                    return $er->createQueryBuilder('f')
                        ->leftJoin('f.groupAccess', 'fg')
                        ->andWhere('fg IN (:groups)')
                        ->setParameter('groups', $groups)
                        ->orderBy('f.title', 'ASC');
                }
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'required' => true,
                'attr' => [
                    'data-choices' => 'choices'
                ],
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u') ->orderBy('u.username', 'ASC');
                }
            ])
            ->add('locked')
            ->add('sticky')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Topic::class,
        ]);
    }
}
