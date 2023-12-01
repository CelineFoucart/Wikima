<?php

namespace App\EventSubscriber;

use App\Service\AccessService;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AccessSubscriber implements EventSubscriberInterface
{
    public function __construct(private AccessService $accessService, private TokenStorageInterface $tokenStorage)
    {
        
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $routeName = strtoupper($event->getRequest()->attributes->get('_route'));

        $routesWiki = array_keys($this->accessService->getAccessKeyWiki());
        $routeOther = array_keys($this->accessService->getAccessKeyOther());
        
        if (!in_array($routeName, array_merge($routeOther, $routesWiki))) {
            return;
        }

        if (!in_array($routeName, $this->accessService->getPublicAccess())) {

            $token = $this->tokenStorage->getToken();
            $currentUser = ($token) ? $token->getUser() : null;

            if (null === $currentUser) {
                throw new AccessDeniedException();
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
