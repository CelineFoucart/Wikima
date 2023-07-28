<?php

namespace App\Repository;

use App\Entity\IdiomCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
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

    public function findMaxPosition(): int
    {
        try {
            $position = $this->createQueryBuilder('i')->select('MAX(i.position)')->getQuery()->getSingleScalarResult();
            
            return $position === null ? 0 : $position;
        } catch (NoResultException $th) {
            return 0;
        }
    }
}
