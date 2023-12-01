<?php

namespace App\Entity;

use App\Repository\ForumGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ForumGroupRepository::class)]
#[UniqueEntity('roleName')]
class ForumGroup
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
    #[Assert\Length(
        min: 3,
        max: 100,
    )]
    private ?string $roleName = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\Length(
        min: 4,
        max: 10,
    )]
    private ?string $colour = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 255,
    )]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: ForumCategory::class, mappedBy: 'groupAccess', cascade: ['persist', 'remove'])]
    private Collection $forumCategories;

    #[ORM\ManyToMany(targetEntity: Forum::class, mappedBy: 'groupAccess')]
    private Collection $forums;

    #[ORM\Column]
    private ?bool $symfonyRole = null;

    #[ORM\OneToMany(mappedBy: 'forumGroup', targetEntity: UserGroup::class, orphanRemoval: true)]
    private Collection $userGroups;

    public function __construct()
    {
        $this->forumCategories = new ArrayCollection();
        $this->forums = new ArrayCollection();
        $this->symfonyRole = false;
        $this->userGroups = new ArrayCollection();
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

    public function getRoleName(): ?string
    {
        return $this->roleName;
    }

    public function setRoleName(string $roleName): static
    {
        $this->roleName = $roleName;

        return $this;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function setColour(?string $colour): static
    {
        $this->colour = $colour;

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
     * @return Collection<int, ForumCategory>
     */
    public function getForumCategories(): Collection
    {
        return $this->forumCategories;
    }

    public function addForumCategory(ForumCategory $forumCategory): static
    {
        if (!$this->forumCategories->contains($forumCategory)) {
            $this->forumCategories->add($forumCategory);
            $forumCategory->addGroupAccess($this);
        }

        return $this;
    }

    public function removeForumCategory(ForumCategory $forumCategory): static
    {
        if ($this->forumCategories->removeElement($forumCategory)) {
            $forumCategory->removeGroupAccess($this);
        }

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
            $forum->addGroupAccess($this);
        }

        return $this;
    }

    public function removeForum(Forum $forum): static
    {
        if ($this->forums->removeElement($forum)) {
            $forum->removeGroupAccess($this);
        }

        return $this;
    }

    public function isSymfonyRole(): ?bool
    {
        return $this->symfonyRole;
    }

    public function setSymfonyRole(bool $symfonyRole): static
    {
        $this->symfonyRole = $symfonyRole;

        return $this;
    }

    public function __toString()
    {
        return $this->title ? $this->title : 'Nouveau groupe';
    }

    /**
     * @return Collection<int, UserGroup>
     */
    public function getUserGroups(): Collection
    {
        return $this->userGroups;
    }

    public function addUserGroup(UserGroup $userGroup): static
    {
        if (!$this->userGroups->contains($userGroup)) {
            $this->userGroups->add($userGroup);
            $userGroup->setForumGroup($this);
        }

        return $this;
    }

    public function removeUserGroup(UserGroup $userGroup): static
    {
        if ($this->userGroups->removeElement($userGroup)) {
            // set the owning side to null (unless already changed)
            if ($userGroup->getForumGroup() === $this) {
                $userGroup->setForumGroup(null);
            }
        }

        return $this;
    }
}
