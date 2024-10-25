<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Category;
use App\Entity\Idiom;
use App\Entity\Image;
use App\Entity\Portal;
use App\Service\ImageResizeHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/file')]
final class MediaFileController extends AbstractController
{
    public function __construct(private string $publicDir, private ImageResizeHelper $imageResizeHelper)
    {
    }

    #[Route('/{id}', name: 'file_show', methods: ['GET'])]
    public function showAction(Image $image): Response
    {
        $path = $this->imageResizeHelper->asset($image);

        return new BinaryFileResponse($this->publicDir.DIRECTORY_SEPARATOR.$path);
    }

    #[Route('/{id}/icon', name: 'file_icon', methods: ['GET'])]
    public function iconAction(Image $image): Response
    {
        return $this->imageResizeHelper->renderResizeImage($image, 'icon_image');

        return $response;
    }

    #[Route('/{id}/thumbnail', name: 'file_thumb', methods: ['GET'])]
    public function thumbnailAction(Image $image): Response
    {
        return $this->imageResizeHelper->renderResizeImage($image, 'gallery_thumbnail');
    }

    #[Route('/{id}/medium', name: 'file_medium', methods: ['GET'])]
    public function mediumAction(Image $image): Response
    {
        return $this->imageResizeHelper->renderResizeImage($image, 'medium_image');
    }

    #[Route('/{id}/card', name: 'file_card', methods: ['GET'])]
    public function cardAction(Image $image): Response
    {
        return $this->imageResizeHelper->renderResizeImage($image, 'card_image');
    }

    #[Route('/{id}/portal-banner', name: 'file_portal_banner', methods: ['GET'])]
    public function portalBannerAction(Portal $portal): Response
    {
        return $this->imageResizeHelper->renderResizeImage($portal, 'medium_banner');
    }

    #[Route('/{id}/category-banner', name: 'file_category_banner', methods: ['GET'])]
    public function categoryBannerAction(Category $category): Response
    {
        return $this->imageResizeHelper->renderResizeImage($category, 'medium_banner');
    }

    #[Route('/{id}/idiom-banner', name: 'file_idiom_banner', methods: ['GET'])]
    public function idiomBannerAction(Idiom $idiom): Response
    {
        return $this->imageResizeHelper->renderResizeImage($idiom, 'medium_banner');
    }
}
