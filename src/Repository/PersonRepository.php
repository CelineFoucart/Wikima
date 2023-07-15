<?php

namespace App\Repository;

use App\Entity\Person;
use App\Entity\Portal;
use App\Entity\Category;
use App\Entity\PersonType;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Data\SearchData;
use App\Service\PaginatorService;
use App\Service\DataFilterService;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Person|null find($id, $lockMode = null, $lockVersion = null)
 * @method Person|null findOneBy(array $criteria, array $orderBy = null)
 * @method Person[]    findAll()
 * @method Person[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Person::class);
        $this->paginatorService = $paginatorService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Person $entity, bool $flush = true): void
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
    public function remove(Person $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Returns a pagination of persons.
     */
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
    public function findByParent($parent, string $parentType = 'category', int $page = 1, int $type = 0, int $limit = 22): PaginationInterface
    {
        $builder = $this->getDefaultQuery();
        $where = ('category' === $parentType) ? 'c.id IN (:parents)' : 'pt.id IN (:parents)';
        $builder->andWhere($where)->setParameter('parents', [$parent->getId()])
        ->andWhere('(p.isArchived != :isArchived  OR p.isArchived IS NULL)')
        ->setParameter('isArchived', true);

        if ($type  > 0) {
            $builder->andWhere('t.id IN (:type)')->setParameter('type', [$type]);
        }

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);
    }

    public function findByType(PersonType $personType, int $page = 1, int $limit = 20): PaginationInterface
    {
        $builder = $this->getDefaultQuery();
        $builder->andWhere('t.id IN (:type)')
            ->setParameter('type', [$personType->getId()])
            ->andWhere('(p.isArchived != :isArchived  OR p.isArchived IS NULL)')
            ->setParameter('isArchived', true);

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);
    }

    public function search(SearchData $search, int $limit = 20): PaginationInterface
    {
        $builder = $this->getDefaultQuery();
        $builder->andWhere('(p.isArchived != :isArchived  OR p.isArchived IS NULL)')->setParameter('isArchived', true);

        if (strlen($search->getQuery()) >= 3 and null !== $search->getQuery()) {
            $q = '%'.$search->getQuery().'%';
            $builder
                ->andWhere('p.firstname LIKE :q')
                ->orWhere('p.lastname LIKE :q')
                ->orWhere('p.description LIKE :q')
                ->orWhere('p.presentation LIKE :q')
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

    public function findBySlug(string $slug): ?Person
    {
        return $this->getDefaultQuery()
            ->andWhere('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findById(int $id): ?Person
    {
        return $this->getDefaultQuery()
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findSticky(?int $portalId = null, ?int $categoryId = null): array
    {
        $builder = $this->createQueryBuilder('p')
            ->orderBy('p.firstname', 'ASC')
            ->andWhere('p.isSticky = 1 AND p.isSticky IS NOT NULL')
            ->andWhere('(p.isArchived != :isArchived  OR p.isArchived IS NULL)')->setParameter('isArchived', true);
        ;

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
            ->andWhere('p.isArchived = :isArchived')
            ->setParameter('isArchived', $isArchived);
        
        if (!$isArchived) {
            $builder->orWhere('p.isArchived IS NULL');
        }

        return $builder->getQuery()->getResult();
    }

    public function searchPaginatedItems(array $parameters): array
    {
        $builder = $this->createQueryBuilder('p');
        $builder->andWhere('(p.isArchived != :isArchived  OR p.isArchived IS NULL)')->setParameter('isArchived', true);
        $params = DataFilterService::formatParams($parameters, 'p');

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('p.firstname LIKE :search')
                ->orWhere('p.lastname LIKE :search')
                ->orWhere('p.nationality LIKE :search')
                ->orWhere('p.birthday LIKE :search')
                ->orWhere('p.deathDate LIKE :search')
                ->setParameter('search', '%' . $parameters['search']['value'] . '%');
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
        $builder = $this->createQueryBuilder('p')->select('COUNT(p.id) AS recordsFiltered');
        $builder->andWhere('(p.isArchived != :isArchived  OR p.isArchived IS NULL)')->setParameter('isArchived', true);

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('p.firstname LIKE :search')
                ->orWhere('p.lastname LIKE :search')
                ->orWhere('p.nationality LIKE :search')
                ->orWhere('p.birthday LIKE :search')
                ->orWhere('p.deathDate LIKE :search')
                ->setParameter('search', '%' . $parameters['search']['value'] . '%');
        }

        return $builder->getQuery()->getOneOrNullResult();
    }

    private function getDefaultQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')
            ->addSelect('c')
            ->leftJoin('p.type', 't')
            ->addSelect('t')
            ->leftJoin('p.image', 'i')
            ->addSelect('i')
            ->leftJoin('p.portals', 'pt')
            ->addSelect('pt')
            ->orderBy('p.firstname', 'ASC')
        ;
    }
}
