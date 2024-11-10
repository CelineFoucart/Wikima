<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\ImageRepository;
use App\Repository\ImageTagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/image-tag')]
final class ImageTagController extends AbstractController
{
    public function __construct(private ImageRepository $imageRepository)
    {
    }

    #[Route('', name: 'api_image_type_index', methods:['GET'])]
    public function indexAction(ImageTagRepository $imageTagRepository): JsonResponse
    {
        return $this->json($imageTagRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'index-media']);
    }
}
