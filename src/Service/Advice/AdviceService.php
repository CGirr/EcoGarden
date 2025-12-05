<?php

namespace App\Service\Advice;

use App\Entity\Advice;
use App\Repository\AdviceRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

readonly class AdviceService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AdviceRepository $adviceRepository
    ) {}

    /**
     * @throws Exception
     */
    public function getAdvicesForCurrentMonth(): array
    {
        return $this->adviceRepository->getAdvicesOfTheMonth();
    }

    /**
     * @throws Exception
     */
    public function getAdvicesByMonth(int $month): array
    {
        return $this->adviceRepository->getAdvicesByMonth($month);
    }

    public function createAdvice(Advice $advice): Advice
    {
        $this->entityManager->persist($advice);
        $this->entityManager->flush();

        return $advice;
    }

    public function updateAdvice(Advice $advice): Advice
    {
        $this->entityManager->flush();

        return $advice;
    }

    public function deleteAdvice(Advice $advice): void
    {
        $this->entityManager->remove($advice);
        $this->entityManager->flush();
    }
}
