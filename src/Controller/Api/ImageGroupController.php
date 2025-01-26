<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\ImageGroupRepository;
use App\Repository\ImageRepository;
use App\Repository\ImageTagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/image-group')]
final class ImageGroupController extends AbstractController
{
    public function __construct(private ImageRepository $imageRepository)
    {
    }

    #[Route('', name: 'api_image_group_index', methods:['GET'])]
    public function indexAction(ImageGroupRepository $imageGroupRepository): JsonResponse
    {
        return $this->json($imageGroupRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'media:index']);
    }
}
