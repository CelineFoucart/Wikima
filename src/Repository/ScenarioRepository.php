<?php

namespace App\Repository;

use App\Entity\Data\SearchData;
use App\Entity\Scenario;
use App\Service\PaginatorService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Scenario>
 *
 * @method Scenario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Scenario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Scenario[]    findAll()
 * @method Scenario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScenarioRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Scenario::class);
        $this->paginatorService = $paginatorService;
    }

    public function findPaginatedByName(SearchData $searchData, int $limit = 30, bool $withPrivate = false): PaginationInterface
    {
        $query = $this->createQueryBuilder('s')->leftJoin('s.portals', 'p')->addSelect('p')->orderBy('s.title', 'ASC');

        if (!empty($searchData->getPortals())) {
            $query->andWhere('p.id IN (:portals)')->setParameter('portals', $searchData->getPortals());
        }

        if (strlen((string) $searchData->getQuery()) >= 3 and null !== $searchData->getQuery()) {
            $query->andWhere('s.title LIKE :q') ->setParameter('q', '%'.$searchData->getQuery().'%');
        }

        if ($withPrivate === false) {
            $query->andWhere('s.public = 1');
        }

        $query->andWhere('(s.archived != :isArchived OR s.archived IS NULL)')->setParameter('isArchived', true);

        return $this->paginatorService->setLimit($limit)->paginate($query, $searchData->getPage());
    }

    public function findById(int $id): ?Scenario
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.episodes', 'e')->addSelect('e')
            ->leftJoin('s.persons', 'p')->addSelect('p')
            ->leftJoin('e.persons', 'pe')->addSelect('pe')
            ->leftJoin('s.places', 'pl')->addSelect('pl')
            ->leftJoin('e.places', 'ple')->addSelect('ple')
            ->orderBy('e.position', 'ASC')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findBySlug(string $slug): ?Scenario
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.episodes', 'e')->addSelect('e')
            ->leftJoin('s.persons', 'p')->addSelect('p')
            ->leftJoin('e.persons', 'pe')->addSelect('pe')
            ->leftJoin('s.places', 'pl')->addSelect('pl')
            ->leftJoin('e.places', 'ple')->addSelect('ple')
            ->leftJoin('s.timelines', 't')->addSelect('t')
            ->leftJoin('s.categories', 'c')->addSelect('c')
            ->leftJoin('s.portals', 'pt')->addSelect('pt')
            ->orderBy('e.position', 'ASC')
            ->andWhere('s.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Find a person by parent.
     *
     * @param int[] $parents
     *
     * @return PaginationInterface
     */
    public function findByParent(array $parents, int $page = 1, int $limit = 23): PaginationInterface
    {
        $builder = $this->createQueryBuilder('s')
            ->leftJoin('s.portals', 'pt')
            ->orderBy('s.title', 'ASC')
            ->andWhere('(s.archived != :isArchived OR s.archived IS NULL)')
            ->setParameter('isArchived', true)
            ->andWhere('pt IN (:parents)')
            ->setParameter('parents', $parents);

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);
    }

    public function findForAdminList(bool $isArchived = false): array
    {
        $builder = $this->createQueryBuilder('s')
            ->andWhere('s.archived = :isArchived')
            ->setParameter('isArchived', $isArchived);
        
        if (!$isArchived) {
            $builder->orWhere('s.archived IS NULL');
        }

        return $builder->getQuery()->getResult();
    }
}
