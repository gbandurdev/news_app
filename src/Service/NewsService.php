<?php
namespace App\Service;

use App\Entity\News;
use App\Repository\NewsRepository;
use App\Service\Interfaces\NewsServiceInterface;
use App\Service\Interfaces\ImageServiceInterface;
use Symfony\Component\Form\FormInterface;

readonly class NewsService implements NewsServiceInterface
{
    public function __construct(
        private NewsRepository $newsRepository,
        private ImageServiceInterface $newsImageService
    ) {}

    /**
     * Create a new news item
     */
    public function createNews(News $news, FormInterface $form): void
    {
        $this->newsImageService->handleImageUpload($form, $news);
        $this->newsRepository->save($news, true);
    }

    /**
     * Update an existing news item
     */
    public function updateNews(News $news, FormInterface $form): void
    {
        $this->newsImageService->handleImageUpload($form, $news);
        $this->newsRepository->save($news, true);
    }

    /**
     * Delete a news item and its associated files
     */
    public function deleteNews(News $news): void
    {
        $this->newsImageService->removeImage($news);
        $this->newsRepository->remove($news, true);
    }

    /**
     * Remove image from news item
     */
    public function removeNewsImage(News $news): void
    {
        $this->newsImageService->removeImage($news);
        $this->newsRepository->save($news, true);
    }
}
