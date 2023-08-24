<?php

namespace App\Repository;

use App\Entity\Idiom;
use App\Entity\Data\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Idiom>
 *
 * @method Idiom|null find($id, $lockMode = null, $lockVersion = null)
 * @method Idiom|null findOneBy(array $criteria, array $orderBy = null)
 * @method Idiom[]    findAll()
 * @method Idiom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdiomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Idiom::class);
    }

    public function findIdiomById(int $id): ?Idiom
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.idiomArticles', 'ia')->addSelect('ia')
            ->leftJoin('ia.category', 'c')->addSelect('c')
            ->leftJoin('i.portals', 'p')->addSelect('p')
            ->orderBy('c.position')
            ->andWhere('i.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findIdiomBySlug(string $slug): ?Idiom
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.idiomArticles', 'ia')->addSelect('ia')
            ->leftJoin('ia.category', 'c')->addSelect('c')
            ->leftJoin('i.portals', 'p')->addSelect('p')
            ->orderBy('c.position')
            ->andWhere('i.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param SearchData $search
     * 
     * @return Idiom[]
     */
    public function advancedSearch(SearchData $search): array
    {
        if (count($search->getFields()) === 1 && in_array('tags', $search->getFields())) {
            return [];
        }
        
        $builder = $this->createQueryBuilder('i')
            ->leftJoin('i.portals', 'p')->addSelect('p')
            ->leftJoin('p.categories', 'c')->addSelect('c')
            ->setParameter('q', '%'.$search->getQuery().'%');

            if (empty($search->getFields())) {
                $builder->andWhere('i.translatedName LIKE :q OR i.originalName LIKE :q OR i.description LIKE :q');
            } else {
                $where = [];

                if (in_array('name', $search->getFields())) {
                    $where[] = 'i.translatedName LIKE :q OR i.originalName LIKE :q';
                }

                if (in_array('description', $search->getFields())) {
                    $where[] = 'i.description LIKE :q';
                }

                $builder->andWhere(join(' OR ', $where));
            }

        if (!empty($search->getPortals())) {
            $builder
                ->andWhere('p.id IN (:portals)')
                ->setParameter('portals', $search->getPortals())
            ;
        }

        if (!empty($search->getCategories())) {
            $builder
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $search->getCategories())
            ;
        }

        return $builder->getQuery()->getResult();
    }
}
