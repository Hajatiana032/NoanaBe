<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\ProductFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        ProductFactory::createMany(25, function (): array {
            return ['category' => CategoryFactory::random()];
        });

        $manager->flush();
    }
}
