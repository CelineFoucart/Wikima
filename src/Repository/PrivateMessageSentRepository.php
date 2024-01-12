<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\PrivateMessageSent;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<PrivateMessageSent>
 *
 * @method PrivateMessageSent|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrivateMessageSent|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrivateMessageSent[]    findAll()
 * @method PrivateMessageSent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrivateMessageSentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrivateMessageSent::class);
    }

    /**
     * @return PrivateMessageSent[] Returns an array of PrivateMessageSent objects
     */
    public function findByAuthor(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'a')->addSelect('a')
            ->leftJoin('p.privateMessageReceived', 'pm')->addSelect('pm')
            ->andWhere('a.id = :id')
            ->setParameter('id', $user->getId())
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return PrivateMessageSent[] Returns an array of PrivateMessageSent objects
     */
    public function findForConversation(User $addressee, User $author): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')->addSelect('u')
            ->leftJoin('p.addressee', 'a')->addSelect('a')
            ->leftJoin('p.privateMessageReceived', 'pm')->addSelect('pm')
            ->andWhere('a.id = :addressee')->setParameter('addressee', $addressee->getId())
            ->andWhere('u.id = :author')->setParameter('author', $author->getId())
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return PrivateMessageSent[] Returns an array of PrivateMessageSent objects
     */
    public function getReferenced(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'u')->addSelect('u')
            ->leftJoin('p.addressee', 'a')->addSelect('a')
            ->leftJoin('p.privateMessageReceived', 'pm')->addSelect('pm')
            ->andWhere('a.id = :userId')
            ->orWhere('u.id = :userId')
            ->setParameter('userId', $user->getId())
            ->getQuery()
            ->getResult()
        ;
    }
}
