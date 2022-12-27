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

        // create admin
        $address = new Address();
        $address->setAddress($faker->address());
        $address->setCity($faker->city());
        $address->setZipcode($faker->postcode());
        $address->setCountry($faker->country());
        // $address->setLongitude($faker->longitude($min = -180, $max = 180));
        // $address->setLatitude($faker->latitude($min = -90, $max = 90));

        $user = new User();
        $user->setEmail('admin@email.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword('$2y$13$yO9JDMzkaqU0jReACDaZzuS8NHwihaR7FFcHDfFk1qP2F0Zpg7fpu');
        $user->setFirstname($faker->firstName());
        $user->setLastname($faker->lastName());
        $user->setNumber($faker->e164PhoneNumber());
        $user->setBirthday($faker->dateTime());
        $user->setAddress($address);
        $manager->persist($user);

        // create 30 more user!
        for ($i = 0; $i < 30; $i++) {
            $address = new Address();
            $address->setAddress($faker->address());
            $address->setCity($faker->city());
            $address->setZipcode($faker->postcode());
            $address->setCountry($faker->country());
            // $address->setLongitude($faker->longitude($min = -180, $max = 180));
            // $address->setLatitude($faker->latitude($min = -90, $max = 90));

            $user = new User();
            $user->setEmail($faker->email());
            $user->setPassword('$2y$13$yO9JDMzkaqU0jReACDaZzuS8NHwihaR7FFcHDfFk1qP2F0Zpg7fpu');
            $user->setFirstname($faker->firstName());
            $user->setLastname($faker->lastName());
            $user->setNumber($faker->e164PhoneNumber());
            $user->setBirthday($faker->dateTime());
            $user->setAddress($address);
            $manager->persist($user);
            $this->addReference("USER" . $i, $user);
        }

        $manager->flush();
    }
}
