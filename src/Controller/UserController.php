<?php

namespace App\Controller;

use App\Entity\User;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/user', name: 'create_user', methods: ['POST'])]
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                Response::HTTP_BAD_REQUEST,
                [],
                true
            );
        }

        $user->setPassword(
            $passwordHasher->hashPassword($user, $user->getPassword())
        );

        $entityManager->persist($user);
        $entityManager->flush();

        $jsonUser = $serializer->serialize($user, 'json');

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/user/{id}', name: 'update_user', methods: ['PUT'])]
    #[IsGranted("ROLE_ADMIN")]
    public function updateUser(
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $entityManager,
        User $currentUser,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $plainPassword = $data['password'] ?? null;
        unset($data['password']);

        $updatedUser = $serializer->deserialize(
            json_encode($data),
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser]
        );

        if($plainPassword) {
            $updatedUser->setPassword(
                $passwordHasher->hashPassword($updatedUser, $plainPassword)
            );
        }

        $entityManager->flush();

        $jsonUser = $serializer->serialize($updatedUser, 'json', ['groups' => 'user_read']);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    #[Route('/api/user/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(EntityManagerInterface $entityManager, User $User): JsonResponse
    {
        $entityManager->remove($User);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur supprimé avec succès'], Response::HTTP_OK);
    }
}
