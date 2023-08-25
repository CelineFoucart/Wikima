<?php

namespace App\Repository;

use App\Entity\ForumCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForumCategory>
 *
 * @method ForumCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForumCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForumCategory[]    findAll()
 * @method ForumCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumCategory::class);
    }

    /**
    * @return ForumCategory[] Returns an array of ForumCategory objects
    */
    public function findByOrder(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.forums', 'f')->addSelect('f')
            ->addOrderBy('c.position', 'ASC')
            ->addOrderBy('f.position', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findBySlug(string $slug): ?ForumCategory
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.forums', 'f')->addSelect('f')
            ->addOrderBy('c.position', 'ASC')
            ->addOrderBy('f.position', 'ASC')
            ->andWhere('c.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
