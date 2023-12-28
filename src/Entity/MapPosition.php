<?php

namespace App\Entity;

use App\Repository\MapPositionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MapPositionRepository::class)]
class MapPosition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 1500, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON)]
    private array $points = [];

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $marker = null;

    #[ORM\ManyToOne(inversedBy: 'mapPositions')]
    private ?Place $place = null;

    #[ORM\ManyToOne(inversedBy: 'mapPositions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Map $map = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPoints(): array
    {
        return $this->points;
    }

    public function setPoints(array $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getMarker(): ?string
    {
        return $this->marker;
    }

    public function setMarker(?string $marker): static
    {
        $this->marker = $marker;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getMap(): ?Map
    {
        return $this->map;
    }

    public function setMap(?Map $map): static
    {
        $this->map = $map;

        return $this;
    }
}
