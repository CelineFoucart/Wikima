<?php

namespace App\Controller;

use App\Entity\Idiom;
use App\Entity\IdiomArticle;
use App\Repository\IdiomRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IdiomController extends AbstractController
{
    #[Route('/idioms', name: 'app_idiom_index')]
    public function indexAction(IdiomRepository $idiomRepository): Response
    {
        return $this->render('idiom/index_idiom.html.twig', [
            'idioms' => $idiomRepository->findAll(),
        ]);
    }

    #[Route('/idioms/{slug}', name: 'app_idiom_show')]
    public function showIdiomAction(Idiom $idiom): Response
    {
        return $this->render('idiom/show_idiom.html.twig', [
            'idiom' => $idiom,
        ]);
    }

    #[Route('/idioms/{idiom}/{article}', name: 'app_idiom_show_article')]
    public function showArticleAction(Idiom $idiom, IdiomArticle $article): Response
    {
        return $this->render('idiom/show_idiom_article.html.twig', [
            'idiom' => $idiom,
            'article' => $article,
        ]);
    }
}