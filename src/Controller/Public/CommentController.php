<?php

namespace App\Controller\Public;

use App\Entity\Comment;
use App\Entity\News;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{
    private CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    #[Route('/new/{id}', name: 'app_comment_new')]
    public function new(
        Request $request,
        News $news
    ): Response {
        $comment = new Comment();
        $comment->setNews($news);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentRepository->save($comment, true);

            $this->addFlash('success', 'Your comment has been added!');

            return $this->redirectToRoute('app_news_show', [
                'id' => $news->getId(),
            ], Response::HTTP_SEE_OTHER, ['_fragment' => 'comment-' . $comment->getId()]);
        }

        return $this->render('public/comment/create.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ajax/new/{id}', name: 'app_comment_create_ajax', methods: ['POST'])]
    public function createAjax(
        Request $request,
        News $news
    ): JsonResponse {
        if (!$request->isXmlHttpRequest()) {
            return $this->createErrorResponse('Invalid request', Response::HTTP_BAD_REQUEST);
        }

        // Verify CSRF token
        if (!$this->isCsrfTokenValid('comment', $request->request->get('_token'))) {
            return $this->createErrorResponse('Invalid CSRF token', Response::HTTP_BAD_REQUEST);
        }

        $comment = new Comment();
        $comment->setNews($news);

        $form = $this->createForm(CommentType::class, $comment);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            try {
                $this->commentRepository->save($comment, true);

                return $this->json([
                    'success' => true,
                    'message' => 'Comment added successfully',
                    'comment' => $this->formatCommentForResponse($comment)
                ]);
            } catch (Exception $e) {
                return $this->createErrorResponse(
                    'Failed to save comment: ' . $e->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        } else {
            return $this->createFormErrorResponse($form);
        }
    }

    /**
     * Format a comment entity for JSON response
     */
    private function formatCommentForResponse(Comment $comment): array
    {
        return [
            'id' => $comment->getId(),
            'author' => $comment->getAuthor(),
            'content' => $comment->getContent(),
            'createdAt' => $comment->getCreatedAt()->format('c')
        ];
    }

    /**
     * Create a standard error response
     */
    private function createErrorResponse(string $message, int $statusCode = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->json(['success' => false, 'message' => $message], $statusCode);
    }

    /**
     * Create an error response from form validation errors
     */
    private function createFormErrorResponse($form): JsonResponse
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $fieldName = $error->getOrigin()->getName();
            $errors[$fieldName] = $error->getMessage();
        }

        return $this->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $errors
        ], Response::HTTP_BAD_REQUEST);
    }
}
