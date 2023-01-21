<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Comment::class);
        $this->paginatorService = $paginatorService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Comment $entity, bool $flush = true): void
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
    public function remove(Comment $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    
    public function findPaginatedByArticle(int $page, Article $article): PaginationInterface
    {
        $builder =  $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC')
            ->andWhere('c.article = :article')
            ->setParameter('article', $article);
        return $this->paginatorService->setLimit(10)->paginate($builder, $page);
    }

    public function findByAuthor(User $user, int $page): PaginationInterface
    {
        $builder = $this->createQueryBuilder('c')
            ->leftJoin('c.article', 'a')->addSelect('a')
            ->orderBy('c.createdAt', 'DESC')
            ->andWhere('c.author = :user')
            ->setParameter('user', $user)
        ;

        return $this->paginatorService->setLimit(6)->paginate($builder, $page);
    }
}
