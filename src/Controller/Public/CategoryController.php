<?php

namespace App\Controller\Public;

use App\Entity\Category;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/{id}', name: 'app_category_show')]
    public function show(
        Request $request,
        Category $category,
        NewsRepository $newsRepository
    ): Response {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 10;

        // Get paginated news for this category
        $news = $newsRepository->findByCategoryPaginated(
            $category->getId(),
            $page,
            $limit
        );

        // Calculate total pages
        $totalItems = count($news);
        $totalPages = ceil($totalItems / $limit);

        return $this->render('public/category/show.html.twig', [
            'category' => $category,
            'news' => $news,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }
}
