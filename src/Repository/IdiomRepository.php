<?php

namespace App\Repository;

use App\Entity\Idiom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Idiom>
 *
 * @method Idiom|null find($id, $lockMode = null, $lockVersion = null)
 * @method Idiom|null findOneBy(array $criteria, array $orderBy = null)
 * @method Idiom[]    findAll()
 * @method Idiom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdiomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Idiom::class);
    }

//    /**
//     * @return Idiom[] Returns an array of Idiom objects
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

//    public function findOneBySomeField($value): ?Idiom
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
