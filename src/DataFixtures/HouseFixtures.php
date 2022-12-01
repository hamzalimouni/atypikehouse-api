<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\House;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class HouseFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        // create 100 House!
        for ($i = 0; $i < 100; $i++) {
            $address = new Address();
            $address->setAddress($faker->address());
            $address->setCity($faker->city());
            $address->setZipcode($faker->postcode());
            $address->setCountry($faker->country());
            $address->setLongitude($faker->longitude($min = -180, $max = 180));
            $address->setLatitude($faker->latitude($min = -90, $max = 90));

            $house = new House();
            $house->setCategory($this->getReference("CATEGORY" . mt_rand(0, 14)));
            $house->setTitle($faker->word());
            $house->setDescription($faker->paragraphs(4, true));
            $house->setPrice(mt_rand(100, 900));
            $house->setNbPerson(mt_rand(1, 8));
            $house->setSurface(10, 200);
            $house->setStatus(mt_rand(0, 1));
            $house->setAddress($address);
            $house->setRooms(mt_rand(1, 10));
            $house->setBeds(mt_rand(1, 20));
            $manager->persist($house);
            $this->addReference("HOUSE" . $i, $house);
        }
        $manager->flush();
    }
}
