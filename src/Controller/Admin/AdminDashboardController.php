<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\NewsRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function dashboard(
        NewsRepository $newsRepository,
        CategoryRepository $categoryRepository,
        CommentRepository $commentRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $totalNews = $newsRepository->count([]);
        $totalCategories = $categoryRepository->count([]);
        $totalComments = $commentRepository->count([]);

        $latestNews = $newsRepository->findLatest(5);

        $recentComments = $commentRepository->findBy(
            [],
            ['createdAt' => 'DESC'],
            5
        );

        $today = new DateTime('today');
        $lastWeek = new DateTime('-7 days');

        $commentsToday = $commentRepository->countCommentsAfterDate($today);
        $commentsThisWeek = $commentRepository->countCommentsAfterDate($lastWeek);

        return $this->render('admin/dashboard.html.twig', [
            'totalNews' => $totalNews,
            'totalCategories' => $totalCategories,
            'totalComments' => $totalComments,
            'latestNews' => $latestNews,
            'recentComments' => $recentComments,
            'commentsToday' => $commentsToday,
            'commentsThisWeek' => $commentsThisWeek,
        ]);
    }
}
