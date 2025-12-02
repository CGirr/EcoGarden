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
use OpenApi\Attributes as OA;

final class AdviceController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
        private readonly AdviceRepository $adviceRepository,
        private readonly ValidatorService $validatorService
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route('/api/advice', name: 'advice_collection', methods: ['GET'])]
    #[OA\Get(
        description: 'Get the advices for the current month',
        summary: 'Advices for the current month',
        tags: ['advice']
    )]
    #[OA\Response(response: 200, description: 'List of advices for the current month')]
    public function getCurrentMonthAdvices(): JsonResponse
    {
        $adviceList = $this->adviceRepository->getAdvicesOfTheMonth();

        return $this->json($adviceList);
    }

    /**
     * @throws Exception
     */
    #[Route('/api/advice/{month}', name: 'advice_item', requirements: ['month' => '\d+'], methods: ['GET'])]
    #[OA\Get(
        description: 'Get the advices for a specific month',
        summary: 'Advices per month',
        tags: ['advice']
    )]
    #[OA\Response(response: 200, description: 'List of advices for a specified month')]
    #[OA\Parameter(
        name: 'month',
        description: 'Month number (1-12)',
        in: 'path',
        required: true,
    )]
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
    #[OA\Post(
        description: 'Create a new advice for the specified months',
        summary: 'Create an advice',
        tags: ['advice']
    )]
    #[OA\Response(response: 201, description: 'Created a new advice')]
    #[OA\RequestBody(
        description: 'Advice data',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'months', type: 'array', items: new OA\Items(type: 'integer')),
            ]
        )
    )]
    public function createAdvice(Request $request): JsonResponse
    {
        $advice = $this->serializer->deserialize($request->getContent(), Advice::class, 'json');

        $this->validatorService->validateEntity($advice);
        $this->entityManager->persist($advice);
        $this->entityManager->flush();

        return $this->json($advice, Response::HTTP_CREATED);
    }

    #[Route('/api/advice/{id}', name: 'edit_advice', requirements: ['id' => '\d+'], methods: ['PUT'])]
    #[isGranted('ROLE_ADMIN')]
    #[OA\Put(description: 'Edit a specific advice', summary: 'Edit advice', tags: ['advice'])]
    #[OA\Response(response: 200, description: 'Updated an advice')]
    #[OA\Parameter(
        name: 'id',
        description: 'id of the advice',
        in: 'path',
        required: true,
    )]
    public function editAdvice(Request $request, Advice $currentAdvice): JsonResponse
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
    #[OA\Delete(description: 'Delete a specific advice', summary: 'Delete advice', tags: ['advice'])]
    #[OA\Response(response: 204, description: 'Deleted an advice')]
    #[OA\Parameter(
        name: 'id',
        description: 'id of the advice',
        in: 'path',
        required: true,
    )]
    public function deleteAdvice(Advice $advice): JsonResponse
    {
        $this->entityManager->remove($advice);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
