<?php

namespace App\Controller;

use App\Entity\Idiom;
use App\Entity\IdiomArticle;
use App\Repository\IdiomCategoryRepository;
use App\Repository\IdiomRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IdiomController extends AbstractController
{
    public function __construct(bool $enableIdiom)
    {
        if (false === $enableIdiom) {
            throw $this->createNotFoundException('Not Found');
        }
    }
    #[Route('/idioms', name: 'app_idiom_index')]
    public function indexAction(IdiomRepository $idiomRepository): Response
    {
        return $this->render('idiom/index_idiom.html.twig', [
            'idioms' => $idiomRepository->findAll(),
        ]);
    }

    #[Route('/idioms/{slug}', name: 'app_idiom_show')]
    #[Entity('idiom', expr: 'repository.findIdiomBySlug(slug)')]
    public function showIdiomAction(Idiom $idiom): Response
    {
        return $this->render('idiom/show_idiom.html.twig', [
            'idiom' => $idiom,
            'navigations' => $this->generateNavigation($idiom),
        ]);
    }

    #[Route('/idioms/{idiom}/{article}', name: 'app_idiom_show_article')]
    #[Entity('idiomArticle', expr: 'repository.findOneBySlug(article)')]
    #[Entity('idiom', expr: 'repository.findIdiomBySlug(idiom)')]
    public function showArticleAction(Idiom $idiom, IdiomArticle $idiomArticle): Response
    {
        return $this->render('idiom/show_idiom_article.html.twig', [
            'idiom' => $idiom,
            'article' => $idiomArticle,
            'navigations' => $this->generateNavigation($idiom),
        ]);
    }

    private function generateNavigation(Idiom $idiom): array
    {
        $navigations = [];

        foreach ($idiom->getIdiomArticles() as $article) {
            $key = $article->getCategory() ? $article->getCategory()->getId() : 0;
            if (isset($navigations[$key])) {
                $navigations[$key]['articles'][] = $article;
            } else {
                $navigations[$key] = [
                    'category' => $article->getCategory() ? $article->getCategory()->getTitle() : 'Sans catégorie',
                    'articles' => [$article]
                ];
            }
        }

        return $navigations;
    }
}