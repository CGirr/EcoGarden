<?php

namespace App\EventListener;

use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

final class ExceptionListener
{
    private iterable $handlers;

    public function __construct(
        #[TaggedIterator('app.exception_handler')] iterable $handlers,
    ) {
        $this->handlers = $handlers;
    }

    #[AsEventListener]
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $status = 500;
        $message = 'Erreur serveur';

        foreach ($this->handlers as $handler) {
            if ($handler->supports($exception)) {
                [$status, $message] = $handler->handle($exception);
                break;
            }
        }

        $event->setResponse(new JsonResponse([
            'status' => $status,
            'message' => $message,
        ], $status));
    }
}
