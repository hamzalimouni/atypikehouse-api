<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // create 30 User!
        for ($i = 0; $i < 30; $i++) {
            $address = new Address();
            $address->setAddress($faker->address());
            $address->setCity($faker->city());
            $address->setZipcode($faker->postcode());
            $address->setCountry($faker->country());
            $address->setLongitude($faker->longitude($min = -180, $max = 180));
            $address->setLatitude($faker->latitude($min = -90, $max = 90));

            $user = new User();
            $user->setEmail($faker->email());
            $user->setRoles();
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $faker->password()));
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setNumber($faker->e164PhoneNumber());
            $user->setBirthday($faker->birth());
            $user->setStatus(mt_rand(0, 1));
            $user->setAddress($address);
            $manager->persist($user);
            $this->addReference("USER" . $i, $user);
        }

        $manager->flush();
    }
}
