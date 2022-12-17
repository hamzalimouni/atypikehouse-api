<?php

namespace App\DataFixtures;

use App\Entity\Category;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create 15 Category!
        for ($i = 0; $i < 15; $i++) {
            $category = new Category();
            $category->setName('category ' . $i);
            $category->setStatus(mt_rand(0, 1));
            $manager->persist($category);
            $this->addReference("CATEGORY" . $i, $category);
        }

        $manager->flush();
    }
}
