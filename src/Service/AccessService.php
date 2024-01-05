<?php

namespace App\Service;

use App\Service\LogService;

final class AccessService
{
    private array $accessKeyWiki = [
        'APP_CATEGORY_INDEX' => "Liste des catégories",
        'APP_CATEGORY_SHOW' => "Page de détail d'une catégorie",
        'APP_PORTAL_INDEX' => "Liste des portails",
        'APP_PORTAL_SHOW' => "Page de détail d'un portail",
        'APP_ARTICLE_INDEX' => "Liste des articles",
        'APP_ARTICLE_SHOW' => "Page de détail d'un article",
        'APP_ARTICLETYPE_INDEX' => "Liste des types d'article",
        'APP_ARTICLETYPE_SHOW' => "Page de détail d'un type d'article",
        'APP_TIMELINE_INDEX' => "Liste des chronologies",
        'APP_TIMELINE_SHOW' => "Page de détail d'une chronologie",
    ];

    private array $accessPersons = [
        'APP_PERSON_INDEX' => "Liste des personnages",
        'APP_PERSON_SHOW' => "Page de détail d'un personnage",
        'APP_PERSON_TYPE' => "Page de détail d'un type de personnage",
    ];

    private array $accessPlaces = [
        'APP_PLACE_INDEX' => "Liste des lieux",
        'APP_PLACE_SHOW' => "Page de détail d'un lieu",
        'APP_PLACE_TYPE' => "Page de détail d'un type de lieu",
        'APP_MAP_INDEX' => "Liste des cartes",
        'APP_MAP_SHOW' => "Page de détail d'une carte",
    ];

    private array $accessImages = [
        'APP_IMAGE_INDEX' => "Liste des images",
        'APP_IMAGE_SHOW' => "Page de détail d'une image",
        'APP_IMAGE_TYPE' => "Page de détail d'un type d'image",
        'APP_IMAGE_GROUP_INDEX' => "Liste des groupes d'images",
        'APP_IMAGE_GROUP_SHOW' => "Page de détail d'un groupe d'images",
    ];

    private array $accessKeyOther = [
        'APP_USER_INDEX' => "Liste des utilisateurs",
        'APP_USER_SHOW' => "Profil public d'un utilisateur",
        'APP_FORUM_INDEX' => "Index du forum",
        'APP_PAGE' => "Page créée par un administrateur",
        'APP_IDIOM_INDEX' => "Liste des langues",
        'APP_IDIOM_SHOW' => "Page de détail d'une langue",
        'APP_SEARCH' => "Page de recherche globale",
        'APP_HOME' => "Page d'accueil",
    ];

    private array $publicAccess = [];

    private string $envVarName = "PUBLIC_ACCESS";

    private ?string $error = null;

    public function __construct(private string $configFile, private LogService $logService)
    {
        if (isset($_ENV[$this->envVarName])) {
            $this->publicAccess = explode(',', $_ENV[$this->envVarName]);
        }
    }

    /**
     * Get the value of accessKeyWiki
     *
     * @return array
     */
    public function getAccessKeyWiki(): array
    {
        return $this->accessKeyWiki;
    }

    /**
     * Get the value of accessImages
     *
     * @return array
     */
    public function getAccessImages(): array
    {
        return $this->accessImages;
    }

    /**
     * Set the value of accessImages
     *
     * @param array $accessImages
     *
     * @return self
     */
    public function setAccessImages(array $accessImages): self
    {
        $this->accessImages = $accessImages;

        return $this;
    }

    /**
     * Get the value of accessKeyOther
     *
     * @return array
     */
    public function getAccessKeyOther(): array
    {
        return $this->accessKeyOther;
    }

    /**
     * Get the value of publicAccess
     *
     * @return array
     */
    public function getPublicAccess(): array
    {
        return $this->publicAccess;
    }


    /**
     * Set the value of publicAccess
     *
     * @param array $publicAccess
     *
     * @return self
     */
    public function setPublicAccess(array $publicAccess): self
    {
        $this->publicAccess = $publicAccess;

        return $this;
    }

    /**
     * Get the value of error
     *
     * @return ?string
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Get the value of accessPersons
     *
     * @return array
     */
    public function getAccessPersons(): array
    {
        return $this->accessPersons;
    }

    /**
     * Set the value of accessPersons
     *
     * @param array $accessPersons
     *
     * @return self
     */
    public function setAccessPersons(array $accessPersons): self
    {
        $this->accessPersons = $accessPersons;

        return $this;
    }

    /**
     * Get the value of accessPlaces
     *
     * @return array
     */
    public function getAccessPlaces(): array
    {
        return $this->accessPlaces;
    }

    /**
     * Set the value of accessPlaces
     *
     * @param array $accessPlaces
     *
     * @return self
     */
    public function setAccessPlaces(array $accessPlaces): self
    {
        $this->accessPlaces = $accessPlaces;

        return $this;
    }

    public function persist(): bool
    {
        try {
            $replacement = $this->envVarName . '="' . join(',', $this->publicAccess) . '"';
            $fileContent = file_get_contents($this->configFile);
            $search = $this->envVarName . '=\"([A-Z_,]+)\"';

            if (preg_match('/'. $search . '/', $fileContent)) {
                return (bool) file_put_contents($this->configFile, preg_replace('/'. $search . '/', $replacement, $fileContent));
            } else {
                return (bool) file_put_contents($this->configFile, $replacement . "\n", FILE_APPEND);
            }
        } catch (\Exception $th) {
            $this->error = $th->getMessage();
            $this->logService->error('Modification des accès', $th->getMessage(), 'Exception');

            return false;
        }
    }
}
