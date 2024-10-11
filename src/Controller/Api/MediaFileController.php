<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\Server;
use League\Glide\ServerFactory;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/file')]
final class MediaFileController extends AbstractController
{
    public const RESIZE_AVAILABLE = [
        'gallery_thumbnail' => ['w' => 150, 'h' => 150, 'fit' => 'crop'],
        'card_image' => ['w' => 305,  'h' => 280, 'fit' => 'crop'],
        'medium_image' => ['w' => 700,  'h' => 1000, 'fit' => 'crop'],
        'medium_banner' => ['w' => 550,  'h' => 140, 'fit' => 'crop'],
    ];

    public function __construct(private string $publicDir, private UploaderHelper $vichHelper)
    {
    }

    #[Route('/{id}', name: 'file_show', methods:['GET'])]
    public function showAction(Image $image): Response
    {
        $path = $this->vichHelper->asset($image);

        return new BinaryFileResponse($this->publicDir . DIRECTORY_SEPARATOR . $path);
    }

    #[Route('/{id}/thumbnail', name: 'file_thumb', methods:['GET'])]
    public function thumbnailAction(Image $image, Server $glide): Response
    {
        $path = $this->vichHelper->asset($image);
        $glide->setResponseFactory(new SymfonyResponseFactory());
        $glide->setCachePathPrefix('/gallery_thumbnail');
        $response = $glide->getImageResponse($path, self::RESIZE_AVAILABLE['gallery_thumbnail']);

        return $response;
    }
}
