<?php

namespace App\Controller\Api;

use App\Repository\PortalRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/portal')]
class PortalController extends AbstractController
{
    #[Route('', name: 'api_portal_index')]
    public function index(PortalRepository $portalRepository): JsonResponse
    {
        return $this->json($portalRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'index']);
    }
}
