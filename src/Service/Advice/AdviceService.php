<?php

namespace App\Service\Advice;

use App\Entity\Advice;
use App\Repository\AdviceRepository;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;

class AdviceService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AdviceRepository $adviceRepository,
        private readonly ValidatorService $validatorService
    ) {}

    public function getAdvicesForCurrentMonth(): array
    {
        return $this->adviceRepository->getAdvicesOfTheMonth();
    }

    public function getAdvicesByMonth(int $month): array
    {
        $this->validatorService->validateMonth($month);
        return $this->adviceRepository->getAdvicesByMonth($month);
    }

    public function createAdvice(Advice $advice): Advice
    {
        $this->validatorService->validateEntity($advice);
        $this->entityManager->persist($advice);
        $this->entityManager->flush();

        return $advice;
    }

    public function updateAdvice(Advice $advice): Advice
    {
        $this->validatorService->validateEntity($advice);
        $this->entityManager->flush();

        return $advice;
    }

    public function deleteAdvice(Advice $advice): void
    {
        $this->entityManager->remove($advice);
        $this->entityManager->flush();
    }
}
