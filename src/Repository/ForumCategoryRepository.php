<?php

namespace App\Repository;

use App\Entity\ForumCategory;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ForumCategory $entity, bool $flush = true): void
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
    public function remove(ForumCategory $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findMaxPosition(): int
    {
        try {
            $position = $this->createQueryBuilder('c')->select('MAX(c.position)')->getQuery()->getSingleScalarResult();
            
            return $position === null ? 0 : $position;
        } catch (NoResultException $th) {
            return 0;
        }
    }

    /**
    * @return ForumCategory[] Returns an array of ForumCategory objects
    */
    public function findByOrder(array $groups = []): array
    {
        $builder = $this->createQueryBuilder('c')
            ->leftJoin('c.forums', 'f')->addSelect('f')
            ->addOrderBy('c.position', 'ASC')
            ->addOrderBy('f.position', 'ASC');

        if (!empty($groups)) {
            $builder
                ->leftJoin('c.groupAccess', 'cg')
                ->andWhere('cg IN (:groups)')
                ->leftJoin('f.groupAccess', 'fg')
                ->andWhere('fg IN (:groups)')
                ->setParameter('groups', $groups)
            ;
        }
        
        return $builder->getQuery()->getResult();
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
