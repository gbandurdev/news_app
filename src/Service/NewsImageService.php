<?php
namespace App\Service;

use App\Entity\News;
use App\Service\Interfaces\ImageServiceInterface;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class NewsImageService extends AbstractImageService implements ImageServiceInterface
{
    public function __construct(
        string $uploadDirectory,
        SluggerInterface $slugger
    ) {
        parent::__construct($uploadDirectory, $slugger);
    }

    /**
     * Handle image upload
     * @throws Exception
     */
    public function handleImageUpload(FormInterface $form, News $news): void
    {
        $imageFile = $form->get('imageFile')->getData();
        if (!$imageFile) {
            return;
        }

        // Remove old image if it exists
        $this->removeImage($news);

        // Upload new image
        $imageName = $this->uploadImage($imageFile);
        $news->setImageName($imageName);

        // Set alt text
        $altText = $form->has('imageAltText') ? $form->get('imageAltText')->getData() : null;
        $news->setImageAltText($altText ?: $news->getTitle());
    }

    /**
     * Remove image from news and filesystem
     */
    public function removeImage(News $news): void
    {
        if (!$news->hasImage()) {
            return;
        }

        $this->removeFile($news->getImageName());
        $news->setImageName(null);
    }
}
