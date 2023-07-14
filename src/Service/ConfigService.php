<?php

namespace App\Service;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ConfigService
{
    private array $envVars = [
        'WIKI_NAME' => 'Wikima',
        'WIKI_DESCRIPTION' => "Une encyclopédie complète pour présenter un univers de fiction et ses personnages",
        'CONTACT_EMAIL' => '',
        'CONTACT_NAME' => '',
        'ENABLE_REGISTRATION' => true,
        'ENABLE_CONTACT' => true,
        'SMTP_USER' => '',
        'SMTP_PASSWORD' => '',
        'SMTP_HOST' => '',
        'SMTP_PORT' => 587,
        'APP_ENV' => 'prod',
        'WIKI_FAVICON' => 'favicon.png',
        'WIKI_BANNER' => 'banner.png',
        'PER_PAGE_ODD_COLUMNS' => 30,
        'PER_PAGE_EVEN_COLUMNS' => 20,
        'BACKGROUND_COLOR' => "#f5f7fa80",
        'DATE_FORMAT' => "d/m/Y à H:i"
    ];

    private bool $appendTo = false;

    public function __construct(private string $configFile, private string $publicDir)
    {
        $this->hydrateEnvVars();
        $this->hydrateDsnParams();
    }

    public function save(array $newData): bool 
    {
        if (!$this->hasConfigFile()) {
            return false;
        }

        $keys = [
            'WIKI_NAME',
            'WIKI_DESCRIPTION',
            'CONTACT_EMAIL',
            'CONTACT_NAME',
            'APP_ENV',
            'WIKI_FAVICON',
            'WIKI_BANNER',
            'PER_PAGE_ODD_COLUMNS',
            'PER_PAGE_EVEN_COLUMNS',
            'BACKGROUND_COLOR',
            'DATE_FORMAT'
        ];

        foreach ($keys as $key) {
            $this->setEnv($key, $newData[$key]);
        }

        if (isset($newData['ENABLE_REGISTRATION']) && true === $newData['ENABLE_REGISTRATION']) {
            $this->setEnv('ENABLE_REGISTRATION', 1);
        } else {
            $this->setEnv('ENABLE_REGISTRATION', 0);
        }

        if (isset($newData['ENABLE_CONTACT']) && true === $newData['ENABLE_CONTACT']) {
            $this->setEnv('ENABLE_CONTACT', 1);
        }else {
            $this->setEnv('ENABLE_CONTACT', 0);
        }

        $smtpUser = isset($newData['SMTP_USER']) ? $newData['SMTP_USER'] : '';
        $smtpPassword = isset($newData['SMTP_PASSWORD']) ? $newData['SMTP_PASSWORD'] : '';
        $smtpHost = isset($newData['SMTP_HOST']) ? $newData['SMTP_HOST'] : '';
        $smtpPort = isset($newData['SMTP_PORT']) ? $newData['SMTP_PORT'] : 587;

        if (strlen($smtpUser) <= 1 || strlen($smtpPassword) <= 1 || strlen($smtpHost) <= 1 || strlen($smtpPort) <= 1) {
            return true;
        }

        $mailerDSN = "smtp://{$smtpUser}:{$smtpPassword}@{$smtpHost}:{$smtpPort}";
        $this->setEnv('MAILER_DSN', $mailerDSN);

        return true;
    }

    /**
     * Gets the value of envVars
     *
     * @return array
     */
    public function getEnvVars(): array
    {
        return $this->envVars;
    }

    /**
     * Get the value of publicDir
     */
    public function getPublicDir()
    {
        return $this->publicDir;
    }

    /**
     * Sets the value of envVars
     *
     * @param array $envVars
     *
     * @return self
     */
    public function setEnvVars(array $envVars): self
    {
        $this->envVars = $envVars;

        return $this;
    }

    /**
     * Hydrates envVars
     *
     * @return self
     */
    public function hydrateEnvVars(): self
    {
        $validKeys = array_keys($this->envVars);

        foreach ($validKeys as $key) {
            if (isset($_ENV[$key])) {
                $this->envVars[$key] = $_ENV[$key];
            }
        }

        $this->envVars['PER_PAGE_ODD_COLUMNS'] = isset($_ENV['PER_PAGE_ODD_COLUMNS']) ? (int) $_ENV['PER_PAGE_ODD_COLUMNS'] : 30;
        $this->envVars['PER_PAGE_EVEN_COLUMNS'] = isset($_ENV['PER_PAGE_EVEN_COLUMNS']) ? (int) $_ENV['PER_PAGE_EVEN_COLUMNS'] : 20;

        $this->envVars['ENABLE_REGISTRATION'] = isset($_ENV['ENABLE_REGISTRATION']) ? (bool) $_ENV['ENABLE_REGISTRATION'] : true;
        $this->envVars['ENABLE_CONTACT'] = isset($_ENV['ENABLE_CONTACT']) ? (bool) $_ENV['ENABLE_CONTACT'] : true;

        return $this;
    }

    public function hydrateDsnParams(): self
    {
        if (!isset($_ENV['MAILER_DSN'])) {
            return $this;
        }
        
        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);

        if (!$transport instanceof EsmtpTransport) {
            return $this;
        }
        $this->envVars['SMTP_USER'] = $transport->getUsername();
        $this->envVars['SMTP_PASSWORD'] = $transport->getPassword();

        $stream = $transport->getStream();

        if ($stream instanceof SocketStream) {
            $this->envVars['SMTP_HOST'] = $stream->getHost();
            $this->envVars['SMTP_PORT'] = $stream->getPort();
        }

        return $this;
    }

    /**
     * Move an uploaded file to a directory in the server.
     *
     * @param  UploadedFile  $file   the file to move
     * @param  string        $name   the name of the file
     */
    public function move(UploadedFile $file, string $name): bool
    {
        try {
            $path = $this->publicDir . '/img';
            $file->move($path, $name);

            return true;
        } catch (FileException $th) {
            return false;
        }
    }

    public function hasConfigFile(): bool
    {
        return file_exists($this->configFile);
    }
    

    private function setEnv(string $key, mixed $value): bool
    {
        $notStringValues = ['MAILER_DSN', 'APP_ENV', 'ENABLE_REGISTRATION','ENABLE_CONTACT', 'PER_PAGE_ODD_COLUMNS', 'PER_PAGE_EVEN_COLUMNS'];
        $parts = (is_string($_ENV[$key]) && !in_array($key, $notStringValues)) ? '"' : '';
        $search = $_ENV[$key];

        if ($key === 'MAILER_DSN') {
            if (preg_match('/null:\/\/default/', file_get_contents($this->configFile))) {
                $search = "null://default";
            }
        }

        if ($this->appendTo) {
            $status = file_put_contents($this->configFile, $key . '=' . $parts . $value . $parts . "\n", FILE_APPEND);
        } else {
            $status = file_put_contents($this->configFile, str_replace(
                $key . '=' . $parts . $search . $parts,
                $key . '=' . $parts . $value . $parts,
                file_get_contents($this->configFile)
            ));
        }

        return is_int($status);
    }


    /**
     * Set the value of appendTo
     *
     * @param bool $appendTo
     *
     * @return self
     */
    public function setAppendTo(bool $appendTo): self
    {
        $this->appendTo = $appendTo;

        return $this;
    }
}
