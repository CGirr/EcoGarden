<?php

namespace App\Exception\Handler;

use App\Exception\WeatherException;

class WeatherExceptionHandler implements ExceptionHandlerInterface
{

    public function supports(\Throwable $exception): bool
    {
        return $exception instanceof WeatherException;
    }

    public function handle(\Throwable $exception): array
    {
        $status = $exception->getStatusCode();
        $message = match ($status) {
            404 => 'Ville non trouvée',
            401 => 'Clé API invalide',
            503 => 'Service météo indisponible',
            default => $exception->getMessage() ?: 'Erreur météo',
        };

        return [$status, $message];
    }
}
