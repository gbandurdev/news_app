<?php
namespace App\Service\Interfaces;

use App\Entity\News;
use Symfony\Component\Form\FormInterface;

interface ImageServiceInterface
{
    public function handleImageUpload(FormInterface $form, News $news): void;
    public function removeImage(News $news): void;
}
