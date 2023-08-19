<?php

namespace App\Repository;

use App\Entity\ImageTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ImageTag>
 *
 * @method ImageTag|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageTag|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageTag[]    findAll()
 * @method ImageTag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageTagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageTag::class);
    }

    public function save(ImageTag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ImageTag $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
