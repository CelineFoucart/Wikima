<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Idiom;
use App\Entity\Image;
use App\Entity\Portal;
use App\Entity\Category;
use League\Glide\Server;
use Symfony\Component\HttpFoundation\Response;
use League\Glide\Responses\SymfonyResponseFactory;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ImageResizeHelper
{
    public const RESIZE_AVAILABLE = [
        'icon_image' => ['w' => 35, 'h' => 25, 'fit' => 'crop'],
        'gallery_thumbnail' => ['w' => 150, 'h' => 150, 'fit' => 'crop'],
        'card_image' => ['w' => 305,  'h' => 280, 'fit' => 'crop'],
        'medium_image' => ['w' => 700,  'h' => 1000, 'fit' => 'contain'],
        'medium_banner' => ['w' => 550,  'h' => 140, 'fit' => 'crop'],
    ];

    public function __construct(private UploaderHelper $vichHelper, private Server $glide)
    {
        
    }

    public function asset(object $object): ?string
    {
        return $this->vichHelper->asset($object);
    }

    public function renderResizeImage(Portal|Image|Category|Idiom $entity, string $size): Response
    {
        $path = $this->vichHelper->asset($entity);

        if (!$path) {
            throw new NotFoundHttpException('File not found');
        }

        $this->glide->setResponseFactory(new SymfonyResponseFactory());
        $this->glide->setCachePathPrefix('/' . $size);

        return $this->glide->getImageResponse($path, self::RESIZE_AVAILABLE[$size]);
    }

    public function removeImageCache(Portal|Image|Category|Idiom $entity): bool
    {
        $path = $this->vichHelper->asset($entity);

        if ($path === null) {
            return false;
        }

        return $this->glide->deleteCache($path); 
    }
}
