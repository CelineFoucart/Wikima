<?php

namespace App\Repository;

use App\Entity\MenuItem;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<MenuItem>
 *
 * @method MenuItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuItem[]    findAll()
 * @method MenuItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuItem::class);
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
