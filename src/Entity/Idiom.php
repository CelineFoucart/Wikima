<?php

namespace App\Entity;

use App\Repository\IdiomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: IdiomRepository::class)]
#[UniqueEntity(fields: ['slug', 'translatedName'])]
class Idiom
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
    private ?string $translatedName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 255
    )]
    private ?string $originalName = null;

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

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $presentation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $banner = null;

    #[Vich\UploadableField(mapping:"upload_images", fileNameProperty:"banner")]
    private ?File $imageBanner = null;

    #[ORM\ManyToOne(inversedBy: 'idioms')]
    private ?User $author = null;

    #[ORM\ManyToMany(targetEntity: Portal::class, inversedBy: 'idioms')]
    private Collection $portals;

    #[ORM\ManyToOne(inversedBy: 'idioms')]
    private ?Article $article = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'idiom', targetEntity: IdiomArticle::class, orphanRemoval: true)]
    private Collection $idiomArticles;

    public function __construct()
    {
        $this->portals = new ArrayCollection();
        $this->idiomArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTranslatedName(): ?string
    {
        return $this->translatedName;
    }

    public function setTranslatedName(string $translatedName): static
    {
        $this->translatedName = $translatedName;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): static
    {
        $this->originalName = $originalName;

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

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): static
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): static
    {
        $this->banner = $banner;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Portal>
     */
    public function getPortals(): Collection
    {
        return $this->portals;
    }

    public function addPortal(Portal $portal): static
    {
        if (!$this->portals->contains($portal)) {
            $this->portals->add($portal);
        }

        return $this;
    }

    public function removePortal(Portal $portal): static
    {
        $this->portals->removeElement($portal);

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): static
    {
        $this->article = $article;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function setImageBanner(File $imageBanner = null): self
    {
        $this->imageBanner = $imageBanner;

        if ($imageBanner) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getImageBanner(): ?File
    {
        return $this->imageBanner;
    }

    public function __toString()
    {
        return $this->translatedName ? $this->translatedName : 'Nouvelle langue';
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
            $idiomArticle->setIdiom($this);
        }

        return $this;
    }

    public function removeIdiomArticle(IdiomArticle $idiomArticle): static
    {
        if ($this->idiomArticles->removeElement($idiomArticle)) {
            // set the owning side to null (unless already changed)
            if ($idiomArticle->getIdiom() === $this) {
                $idiomArticle->setIdiom(null);
            }
        }

        return $this;
    }
}
