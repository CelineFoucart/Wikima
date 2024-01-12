<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\PrivateMessageReceived;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Service\PaginatorService;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @extends ServiceEntityRepository<PrivateMessageReceived>
 *
 * @method PrivateMessageReceived|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrivateMessageReceived|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrivateMessageReceived[]    findAll()
 * @method PrivateMessageReceived[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrivateMessageReceivedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrivateMessageReceived::class);
    }

    /**
     * @return PrivateMessageReceived[]
     */
    public function findByAddressee(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.addressee', 'a')->addSelect('a')
            ->leftJoin('p.privateMessageSent', 'pm')->addSelect('pm')
            ->andWhere('a.id = :id')
            ->setParameter('id', $user->getId())
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function countNotRead(User $user): int
    {
        try {
            return $this->createQueryBuilder('p')
                ->leftJoin('p.addressee', 'u')
                ->select('COUNT(p.id)')
                ->andWhere('(p.readStatus = :readStatus OR p.readStatus IS NULL)')
                ->setParameter('readStatus', false)
                ->andWhere('u.id = :userId')
                ->setParameter('userId', $user->getId())
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (\Exception $th) {
            return 0;
        }
    }

    /**
     * @return PrivateMessageReceived[]
     */
    public function findForConversation(User $addressee, User $author): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')->addSelect('u')
            ->leftJoin('p.addressee', 'a')->addSelect('a')
            ->leftJoin('p.privateMessageSent', 'pm')->addSelect('pm')
            ->andWhere('a.id = :addressee')->setParameter('addressee', $addressee->getId())
            ->andWhere('u.id = :author')->setParameter('author', $author->getId())
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
    /**
     * 
     * @return PrivateMessageReceived[]
     */
    public function getReferenced(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')->addSelect('u')
            ->leftJoin('p.addressee', 'a')->addSelect('a')
            ->andWhere('a.id = :userId')
            ->orWhere('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult()
        ;
    }
}
