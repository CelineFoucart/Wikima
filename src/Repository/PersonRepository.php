<?php

namespace App\Repository;

use App\Entity\Data\SearchData;
use App\Entity\Person;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

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
    public function findAllPaginated(int $page = 1, int $limit = 10): PaginationInterface
    {
        $query = $this->getDefaultQuery();

        return $this->paginatorService->setLimit($limit)->paginate($query, $page);
    }

    /**
     * Undocumented function.
     *
     * @param Category|Portal $parent
     *
     * @return void
     */
    public function findByParent(string $parentType = 'category', $parent, int $page): PaginationInterface
    {
        $builder = $this->getDefaultQuery();
        $where = ('category' === $parentType) ? 'c.id IN (:parents)' : 'pt.id IN (:parents)';
        $builder->andWhere($where)->setParameter('parents', [$parent->getId()]);

        return $this->paginatorService->paginate($builder, $page);
    }

    public function search(SearchData $search): PaginationInterface
    {
        $builder = $this->getDefaultQuery();

        if (strlen($search->getQuery()) >= 3 and null !== $search->getQuery()) {
            $q = '%'.$search->getQuery().'%';
            $builder
                ->andWhere('p.firstname LIKE :q_1')
                ->andWhere('p.lastname LIKE :q_2')
                ->orWhere('p.description LIKE :q_3')
                ->orWhere('p.presentation LIKE :q_4')
                ->setParameters(['q_1' => $q, 'q_2' => $q, 'q_3' => $q, 'q_4' => $q])
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

        return $this->paginatorService->paginate($builder, $search->getPage());
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

    private function getDefaultQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')
            ->addSelect('c')
            ->leftJoin('p.portals', 'pt')
            ->addSelect('pt')
            ->orderBy('p.firstname', 'ASC')
        ;
    }
}
