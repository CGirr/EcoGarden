<?php

namespace App\Exception\Handler;

use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpExceptionHandler implements ExceptionHandlerInterface
{

    public function supports(\Throwable $exception): bool
    {
        return $exception instanceof HttpException;
    }

    public function handle(\Throwable $exception): array
    {
        return [
            $exception->getStatusCode(),
            $exception->getMessage() ?: 'Erreur HTTP'
        ];
    }
}
