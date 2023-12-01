<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Form\Admin\CommentFormType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/admin/comment')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class AdminCommentController extends AbstractAdminController
{
    protected string $entityName = "comment";

    public function __construct(
        private CommentRepository $commentRepository,
        bool $enableComment
    ) {
        if (false === $enableComment) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'admin_app_comment_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/comment/list.html.twig', [
            'comments' => $this->commentRepository->findCommentsForAdminIndex(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_comment_show', methods:['GET'])]
    public function showAction(Comment $comment): Response
    {
        return $this->render('Admin/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_comment_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentRepository->add($comment, true);
            $this->addFlash('success', "Le commentaire a bien été modifié.");

            return $this->redirectTo($request, $comment->getId());
        }

        return $this->render('Admin/comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_comment_delete', methods:['POST'])]
    public function deleteAction(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $this->commentRepository->remove($comment, true);
            $this->addFlash('success', "Le commentaire a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_comment_list', [], Response::HTTP_SEE_OTHER);
    }
}