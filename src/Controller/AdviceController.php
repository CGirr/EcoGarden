<?php

namespace App\Controller;

use App\Entity\Advice;
use App\Repository\AdviceRepository;
use App\Service\ValidatorService;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final class AdviceController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
        private readonly AdviceRepository $adviceRepository,
        private readonly ValidatorService $validatorService
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/api/advice', name: 'advice_collection', methods: ['GET'])]
    public function getCurrentMonthAdvices(): JsonResponse
    {
        $adviceList = $this->adviceRepository->getAdvicesOfTheMonth();

        return $this->json($adviceList);
    }

    /**
     * @throws Exception
     */
    #[Route('/api/advice/{month}', name: 'advice_item', requirements: ['month' => '\d+'], methods: ['GET'])]
    public function getAdvicesByMonth(int $month): JsonResponse
    {
        $month = $this->validatorService->validateMonth($month);
        $adviceList = $this->adviceRepository->getAdvicesByMonth($month);

        return $this->json($adviceList);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/advice', name: 'create_advice', methods: ['POST'])]
    #[isGranted('ROLE_ADMIN')]
    public function createAdvice(Request $request) : JsonResponse
    {
        $advice = $this->serializer->deserialize($request->getContent(), Advice::class, 'json');

        $this->validatorService->validateEntity($advice);
        $this->entityManager->persist($advice);
        $this->entityManager->flush();

        return $this->json($advice, Response::HTTP_CREATED);
    }

    #[Route('/api/advice/{id}', name: 'edit_advice', requirements: ['id' => '\d+'], methods: ['PUT'])]
    #[isGranted('ROLE_ADMIN')]
    public function editAdvice(Request $request, Advice $currentAdvice) : JsonResponse
    {
       $updatedAdvice = $this->serializer->deserialize(
           $request->getContent(),
           Advice::class,
           'json',
        [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAdvice]
       );

       $this->validatorService->validateEntity($updatedAdvice);
       $this->entityManager->flush();

       return $this->json($updatedAdvice, Response::HTTP_OK);
    }

    #[Route('/api/advice/{id}', name: 'delete_advice', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[isGranted('ROLE_ADMIN')]
    public function deleteAdvice(Advice $advice) : JsonResponse
    {
        $this->entityManager->remove($advice);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
