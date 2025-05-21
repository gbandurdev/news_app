<?php

namespace App\Tests\Controller\Admin;

use App\Entity\News;
use App\Repository\NewsRepository;
use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NewsControllerTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?NewsRepository $newsRepository = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->newsRepository = $this->entityManager->getRepository(News::class);
    }

    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $this->entityManager->close();
            $this->entityManager = null;
        }
        parent::tearDown();
    }

    /**
     * @throws Exception
     */
    public function testEntityManagerConnection(): void
    {
        // Test that entity manager is working
        $this->assertNotNull($this->entityManager);

        // Test connection (newer way to avoid deprecation)
        $connection = $this->entityManager->getConnection();
        $this->assertNotNull($connection);

        // Test if we can execute a simple query
        $result = $connection->executeQuery('SELECT 1 as test')->fetchOne();
        $this->assertEquals(1, $result);
    }

    public function testCreateNews(): void
    {
        // Ensure entity manager is available
        $this->assertNotNull($this->entityManager);

        $news = new News();
        $news->setTitle('Test News Title');
        $news->setShortDescription('Test short description');
        $news->setContent('Test content for news article');

        $this->entityManager->persist($news);
        $this->entityManager->flush();

        // Verify the news was created
        $this->assertNotNull($news->getId());
        $this->assertEquals('Test News Title', $news->getTitle());
        $this->assertEquals('Test short description', $news->getShortDescription());
        $this->assertEquals('Test content for news article', $news->getContent());
        $this->assertInstanceOf(DateTime::class, $news->getInsertDate());
        $this->assertEquals(0, $news->getViews());

        // Verify it exists in database
        $foundNews = $this->newsRepository->find($news->getId());
        $this->assertNotNull($foundNews);
        $this->assertEquals('Test News Title', $foundNews->getTitle());
    }

    public function testUpdateNews(): void
    {
        // Create
        $news = new News();
        $news->setTitle('Original Title');
        $news->setShortDescription('Original desc');
        $news->setContent('Original content');

        $this->entityManager->persist($news);
        $this->entityManager->flush();

        // Update
        $news->setTitle('Updated Title');
        $this->entityManager->flush();

        // Verify
        $updated = $this->newsRepository->find($news->getId());
        $this->assertEquals('Updated Title', $updated->getTitle());
    }

    public function testDeleteNews(): void
    {
        // Create
        $news = new News();
        $news->setTitle('News to Delete');
        $news->setShortDescription('Description');
        $news->setContent('Content');

        $this->entityManager->persist($news);
        $this->entityManager->flush();
        $newsId = $news->getId();

        // Delete
        $this->entityManager->remove($news);
        $this->entityManager->flush();

        // Verify deleted
        $deleted = $this->newsRepository->find($newsId);
        $this->assertNull($deleted);
    }

    public function testEmptyNewsHasValidationErrors(): void
    {
        $validator = self::getContainer()->get('validator');

        // Create empty News - should fail validation
        $emptyNews = new News();
        $violations = $validator->validate($emptyNews);

        // Should have at least 1 violation (empty title, shortDescription, or content)
        $this->assertGreaterThan(0, $violations->count(),
            'Empty news should have validation errors'
        );
    }

}
