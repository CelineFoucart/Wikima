<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Episode;
use App\Entity\Portal;
use App\Entity\Timeline;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Reorder an array of sortable object by position and persist in the database.
 */
final class ReorderService
{
    /**
     * @var Episode[]|Event[]|Section[]|Timeline[]|Portal[]
     */
    private array $elements = [];

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * Déplace et unsère à une nouvelle position l'élément dont l'id est passé en paramètre.
     */
    public function insertToNewPosition(int $id, int $position): self
    {
        $this->sort();

        for ($i = 0; $i < count($this->elements); ++$i) {
            if ($this->elements[$i]->getId() === $id) {
                $part2 = array_splice($this->elements, $i, 1);
                $part1 = array_slice($this->elements, 0, $position);
                $part3 = array_slice($this->elements, $position);
                $this->elements = array_merge($part1, $part2, $part3);
            }
        }

        $this->redefineAllPosition();

        return $this;
    }

    /**
     * Get the value of elements.
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * Set the value of elements.
     */
    public function setElements(array $elements): self
    {
        $this->elements = $elements;

        return $this;
    }

    private function sort(): self
    {
        if (!empty($elements)) {
            usort($this->elements, fn (object $a, object $b) => $a->getPosition() <=> $b->getPosition());
        }

        return $this;
    }

    /**
     * Redéfinit les positions de tous les éléments pour qu'elles soient égales à l'index.
     */
    private function redefineAllPosition(): self
    {
        for ($i = 0; $i < count($this->elements); ++$i) {
            $this->elements[$i]->setPosition($i);
            $this->entityManager->persist($this->elements[$i]);
        }

        $this->entityManager->flush();

        return $this;
    }
}
