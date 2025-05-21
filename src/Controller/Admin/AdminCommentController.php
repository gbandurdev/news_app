<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Entity\News;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/comment')]
#[IsGranted('ROLE_ADMIN')]
class AdminCommentController extends AbstractController
{
    #[Route('/news/{id}', name: 'app_admin_comment_index')]
    public function index(News $news): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'news' => $news,
            'comments' => $news->getComments(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_comment_delete')]
    public function delete(
        Request $request,
        Comment $comment,
        CommentRepository $commentRepository
    ): Response {
        $newsId = $comment->getNews()->getId();

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment, true);

            $this->addFlash('success', 'Comment deleted successfully.');
        }

        return $this->redirectToRoute('app_admin_comment_index', ['id' => $newsId]);
    }

    #[Route('/all', name: 'app_admin_comments_all')]
    public function all(CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/comment/all.html.twig', [
            'comments' => $comments,
        ]);
    }
}
