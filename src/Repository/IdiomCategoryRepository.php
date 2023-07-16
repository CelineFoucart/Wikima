<?php

namespace App\Repository;

use App\Entity\IdiomCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IdiomCategory>
 *
 * @method IdiomCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method IdiomCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method IdiomCategory[]    findAll()
 * @method IdiomCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdiomCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdiomCategory::class);
    }

//    /**
//     * @return IdiomCategory[] Returns an array of IdiomCategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IdiomCategory
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
