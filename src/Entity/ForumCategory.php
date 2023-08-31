<?php

namespace App\Entity;

use App\Repository\ForumCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ForumCategoryRepository::class)]
#[UniqueEntity('slug')]
class ForumCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 50,
    )]
    private ?string $title = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
    private ?string $slug = null;

    #[ORM\Column(length: 300, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 300,
    )]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Forum::class, orphanRemoval: true)]
    private Collection $forums;

    #[ORM\ManyToMany(targetEntity: ForumGroup::class, inversedBy: 'forumCategories')]
    private Collection $groupAccess;

    public function __construct()
    {
        $this->forums = new ArrayCollection();
        $this->groupAccess = new ArrayCollection();
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

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return Collection<int, Forum>
     */
    public function getForums(): Collection
    {
        return $this->forums;
    }

    public function addForum(Forum $forum): static
    {
        if (!$this->forums->contains($forum)) {
            $this->forums->add($forum);
            $forum->setCategory($this);
        }

        return $this;
    }

    public function removeForum(Forum $forum): static
    {
        if ($this->forums->removeElement($forum)) {
            // set the owning side to null (unless already changed)
            if ($forum->getCategory() === $this) {
                $forum->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ForumGroup>
     */
    public function getGroupAccess(): Collection
    {
        return $this->groupAccess;
    }

    public function addGroupAccess(ForumGroup $groupAccess): static
    {
        if (!$this->groupAccess->contains($groupAccess)) {
            $this->groupAccess->add($groupAccess);
        }

        return $this;
    }

    public function removeGroupAccess(ForumGroup $groupAccess): static
    {
        $this->groupAccess->removeElement($groupAccess);

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }
}
