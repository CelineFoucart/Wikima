<?php

namespace App\Controller\Wiki;

use App\Entity\User;
use App\Entity\ArticleType;
use App\Repository\ArticleRepository;
use App\Repository\ArticleTypeRepository;
use App\Service\AlphabeticalHelperService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleTypeController extends AbstractController
{
    public function __construct(
        private ArticleTypeRepository $articleTypeRepository
    ) {
    }

    #[Route('/type/articles', name: 'app_articletype_index')]
    public function index(): Response
    {
        return $this->render('article_type/index.html.twig', [
            'types' => $this->articleTypeRepository->findBy([], ['title' => 'ASC']),
        ]);
    }

    #[Route('/type/articles/{slug}', name: 'app_articletype_show')]
    public function show(int $perPageOdd, ArticleType $articleType, ArticleRepository $articleRepository, Request $request, AlphabeticalHelperService $helper): Response
    {
        $page = $request->query->getInt('page', 1);
        $articles = $articleRepository->findByType($articleType, $page, $this->hidePrivate(), $perPageOdd);

        return $this->render('article_type/show.html.twig', [
            'type' => $articleType,
            'items' => $helper->formatArray($articles->getItems()),
            'articles' => $articles,
            'types' => $this->articleTypeRepository->findBy([], ['title' => 'ASC']),
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
