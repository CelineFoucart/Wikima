<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MapRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MapRepository::class)]
#[UniqueEntity('slug')]
class Map
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3,max: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
    private ?string $slug = null;

    #[ORM\Column(length: 2500, nullable: true)]
    #[Assert\Length(min: 3, max: 2500)]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\ManyToOne(inversedBy: 'maps')]
    private ?Image $image = null;

    #[ORM\ManyToMany(targetEntity: Portal::class, inversedBy: 'maps')]
    private Collection $portals;

    #[ORM\OneToMany(mappedBy: 'map', targetEntity: MapPosition::class, orphanRemoval: true)]
    #[ORM\OrderBy(['title' => 'ASC'])]
    private Collection $mapPositions;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'maps')]
    private Collection $categories;

    public function __construct()
    {
        $this->portals = new ArrayCollection();
        $this->mapPositions = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): static
    {
        $this->image = $image;

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
     * @return Collection<int, MapPosition>
     */
    public function getMapPositions(): Collection
    {
        return $this->mapPositions;
    }

    public function addMapPosition(MapPosition $mapPosition): static
    {
        if (!$this->mapPositions->contains($mapPosition)) {
            $this->mapPositions->add($mapPosition);
            $mapPosition->setMap($this);
        }

        return $this;
    }

    public function removeMapPosition(MapPosition $mapPosition): static
    {
        if ($this->mapPositions->removeElement($mapPosition)) {
            // set the owning side to null (unless already changed)
            if ($mapPosition->getMap() === $this) {
                $mapPosition->setMap(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function __toString()
    {
        return $this->title ? $this->title : 'Nouvelle carte';
    }
}
