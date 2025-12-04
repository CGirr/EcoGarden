<?php

namespace App\Service\User;

use App\Entity\User;
use App\Service\ValidatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ValidatorService $validatorService
    ) {}

    public function createUser(User $user): User
    {
        $user->setPlainPassword($user->getPassword());
        $user->setRoles(['ROLE_USER']);
        $this->validatorService->validateEntity($user);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));
        $user->setPlainPassword(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(User $user, ?string $plainPassword): User
    {
        if ($plainPassword !== null) {
            $user->setPlainPassword($plainPassword);
            $this->validatorService->validateEntity($user, ['password_update']);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setPlainPassword(null);
        }

        $this->validatorService->validateEntity($user);
        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}

