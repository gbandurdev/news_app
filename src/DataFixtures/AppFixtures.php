<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AdminUserFixture::class,
            CategoryFixtures::class,
            CommentFixtures::class,
            NewsFixtures::class,
        ];
    }
}
