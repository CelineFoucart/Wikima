<?php

namespace App\Repository;

use App\Entity\TemplateGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TemplateGroup>
 *
 * @method TemplateGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method TemplateGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method TemplateGroup[]    findAll()
 * @method TemplateGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TemplateGroup::class);
    }

//    /**
//     * @return TemplateGroup[] Returns an array of TemplateGroup objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TemplateGroup
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
