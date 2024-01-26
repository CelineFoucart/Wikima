<?php

namespace App\Repository;

use App\Entity\Section;
use Doctrine\ORM\ORMException;
use App\Service\DataFilterService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Section|null find($id, $lockMode = null, $lockVersion = null)
 * @method Section|null findOneBy(array $criteria, array $orderBy = null)
 * @method Section[]    findAll()
 * @method Section[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Section::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Section $entity, bool $flush = true): void
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
    public function remove(Section $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findOneByArticle(int $id, int $article): ?Section
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.article', 'a')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->andWhere('a.id = :article')
            ->setParameter('article', $article)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countSearchTotal(array $parameters): array
    {
        $builder = $this->createQueryBuilder('s')->leftJoin('s.article', 'a')->select('COUNT(s.id) AS recordsFiltered');

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('s.title LIKE :search')
                ->orWhere('a.title LIKE :search')
                ->orWhere('a.keywords LIKE :search')
                ->setParameter('search', '%' . $parameters['search']['value'] . '%');
        }

        return $builder->getQuery()->getOneOrNullResult();
    }

    public function searchPaginatedItems(array $parameters): array
    {
        $builder = $this->createQueryBuilder('s')->leftJoin('s.article', 'a');
        
        $params = DataFilterService::formatParams($parameters, 'p');

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('s.title LIKE :search')
                ->orWhere('a.title LIKE :search')
                ->orWhere('a.keywords LIKE :search')
                ->setParameter('search', '%' . $parameters['search']['value'] . '%');
        }

        if ($params['limit'] > 0) {
            $builder->setMaxResults($params['limit'])->setFirstResult($params['start']);
        }

        return $builder->orderBy($params['orderBy'], $params['direction'])->getQuery()->getResult();
    }
}
