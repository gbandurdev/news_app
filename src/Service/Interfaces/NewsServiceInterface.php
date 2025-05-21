<?php
namespace App\Service\Interfaces;

use App\Entity\News;
use Symfony\Component\Form\FormInterface;

interface NewsServiceInterface
{
    public function createNews(News $news, FormInterface $form): void;
    public function updateNews(News $news, FormInterface $form): void;
    public function deleteNews(News $news): void;
    public function removeNewsImage(News $news): void;
}
