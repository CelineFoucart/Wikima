<?php

namespace App\Entity;

use DateTimeImmutable;
use App\Repository\PrivateMessageReceivedRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrivateMessageReceivedRepository::class)]
class PrivateMessageReceived
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private $createdAt;

    #[ORM\ManyToOne(inversedBy: 'receivedPrivateMessages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $addressee = null;

    #[ORM\Column]
    private ?bool $readStatus = null;

    #[ORM\OneToOne(mappedBy: 'privateMessageReceived', cascade: ['persist', 'remove'])]
    private ?PrivateMessageSent $privateMessageSent = null;

    #[ORM\ManyToOne]
    private ?User $author = null;

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

    public function getAddressee(): ?User
    {
        return $this->addressee;
    }

    public function setAddressee(?User $addressee): static
    {
        $this->addressee = $addressee;

        return $this;
    }

    public function isReadStatus(): ?bool
    {
        return $this->readStatus;
    }

    public function setReadStatus(bool $readStatus): static
    {
        $this->readStatus = $readStatus;

        return $this;
    }

    public function getPrivateMessageSent(): ?PrivateMessageSent
    {
        return $this->privateMessageSent;
    }

    public function setPrivateMessageSent(?PrivateMessageSent $privateMessageSent): static
    {
        // unset the owning side of the relation if necessary
        if ($privateMessageSent === null && $this->privateMessageSent !== null) {
            $this->privateMessageSent->setPrivateMessageReceived(null);
        }

        // set the owning side of the relation if necessary
        if ($privateMessageSent !== null && $privateMessageSent->getPrivateMessageReceived() !== $this) {
            $privateMessageSent->setPrivateMessageReceived($this);
        }

        $this->privateMessageSent = $privateMessageSent;

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

    public function __toString()
    {
        return $this->title ? $this->title : 'Message privé';
    }
}
