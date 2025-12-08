<?php

namespace App\Exception\Handler;

interface ExceptionHandlerInterface
{
    public function supports(\Throwable $exception): bool;
    public function handle(\Throwable $exception): array;
}

