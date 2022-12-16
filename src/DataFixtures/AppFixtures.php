<?php

namespace App\DataFixtures;

use App\Entity\Prestation;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();

        for($i=0;$i<10;$i++) {
            $user = new User();
            $user->setEmail('user'.$i.'@gmail.com');
            $user->setPassword($this->userPasswordHasher->hashPassword($user,"123"));
            $user->setNom($faker->lastName);
            $user->setPrenom($faker->firstName);
            $user->setRoles(['ROLE_USER']);
            $user->setDateInscription($faker->dateTime);
            $manager->persist($user);

            $prestation = new Prestation();
            $prestation->setProprietaire($user);
            $prestation->setNom($faker->sentence);
            $prestation->setExtrait($faker->sentence(20));
            $prestation->setDescription($faker->paragraph(8));
            $prestation->setDateCreation($faker->dateTime);
            $prestation->setRemuneration($faker->randomFloat(2,10,500));
            $prestation->setTelephone($faker->phoneNumber);
            $manager->persist($prestation);
        }

        $manager->flush();
    }
}
