<?php

namespace App\Repository;

use App\Entity\Data\SearchData;
use App\Entity\Portal;
use App\Entity\Timeline;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Timeline|null find($id, $lockMode = null, $lockVersion = null)
 * @method Timeline|null findOneBy(array $criteria, array $orderBy = null)
 * @method Timeline[]    findAll()
 * @method Timeline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimelineRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Timeline::class);
        $this->paginatorService = $paginatorService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Timeline $entity, bool $flush = true): void
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
    public function remove(Timeline $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Returns a pagination of Timeline.
     */
    public function findPaginated(int $page = 1, int $limit = 10): PaginationInterface
    {
        $query = $this->createQueryBuilder('t')->orderBy('t.title', 'ASC');

        return $this->paginatorService->setLimit($limit)->paginate($query, $page);
    }

    public function search(SearchData $search, int $limit): PaginationInterface
    {
        $builder = $this->createQueryBuilder('t')
            ->orderBy('t.title', 'ASC')
            ->leftJoin('t.portals', 'p')->addSelect('p')
            ->leftJoin('t.categories', 'c')->addSelect('c')
        ;

        if (strlen($search->getQuery()) >= 3 and null !== $search->getQuery()) {
            $builder
                ->andWhere('t.title LIKE :q_1')
                ->setParameter('q_1', '%'.$search->getQuery().'%')
                ->orWhere('t.description LIKE :q_2')
                ->setParameter('q_2', '%'.$search->getQuery().'%')
            ;
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

    public function findTimelineEventsBySlug(string $slug): ?Timeline
    {
        return $this->getDefaultQuery()
            ->andWhere('t.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findTimelineEventsById(int $id): ?Timeline
    {
        return $this->getDefaultQuery()
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    private function getDefaultQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.categories', 'c')->addSelect('c')
            ->leftJoin('t.portals', 'p')->addSelect('p')
            ->leftJoin('t.events', 'e')->addSelect('e')
            ->orderBy('e.timelineOrder')
        ;
    }
}
