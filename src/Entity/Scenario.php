<?php

namespace App\Entity;

use App\Repository\ScenarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScenarioRepository::class)]
#[UniqueEntity(fields: ['slug', 'title'])]
class Scenario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 255
    )]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
    #[Assert\Length(
        min: 3,
        max: 255
    )]
    private ?string $slug = null;

    #[ORM\Column(length: 3000, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 3000
    )]
    private ?string $pitch = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: ScenarioCategory::class, inversedBy: 'scenarios')]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: Portal::class, inversedBy: 'scenarios')]
    private Collection $portals;

    #[ORM\ManyToMany(targetEntity: Timeline::class, inversedBy: 'scenarios')]
    private Collection $timelines;

    #[ORM\OneToMany(mappedBy: 'scenario', targetEntity: Episode::class, orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $episodes;

    #[ORM\Column(nullable: true)]
    private ?bool $public = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\ManyToOne(inversedBy: 'scenarios')]
    private ?ImageGroup $imageGroup = null;

    #[ORM\ManyToMany(targetEntity: Person::class, inversedBy: 'scenarios')]
    private Collection $persons;

    #[ORM\ManyToMany(targetEntity: Place::class, inversedBy: 'scenarios')]
    private Collection $places;

    #[ORM\Column(nullable: true)]
    private ?bool $archived = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $defaultColor = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->portals = new ArrayCollection();
        $this->timelines = new ArrayCollection();
        $this->episodes = new ArrayCollection();
        $this->persons = new ArrayCollection();
        $this->places = new ArrayCollection();
    }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPitch(): ?string
    {
        return $this->pitch;
    }

    public function setPitch(?string $pitch): static
    {
        $this->pitch = $pitch;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, ScenarioCategory>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(ScenarioCategory $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(ScenarioCategory $category): static
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

    public function addPortal(Portal $portal): static
    {
        if (!$this->portals->contains($portal)) {
            $this->portals->add($portal);
        }

        return $this;
    }

    public function removePortal(Portal $portal): static
    {
        $this->portals->removeElement($portal);

        return $this;
    }

    /**
     * @return Collection<int, Timeline>
     */
    public function getTimelines(): Collection
    {
        return $this->timelines;
    }

    public function addTimeline(Timeline $timeline): static
    {
        if (!$this->timelines->contains($timeline)) {
            $this->timelines->add($timeline);
        }

        return $this;
    }

    public function removeTimeline(Timeline $timeline): static
    {
        $this->timelines->removeElement($timeline);

        return $this;
    }

    /**
     * @return Collection<int, Episode>
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): static
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes->add($episode);
            $episode->setScenario($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): static
    {
        if ($this->episodes->removeElement($episode)) {
            // set the owning side to null (unless already changed)
            if ($episode->getScenario() === $this) {
                $episode->setScenario(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title ? $this->title : 'Nouveau scénario';
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(?bool $public): static
    {
        $this->public = $public;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getImageGroup(): ?ImageGroup
    {
        return $this->imageGroup;
    }

    public function setImageGroup(?ImageGroup $imageGroup): static
    {
        $this->imageGroup = $imageGroup;

        return $this;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getPersons(): Collection
    {
        return $this->persons;
    }

    public function addPerson(Person $person): static
    {
        if (!$this->persons->contains($person)) {
            $this->persons->add($person);
        }

        return $this;
    }

    public function removePerson(Person $person): static
    {
        $this->persons->removeElement($person);

        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): static
    {
        if (!$this->places->contains($place)) {
            $this->places->add($place);
        }

        return $this;
    }

    public function removePlace(Place $place): static
    {
        $this->places->removeElement($place);

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(?bool $archived): static
    {
        $this->archived = $archived;

        return $this;
    }

    public function getDefaultColor(): ?string
    {
        return $this->defaultColor;
    }

    public function setDefaultColor(?string $defaultColor): static
    {
        $this->defaultColor = $defaultColor;

        return $this;
    }
}
