<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PaginatorService.
 *
 * PaginatorService manages pagination.
 *
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class PaginatorService
{
    private PaginatorInterface $paginator;

    private int $limit = 10;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * Paginate a query.
     */
    public function paginate(QueryBuilder $builder, int $page): PaginationInterface
    {
        $items = $this->paginator->paginate(
            $builder->getQuery(),
            $page,
            $this->limit
        );

        return $items;
    }

    /**
     * Set the value of limit.
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }
}
