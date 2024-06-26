<?php

namespace App\DataFixtures;

use App\Entity\Category;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        // create 15 Category!
        for ($i = 0; $i < 15; $i++) {
            $category = new Category();
            $category->setName($faker->word());
            $category->setStatus(1);
            $manager->persist($category);
            $this->addReference("CATEGORY" . $i, $category);
        }
        $manager->flush();
    }
}
