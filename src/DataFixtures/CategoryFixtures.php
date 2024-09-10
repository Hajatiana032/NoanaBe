<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Factory\CategoryFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger) {}

    public function load(ObjectManager $manager): void
    {
        CategoryFactory::createOne(['name' => 'Pizza']);
        CategoryFactory::createOne(['name' => 'Tacos']);
        CategoryFactory::createOne(['name' => 'Pâtes']);
        CategoryFactory::createOne(['name' => 'Hamburger']);
        CategoryFactory::createOne(['name' => 'Panini']);

        $manager->flush();
    }
}
