<?php

namespace App\Entity;

use App\Repository\UserGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserGroupRepository::class)]
class UserGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $member = null;

    #[ORM\ManyToOne(inversedBy: 'userGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ForumGroup $forumGroup = null;

    #[ORM\Column]
    private ?bool $defaultGroup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function setMember(?User $member): static
    {
        $this->member = $member;

        return $this;
    }

    public function getForumGroup(): ?ForumGroup
    {
        return $this->forumGroup;
    }

    public function setForumGroup(?ForumGroup $forumGroup): static
    {
        $this->forumGroup = $forumGroup;

        return $this;
    }

    public function isDefaultGroup(): ?bool
    {
        return $this->defaultGroup;
    }

    public function setDefaultGroup(bool $defaultGroup): static
    {
        $this->defaultGroup = $defaultGroup;

        return $this;
    }
}
