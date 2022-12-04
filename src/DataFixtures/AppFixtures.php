<?php

namespace App\DataFixtures;

use App\Entity\AddressBook;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 50; ++$i) {
            $addressBook = new AddressBook();
            $addressBook->setFirstName($faker->firstName);
            $manager->persist($addressBook);
        }

        $manager->flush();
    }
}
