<?php

namespace App\Entity;

use App\Repository\ForumGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForumGroupRepository::class)]
class ForumGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[ORM\Column(length: 100)]
    private ?string $roleName = null;

    #[ORM\Column(length: 15, nullable: true)]
    private ?string $colour = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'forumGroups')]
    private Collection $members;

    #[ORM\ManyToMany(targetEntity: ForumCategory::class, mappedBy: 'groupAccess')]
    private Collection $forumCategories;

    #[ORM\ManyToMany(targetEntity: Forum::class, mappedBy: 'groupAccess')]
    private Collection $forums;

    #[ORM\Column]
    private ?bool $symfonyRole = null;

    public function __construct()
    {
        $this->members = new ArrayCollection();
        $this->forumCategories = new ArrayCollection();
        $this->forums = new ArrayCollection();
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
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): static
    {
        if (!$this->members->contains($member)) {
            $this->members->add($member);
        }

        return $this;
    }

    public function removeMember(User $member): static
    {
        $this->members->removeElement($member);

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
}
