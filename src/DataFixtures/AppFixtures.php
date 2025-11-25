<?php

namespace App\DataFixtures;

use App\Factory\AdviceFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::new(['username' => 'admin'])->create();
        UserFactory::new()->many(20)->create();
        AdviceFactory::new()->many(30)->create();
    }
}
