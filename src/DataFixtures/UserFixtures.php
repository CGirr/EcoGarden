<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('Isydia');
        $user->setPassword(password_hash('123456', PASSWORD_DEFAULT));
        $user->setRoles(['ROLE_USER']);
        $user->setCity('67300');
        $manager->persist($user);

        $admin = new User();
        $admin->setUsername('Admin');
        $admin->setPassword(password_hash('123456', PASSWORD_DEFAULT));
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setCity('67000');
        $manager->persist($admin);

        $manager->flush();
    }
}
