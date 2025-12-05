<?php

namespace App\Service\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {}

    public function createUser(User $user): User
    {
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));
        $user->setPlainPassword(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(User $user, ?string $plainPassword): User
    {
        if ($plainPassword !== null) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));
            $user->setPlainPassword(null);
        }

        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }
}

