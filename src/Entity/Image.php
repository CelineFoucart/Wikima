<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[UniqueEntity('slug')]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(
        min: 2,
        max: 255
    )]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
    private $slug;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(
        min: 2,
        max: 255
    )]
    private $keywords;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(
        min: 10,
        max: 255
    )]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(
        min: 1,
        max: 255
    )]
    private $filename;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'images')]
    private $categories;

    #[ORM\ManyToMany(targetEntity: Portal::class, inversedBy: 'images')]
    private $portals;

    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'images')]
    private $articles;
    
    #[Vich\UploadableField(mapping:"upload_images", fileNameProperty:"filename")]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'image', targetEntity: Person::class)]
    private $people;

    #[ORM\OneToMany(mappedBy: 'illustration', targetEntity: Place::class)]
    private Collection $places;

    #[ORM\ManyToMany(targetEntity: IdiomArticle::class, mappedBy: 'images')]
    private Collection $idiomArticles;

    #[ORM\ManyToMany(targetEntity: ImageTag::class, inversedBy: 'images')]
    private Collection $tags;

    #[ORM\ManyToMany(targetEntity: ImageGroup::class, mappedBy: 'images')]
    private Collection $imageGroups;

    #[ORM\OneToMany(mappedBy: 'image', targetEntity: Map::class)]
    private Collection $maps;
    
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->portals = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->people = new ArrayCollection();
        $this->places = new ArrayCollection();
        $this->idiomArticles = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->imageGroups = new ArrayCollection();
        $this->maps = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function setCategories(Collection $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection<int, Portal>
     */
    public function getPortals(): Collection
    {
        return $this->portals;
    }

    public function addPortal(Portal $portal): self
    {
        if (!$this->portals->contains($portal)) {
            $this->portals[] = $portal;
        }

        return $this;
    }

    public function setPortals(Collection $portals): self
    {
        $this->portals = $portals;

        return $this;
    }

    public function removePortal(Portal $portal): self
    {
        $this->portals->removeElement($portal);

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->addImage($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            $article->removeImage($this);
        }

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

    public function setImageFile(File $image = null): self
    {
        $this->imageFile = $image;

        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * @return Collection<int, Person>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): self
    {
        if (!$this->people->contains($person)) {
            $this->people[] = $person;
            $person->setImage($this);
        }

        return $this;
    }

    public function removePerson(Person $person): self
    {
        if ($this->people->removeElement($person)) {
            // set the owning side to null (unless already changed)
            if ($person->getImage() === $this) {
                $person->setImage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Place>
     */
    public function getPlaces(): Collection
    {
        return $this->places;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->places->contains($place)) {
            $this->places->add($place);
            $place->setIllustration($this);
        }

        return $this;
    }

    public function removePlace(Place $place): self
    {
        if ($this->places->removeElement($place)) {
            // set the owning side to null (unless already changed)
            if ($place->getIllustration() === $this) {
                $place->setIllustration(null);
            }
        }

        return $this;
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
            $idiomArticle->addImage($this);
        }

        return $this;
    }

    public function removeIdiomArticle(IdiomArticle $idiomArticle): static
    {
        if ($this->idiomArticles->removeElement($idiomArticle)) {
            $idiomArticle->removeImage($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ImageTag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(ImageTag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(ImageTag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection<int, ImageGroup>
     */
    public function getImageGroups(): Collection
    {
        return $this->imageGroups;
    }

    public function addImageGroup(ImageGroup $imageGroup): static
    {
        if (!$this->imageGroups->contains($imageGroup)) {
            $this->imageGroups->add($imageGroup);
            $imageGroup->addImage($this);
        }

        return $this;
    }

    public function removeImageGroup(ImageGroup $imageGroup): static
    {
        if ($this->imageGroups->removeElement($imageGroup)) {
            $imageGroup->removeImage($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Map>
     */
    public function getMaps(): Collection
    {
        return $this->maps;
    }

    public function addMap(Map $map): static
    {
        if (!$this->maps->contains($map)) {
            $this->maps->add($map);
            $map->setImage($this);
        }

        return $this;
    }

    public function removeMap(Map $map): static
    {
        if ($this->maps->removeElement($map)) {
            // set the owning side to null (unless already changed)
            if ($map->getImage() === $this) {
                $map->setImage(null);
            }
        }

        return $this;
    }
}
