<?php

namespace App\Entity;

use App\Repository\ImageGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageGroupRepository::class)]
class ImageGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 5000, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Image::class, inversedBy: 'imageGroups')]
    private Collection $images;

    #[ORM\ManyToMany(targetEntity: Portal::class, inversedBy: 'imageGroups')]
    private Collection $portals;

    #[ORM\OneToMany(mappedBy: 'imageGroup', targetEntity: Scenario::class)]
    private Collection $scenarios;

    #[ORM\OneToMany(mappedBy: 'imageGroup', targetEntity: Place::class)]
    private Collection $places;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->portals = new ArrayCollection();
        $this->scenarios = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        $this->images->removeElement($image);

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
            $scenario->setImageGroup($this);
        }

        return $this;
    }

    public function removeScenario(Scenario $scenario): static
    {
        if ($this->scenarios->removeElement($scenario)) {
            // set the owning side to null (unless already changed)
            if ($scenario->getImageGroup() === $this) {
                $scenario->setImageGroup(null);
            }
        }

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
            $place->setImageGroup($this);
        }

        return $this;
    }

    public function removePlace(Place $place): static
    {
        if ($this->places->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getImageGroup() === $this) {
                $place->setImageGroup(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title ? $this->title : 'Nouveau groupe';
    }
}
