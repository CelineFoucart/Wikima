<?php

namespace App\Service\Statistics;

use Doctrine\ORM\EntityManagerInterface;

final class DatabaseSize
{
    public function __construct(private EntityManagerInterface $em)
    { 
    }

    public function getSize()
    {
        $connection = $this->em->getConnection();
        $params = $connection->getParams();
        $databaseName = $params['dbname'];

        $sql = "SELECT
            SUM(data_length + index_length)/1024/1024 AS size
            FROM information_schema.TABLES 
            WHERE table_schema='$databaseName' 
            GROUP BY table_schema"
        ;
        $query = $connection->prepare($sql);
        $size = $query->executeQuery()->fetchAssociative();

        if (!isset($size['size'])) {
            return 0;
        }

        return $size['size'];
    }
}
