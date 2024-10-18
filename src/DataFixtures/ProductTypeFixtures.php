<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\ProductType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProductTypeFixtures extends Fixture
{


    private Generator $faker;
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {


        for ($i = 1; $i <= 10; $i++) {
            $productType = new ProductType();
            $productType->setStatus('on');
            $productType->setName($this->faker->firstNameMale());
            $productType->setPrice($this->faker->randomNumber(0,false));
            $productType->setCreatedAt($this->faker->dateTimeThisYear());
            $productType->setUpdatedAt($this->faker->dateTimeThisMonth());

            $manager->persist($productType);
        }

        $manager->flush();
    }
}
