<?php

namespace App\Entity;

use App\Repository\TimelineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TimelineRepository::class)]
#[UniqueEntity('slug')]
class Timeline
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 255
    )]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
    private $slug;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 255
    )]
    private $description;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'timelines')]
    private $categories;

    #[ORM\ManyToMany(targetEntity: Portal::class, inversedBy: 'timelines')]
    private $portals;

    #[ORM\OneToMany(mappedBy: 'timeline', targetEntity: Event::class, orphanRemoval: true)]
    #[ORM\OrderBy(['timelineOrder' => 'ASC'])]
    private $events;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    #[ORM\ManyToMany(targetEntity: Scenario::class, mappedBy: 'timelines')]
    private Collection $scenarios;

    #[ORM\ManyToMany(targetEntity: Section::class, mappedBy: 'referencedTimelines')]
    private Collection $sections;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'previousReferences')]
    #[ORM\JoinColumn(onDelete:"SET NULL")]
    private ?self $previous = null;

    #[ORM\OneToMany(mappedBy: 'previous', targetEntity: self::class)]
    private Collection $previousReferences;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'nextReferences')]
    #[ORM\JoinColumn(onDelete:"SET NULL")]
    private ?self $next = null;

    #[ORM\OneToMany(mappedBy: 'next', targetEntity: self::class)]
    private Collection $nextReferences;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->portals = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->scenarios = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->previousReferences = new ArrayCollection();
        $this->nextReferences = new ArrayCollection();
    }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Portal>
     */
    public function getPortals(): Collection
    {
        return $this->portals;
    }

    public function addPortal(Portal $portal): self
    {
        if (!$this->portals->contains($portal)) {
            $this->portals[] = $portal;
        }

        return $this;
    }

    public function removePortal(Portal $portal): self
    {
        $this->portals->removeElement($portal);

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setTimeline($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getTimeline() === $this) {
                $event->setTimeline(null);
            }
        }

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function __toString(): string
    {
        if (null === $this->title) {
            return '';
        }

        return $this->title;
    }

    /**
     * @return Collection<int, Scenario>
     */
    public function getScenarios(): Collection
    {
        return $this->scenarios;
    }

    public function addScenario(Scenario $scenario): static
    {
        if (!$this->scenarios->contains($scenario)) {
            $this->scenarios->add($scenario);
            $scenario->addTimeline($this);
        }

        return $this;
    }

    public function removeScenario(Scenario $scenario): static
    {
        if ($this->scenarios->removeElement($scenario)) {
            $scenario->removeTimeline($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Section>
     */
    public function getSections(): Collection
    {
        return $this->sections;
    }

    public function addSection(Section $section): static
    {
        if (!$this->sections->contains($section)) {
            $this->sections->add($section);
            $section->addReferencedTimeline($this);
        }

        return $this;
    }

    public function removeSection(Section $section): static
    {
        if ($this->sections->removeElement($section)) {
            $section->removeReferencedTimeline($this);
        }

        return $this;
    }

    public function getPrevious(): ?self
    {
        return $this->previous;
    }

    public function setPrevious(?self $previous): static
    {
        $this->previous = $previous;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getPreviousReferences(): Collection
    {
        return $this->previousReferences;
    }

    public function addPreviousReference(self $previousReference): static
    {
        if (!$this->previousReferences->contains($previousReference)) {
            $this->previousReferences->add($previousReference);
            $previousReference->setPrevious($this);
        }

        return $this;
    }

    public function removePreviousReference(self $previousReference): static
    {
        if ($this->previousReferences->removeElement($previousReference)) {
            // set the owning side to null (unless already changed)
            if ($previousReference->getPrevious() === $this) {
                $previousReference->setPrevious(null);
            }
        }

        return $this;
    }

    public function getNext(): ?self
    {
        return $this->next;
    }

    public function setNext(?self $next): static
    {
        $this->next = $next;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getNextReferences(): Collection
    {
        return $this->nextReferences;
    }

    public function addNextReference(self $nextReference): static
    {
        if (!$this->nextReferences->contains($nextReference)) {
            $this->nextReferences->add($nextReference);
            $nextReference->setNext($this);
        }

        return $this;
    }

    public function removeNextReference(self $nextReference): static
    {
        if ($this->nextReferences->removeElement($nextReference)) {
            // set the owning side to null (unless already changed)
            if ($nextReference->getNext() === $this) {
                $nextReference->setNext(null);
            }
        }

        return $this;
    }
}
