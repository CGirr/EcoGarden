<?php

namespace App\DataFixtures;

use App\Entity\Advice;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AdviceFixtures extends Fixture
{
    /***
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for($i = 0; $i < 30; $i++) {
            $advice = new Advice();
            $advice->setMonths($faker->randomElements(range(1,12), rand(1,3)));
            $advice->setDescription($faker->text(255));
            $manager->persist($advice);
        }
        $manager->flush();
    }
}
