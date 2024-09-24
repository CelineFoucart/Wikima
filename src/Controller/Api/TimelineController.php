<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Timeline;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/timeline')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class TimelineController extends AbstractController
{
    #[Route('/{id}', name: 'api_timeline_show', methods:['GET'])]
    public function showAction(Timeline $timeline): JsonResponse
    {
        return $this->json($timeline, Response::HTTP_OK, [], ['groups' => 'timeline-admin']);
    }
}
