<?php

namespace App\Service;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Ifsnop\Mysqldump\Mysqldump;

/**
 * Class BackupService
 * 
 * BackupService creates a backup of the database in a Symfony application.
 * 
 * @method self                    save()            Creates an sql file in the backup folder
 * @method string|null             getFilename()     Get the backup filename if the file has been created
 * @method string                  getBackupFolder() Get the path to the backup folder
 * @method DateTimeImmutable|null  getDate()         Get the creation date of the backup
 * @method array                   getErrors()      Get errors
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class BackupService 
{
    private string  $backupFolder;
    private ?string $dbname = null;
    private string  $dsn;
    private string  $user;
    private string  $password;
    private ?string $filename = null;
    private ?DateTimeImmutable $date = null;
    private array   $errors = [];

    /**
     * Constructor of BackupService.
     *
     * @param EntityManagerInterface $entityManager
     * @param string                 $backupFolder
     */
    public function __construct(EntityManagerInterface $entityManager, string $backupFolder)
    { 
        $dbparams = $entityManager->getConnection()->getParams();
        $this->setBackupFolder($backupFolder);
        $this->setSettings($dbparams);
    }

    /**
     * Creates a backup file from the database.
     */
    public function save(): self
    {
        try {
            $this->setFilename();
            $dump = new Mysqldump($this->dsn,  $this->user, $this->password);
            $dump->start($this->backupFolder . DIRECTORY_SEPARATOR . $this->filename);
        } catch (\Exception $e) {
            $this->errors['save'] = ['mysqldump-php error: ' . $e->getMessage()];
        }

        return $this;
    }

    /**
     * Get the value of errors.
     */ 
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the Backup Folder path.
     */ 
    public function getBackupFolder(): string
    {
        return $this->backupFolder;
    }

    /**
     * Get the value of filename.
     */ 
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * Set the value of backupFolder.
     *
     * @return  self
     */ 
    private function setBackupFolder(string $backupFolder): self
    {
        if(!file_exists($backupFolder)) {
            mkdir($backupFolder, 777, true);
        }        
        $this->backupFolder = $backupFolder;

        return $this;
    }

    /**
     * Set the database settings.
     */
    private function setSettings(array $dbparams): void
    {
        try {
            $this->dbname  = $dbparams['dbname'];
            $this->dsn = "mysql:host=" . $dbparams['host'] . ':' . $dbparams['port'];
            $this->dsn .= ";dbname=" . $dbparams['dbname'];
            $this->user = $dbparams['user'];
            $this->password = $dbparams['password'];
        } catch (\Exception $e) {
            $this->errors['settings'] = 'settings error: ' . $e->getMessage();
        }
    }

    /**
     * Set the value of filename.
     */ 
    private function setFilename(): self
    {
        if ($this->dbname === null) {
            $this->errors['settings'] = 'settings error: the database name is invalid';
        } else {
            $this->date = new DateTimeImmutable();
            $this->filename = $this->dbname . '-' . $this->date->format('Y-m-d-H-i-s') . ".sql";
        }

        return $this;
    }

    /**
     * Get the value of date
     */ 
    public function getDate(): ?DateTimeImmutable
    {
        return $this->date;
    }
}