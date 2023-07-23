<?php

namespace App\Entity;

use App\Repository\TemplateGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TemplateGroupRepository::class)]
class TemplateGroup
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

    #[ORM\ManyToMany(targetEntity: Template::class, mappedBy: 'templateGroups')]
    private Collection $templates;

    public function __construct()
    {
        $this->templates = new ArrayCollection();
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
     * @return Collection<int, Template>
     */
    public function getTemplates(): Collection
    {
        return $this->templates;
    }

    public function addTemplate(Template $template): static
    {
        if (!$this->templates->contains($template)) {
            $this->templates->add($template);
            $template->addTemplateGroup($this);
        }

        return $this;
    }

    public function removeTemplate(Template $template): static
    {
        if ($this->templates->removeElement($template)) {
            $template->removeTemplateGroup($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title ? $this->title : "Nouveau groupe de modèles";
    }
}
