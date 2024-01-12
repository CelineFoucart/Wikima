<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PrivateMessageSentRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PrivateMessageSentRepository::class)]
class PrivateMessageSent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 15000)]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\ManyToOne(inversedBy: 'privateMessageSents', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'])]
    #[Assert\NotBlank]
    private ?User $addressee = null;

    #[ORM\OneToOne(inversedBy: 'privateMessageSent', cascade: ['persist', 'remove'])]
    private ?PrivateMessageReceived $privateMessageReceived = null;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    public function getAddressee(): ?User
    {
        return $this->addressee;
    }

    public function setAddressee(?User $addressee): static
    {
        $this->addressee = $addressee;

        return $this;
    }

    public function getPrivateMessageReceived(): ?PrivateMessageReceived
    {
        return $this->privateMessageReceived;
    }

    public function setPrivateMessageReceived(?PrivateMessageReceived $privateMessageReceived): static
    {
        $this->privateMessageReceived = $privateMessageReceived;

        return $this;
    }

    public function __toString()
    {
        return $this->title ? $this->title : 'Message privé';
    }
}
