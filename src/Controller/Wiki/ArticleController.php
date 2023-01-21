<?php

namespace App\Controller\Wiki;

use App\Entity\Article;
use App\Entity\Data\SearchData;
use App\Entity\User;
use App\Form\SearchPortalType;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ArticleController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {
    }

    #[Route('/articles/{slug}', name: 'app_article_show', requirements: ['slug' => '[a-z\-]*'])]
    #[Entity('article', expr: 'repository.findBySlug(slug)')]
    public function article(Article $article): Response
    {
        $this->denyAccessUnlessGranted('view', $article);

        return $this->render('article/show_article.html.twig', [
            'article' => $article,
        ]);
    }

    #[Route('/articles', name: 'app_article_index')]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $search = (new SearchData())->setPage($page);
        $form = $this->createForm(SearchPortalType::class, $search);
        $form->handleRequest($request);

        return $this->render('article/index_article.html.twig', [
            'articles' => $this->articleRepository->search($search, 10, $this->hidePrivate()),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/draft/articles', name: 'app_article_draft')]
    public function draft(Request $request)
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $page = $request->query->getInt('page', 1);

        $drafts = $this->articleRepository->findAuthorDrafts($user, $page);

        return $this->render('article/drafts.html.twig', [
            'drafts' => $drafts,
        ]);
    }

    #[Route('/user/{id}/articles', name: 'app_article_user')]
    public function user(Request $request, User $user)
    {
        $page = $request->query->getInt('page', 1);
        $hidePrivate = $this->hidePrivate();
        // if current user === user
        $articles = $this->articleRepository->findByUser($user, $page, $hidePrivate);

        return $this->render('article/user_articles.html.twig', [
            'articles' => $articles,
            'user' => $user,
        ]);
    }

    private function hidePrivate(): bool
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return true;
        }

        $roles = $user->getRoles();

        return !in_array('ROLE_ADMIN', $roles) || !in_array('ROLE_SUPER_ADMIN', $roles);
    }
}
