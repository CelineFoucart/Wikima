<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 *
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    public function save(Note $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Note $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Note[] Returns an array of Note objects
     */
    public function findLastNotes(int $limit = 5): array
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->andWhere('n.isArchived = :isArchived')
            ->setParameter('isArchived', false)
            ->orWhere('n.isArchived IS NULL')
            ->getQuery()
            ->getResult()
        ;
    }

    

    public function findForAdminList(bool $isArchived = false): array
    {
        $builder = $this->createQueryBuilder('n')
            ->leftJoin('n.portal', 'p')->addSelect('p')
            ->leftJoin('n.category', 'c')->addSelect('c')
            ->andWhere('n.isArchived = :isArchived')
            ->setParameter('isArchived', $isArchived);

        if (!$isArchived) {
            $builder->orWhere('n.isArchived IS NULL');
        } 
            
        return $builder->getQuery()->getResult();
    }

    public function countNotProcessed(): int
    {
        try {
            return $this->createQueryBuilder('n')
                ->select('COUNT(n.id)')
                ->andWhere('(n.isArchived = :isArchived OR n.isArchived IS NULL)')
                ->setParameter('isArchived', false)
                ->andWhere('(n.isProcessed = :isProcessed OR n.isProcessed IS NULL)')
                ->setParameter('isProcessed', false)
                ->getQuery()
                ->getSingleScalarResult()
            ;
        } catch (\Exception $th) {
            return 0;
        }
    }
}
