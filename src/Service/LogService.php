<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Log;
use App\Entity\User;
use App\Repository\LogRepository;
use Symfony\Bundle\SecurityBundle\Security;

final class LogService
{
    public function __construct(private LogRepository $logRepository, private Security $security)
    {
        
    }

    public function info(string $action, string $message, string $object): static
    {
        $log = $this->createLog($action, $object, $message)->setLevel('INFO');
        $this->logRepository->add($log, true);

        return $this;
    }

    public function error(string $action, string $message, string $object = "Exception"): static
    {
        $log = $this->createLog($action, $object, $message)->setLevel('ERROR');
        $this->logRepository->add($log, true);
        
        return $this;
    }

    private function createLog(string $action, string $object, string $message): Log
    {

        $log = (new Log())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setAction($action)
            ->setObject($object)
            ->setMessage($message)
        ;

        $user = $this->security->getUser();
        if ($user instanceof User) {
            $log->setUsername($user->getUsername())->setUserid($user->getId());
        } else {
            $log->setUsername("Anonyme");
        }

        return $log;
    }
}
