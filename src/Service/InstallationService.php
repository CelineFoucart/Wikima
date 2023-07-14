<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

        shell_exec("php ../bin/console doctrine:database:create --if-not-exists --no-interaction");
        shell_exec("php ../bin/console doctrine:migrations:migrate --no-interaction");
        shell_exec("php ../bin/console cache:clear --no-interaction");
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
        file_put_contents($this->configFile, 'INSTALLATION=1', FILE_APPEND);
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
