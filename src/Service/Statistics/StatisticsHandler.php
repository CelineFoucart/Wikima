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