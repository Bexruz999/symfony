<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const ADMIN = 'ADMIN_USER';

    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {

        $user = (new User());
        $user->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@admin.com')
            ->setUsername('admin')
            ->setPassword($this->hasher->hashPassword($user, 'admin'))
            ->setVerified(true)
            ->setApiToken('api_token');
        $this->addReference(self::ADMIN, $user);
        $manager->persist($user);

        for ($i = 1; $i < 10; $i++) {
            $user = new User();
            $user->setRoles([])
                ->setEmail("user$i@doe.com")
                ->setUsername("user$i")
                ->setPassword($this->hasher->hashPassword($user, '0000'))
                ->setVerified(true)
                ->setApiToken("api_token$i");
            $this->addReference("user$i", $user);
            $manager->persist($user);
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
