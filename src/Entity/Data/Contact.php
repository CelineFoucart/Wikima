<?php

declare(strict_types=1);

namespace App\Entity\Data;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 150
    )]
    private ?string $username = null;

    #[Assert\NotBlank]
    #[Assert\Email()]
    private ?string $email = null;
    
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 150
    )]
    private ?string $subject = null;
    
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 10,
    )]
    private ?string $content = null;

     /**
     * Get the value of username
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of subject
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * Set the value of subject
     *
     * @return  self
     */
    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the value of content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @return  self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}