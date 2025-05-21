<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\News;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Add comments to some news articles
        for ($i = 0; $i < 50; $i++) {
            // Get random news
            $newsReference = 'news-' . rand(0, 49);
            $news = $this->getReference($newsReference, News::class);

            // Add 1-5 comments per news article
            $commentCount = rand(1, 5);
            for ($j = 0; $j < $commentCount; $j++) {
                $comment = new Comment();
                $comment->setAuthor($faker->name);
                $comment->setContent($faker->paragraph(3));
                $comment->setCreatedAt($faker->dateTimeBetween('-1 month', 'now'));
                $comment->setNews($news);

                $manager->persist($comment);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            NewsFixtures::class,
        ];
    }
}
