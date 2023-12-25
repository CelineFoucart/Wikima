<?php

namespace App\EventSubscriber;

use App\Service\LogService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private LogService $logService)
    {
        
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $className = get_class($exception);
        $message = $exception->getMessage();
        $statusCode = ($exception instanceof HttpException) ? $exception->getStatusCode() : 500;
        $this->logService->error("Erreur $statusCode", $message, $className);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
