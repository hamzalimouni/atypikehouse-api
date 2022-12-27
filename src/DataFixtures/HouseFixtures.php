<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\House;
use App\Entity\Image;
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

            $image = new Image();
            $image->setFileName('63a474b1a88b7951978238.png');
            $image->setFilePath('\public\media');
            $manager->persist($image);

            $house = new House();
            $house->setCategory($this->getReference("CATEGORY" . mt_rand(0, 14)));
            $house->setTitle($faker->sentence());
            $house->setDescription($faker->paragraphs(4, true));
            $house->setPrice(mt_rand(100, 900));
            $house->setNbPerson(mt_rand(1, 8));
            $house->setSurface(10, 200);
            $house->setStatus("APPROVED");
            $house->setAddress($address);
            $house->setRooms(mt_rand(1, 10));
            $house->setBeds(mt_rand(1, 20));
            $house->addImage($image);
            $house->addEquipment($this->getReference("EQUIPMENT" . mt_rand(0, 14)));
            // for ($i = 0; $i < 4; $i++) {
            // }
            $house->setOwner($this->getReference("USER" . mt_rand(0, 29)));
            $manager->persist($house);
            // $this->addReference("HOUSE" . $i, $house);
        }
        $manager->flush();
    }
}
