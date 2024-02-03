<?php

declare(strict_types=1);

namespace App\Entity\Data;

use App\Entity\Forum;
use App\Entity\ForumCategory;

final class SearchForumData extends AbstractSearchData
{
    /**
     * @var ForumCategory[]
     */
    private array $categories = [];

    /**
     * @var Forum[]
     */
    private array $forums = [];

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
     * Get the value of forums.
     */
    public function getForums(): array
    {
        return $this->forums;
    }

    /**
     * Set the value of forums.
     */
    public function setForums(array $forums): self
    {
        $this->forums = $forums;

        return $this;
    }
}
