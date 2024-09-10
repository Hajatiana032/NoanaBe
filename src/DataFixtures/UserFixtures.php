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
    private $user;

    public function __construct(private SluggerInterface $slugger, private UserPasswordHasherInterface $passwordHasher)
    {
        $this->user = new User();
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // ? create a super admin
        UserFactory::createOne(function () {
            return [
                'email' => 'nirilantohajatiana032@gmail.com',
                'firstname' => 'Hajatiana',
                'lastname' => 'RANDRIANANTOANDRO',
                'password' => $this->passwordHasher->hashPassword($this->user, '0329250604'),
                'roles' => ["ROLE_SUPER_ADMIN"],
            ];
        });

        // ? create 3 admin
        UserFactory::createMany(3, ['roles' => ["ROLE_ADMIN"], 'password' => $this->passwordHasher->hashPassword($this->user, '123456')]);

        // ? create 15 customers
        UserFactory::createMany(15, ['roles' => ["ROLE_USER"], 'password' => $this->passwordHasher->hashPassword($this->user, '123456')]);

        $manager->flush();
    }
}
