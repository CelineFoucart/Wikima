<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Data\SearchData;
use App\Repository\ImageRepository;
use App\Repository\ImageTagRepository;
use App\Form\Search\AdvancedImageSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/image-tag')]
final class ImageTagController extends AbstractController
{
    public function __construct(private ImageRepository $imageRepository)
    {
    }
    
    #[Route('', name: 'api_image_type_index')]
    public function indexAction(ImageTagRepository $imageTagRepository): JsonResponse
    {
        return $this->json($imageTagRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'index-media']);
    }
}
