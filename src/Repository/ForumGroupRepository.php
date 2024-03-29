<?php

namespace App\Repository;

use App\Entity\ForumGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ForumGroup>
 *
 * @method ForumGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method ForumGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method ForumGroup[]    findAll()
 * @method ForumGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ForumGroup::class);
    }
}
