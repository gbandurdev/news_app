<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const TECHNOLOGY_REFERENCE = 'category-technology';
    public const SPORTS_REFERENCE = 'category-sports';
    public const POLITICS_REFERENCE = 'category-politics';
    public const BUSINESS_REFERENCE = 'category-business';
    public const ENTERTAINMENT_REFERENCE = 'category-entertainment';

    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['title' => 'Technology', 'reference' => self::TECHNOLOGY_REFERENCE],
            ['title' => 'Sports', 'reference' => self::SPORTS_REFERENCE],
            ['title' => 'Politics', 'reference' => self::POLITICS_REFERENCE],
            ['title' => 'Business', 'reference' => self::BUSINESS_REFERENCE],
            ['title' => 'Entertainment', 'reference' => self::ENTERTAINMENT_REFERENCE],
        ];

        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setTitle($categoryData['title']);

            $manager->persist($category);
            $this->addReference($categoryData['reference'], $category);
        }

        $manager->flush();
    }
}
