<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['timeline:show'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['timeline:show'])]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length( min: 1, max: 255)]
    #[Groups(['timeline:show'])]
    private $duration;

    #[ORM\Column(type: 'string', length: 2500, nullable: true)]
    #[Assert\Length(min: 3, max: 2500)]
    #[Groups(['timeline:show'])]
    private $presentation;

    #[ORM\Column(type: 'smallint', nullable: true)]
    #[Groups(['timeline:show'])]
    private $timelineOrder;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['timeline:show'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['timeline:show'])]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Timeline::class, inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private $timeline;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getTimelineOrder(): ?int
    {
        return $this->timelineOrder;
    }

    public function setTimelineOrder(?int $timelineOrder): self
    {
        $this->timelineOrder = $timelineOrder;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->timelineOrder;
    }

    /**
     * Rentre l'élément compatible avec reorder et les autres entités sortable.
     * 
     * @param int|null $position
     * 
     * @return self
     */
    public function setPosition(?int $position): self
    {
        $this->timelineOrder = $position;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTimeline(): ?Timeline
    {
        return $this->timeline;
    }

    public function setTimeline(?Timeline $timeline): self
    {
        $this->timeline = $timeline;

        return $this;
    }

    public function __toString()
    {
        return $this->title ? $this->title : 'Evénement';
    }
}
