<?php

namespace App\Repository;

use App\Entity\ScenarioCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScenarioCategory>
 *
 * @method ScenarioCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ScenarioCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ScenarioCategory[]    findAll()
 * @method ScenarioCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScenarioCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ScenarioCategory::class);
    }

//    /**
//     * @return ScenarioCategory[] Returns an array of ScenarioCategory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ScenarioCategory
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
