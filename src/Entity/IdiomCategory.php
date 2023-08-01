<?php

namespace App\Entity;

use App\Repository\IdiomCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: IdiomCategoryRepository::class)]
#[UniqueEntity('slug')]
class IdiomCategory
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

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 255
    )]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: IdiomArticle::class)]
    private Collection $idiomArticles;

    public function __construct()
    {
        $this->idiomArticles = new ArrayCollection();
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function __toString()
    {
        return $this->title ? $this->title : '';
    }

    /**
     * @return Collection<int, IdiomArticle>
     */
    public function getIdiomArticles(): Collection
    {
        return $this->idiomArticles;
    }

    public function addIdiomArticle(IdiomArticle $idiomArticle): static
    {
        if (!$this->idiomArticles->contains($idiomArticle)) {
            $this->idiomArticles->add($idiomArticle);
            $idiomArticle->setCategory($this);
        }

        return $this;
    }

    public function removeIdiomArticle(IdiomArticle $idiomArticle): static
    {
        if ($this->idiomArticles->removeElement($idiomArticle)) {
            // set the owning side to null (unless already changed)
            if ($idiomArticle->getCategory() === $this) {
                $idiomArticle->setCategory(null);
            }
        }

        return $this;
    }
}
