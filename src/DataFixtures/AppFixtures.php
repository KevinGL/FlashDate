<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Validator\Constraints\Date;

class AppFixtures extends Fixture
{
    private PasswordHasherFactoryInterface $hasher;
	private $faker;

    public function __construct(PasswordHasherFactoryInterface $hasher)
    {
        $this->hasher = $hasher;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user
            ->setEmail("aa@aa.fr")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($this->hasher->getPasswordHasher($user)->hash("1234"))
            ->setUsername("Kévin")
            ->setPhone("+33000000000")
            ->setCreatedAt(new \DateTimeImmutable())
            ->setBirthDate(new \DateTime("1993-06-01"))
            ->setGender("man")
            ->setSearch("woman")
            ->setCity("Marseille")
            ->setDistanceFilter(100)
            ->setAgeRange("18-40")
        ;

        $manager->persist($user);
    
        for($i = 0 ; $i < 199 ; $i++)
        {
            $user = new User();
        
            $user
                ->setEmail($this->faker->email())
                ->setRoles(["ROLE_USER"])
                ->setPassword($this->hasher->getPasswordHasher($user)->hash("1234"))
                ->setUsername($this->faker->firstName())
                ->setPhone($this->faker->phoneNumber())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setBirthDate(new \DateTime($this->faker->date()))
                ->setGender(rand(0, 1) == 0 ? "man" : "woman")
                ->setSearch(rand(0, 1) == 0 ? "man" : "woman")
                ->setCity($this->faker->city())
                ->setDistanceFilter(random_int(10, 200))
                ->setAgeRange("18-40")
            ;

            $manager->persist($user);
        }

        $manager->flush();
    }
}
