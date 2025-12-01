<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService
{
    public function __construct(private readonly ValidatorInterface $validator)
    {

    }

    public function validateMonth(int $month): int
    {
        if ($month < 1 || $month > 12) {
            throw new BadRequestException('Le mois doit être un entier entre 1 et 12');
        }

        return $month;
    }

    public function validateEntity(object $entity, ?array $groups = null): void
    {
        $errors = $this->validator->validate($entity, groups: $groups);

        if (count($errors) > 0) {
            $firstError = $errors[0]->getMessage();
            throw new BadRequestException($firstError);
        }
    }
}
