<?php

namespace App\Repository;

use App\Entity\Map;
use App\Entity\Data\SearchData;
use App\Service\PaginatorService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Map>
 *
 * @method Map|null find($id, $lockMode = null, $lockVersion = null)
 * @method Map|null findOneBy(array $criteria, array $orderBy = null)
 * @method Map[]    findAll()
 * @method Map[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorService $paginatorService)
    {
        parent::__construct($registry, Map::class);
    }

    public function save(Map $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Map $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    
    public function searchPaginated(SearchData $search, int $limit = 20): PaginationInterface
    {
        $builder = $this->createQueryBuilder('m')
            ->leftJoin('m.portals', 'p')
            ->leftJoin('m.categories', 'c');

        if (strlen($search->getQuery()) >= 3 and null !== $search->getQuery()) {
            $builder ->andWhere('m.title LIKE :q')->setParameter('q', '%'.$search->getQuery().'%');
        }

        if (!empty($search->getPortals())) {
            $builder
                ->andWhere('p.id IN (:portals)')
                ->setParameter('portals', $search->getPortals())
            ;
        }

        if (!empty($search->getCategories())) {
            $builder
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->getCategories())
            ;
        }
        
        return $this->paginatorService->setLimit($limit)->paginate($builder, $search->getPage());
    }
}
