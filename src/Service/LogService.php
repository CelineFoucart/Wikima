<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Log;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

final class LogService
{
    public function __construct(private ManagerRegistry $doctrine, private Security $security, private EntityManagerInterface $entityManager)
    {
    }

    public function info(string $action, string $message, string $object): static
    {
        $log = $this->createLog($action, $object, $message)->setLevel('INFO');
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return $this;
    }

    public function error(string $action, string $message, string $trace, string $object = 'Exception'): static
    {
        $log = $this->createLog($action, $object, $message)->setLevel('ERROR')->setTrace($trace);

        if (!$this->entityManager->isOpen()) {
            $this->doctrine->resetManager();
        }

        $this->entityManager->persist($log);
        $this->entityManager->flush();

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
            $log->setUsername('Anonyme');
        }

        return $log;
    }
}
