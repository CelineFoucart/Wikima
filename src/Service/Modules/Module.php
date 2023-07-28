<?php

namespace App\Service\Modules;

/**
 * Class Module represents a module of the application
 * for the module settings page.
 */
class Module
{
    public function __construct(
        private string $id,
        private string $title,
        private string $icon,
        private string $description,
        private bool $status = true
    )  {
    }

    /**
     * Get the value of id
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the value of title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the value of icon
     *
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Get the value of description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }


        /**
         * Set the value of status
         *
         * @param bool $status
         *
         * @return self
         */
        public function setStatus(bool $status): self
        {
            $this->status = $status;

            return $this;
        }

        /**
         * Get the value of status
         *
         * @return bool
         */
        public function getStatus(): bool
        {
            return $this->status;
        }
}