<?php

namespace App\Service\Statistics;

class SatisticsEntity implements StatisticsEntityInterface
{
    private string $query;

    private string $table;

    public function __construct(string $table)
    {
        $this->table = $table;

        $this->query = "SELECT count(id) AS counts, '{$this->table}' AS element FROM {$this->table}";

        if ('article' === $table) {
            $this->query .= ' WHERE is_draft = 0 or is_draft IS NULL';
        }
    }

    /**
     * Get the sql query.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Get table name.
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
