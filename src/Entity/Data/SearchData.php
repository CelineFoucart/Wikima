<?php

namespace App\Entity\Data;

use App\Entity\Category;
use App\Entity\Portal;
use Symfony\Component\Validator\Constraints as Assert;

class SearchData
{
    #[Assert\Length(
        min: 3
    )]
    private ?string $query = null;

    private int $page = 1;

    /**
     * @var Portal[]
     */
    private array $portals = [];

    /**
     * @var Category[]
     */
    private array $categories = [];

    private array $tags = [];

    private ?array $fields = [];

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

    /**
     * Get the value of portals.
     *
     * @return Portal[]
     */
    public function getPortals(): array
    {
        return $this->portals;
    }

    /**
     * Set the value of portals.
     *
     * @param Portal[] $portals
     */
    public function setPortals(array $portals): self
    {
        $this->portals = $portals;

        return $this;
    }

    /**
     * Get the value of categories.
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * Set the value of categories.
     */
    public function setCategories(array $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get the value of tags
     *
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * Set the value of tags
     *
     * @param array $tags
     *
     * @return self
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get the value of fields
     *
     * @return ?array
     */
    public function getFields(): ?array
    {
        return $this->fields;
    }

    /**
     * Set the value of fields
     *
     * @param ?array $fields
     *
     * @return self
     */
    public function setFields(?array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }
}
