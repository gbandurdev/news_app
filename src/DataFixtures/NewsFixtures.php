<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\News;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class NewsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $categories = [
            $this->getReference(CategoryFixtures::TECHNOLOGY_REFERENCE, Category::class),
            $this->getReference(CategoryFixtures::SPORTS_REFERENCE, Category::class),
            $this->getReference(CategoryFixtures::POLITICS_REFERENCE, Category::class),
            $this->getReference(CategoryFixtures::BUSINESS_REFERENCE, Category::class),
            $this->getReference(CategoryFixtures::ENTERTAINMENT_REFERENCE, Category::class),
        ];

        for ($i = 0; $i < 50; $i++) {
            $news = new News();
            $news->setTitle($faker->sentence(6, true));
            $news->setShortDescription($faker->paragraph(2));
            $news->setContent($faker->paragraphs(5, true));
            $news->setInsertDate($faker->dateTimeBetween('-2 months', 'now'));

            // Add random categories (1-3 per news)
            $categoryCount = rand(1, 3);
            $selectedCategories = $faker->randomElements($categories, $categoryCount);

            foreach ($selectedCategories as $category) {
                $news->addCategory($category);
            }

            $manager->persist($news);
            $this->addReference('news-' . $i, $news);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
