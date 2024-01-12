<?php

namespace App\EventSubscriber;

use App\Entity\Episode;
use App\Entity\IdiomArticle;
use App\Entity\Section;
use App\Service\LogService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::postPersist, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::postUpdate, priority: 500, connection: 'default')]
#[AsDoctrineListener(event: Events::preRemove, priority: 500, connection: 'default')]
final class EntityDoctrineSubscriber
{
    public const NO_LOG = ['Log', 'Post', 'Topic', 'Note', 'ResetPasswordRequest', 'PrivateMessageReceived', 'PrivateMessageSent'];

    public function __construct(private LogService $logService)
    {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        $className = (new \ReflectionClass($entity))->getShortName();
        if (in_array($className, self::NO_LOG)) {
            return;
        }

        if ($entity instanceof Episode) {
            $parent = (string) $entity->getScenario().' ('.$entity->getScenario()->getId().')';
            $message = "Ajout d'un épisode au scénario ".$parent.' : '.(string) $entity.' ('.$entity->getId().')';
        } elseif ($entity instanceof Section) {
            $parent = (string) $entity->getArticle().' ('.$entity->getArticle()->getId().')';
            $message = "Ajout d'une partie à l'article ".$parent.' : '.(string) $entity.' ('.$entity->getId().')';
        } elseif ($entity instanceof IdiomArticle) {
            $parent = (string) $entity->getIdiom().' ('.$entity->getIdiom()->getId().')';
            $message = "Ajout d'un article à la la langue ".$parent.' : '.(string) $entity.' ('.$entity->getId().')';
        } else {
            $message = "Création d'un élément : ".(string) $entity.' ('.$entity->getId().')';
        }

        $this->logService->info('Création', $message, $className);
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        $className = (new \ReflectionClass($entity))->getShortName();
        if (in_array($className, self::NO_LOG)) {
            return;
        }

        $this->logService->info('Edition', "Edition d'un élément : ".(string) $entity.' ('.$entity->getId().')', $className);
    }

    public function preRemove(PreRemoveEventArgs $args): void 
    {
        $entity = $args->getObject();
        
        $className = (new \ReflectionClass($entity))->getShortName();
        if (in_array($className, self::NO_LOG)) {
            return;
        }

        $this->logService->info('Suppression', "Suppression d'un élément : ".(string) $entity.' ('.$entity->getId().')', $className);
    }
}
