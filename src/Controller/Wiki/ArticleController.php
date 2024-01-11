<?php

namespace App\Controller\Wiki;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Data\SearchData;
use App\Form\Search\SearchType;
use App\Repository\ArticleRepository;
use App\Service\Word\ArticleWordGenerator;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Search\AdvancedArticleSearchType;
use App\Service\LogService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ArticleController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository
    ) {
    }

    #[Route('/articles', name: 'app_article_index')]
    public function index(Request $request, int $perPageEven): Response
    {
        $page = $request->query->getInt('page', 1);

        $search = (new SearchData())->setPage($page);
        $form = $this->createForm(AdvancedArticleSearchType::class, $search, ['allow_extra_fields' => true]);
        $form->handleRequest($request);

        return $this->render('article/index_article.html.twig', [
            'articles' => $this->articleRepository->search($search, $perPageEven, $this->hidePrivate()),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/draft/articles', name: 'app_article_draft')]
    public function draft(Request $request, int $perPageEven): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $page = $request->query->getInt('page', 1);

        $drafts = $this->articleRepository->findAuthorDrafts($user, $page, $perPageEven);

        return $this->render('article/drafts.html.twig', [
            'drafts' => $drafts,
        ]);
    }

    #[Route('/user/{id}/articles', name: 'app_article_user')]
    public function user(Request $request, User $user, int $perPageEven): Response
    {
        $page = $request->query->getInt('page', 1);
        $hidePrivate = $this->hidePrivate();
        $articles = $this->articleRepository->findByUser($user, $page, $hidePrivate, $perPageEven);

        return $this->render('article/user_articles.html.twig', [
            'articles' => $articles,
            'user' => $user,
        ]);
    }

    #[Route('/articles/{slug}', name: 'app_article_show')]
    public function article(#[MapEntity(expr: 'repository.findBySlug(slug)')] Article $article): Response
    {
        $this->denyAccessUnlessGranted('view', $article);

        return $this->render('article/show_article.html.twig', [
            'article' => $article,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }
    
    #[Route('/articles/{slug}/word', name: 'app_article_word')]
    public function articleToWord(#[MapEntity(expr: 'repository.findBySlug(slug)')] Article $article, ArticleWordGenerator $generator, LogService $logService): Response
    {
        $this->denyAccessUnlessGranted('view', $article);
        try {
            $file = $generator->setArticle($article)->generate();
            $response = new BinaryFileResponse($file['path']);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file['filename']
            );
            $response->deleteFileAfterSend();

            return $response;
        } catch (\Exception $th) {
            $this->addFlash('error',"Le fichier n'a pas pu être généré, car il y a des liens vers des images invalides ou un code HTML invalide.");
            $logService->error("Génération de '{$article->getSlug()}.docx'", $th->getMessage(), 'Article');
            
            return $this->redirectToRoute('app_article_show', ['slug' => $article->getSlug()]);
        }
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
