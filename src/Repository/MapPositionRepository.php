<?php

namespace App\Repository;

use App\Entity\MapPosition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MapPosition>
 *
 * @method MapPosition|null find($id, $lockMode = null, $lockVersion = null)
 * @method MapPosition|null findOneBy(array $criteria, array $orderBy = null)
 * @method MapPosition[]    findAll()
 * @method MapPosition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapPositionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MapPosition::class);
    }

//    /**
//     * @return MapPosition[] Returns an array of MapPosition objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MapPosition
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
