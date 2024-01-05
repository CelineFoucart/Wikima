<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Log;
use App\Entity\User;
use App\Repository\LogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\ManagerRegistry;

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

    public function error(string $action, string $message, string $object = "Exception"): static
    {
        $log = $this->createLog($action, $object, $message)->setLevel('ERROR');
        
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
            $log->setUsername("Anonyme");
        }

        return $log;
    }
}
