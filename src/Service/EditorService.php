<?php

namespace App\Service;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Portal;
use App\Entity\Timeline;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\String\Slugger\SluggerInterface;

class EditorService
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    /**
     * @param Article|Category|Portal|Image|Timeline $entity
     *
     * @return Article|Category|Portal|Image|Timeline
     */
    public function prepareCreation($entity)
    {
        $entity->setSlug($this->updateSlug($entity->getTitle()));
        $entity->setCreatedAt(new DateTimeImmutable());

        return $entity;
    }

    /**
     * @param Article|Category|Portal|Image|Timeline $entity
     *
     * @return Article|Category|Portal|Image|Timeline
     */
    public function prepareEditing($entity)
    {
        $entity->setSlug($this->updateSlug($entity->getTitle()));
        $entity->setUpdatedAt(new DateTime());

        return $entity;
    }

    public function updateSlug(string $title): string
    {
        return (string) $this->slugger->slug(strtolower($title));
    }
}
