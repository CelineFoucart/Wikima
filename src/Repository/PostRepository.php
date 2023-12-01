<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\PaginatorService;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Post::class);
        $this->paginatorService = $paginatorService;
    }

    /**
     * Returns the paginated result of Topic.
     */
    public function findPaginated(int $forumId, int $page, int $limit = 30): PaginationInterface
    {
        $builder = $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')->addSelect('u')
            ->leftJoin('u.userGroups', 'g')->addSelect('g')
            ->leftJoin('g.forumGroup', 'fg')->addSelect('fg')
            ->leftJoin('p.topic', 't')->andWhere('t.id = :id')
            ->setParameter('id', $forumId)
            ->orderBy('p.createdAt', 'ASC')
        ;

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);

        return $this->getPaginatedQuery($builder, $page);
    }

    /**
     * @param int $topicId
     * 
     * @return Post[]
     */
    public function findFirstPost(int $topicId): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.topic', 't')->addSelect('t')
            ->andWhere('t.id = :id')
            ->setParameter('id', $topicId)
            ->orderBy('p.createdAt', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
    }
}
