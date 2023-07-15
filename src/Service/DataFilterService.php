<?php

namespace App\Service;

final class DataFilterService
{
    public static function formatParams(array $parameters, string $alias)
    {
        $limit = isset($parameters['length']) ? (int)$parameters['length'] : 10;
        $orderColumns = isset($parameters['order'][0]['column']) ? (int)$parameters['order'][0]['column'] : 0;
        $direction = isset($parameters['order'][0]['dir']) ? $parameters['order'][0]['dir'] : 'asc';
        $orderBy = isset($parameters['columns'][$orderColumns]['name']) ? $parameters['columns'][$orderColumns]['name'] : $alias.'.id';

        return [
            'limit' => (int)$limit,
            'direction' => $direction,
            'orderBy' => $orderBy,
            'start' => isset($parameters['start']) ? (int)$parameters['start'] : 0
        ];
    }
}
