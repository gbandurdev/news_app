<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserFixture extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);

        $plaintextPassword = $_ENV['ADMIN_PASSWORD'] ?? 'admin123';
        $hashedPassword = $this->passwordHasher->hashPassword($admin, $plaintextPassword);
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);
        $manager->flush();
    }
}
