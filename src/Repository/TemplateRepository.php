<?php

namespace App\Repository;

use App\Entity\Template;
use App\Service\DataFilterService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Template>
 *
 * @method Template|null find($id, $lockMode = null, $lockVersion = null)
 * @method Template|null findOneBy(array $criteria, array $orderBy = null)
 * @method Template[]    findAll()
 * @method Template[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Template::class);
    }

    public function searchItems(array $parameters): array
    {
        $builder = $this->createQueryBuilder('t');
        $params = DataFilterService::formatParams($parameters, 't');
        
        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('t.title LIKE :search')
                ->orWhere('t.description LIKE :search')
                ->setParameter('search', '%' . $parameters['search']['value'] . '%' );
        }

        if ($params['limit'] > 0) {
            $builder->setMaxResults($params['limit'])->setFirstResult($params['start']);
        }

        return $builder
            ->orderBy($params['orderBy'], $params['direction'])
            ->getQuery()
            ->getResult();
    }

    public function countSearchTotal(array $parameters): array
    {
        $builder = $this->createQueryBuilder('t')->select('COUNT(DISTINCT t.id) AS recordsFiltered');

        if (isset($parameters['search']['value']) && strlen($parameters['search']['value']) > 1) {
            $builder
                ->andWhere('t.title LIKE :search')
                ->orWhere('t.description LIKE :search')
                ->setParameter('search', '%' . $parameters['search']['value'] . '%' );
        }

        return $builder->getQuery()->getOneOrNullResult();
    }
}
