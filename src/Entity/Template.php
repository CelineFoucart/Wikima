<?php

namespace App\Entity;

use App\Repository\TemplateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['index'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 3,
        max: 255
    )]
    #[Groups(['index'])]
    private ?string $title = null;

    #[ORM\Column(length: 2000, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 2000
    )]
    #[Groups(['index'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 1
    )]
    #[Groups(['index'])]
    private ?string $content = null;

    #[ORM\ManyToMany(targetEntity: TemplateGroup::class, inversedBy: 'templates')]
    private Collection $templateGroups;

    public function __construct()
    {
        $this->templateGroups = new ArrayCollection();
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
    public function getTemplateGroups(): Collection
    {
        return $this->templateGroups;
    }

    public function addTemplateGroup(TemplateGroup $templateGroup): static
    {
        if (!$this->templateGroups->contains($templateGroup)) {
            $this->templateGroups->add($templateGroup);
        }

        return $this;
    }

    public function removeTemplateGroup(TemplateGroup $templateGroup): static
    {
        $this->templateGroups->removeElement($templateGroup);

        return $this;
    }

    public function __toString()
    {
        return $this->title ? $this->title : "Nouveau modèle";
    }
}
