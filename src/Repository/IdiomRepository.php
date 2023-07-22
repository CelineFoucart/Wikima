<?php

namespace App\Repository;

use App\Entity\Idiom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Idiom>
 *
 * @method Idiom|null find($id, $lockMode = null, $lockVersion = null)
 * @method Idiom|null findOneBy(array $criteria, array $orderBy = null)
 * @method Idiom[]    findAll()
 * @method Idiom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdiomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Idiom::class);
    }

    public function findIdiomBySlug(string $slug): ?Idiom
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.idiomArticles', 'ia')->addSelect('ia')
            ->leftJoin('ia.category', 'c')->addSelect('c')
            ->leftJoin('i.portals', 'p')->addSelect('p')
            ->orderBy('c.position')
            ->andWhere('i.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
