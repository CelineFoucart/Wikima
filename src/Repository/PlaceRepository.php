<?php

namespace App\Repository;

use App\Entity\Place;
use App\Entity\Portal;
use App\Entity\Category;
use App\Entity\PlaceType;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Data\SearchData;
use App\Service\PaginatorService;
use App\Service\DataFilterService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Place>
 *
 * @method Place|null find($id, $lockMode = null, $lockVersion = null)
 * @method Place|null findOneBy(array $criteria, array $orderBy = null)
 * @method Place[]    findAll()
 * @method Place[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaceRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;
    
    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Place::class);
        $this->paginatorService = $paginatorService;
    }

    public function save(Place $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Place $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByType(PlaceType $placeType, int $page = 1, int $limit = 20): PaginationInterface
    {
        $builder = $this->getDefaultQuery();
        $builder->andWhere('t.id IN (:type)')->setParameter('type', [$placeType->getId()]);
        $builder->andWhere('(pl.isArchived != :isArchived  OR pl.isArchived IS NULL)')->setParameter('isArchived', true);

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);
    }

    public function findAllPaginated(int $page = 1, int $limit = 20): PaginationInterface
    {
        $query = $this->getDefaultQuery();

        return $this->paginatorService->setLimit($limit)->paginate($query, $page);
    }

    /**
     * Find a person by parent.
     *
     * @param Category|Portal $parent
     *
     * @return PaginationInterface
     */
    public function findByParent($parent, string $parentType = 'category', int $page = 1, int $type = 0, int $limit = 23): PaginationInterface
    {
        $builder = $this->getDefaultQuery();
        $where = ('category' === $parentType) ? 'c.id IN (:parents)' : 'pt.id IN (:parents)';
        $builder->andWhere($where)->setParameter('parents', [$parent->getId()]);
        $builder->andWhere('(pl.isArchived != :isArchived  OR pl.isArchived IS NULL)')->setParameter('isArchived', true);

        if ($type  > 0) {
            $builder->andWhere('t.id IN (:type)')->setParameter('type', [$type]);
        }

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);
    }

    public function search(SearchData $search, int $limit = 20): PaginationInterface
    {
        $builder = $this->getDefaultQuery();
        
        $builder->andWhere('(pl.isArchived != :isArchived  OR pl.isArchived IS NULL)')->setParameter('isArchived', true);

        if (strlen($search->getQuery()) >= 3 and null !== $search->getQuery()) {
            $q = '%'.$search->getQuery().'%';
            $builder
                ->andWhere('pl.title LIKE :q')
                ->orWhere('pl.description LIKE :q')
                ->setParameter('q', $q)
            ;
        }

        if (!empty($search->getPortals())) {
            $builder
                ->andWhere('pt.id IN (:portals)')
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

    public function findBySlug(string $slug): ?Place
    {
        return $this->getDefaultQuery()
            ->leftJoin('pl.places', 'apl')->addSelect('apl')
            ->leftJoin('apl.types', 'aplt')->addSelect('aplt')
            ->andWhere('pl.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findSticky(?int $portalId = null, ?int $categoryId = null): array
    {
        $builder = $this->createQueryBuilder('p')
            ->orderBy('p.title', 'ASC')
            ->andWhere('p.isSticky = 1 AND p.isSticky IS NOT NULL')
        ;
        
        $builder->andWhere('(p.isArchived != :isArchived  OR p.isArchived IS NULL)')->setParameter('isArchived', true);

        if ($portalId) {
            $builder
                ->leftJoin('p.portals', 'pt')->addSelect('pt')
                ->andWhere('pt.id = :portalId')
                ->setParameter('portalId', $portalId)
            ;
        }

        if ($categoryId) {
            $builder
                ->leftJoin('p.categories', 'c')->addSelect('c')
                ->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId)
            ;
        }

        return $builder->getQuery()->getResult();
    }

    public function findForAdminList(bool $isArchived = false): array
    {
        $builder = $this->getDefaultQuery()
            ->andWhere('pl.isArchived = :isArchived')
            ->setParameter('isArchived', $isArchived);
        
        if (!$isArchived) {
            $builder->orWhere('pl.isArchived IS NULL');
        }

        return $builder->getQuery()->getResult();
    }

    public function searchPaginatedItems(array $parameters): array
    {
        $builder = $this->createQueryBuilder('p');
        $params = DataFilterService::formatParams($parameters, 'p');
        $builder->andWhere('(p.isArchived != :isArchived  OR p.isArchived IS NULL)')->setParameter('isArchived', true);
        
        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('p.title LIKE :search')
                ->orWhere('p.description LIKE :search')
                ->setParameter('search', '%' . $parameters['search']['value'] . '%' );
        }

        if ($params['limit'] > 0) {
            $builder->setMaxResults($params['limit'])->setFirstResult($params['start']);
        }

        return $builder
            ->andWhere('(p.isArchived != :isArchived  OR p.isArchived IS NULL)')->setParameter('isArchived', true)
            ->orderBy($params['orderBy'], $params['direction'])
            ->getQuery()
            ->getResult();
    }

    public function countSearchTotal(array $parameters): array
    {
        $builder = $this->createQueryBuilder('p')->select('COUNT(DISTINCT p.id) AS recordsFiltered');
        $builder->andWhere('(p.isArchived != :isArchived  OR p.isArchived IS NULL)')->setParameter('isArchived', true);

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('p.title LIKE :search')
                ->orWhere('p.description LIKE :search')
                ->setParameter('search', '%' . $parameters['search']['value'] . '%' );
        }

        return $builder->getQuery()->getOneOrNullResult();
    }

    private function getDefaultQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('pl')
            ->leftJoin('pl.categories', 'c')
            ->addSelect('c')
            ->leftJoin('pl.types', 't')
            ->addSelect('t')
            ->leftJoin('pl.illustration', 'i')
            ->addSelect('i')
            ->leftJoin('pl.portals', 'pt')
            ->addSelect('pt')
            ->orderBy('pl.title', 'ASC')
        ;
    }
}
