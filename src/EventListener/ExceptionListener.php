<?php

namespace App\EventListener;

use App\Service\Weather\WeatherException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ExceptionListener
{
    #[AsEventListener]
    public function onExceptionEvent(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof WeatherException) {
            $status = $exception->getStatusCode();
            $message = match ($status) {
                404 => 'Ville non trouvée',
                401 => 'Clé API invalide',
                503 => 'Service météo indisponible',
                default => $exception->getMessage() ?: 'Erreur météo',
            };
        } elseif ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
            $message = $exception->getMessage() ?: 'Erreur HTTP';
        } elseif ($exception instanceof \TypeError) {
            $status = 400;
            $message = 'Mauvais type de paramètre : ' . $exception->getMessage();
        } else {
            $status = 500;
            $message = 'Erreur serveur';
        }

        $event->setResponse(new JsonResponse([
            'status' => $status,
            'message' => $message,
        ], $status));
    }
}
