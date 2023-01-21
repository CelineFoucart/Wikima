<?php

namespace App\Service\Statistics;

interface StatisticsEntityInterface
{
    /**
     * Get the sql query
     */ 
    public function getQuery(): string;

    /**
     * Get the table name
     */ 
    public function getTable(): string;
}