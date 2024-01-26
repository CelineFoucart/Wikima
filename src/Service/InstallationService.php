<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

final class InstallationService
{
    public function __construct(
        private string $configFile, 
        private $projectDir, 
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepository $userRepository
    ) {
    }

    /**
     * Set the DATABASE_URL value in .env.local and create the database. 
     * If filename does not exist, the file is created. Otherwise, the existing file is overwritten.
     * 
     * @param array $data
     * 
     * @return void
     */
    public function setDatabaseURL(array $data): void
    {
        $user = $data['DB_USER'];
        $password = $data['DB_PASSWORD'];
        $host = $data['DB_HOST'];
        $port = $data['DB_PORT'];
        $dbname = $data['DB_NAME'];
        $serverVersion = $data['serverVersion'];
        $databaseURL = 'mysql://'.$user.':'.$password.'@'.$host.':'.$port.'/'.$dbname.'?serverVersion='.$serverVersion;
        file_put_contents($this->configFile, 'DATABASE_URL="'.$databaseURL.'"' . "\n");
    }

    public function execMigrations(KernelInterface $kernel): void
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $createDatabase = new ArrayInput(['command' => 'doctrine:database:create', '--if-not-exists' => true, '--no-interaction' => true]);
        $migrations = new ArrayInput(['command' => 'doctrine:migrations:migrate', '--no-interaction' => true]);
        $cacheClear = new ArrayInput(['command' => 'cache:clear', '--no-interaction' => true]);

        $application->run($createDatabase);
        $application->run($migrations);
        $application->run($cacheClear);
    }

    public function setAdmin(User $user, string $plainPassword): void
    {
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
        $user->setPassword(
        $this->userPasswordHasher->hashPassword(
                $user,
                $plainPassword
            )
        );

        $this->userRepository->add($user, true);
    }

    public function endInstallation(): void
    {
        file_put_contents($this->configFile, 'INSTALLATION=1' . "\n", FILE_APPEND);
    }

    public function isInstalled(): bool
    {
        return $_ENV['INSTALLATION'] === '1';
    }

    public function hasEnvVarFile(): bool
    {
        return file_exists($this->configFile);
    }
}
