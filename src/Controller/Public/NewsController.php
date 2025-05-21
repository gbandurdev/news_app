<?php

namespace App\Controller\Public;

use App\Entity\News;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/news')]
class NewsController extends AbstractController
{
    #[Route('/{id}', name: 'app_news_show')]
    public function show(News $news, NewsRepository $newsRepository): Response
    {
        // Increment view count
        $news->incrementViews();
        $newsRepository->save($news, true);

        return $this->render('public/news/show.html.twig', [
            'news' => $news,
        ]);
    }
}
