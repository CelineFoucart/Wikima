<?php

declare(strict_types=1);

namespace App\Entity\Data;

use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractSearchData
{
    #[Assert\Length(
        min: 3
    )]
    protected ?string $query = null;

    protected int $page = 1;

    /**
     * Get the value of query.
     */
    public function getQuery(): ?string
    {
        return $this->query;
    }

    /**
     * Set the value of query.
     */
    public function setQuery(?string $query = null): self
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get the value of page.
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Set the value of page.
     */
    public function setPage(int $page): self
    {
        $this->page = $page;

        return $this;
    }
}
