<?php

namespace App\Repository;

use App\Entity\Data\SearchForumData;
use App\Entity\Topic;
use App\Service\PaginatorService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Topic>
 *
 * @method Topic|null find($id, $lockMode = null, $lockVersion = null)
 * @method Topic|null findOneBy(array $criteria, array $orderBy = null)
 * @method Topic[]    findAll()
 * @method Topic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TopicRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Topic::class);
        $this->paginatorService = $paginatorService;
    }

    /**
     * Returns the paginated result of Topic.
     */
    public function findPaginated(int $forumId, int $page, int $limit = 30): PaginationInterface
    {
        $builder = $this->createQueryBuilder('t')
            ->leftJoin('t.author', 'u')->addSelect('u')
            ->leftJoin('u.userGroups', 'g')->addSelect('g')
            ->leftJoin('t.forum', 'f')
            ->andWhere('f.id = :id')
            ->andWhere('t.sticky != :sticky')
            ->setParameter('id', $forumId)
            ->setParameter('sticky', true)
            ->orderBy('t.createdAt', 'DESC')
        ;

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);

        return $this->getPaginatedQuery($builder, $page);
    }

    public function findSearchResultPaginated(SearchForumData $searchData, array $groups, int $limit = 30): PaginationInterface
    {
        $builder = $this->createQueryBuilder('t')
            ->leftJoin('t.author', 'u')->addSelect('u')
            ->leftJoin('u.userGroups', 'g')->addSelect('g')
            ->leftJoin('t.forum', 'f')->addSelect('f')
            ->leftJoin('f.category', 'c')->addSelect('f')
            ->leftJoin('c.groupAccess', 'cg')
            ->leftJoin('f.groupAccess', 'fg')
            ->andWhere('fg IN (:groups)')
            ->andWhere('cg IN (:groups)')
            ->setParameter('groups', $groups)
            ->orderBy('t.createdAt', 'DESC');
        
        if (strlen((string) $searchData->getQuery()) >= 3 and null !== $searchData->getQuery()) {
            $builder->andWhere('t.title LIKE :q')->setParameter('q', '%'.$searchData->getQuery().'%');
        }

        return $this->paginatorService->setLimit($limit)->paginate($builder, $searchData->getPage());
    }

    /**
     * @param int $forumId
     * 
     * @return Topic[]
     */
    public function findStickies(int $forumId): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.author', 'u')->addSelect('u')
            ->leftJoin('u.userGroups', 'g')->addSelect('g')
            ->leftJoin('t.forum', 'f')
            ->andWhere('f.id = :id')
            ->andWhere('t.sticky = :sticky')
            ->setParameter('id', $forumId)
            ->setParameter('sticky', true)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
