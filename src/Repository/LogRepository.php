<?php

namespace App\Repository;

use App\Entity\Log;
use App\Service\DataFilterService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Log>
 *
 * @method Log|null find($id, $lockMode = null, $lockVersion = null)
 * @method Log|null findOneBy(array $criteria, array $orderBy = null)
 * @method Log[]    findAll()
 * @method Log[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Log::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Log $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function clearLogs(?string $date = null)
    {
        try {
            if (strlen($date) > 0) {
                $date .= ' 23:59:59';
            } else {
                $date = new \DateTime();
                $date = $date->format('Y-m-d H:i:s');
            }

            $this->createQueryBuilder('l')
                ->delete(Log::class, 'l')
                ->andWhere('l.createdAt <= :end')
                ->setParameter('end', $date)
                ->getQuery()
                ->execute();

            return true;
        } catch (\Exception $th) {
            return false;
        }

    }
    
    public function countSearchTotal(array $parameters): array
    {
        $builder = $this->createQueryBuilder('l')->select('COUNT(DISTINCT l.id) AS recordsFiltered');

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('l.action LIKE :search')
                ->orWhere('l.level LIKE :search')
                ->orWhere('l.object LIKE :search')
                ->orWhere('l.message LIKE :search')
                ->setParameter('search', '%'.$parameters['search']['value'].'%');
        }

        return $builder->getQuery()->getOneOrNullResult();
    }
    
    public function searchPaginatedItems(array $parameters): array
    {
        $builder = $this->createQueryBuilder('l');
        $params = DataFilterService::formatParams($parameters, 'l');

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('l.action LIKE :search')
                ->orWhere('l.level LIKE :search')
                ->orWhere('l.object LIKE :search')
                ->orWhere('l.message LIKE :search')
                ->setParameter('search', '%'.$parameters['search']['value'].'%');
        }

        if ($params['limit'] > 0) {
            $builder->setMaxResults($params['limit'])->setFirstResult($params['start']);
        }

        return $builder ->orderBy($params['orderBy'], $params['direction'])->getQuery()->getResult();
    }
}
