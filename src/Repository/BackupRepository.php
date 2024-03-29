<?php

namespace App\Repository;

use App\Entity\Backup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Backup|null find($id, $lockMode = null, $lockVersion = null)
 * @method Backup|null findOneBy(array $criteria, array $orderBy = null)
 * @method Backup[]    findAll()
 * @method Backup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BackupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Backup::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Backup $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Backup $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Backup[] Returns an array of Backup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findLastBackup(): ?Backup
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
