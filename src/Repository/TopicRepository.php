<?php

namespace App\Repository;

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
    public function findPaginated(int $topicId, int $page, int $limit = 30): PaginationInterface
    {
        $builder = $this->createQueryBuilder('t')
            ->leftJoin('t.author', 'u')->addSelect('u')
            ->leftJoin('u.userGroups', 'g')->addSelect('g')
            ->leftJoin('t.forum', 'f')->andWhere('f.id = :id')
            ->setParameter('id', $topicId)
            ->orderBy('t.createdAt', 'DESC')
        ;

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);

        return $this->getPaginatedQuery($builder, $page);
    }
}
