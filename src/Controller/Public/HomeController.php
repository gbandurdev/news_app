<?php

namespace App\Controller\Public;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        // Get all categories with latest news
        $categories = $categoryRepository->findAllWithLatestNews();

        return $this->render('public/home/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
