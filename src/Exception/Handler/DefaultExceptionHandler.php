<?php

namespace App\Exception\Handler;

class DefaultExceptionHandler implements ExceptionHandlerInterface
{

    public function supports(\Throwable $exception): bool
    {
        return true;
    }

    public function handle(\Throwable $exception): array
    {
        return [500, 'Erreur serveur'];
    }
}
