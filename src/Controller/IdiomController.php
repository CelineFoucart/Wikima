<?php

namespace App\Controller;

use App\Entity\Idiom;
use App\Entity\IdiomArticle;
use App\Repository\IdiomRepository;
use App\Service\IdiomNavigationHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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
    public function showIdiomAction(#[MapEntity(expr: 'repository.findIdiomBySlug(slug)')] Idiom $idiom): Response
    {
        return $this->render('idiom/show_idiom.html.twig', [
            'idiom' => $idiom,
            'navigations' => IdiomNavigationHelper::generateNavigation($idiom),
        ]);
    }

    #[Route('/idioms/{idiom}/{article}', name: 'app_idiom_show_article')]
    public function showArticleAction(
        #[MapEntity(expr: 'repository.findIdiomBySlug(idiom)')] Idiom $idiomEntity, 
        #[MapEntity(expr: 'repository.findOneBySlug(article)')]  IdiomArticle $idiomArticle
    ): Response {
        return $this->render('idiom/show_idiom_article.html.twig', [
            'idiom' => $idiomEntity,
            'article' => $idiomArticle,
            'navigations' => IdiomNavigationHelper::generateNavigation($idiomEntity),
        ]);
    }
}