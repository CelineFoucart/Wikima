<?php

namespace App\Service\Statistics;

use Doctrine\ORM\EntityManagerInterface;

class StatisticsHandler 
{
    /**
     * @var StatisticsEntityInterface[]
     */
    private array $entities = [];

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    { 
        $this->em = $em;
    }

    public function addEntity(StatisticsEntityInterface $entity): self
    {
        $this->entities[] = $entity;

        return $this;
    }

    public function getStatistics(): array
    {
        try {
            $connection = $this->em->getConnection();
            $query = $connection->prepare($this->formatQuery());
            $data = $query->executeQuery()->fetchAllAssociative();
            return $this->formatStats($data);
        } catch (\Exception $th) {
            return [];
        }
    }

    public function getStatsByMonth(string $table, string $dateField, string $year, ?string $condition = null)
    {
        if ($condition !== null) {
            $condition = ' AND ' . $condition;
        }

        $sql = "SELECT COUNT(id) AS total, MONTH({$dateField}) AS monthId
            FROM `{$table}`
            WHERE YEAR({$dateField}) = :year_param {$condition}
            GROUP BY monthId";
        
        $connection = $this->em->getConnection();
        $query = $connection->prepare($sql);
        
        return $query->executeQuery(['year_param' => $year])->fetchAllAssociative();
    }

    private function formatQuery(): string
    {
        $sql = [];

        foreach ($this->entities as $entity) {
            $sql[] = $entity->getQuery();
        }

        return join(" UNION ", $sql);
    }

    private function formatStats($stats): array
    {
        if(empty($stats)) {
            return [];
        }

        $data = [];

        foreach ($stats as $value) {
            $data[$value['element']] = $value['counts'];
        }

        return $data;
    }
}