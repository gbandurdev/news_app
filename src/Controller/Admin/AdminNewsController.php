<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Form\NewsType;
use App\Service\NewsService;
use App\Repository\NewsRepository;
use App\Controller\Traits\FlashMessageTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/news')]
class AdminNewsController extends AbstractController
{
    use FlashMessageTrait;

    public function __construct(
        private readonly NewsService $newsService,
        private readonly NewsRepository $newsRepository
    ) {}

    #[Route('/', name: 'app_admin_news_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = max(5, min(50, $request->query->getInt('limit', 10))); // Between 5 and 50
        $sortBy = $request->query->get('sort', 'insertDate');
        $order = $request->query->get('order', 'DESC');
        $search = $request->query->get('search', '');

        if ($search) {
            $paginator = $this->newsRepository->searchPaginated($search, $page, $limit, $sortBy, $order);
        } else {
            $paginator = $this->newsRepository->findPaginated($page, $limit, $sortBy, $order);
        }

        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $limit);

        return $this->render('admin/news/index.html.twig', [
            'news' => $paginator,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'limit' => $limit,
            'sortBy' => $sortBy,
            'order' => $order,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'app_admin_news_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->executeWithFlash(
                fn() => $this->newsService->createNews($news, $form),
                'News created successfully.',
                'app_admin_news_index',
                'admin/news/create.html.twig',
                [],
                ['news' => $news, 'form' => $form]
            );
        }

        return $this->render('admin/news/create.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_news_show', methods: ['GET'])]
    public function show(News $news): Response
    {
        return $this->render('admin/news/show.html.twig', [
            'news' => $news,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_news_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, News $news): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->executeWithFlash(
                fn() => $this->newsService->updateNews($news, $form),
                'News updated successfully.',
                'app_admin_news_index',
                'admin/news/edit.html.twig',
                [],
                ['news' => $news, 'form' => $form]
            );
        }

        return $this->render('admin/news/edit.html.twig', [
            'news' => $news,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_news_delete', methods: ['POST'])]
    public function delete(Request $request, News $news): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_admin_news_index');
        }

        return $this->executeWithFlash(
            fn() => $this->newsService->deleteNews($news),
            'News deleted successfully.',
            'app_admin_news_index',
            'app_admin_news_index'
        );
    }

    #[Route('/{id}/remove-image', name: 'app_admin_news_remove_image', methods: ['POST'])]
    public function removeImage(Request $request, News $news): Response
    {
        if (!$this->isCsrfTokenValid('remove-image'.$news->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_admin_news_edit', ['id' => $news->getId()]);
        }

        return $this->executeWithFlash(
            fn() => $this->newsService->removeNewsImage($news),
            'Image removed successfully.',
            'app_admin_news_edit',
            'app_admin_news_edit',
            ['id' => $news->getId()]
        );
    }
}
