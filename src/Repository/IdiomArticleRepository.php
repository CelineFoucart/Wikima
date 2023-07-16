<?php

namespace App\Repository;

use App\Entity\IdiomArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IdiomArticle>
 *
 * @method IdiomArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method IdiomArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method IdiomArticle[]    findAll()
 * @method IdiomArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdiomArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdiomArticle::class);
    }

//    /**
//     * @return IdiomArticle[] Returns an array of IdiomArticle objects
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

//    public function findOneBySomeField($value): ?IdiomArticle
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
