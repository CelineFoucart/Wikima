<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\ArticleType;
use App\Entity\Data\SearchData;
use App\Entity\User;
use App\Service\DataFilterService;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Article::class);
        $this->paginatorService = $paginatorService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Article $entity, bool $flush = true): void
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
    public function remove(Article $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Returns a pagination of articles by portals.
     */
    public function findByPortals(array $portals, int $page, int $limit = 30, bool $hidePrivate = true, ?ArticleType $type = null): PaginationInterface
    {
        $query = $this->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC')
            ->leftJoin('a.portals', 'p')->addSelect('p')
            ->leftJoin('a.type', 't')->addSelect('t')
            ->andWhere('p.id IN (:portals)')
            ->andWhere('a.isDraft IS NULL OR a.isDraft = 0')
            ->setParameter('portals', $portals)
        ;
        $query->andWhere('(a.isArchived != :isArchived  OR a.isArchived IS NULL)')->setParameter('isArchived', true);

        if ($type) {
            $query->andWhere('t.slug = :type')->setParameter('type', $type->getSlug());
        }

        if ($hidePrivate) {
            $query->andWhere('a.isPrivate IS NULL OR a.isPrivate = 0');
        }

        return $this->paginatorService->setLimit($limit)->paginate($query, $page);
    }

    /**
     * Finds an article and its portals.
     */
    public function findBySlug(string $slug): ?Article
    {
        return $this->getDefaultQueryBuilder()
            ->andWhere('a.slug = :slug')
            ->setParameter('slug', $slug)
            ->addOrderBy('s.position')
            ->addOrderBy('s.id')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findById(int $id): ?Article
    {
        return $this->getDefaultQueryBuilder()
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->orderBy('s.position')
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByUser(User $user, int $page = 1, bool $hidePrivate = true, int $perPage = 12): PaginationInterface
    {
        $builder = $this->getDefaultQueryBuilder()
            ->andWhere('a.author = :user')
            ->andWhere('a.isDraft IS NULL OR a.isDraft = 0')
            ->setParameter('user', $user)
        ;
        $builder->andWhere('(a.isArchived != :isArchived  OR a.isArchived IS NULL)')->setParameter('isArchived', true);

        if ($hidePrivate) {
            $builder->andWhere('a.isPrivate IS NULL OR a.isPrivate = 0');
        }

        return $this->paginatorService->setLimit($perPage)->paginate($builder, $page);
    }

    public function findAuthorDrafts(User $user, int $page, int $perPage): PaginationInterface
    {
        $builder = $this->getDefaultQueryBuilder()
            ->andWhere('a.author = :user')
            ->andWhere('a.isDraft = 1')
            ->setParameter('user', $user)
        ;
        $builder->andWhere('(a.isArchived != :isArchived  OR a.isArchived IS NULL)')->setParameter('isArchived', true);

        return $this->paginatorService->setLimit($perPage)->paginate($builder, $page);
    }

    public function search(SearchData $search, int $limit = 10, bool $hidePrivate = true): PaginationInterface
    {
        $builder = $this->getDefaultQueryBuilder();
        $builder->andWhere('(a.isArchived != :isArchived  OR a.isArchived IS NULL)')->setParameter('isArchived', true);

        if (strlen($search->getQuery()) >= 3 and null !== $search->getQuery()) {
            $builder
                ->andWhere('a.title LIKE :q')
                ->orWhere('a.description LIKE :q')
                ->orWhere('a.content LIKE :q')
                ->setParameter('q', '%'.$search->getQuery().'%')
            ;
        }

        if (!empty($search->getPortals())) {
            $builder
                ->andWhere('p.id IN (:portals)')
                ->setParameter('portals', $search->getPortals())
            ;
        }

        $builder->andWhere('a.isDraft IS NULL OR a.isDraft = 0');

        if ($hidePrivate) {
            $builder->andWhere('a.isPrivate IS NULL OR a.isPrivate = 0');
        }

        return $this->paginatorService->setLimit($limit)->paginate($builder, $search->getPage());
    }

    public function findByType(ArticleType $articleType, int $page = 1, bool $hidePrivate = true, int $limit = 10): PaginationInterface
    {
        $builder = $this->getDefaultQueryBuilder();
        $builder->andWhere('(a.isArchived != :isArchived  OR a.isArchived IS NULL)')->setParameter('isArchived', true);

        if ($hidePrivate) {
            $builder->andWhere('a.isPrivate IS NULL OR a.isPrivate = 0');
        }

        $builder->andWhere('a.type = :type')->setParameter('type', $articleType);

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);
    }

    private function getDefaultQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC')
            ->leftJoin('a.portals', 'p')->addSelect('p')
            ->leftJoin('a.author', 'u')->addSelect('u')
            ->leftJoin('a.sections', 's')->addSelect('s')
        ;
    }

    public function findSticky(?int $portalId = null): array
    {
        $builder = $this->createQueryBuilder('a')
            ->orderBy('a.title', 'ASC')
            ->andWhere('a.isSticky = 1 AND a.isSticky IS NOT NULL')
        ;
        $builder->andWhere('(a.isArchived != :isArchived  OR a.isArchived IS NULL)')->setParameter('isArchived', true);

        if ($portalId) {
            $builder
                ->leftJoin('a.portals', 'p')->addSelect('p')
                ->andWhere('p.id = :portalId')
                ->setParameter('portalId', $portalId)
            ;
        }

        return $builder->getQuery()->getResult();
    }

    public function findForAdminList(bool $isArchived = false): array
    {
        $builder = $this->getDefaultQueryBuilder()
            ->orderBy('a.id', 'ASC')
            ->andWhere('a.isArchived = :isArchived')
            ->setParameter('isArchived', $isArchived);

        if (!$isArchived) {
            $builder->orWhere('a.isArchived IS NULL');
        }

        return $builder->getQuery()->getResult();
    }

    public function searchPaginatedItems(array $parameters): array
    {
        $builder = $this->createQueryBuilder('a');
        $params = DataFilterService::formatParams($parameters, 'a');
        $builder->andWhere('(a.isArchived != :isArchived  OR a.isArchived IS NULL)')->setParameter('isArchived', true);

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('(a.title LIKE :search OR a.description LIKE :search OR a.keywords LIKE :search)')
                ->setParameter('search', '%'.$parameters['search']['value'].'%')
            ;
        }

        if ($params['limit'] > 0) {
            $builder->setMaxResults($params['limit'])->setFirstResult($params['start']);
        }

        return $builder ->orderBy($params['orderBy'], $params['direction'])->getQuery()->getResult();
    }

    public function countSearchTotal(array $parameters): array
    {
        $builder = $this->createQueryBuilder('a')->select('COUNT(DISTINCT a.id) AS recordsFiltered');
        $builder->andWhere('(a.isArchived != :isArchived  OR a.isArchived IS NULL)')->setParameter('isArchived', true);

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('a.title LIKE :search')
                ->orWhere('a.description LIKE :search')
                ->orWhere('a.keywords LIKE :search')
                ->setParameter('search', '%'.$parameters['search']['value'].'%');
        }

        return $builder->getQuery()->getOneOrNullResult();
    }

    public function getRandomArticle(): array
    {
        $sql = "SELECT a.id, a.title, a.slug, a.description, a.content 
            FROM article a
            ORDER BY RAND ( )  
            LIMIT 1";

        $connection = $this->getEntityManager()->getConnection();
        $stmt = $connection->prepare($sql);
        $result = $stmt->executeQuery();

        $article = $result->fetchAssociative();

        if (!$article) {
            return [];
        }

        return $article;
    }
}
