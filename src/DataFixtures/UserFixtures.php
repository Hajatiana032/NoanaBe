<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher, private SluggerInterface $slugger) {}

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // ? create a super admin
        UserFactory::createOne(function () {
            $user = new User();
            return [
                'email' => 'nirilantohajatiana032@gmail.com',
                'firstname' => 'Hajatiana',
                'lastname' => 'RANDRIANANTOANDRO',
                'password' => $this->passwordHasher->hashPassword($user, '0329250604'),
                'roles' => ["ROLE_SUPER_ADMIN"],
            ];
        });

        // ? create 3 admin
        UserFactory::createMany(3, function () {
            $user = new User();
            return [
                'roles' => ["ROLE_ADMIN"]
            ];
        });

        // ? create 15 customers
        UserFactory::createMany(15, function () {
            $user = new User();
            return [
                'roles' => ["ROLE_USER"]
            ];
        });

        $manager->flush();
    }
}
