<?php

namespace App\Controller;

use App\Entity\Advice;
use App\Repository\AdviceRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class AdviceController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    #[Route('/api/conseil', name: 'advice_collection', methods: ['GET'])]
    public function getCurrentMonthAdvices(
        SerializerInterface $serializer,
        AdviceRepository $adviceRepository
    ): JsonResponse
    {
        $adviceList = $adviceRepository->getAdvicesOfTheMonth();
        $jsonAdviceList = $serializer->serialize($adviceList, 'json');

        return new JsonResponse($jsonAdviceList, Response::HTTP_OK, [], true);
    }

    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    #[Route('/api/conseil/{month}', name: 'advice_item', methods: ['GET'])]
    public function getAdvicesByMonth(SerializerInterface $serializer, AdviceRepository $adviceRepository, int $month): JsonResponse
    {
        $adviceList = $adviceRepository->getAdvicesByMonth($month);
        $jsonAdviceList = $serializer->serialize($adviceList, 'json');

        return new JsonResponse($jsonAdviceList, Response::HTTP_OK, [], true);
    }
}
