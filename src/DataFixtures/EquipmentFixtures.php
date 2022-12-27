<?php

namespace App\DataFixtures;

use App\Entity\Equipement;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EquipmentFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        // create 15 Category!
        for ($i = 0; $i < 15; $i++) {
            $equipment = new Equipement();
            $equipment->setName($faker->word());
            $equipment->setStatus(1);
            $manager->persist($equipment);
            $this->addReference("EQUIPMENT" . $i, $equipment);
        }
        $manager->flush();
    }
}
