<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Repository\LogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AdminApiLogController extends AbstractController
{
    #[Route('/api/admin/logs', name: 'api_log_index', methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')"))]
    public function indexAction(LogRepository $logRepository, Request $request): JsonResponse
    {
        $parameters = $request->query->all();
        $recordsFiltered = $logRepository->countSearchTotal($parameters);
        $recordsTotal = $logRepository->countSearchTotal([]);

        $data = $logRepository->searchPaginatedItems($parameters);

        $data = [
            'draw' => isset($parameters['draw']) ? (int)$parameters['draw'] : 0,
            'recordsFiltered' => isset($recordsFiltered['recordsFiltered']) ? $recordsFiltered['recordsFiltered'] : 0,
            "data" => $data,
            'recordsTotal' => isset($recordsTotal['recordsFiltered']) ? $recordsTotal['recordsFiltered'] : 0,
        ];

        return $this->json($data, 200, [], ['groups' => 'index']);
    }
}
