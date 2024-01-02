<?php

namespace App\Controller\Wiki;

use DateTimeImmutable;
use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\Data\SearchData;
use App\Form\Search\SearchType;
use App\Security\Voter\VoterHelper;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CommentController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository,
        bool $enableComment
    ) {
        if (false === $enableComment) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/articles/{slug}/comment', name: 'app_comment')]
    public function index(#[MapEntity(expr: 'repository.findBySlug(slug)')] Article $article, Request $request, CommentRepository $commentRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $user = $this->getUser();
        $comment = new Comment();

        if (null !== $user) {
            $comment->setAuthor($user)->setArticle($article);
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && null !== $user) {
            $comment->setCreatedAt(new DateTimeImmutable());
            $commentRepository->add($comment, true);
            $this->addFlash('success', 'Votre commentaire a bien été enregistré.');

            return $this->redirectToRoute('app_comment', ['slug' => $article->getSlug()]);
        }

        return $this->render('comment/index.html.twig', [
            'article' => $article,
            'comments' => $commentRepository->findPaginatedByArticle($page, $article),
            'formComment' => $form->createView(),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/comment/{id}/edit', name: 'app_comment_edit')]
    public function edit(Comment $comment, Request $request, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $comment);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre message a bien été modifié.');

            return $this->redirectToRoute(
                'app_comment',
                ['slug' => $comment->getArticle()->getSlug()]
            );
        }

        return $this->render('comment/edit.html.twig', [
            'formComment' => $form->createView(),
            'article' => $comment->getArticle(),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/comment/{id}/delete', name: 'app_comment_delete')]
    public function delete(Comment $comment, CommentRepository $commentRepository, Request $request): Response
    {
        $article = $comment->getArticle();
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $commentRepository->remove($comment);
            $this->addFlash('success', 'Le commentaire a bien été supprimée.');

            return $this->redirectToRoute('app_comment', ['slug' => $article->getSlug()]);
        }

        return $this->render('comment/delete.html.twig', [
            'comment' => $comment,
            'article' => $article,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }
}
