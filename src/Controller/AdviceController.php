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
    /**
     * @throws ExceptionInterface
     * @throws Exception
     */
    #[Route('/api/advice', name: 'advice_collection', methods: ['GET'])]
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
    #[Route('/api/advice/{month}', name: 'advice_item', requirements: ['month' => '\d+'], methods: ['GET'])]
    public function getAdvicesByMonth(
        SerializerInterface $serializer,
        AdviceRepository $adviceRepository,
        ValidatorService $validator,
        int $month
    ): JsonResponse
    {
        $month = $validator->validateMonth($month);

        $adviceList = $adviceRepository->getAdvicesByMonth($month);
        $jsonAdviceList = $serializer->serialize($adviceList, 'json');

        return new JsonResponse($jsonAdviceList, Response::HTTP_OK, [], true);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/advice', name: 'create_advice', methods: ['POST'])]
    #[isGranted('ROLE_ADMIN')]
    public function createAdvice(
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $entityManager,
        ValidatorService $validatorService
    ) : JsonResponse
    {
        $advice = $serializer->deserialize($request->getContent(), Advice::class, 'json');

        $validatorService->validateEntity($advice);

        $entityManager->persist($advice);
        $entityManager->flush();

        $jsonAdvice = $serializer->serialize($advice, 'json');

        return new JsonResponse($jsonAdvice, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/advice/{id}', name: 'edit_advice', requirements: ['id' => '\d+'], methods: ['PUT'])]
    #[isGranted('ROLE_ADMIN')]
    public function editAdvice(
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $entityManager,
        Advice $currentAdvice,
        ValidatorService  $validatorService
    ) : JsonResponse
    {
       $updatedAdvice = $serializer->deserialize(
           $request->getContent(),
           Advice::class,
           'json',
        [AbstractNormalizer::OBJECT_TO_POPULATE => $currentAdvice]
       );

       $validatorService->validateEntity($updatedAdvice);

       $entityManager->flush();

       $updatedJsonAdvice = $serializer->serialize($updatedAdvice, 'json');

       return new JsonResponse($updatedJsonAdvice, Response::HTTP_OK, [], true);
    }

    #[Route('/api/advice/{id}', name: 'delete_advice', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[isGranted('ROLE_ADMIN')]
    public function deleteAdvice(EntityManagerInterface $entityManager, Advice $advice) : JsonResponse
    {
        $entityManager->remove($advice);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Conseil supprimé avec succès'], Response::HTTP_OK);
    }
}
