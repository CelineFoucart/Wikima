<?php

namespace App\Entity;

use App\Repository\SectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['index'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['index'])]
    private $title;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $position;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['index'])]
    private $article;

    #[ORM\ManyToMany(targetEntity: Article::class, inversedBy: 'referencingSections')]
    #[ORM\OrderBy(['title' => 'ASC'])]
    private Collection $referencedArticles;

    public function __construct()
    {
        $this->referencedArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function __toString()
    {
        return $this->title ? $this->title : 'Section';
    }

    /**
     * @return Collection<int, Article>
     */
    public function getReferencedArticles(): Collection
    {
        return $this->referencedArticles;
    }

    public function addReferencedArticle(Article $referencedArticle): static
    {
        if (!$this->referencedArticles->contains($referencedArticle)) {
            $this->referencedArticles->add($referencedArticle);
        }

        return $this;
    }

    public function removeReferencedArticle(Article $referencedArticle): static
    {
        $this->referencedArticles->removeElement($referencedArticle);

        return $this;
    }
}
