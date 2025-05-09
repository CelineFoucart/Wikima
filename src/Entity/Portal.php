<?php

namespace App\Entity;

use App\Repository\PortalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: PortalRepository::class)]
#[UniqueEntity('slug')]
class Portal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['timeline:show', 'media:index', 'portal:index'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length( min: 3, max: 255)]
    #[Groups(['timeline:show', 'media:index', 'portal:index'])]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
    #[Groups(['timeline:show', 'media:index', 'portal:index'])]
    private $slug;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['portal:index'])]
    private $keywords;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[Groups(['timeline:show', 'media:index', 'portal:index'])]
    private $description;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['portal:index'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['portal:index'])]
    private $updatedAt;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'portals')]
    #[Groups(['portal:index'])]
    private $categories;

    #[ORM\ManyToMany(targetEntity: Article::class, mappedBy: 'portals')]
    #[ORM\OrderBy(['title' => 'ASC'])]
    private $articles;

    #[ORM\ManyToMany(targetEntity: Image::class, mappedBy: 'portals')]
    private $images;

    #[ORM\ManyToMany(targetEntity: Page::class, mappedBy: 'portals')]
    private $pages;

    #[ORM\ManyToMany(targetEntity: Timeline::class, mappedBy: 'portals')]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private $timelines;

    #[ORM\ManyToMany(targetEntity: Person::class, mappedBy: 'portals')]
    #[ORM\OrderBy(['firstname' => 'ASC'])]
    private $people;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['portal:index'])]
    private ?string $presentation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $banner = null;

    #[Vich\UploadableField(mapping: 'upload_images', fileNameProperty: 'banner')]
    private ?File $imageBanner = null;

    #[ORM\OneToMany(mappedBy: 'portal', targetEntity: Note::class)]
    private Collection $notes;

    #[ORM\ManyToMany(targetEntity: Place::class, mappedBy: 'portals')]
    #[ORM\OrderBy(['title' => 'ASC'])]
    private Collection $places;

    #[ORM\Column(nullable: true)]
    #[Groups(['portal:index'])]
    private ?int $position = null;

    #[ORM\ManyToMany(targetEntity: Idiom::class, mappedBy: 'portals')]
    private Collection $idioms;

    #[ORM\ManyToMany(targetEntity: Scenario::class, mappedBy: 'portals')]
    private Collection $scenarios;

    #[ORM\ManyToMany(targetEntity: ImageGroup::class, mappedBy: 'portals')]
    private Collection $imageGroups;

    #[ORM\ManyToMany(targetEntity: Map::class, mappedBy: 'portals')]
    private Collection $maps;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->pages = new ArrayCollection();
        $this->timelines = new ArrayCollection();
        $this->people = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->places = new ArrayCollection();
        $this->idioms = new ArrayCollection();
        $this->scenarios = new ArrayCollection();
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        if ($createdAt instanceof \DateTimeImmutable) {
            $this->createdAt = $createdAt;
        } else {
            $this->createdAt = new \DateTimeImmutable($createdAt->format('Y-m-d H:i:s'));
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
            $article->addPortal($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            $article->removePortal($this);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->addPortal($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            $image->removePortal($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Page>
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->addPortal($this);
        }

        return $this;
    }

    public function removePage(Page $page): self
    {
        if ($this->pages->removeElement($page)) {
            $page->removePortal($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Timeline>
     */
    public function getTimelines(): Collection
    {
        return $this->timelines;
    }

    public function addTimeline(Timeline $timeline): self
    {
        if (!$this->timelines->contains($timeline)) {
            $this->timelines[] = $timeline;
            $timeline->addPortal($this);
        }

        return $this;
    }

    public function removeTimeline(Timeline $timeline): self
    {
        if ($this->timelines->removeElement($timeline)) {
            $timeline->removePortal($this);
        }

        return $this;
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
            $person->addPortal($this);
        }

        return $this;
    }

    public function removePerson(Person $person): self
    {
        if ($this->people->removeElement($person)) {
            $person->removePortal($this);
        }

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): self
    {
        $this->banner = $banner;

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

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setPortal($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getPortal() === $this) {
                $note->setPortal(null);
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
            $place->addPortal($this);
        }

        return $this;
    }

    public function removePlace(Place $place): self
    {
        if ($this->places->removeElement($place)) {
            $place->removePortal($this);
        }

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

    /**
     * @return Collection<int, Idiom>
     */
    public function getIdioms(): Collection
    {
        return $this->idioms;
    }

    public function addIdiom(Idiom $idiom): static
    {
        if (!$this->idioms->contains($idiom)) {
            $this->idioms->add($idiom);
            $idiom->addPortal($this);
        }

        return $this;
    }

    public function removeIdiom(Idiom $idiom): static
    {
        if ($this->idioms->removeElement($idiom)) {
            $idiom->removePortal($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Scenario>
     */
    public function getScenarios(): Collection
    {
        return $this->scenarios;
    }

    public function addScenario(Scenario $scenario): static
    {
        if (!$this->scenarios->contains($scenario)) {
            $this->scenarios->add($scenario);
            $scenario->addPortal($this);
        }

        return $this;
    }

    public function removeScenario(Scenario $scenario): static
    {
        if ($this->scenarios->removeElement($scenario)) {
            $scenario->removePortal($this);
        }

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
            $imageGroup->addPortal($this);
        }

        return $this;
    }

    public function removeImageGroup(ImageGroup $imageGroup): static
    {
        if ($this->imageGroups->removeElement($imageGroup)) {
            $imageGroup->removePortal($this);
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
            $map->addPortal($this);
        }

        return $this;
    }

    public function removeMap(Map $map): static
    {
        if ($this->maps->removeElement($map)) {
            $map->removePortal($this);
        }

        return $this;
    }
}
