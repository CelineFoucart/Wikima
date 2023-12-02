<?php

namespace App\Entity;

use App\Repository\ScenarioCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ScenarioCategoryRepository::class)]
#[UniqueEntity('title')]
class ScenarioCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(min: 3, max: 15000)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Scenario::class, mappedBy: 'categories')]
    private Collection $scenarios;

    public function __construct()
    {
        $this->scenarios = new ArrayCollection();
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
            $scenario->addCategory($this);
        }

        return $this;
    }

    public function removeScenario(Scenario $scenario): static
    {
        if ($this->scenarios->removeElement($scenario)) {
            $scenario->removeCategory($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title ? $this->title : 'Nouvelle catégorie';
    }
}
