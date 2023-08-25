<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Portal;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Portal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Portal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Portal[]    findAll()
 * @method Portal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortalRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Portal::class);
        $this->paginatorService = $paginatorService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Portal $entity, bool $flush = true): void
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
    public function remove(Portal $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    
    public function findPaginated(int $page, int $perPageOdd): PaginationInterface
    {
        $builder =  $this->createQueryBuilder('p')->orderBy('p.title', 'ASC');
        return $this->paginatorService->setLimit($perPageOdd)->paginate($builder, $page);
    }

    /**
     * Finds a portal and its categories.
     */
    public function findBySlug(string $slug): ?Portal
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')->addSelect('c')
            ->andWhere('p.slug = :slug')
            ->setParameters(['slug' => $slug])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findWithSticky(int $id): ?Portal
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.people', 'ps')->addSelect('ps')->andWhere('ps.isSticky = 1 AND ps.isSticky IS NOT NULL')
            ->leftJoin('p.articles', 'a')->addSelect('a')->andWhere('a.isSticky = 1 AND a.isSticky IS NOT NULL')
            ->leftJoin('p.places', 'pl')->addSelect('pl')->andWhere('pl.isSticky = 1 AND pl.isSticky IS NOT NULL')
            ->andWhere('p.id = :id')
            ->setParameters(['id' => $id])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByCategory(Category $category): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')->addSelect('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $category->getId())
            ->addOrderBy('p.position')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllWithCategory(): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.categories', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult()
        ;
    }

    public function getPortalStats(): array
    {
        return $this->createQueryBuilder('p')
            ->select('COUNT(DISTINCT a.id) AS articles, COUNT(DISTINCT pe.id) AS persons, COUNT(DISTINCT pl.id) AS places, p.title AS title')
            ->leftJoin('p.articles', 'a')
            ->leftJoin('p.people', 'pe')
            ->leftJoin('p.places', 'pl')
            ->groupBy('title')
            ->getQuery()
            ->getResult()
        ;
    }
}
