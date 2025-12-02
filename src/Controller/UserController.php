<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;

final class UserController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorService $validator,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/user', name: 'create_user', methods: ['POST'])]
    #[OA\Post(
        description: 'Create a new user',
        summary: 'Create user',
        tags: ['user'],
    )]
    #[OA\Response(response: 201, description: 'Created a new user')]
    #[OA\RequestBody(
        description: 'User data',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'username', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
                new OA\Property(property: 'city', type: 'string'),
            ]
        )
    )]
    public function createUser(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $this->validator->validateEntity($user);

        $user->setPassword(
            $passwordHasher->hashPassword($user, $user->getPassword())
        );
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json($user, Response::HTTP_CREATED, [], ['groups' => 'user_read']);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/user/{id}', name: 'update_user', requirements: ['id' => '\d+'], methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
    #[OA\Put(
        description: 'Update the specified user',
        summary: 'Update user',
        tags: ['user'],
    )]
    #[OA\Response(response: 200, description: 'Updated the specified user')]
    #[OA\Parameter(
        name: 'id',
        description: 'id of the user',
        in: 'path',
        required: true,
    )]
    public function updateUser(
        Request $request,
        User $currentUser,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $plainPassword = $data['password'] ?? null;

        $updatedUser = $this->serializer->deserialize(
            json_encode($data),
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser]
        );

        if (array_key_exists('password', $data)) {
            $updatedUser->setPlainPassword($plainPassword);

            $this->validator->validateEntity($updatedUser, ['password_update']);

            $updatedUser->setPassword(
                $passwordHasher->hashPassword($updatedUser, $plainPassword)
            );

            $updatedUser->setPlainPassword(null);
        }

        $this->validator->validateEntity($updatedUser);
        $this->entityManager->flush();

        return $this->json($updatedUser, Response::HTTP_OK, [], ['groups' => 'user_read']);
    }

    #[Route('/api/user/{id}', name: 'delete_user', requirements: ['id' => '\d+'],  methods: ['DELETE'])]
    #[IsGranted("ROLE_ADMIN")]
    #[OA\Delete(
        description: 'Delete the specified user',
        summary: 'Delete user',
        tags: ['user'],
    )]
    #[OA\Response(response: 204, description: 'Deleted the specified user')]
    #[OA\Parameter(
        name: 'id',
        description: 'id of the user',
        in: 'path',
        required: true,
    )]
    public function deleteUser(User $user): JsonResponse
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
