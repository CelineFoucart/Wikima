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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(IdiomArticle $entity, bool $flush = true): void
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
    public function remove(IdiomArticle $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findOneBySlug(string $slug): ?IdiomArticle
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
