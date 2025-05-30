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
        $statusCode = ($exception instanceof HttpException) ? $exception->getStatusCode() : 500;
        $className = (new \ReflectionClass($exception))->getShortName();
        $message = $exception->getMessage();
        $trace = $exception->getTraceAsString();
        $this->logService->error("Erreur $statusCode", $message, $trace, $className);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
