<?php

namespace App\Entity;

use App\Repository\MapPositionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MapPositionRepository::class)]
class MapPosition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['index'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3,max: 255)]
    #[Groups(['index'])]
    private ?string $title = null;

    #[ORM\Column(length: 1500, nullable: true)]
    #[Assert\Length(min: 0, max: 1500)]
    #[Groups(['index'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotBlank]
    #[Groups(['index'])]
    private array $points = [];

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3,max: 30)]
    #[Groups(['index'])]
    private ?string $color = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3,max: 50)]
    #[Groups(['index'])]
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

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

    public function __toString()
    {
        return $this->title ? $this->title : 'Position';
    }
}
