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
    private $cities;

    public function __construct(PasswordHasherFactoryInterface $hasher)
    {
        $this->hasher = $hasher;
        $this->faker = Factory::create();

        $this->cities =
        [
            'Paris' => '48.8566,2.3522',
            'Marseille' => '43.2965,5.3698',
            'Lyon' => '45.7640,4.8357',
            'Toulouse' => '43.6047,1.4442',
            'Nice' => '43.7102,7.2620',
            'Nantes' => '47.2184,-1.5536',
            'Montpellier' => '43.6108,3.8767',
            'Strasbourg' => '48.5734,7.7521',
            'Bordeaux' => '44.8378,-0.5792',
            'Lille' => '50.6292,3.0573',
            'Rennes' => '48.1173,-1.6778',
            'Reims' => '49.2583,4.0317',
            'Saint-Étienne' => '45.4397,4.3872',
            'Toulon' => '43.1242,5.9280',
            'Grenoble' => '45.1885,5.7245',
            'Dijon' => '47.3220,5.0415',
            'Angers' => '47.4784,-0.5632',
            'Nîmes' => '43.8367,4.3601',            
            'Aix-en-Provence' => '43.5297,5.4474',
            'Clermont-Ferrand' => '45.7772, 3.0870',
        ];
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
            ->setCoord($this->cities['Marseille'])
            ->setDistanceFilter(100)
            ->setAgeRange("18-40")
        ;

        $manager->persist($user);
    
        for($i = 0 ; $i < 199 ; $i++)
        {
            $listCities = array_keys($this->cities);
            $indexCity = random_int(0, count($listCities) - 1);
            $city = $listCities[$indexCity];
        
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
                ->setCity($city)
                ->setCoord($this->cities[$city])
                ->setDistanceFilter(random_int(10, 200))
                ->setAgeRange("18-40")
            ;

            $manager->persist($user);
        }

        $manager->flush();
    }
}
