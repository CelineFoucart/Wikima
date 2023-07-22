<?php

namespace App\Entity;

use App\Repository\TemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
class Template
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

    #[ORM\Column(length: 2000, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 2000
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 20
    )]
    private ?string $content = null;

    #[ORM\ManyToMany(targetEntity: TemplateGroup::class, inversedBy: 'templates')]
    private Collection $templateGroup;

    public function __construct()
    {
        $this->templateGroup = new ArrayCollection();
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, TemplateGroup>
     */
    public function getTemplateGroup(): Collection
    {
        return $this->templateGroup;
    }

    public function addTemplateGroup(TemplateGroup $templateGroup): static
    {
        if (!$this->templateGroup->contains($templateGroup)) {
            $this->templateGroup->add($templateGroup);
        }

        return $this;
    }

    public function removeTemplateGroup(TemplateGroup $templateGroup): static
    {
        $this->templateGroup->removeElement($templateGroup);

        return $this;
    }
}
