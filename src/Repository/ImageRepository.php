<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Data\SearchData;
use App\Entity\Image;
use App\Entity\ImageTag;
use App\Entity\Portal;
use App\Service\PaginatorService;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    private PaginatorService $paginatorService;

    public function __construct(ManagerRegistry $registry, PaginatorService $paginatorService)
    {
        parent::__construct($registry, Image::class);
        $this->paginatorService = $paginatorService;
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Image $entity, bool $flush = true): void
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
    public function remove(Image $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findBySlug(string $slug): ?Image
    {
        return $this->createQueryBuilder('i')
            ->leftJoin('i.portals', 'p')->addSelect('p')
            ->leftJoin('i.categories', 'c')->addSelect('c')
            ->andWhere('i.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findPaginated(int $page, array $excludes = [], int $limit = 14): PaginationInterface
    {
        $builder = $this->createQueryBuilder('i')->orderBy('i.title', 'ASC');

        if (!empty($excludes)) {
            $builder->andWhere('i.id NOT IN (:excludes)')->setParameter('excludes', $excludes);
        }

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);
    }

    public function findByCategory(Category $category, int $page, int $limit = 24, int $type = 0): PaginationInterface
    {
        $builder = $this->createQueryBuilder('i')
            ->leftJoin('i.categories', 'c')
            ->leftJoin('i.tags', 't')
            ->orderBy('i.title', 'ASC')
            ->andWhere('c.id IN (:categories)')
            ->setParameter('categories', [$category->getId()])
        ;

        if ($type  > 0) {
            $builder->andWhere('t.id IN (:type)')->setParameter('type', [$type]);
        }

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);
    }

    public function findByPortal(Portal $portal, int $page, int $limit = 24, int $type = 0): PaginationInterface
    {
        $builder = $this->createQueryBuilder('i')
            ->leftJoin('i.portals', 'p')
            ->orderBy('i.title', 'ASC')
            ->leftJoin('i.tags', 't')
            ->andWhere('p.id IN (:portals)')
            ->setParameter('portals', [$portal->getId()])
        ;

        if ($type  > 0) {
            $builder->andWhere('t.id IN (:type)')->setParameter('type', [$type]);
        }

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);
    }

    /**
     * @param SearchData $search
     * 
     * @return Image[]
     */
    public function advancedSearch(SearchData $search): array
    {
        $builder = $this->createQueryBuilder('i')
            ->leftJoin('i.portals', 'p')->addSelect('p')
            ->leftJoin('i.categories', 'c')->addSelect('c')
            ->leftJoin('i.tags', 't')->addSelect('t')
            ->setParameter('q', '%'.$search->getQuery().'%');

            if (empty($search->getFields())) {
                $builder->andWhere('i.title LIKE :q OR i.keywords LIKE :q OR i.description LIKE :q OR t.title LIKE :q');
            } else {
                $where = [];

                if (in_array('name', $search->getFields())) {
                    $where[] = 'i.title LIKE :q';
                }

                if (in_array('description', $search->getFields())) {
                    $where[] = 'i.description LIKE :q OR i.keywords LIKE :q';
                }

                if (in_array('tags', $search->getFields())) {
                    $where[] = 't.title LIKE :q';
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

    public function search(SearchData $search, array $excludes = [], int $limit = 10): PaginationInterface
    {
        $builder = $this->createQueryBuilder('i')
            ->orderBy('i.title', 'ASC')
            ->leftJoin('i.portals', 'p')->addSelect('p')
            ->leftJoin('i.categories', 'c')->addSelect('c')
            ->leftJoin('i.tags', 't')->addSelect('t')
        ;

        if (strlen($search->getQuery()) >= 3 and null !== $search->getQuery()) {
            $builder
                ->andWhere('i.title LIKE :q_1')
                ->setParameter('q_1', '%'.$search->getQuery().'%')
                ->orWhere('i.description LIKE :q_2')
                ->setParameter('q_2', '%'.$search->getQuery().'%')
                ->orWhere('i.keywords LIKE :q_3')
                ->setParameter('q_3', '%'.$search->getQuery().'%')
            ;
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

        if (!empty($search->getTags())) {
            $builder
                ->andWhere('t.id IN (:tags)')
                ->setParameter('tags', $search->getTags())
            ;
        }

        if (!empty($excludes)) {
            $builder->andWhere('i.id NOT IN (:excludes)')->setParameter('excludes', $excludes);
        }

        return $this->paginatorService->setLimit($limit)->paginate($builder, $search->getPage());
    }

    public function findByType(ImageTag $imageType, int $page = 1, int $limit = 20): PaginationInterface
    {
        $builder = $this->createQueryBuilder('i')
            ->orderBy('i.title', 'ASC')
            ->leftJoin('i.portals', 'p')->addSelect('p')
            ->leftJoin('i.categories', 'c')->addSelect('c')
            ->leftJoin('i.tags', 't')->addSelect('t')
            ->andWhere('t.id IN (:type)')
            ->setParameter('type', [$imageType->getId()])
        ;

        return $this->paginatorService->setLimit($limit)->paginate($builder, $page);
    }
}
