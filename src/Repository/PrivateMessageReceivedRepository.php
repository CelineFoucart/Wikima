<?php

namespace App\Repository;

use App\Entity\PrivateMessageReceived;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrivateMessageReceived>
 *
 * @method PrivateMessageReceived|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrivateMessageReceived|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrivateMessageReceived[]    findAll()
 * @method PrivateMessageReceived[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrivateMessageReceivedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrivateMessageReceived::class);
    }

//    /**
//     * @return PrivateMessageReceived[] Returns an array of PrivateMessageReceived objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function countNotRead(): int
    {
        try {
            return $this->createQueryBuilder('p')
                ->select('COUNT(p.id)')
                ->andWhere('(p.readStatus = :readStatus OR p.readStatus IS NULL)')
                ->setParameter('readStatus', false)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (\Exception $th) {
            return 0;
        }
    }

//    public function findOneBySomeField($value): ?PrivateMessageReceived
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
