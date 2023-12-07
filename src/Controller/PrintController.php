<?php

namespace App\Controller;

use App\Entity\Idiom;
use App\Entity\Place;
use App\Entity\Person;
use App\Entity\Portal;
use App\Entity\Article;
use App\Entity\Scenario;
use App\Entity\IdiomArticle;
use App\Service\IdiomNavigationHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PrintController extends AbstractController
{
    #[Route('/print/article/{slug}', name: 'app_print_article')]
    public function article(#[MapEntity(expr: 'repository.findBySlug(slug)')] Article $article): Response
    {
        $portals = [];
        foreach ($article->getPortals() as $portal) {
            $portals[] = $portal->getTitle();
        }

        return $this->render('print/article.html.twig', [
            'article' => $article,
            'portals' => $portals,
        ]);
    }

    #[Route('/print/person/{slug}', name: 'app_print_person')]
    public function person(#[MapEntity(expr: 'repository.findBySlug(slug)')] Person $person): Response
    {
        $portals = [];
        foreach ($person->getPortals() as $portal) {
            $portals[] = $portal->getTitle();
        }

        $categories = [];
        foreach ($person->getCategories() as $category) {
            $categories[] = $category->getTitle();
        }

        return $this->render('print/person.html.twig', [
            'person' => $person,
            'portals' => $portals,
            'categories' => $categories,
        ]);
    }

    #[Route('/print/place/{slug}', name: 'app_print_place')]
    public function place(#[MapEntity(expr: 'repository.findBySlug(slug)')] Place $place): Response
    {
        $portals = [];
        foreach ($place->getPortals() as $portal) {
            $portals[] = $portal->getTitle();
        }

        $categories = [];
        foreach ($place->getCategories() as $category) {
            $categories[] = $category->getTitle();
        }

        $localisations = [];
        foreach ($place->getLocalisations() as $localisation) {
            $localisations[] = $localisation->getTitle();
        }

        return $this->render('print/place.html.twig', [
            'place' => $place,
            'portals' => $portals,
            'categories' => $categories,
            'localisations' => $localisations,
        ]);
    }

    #[Route('/print/portal/{slug}', name: 'app_print_portal')]
    public function portal(#[MapEntity(expr: 'repository.findBySlug(slug)')] Portal $portal): Response
    {
        $categories = [];
        foreach ($portal->getCategories() as $category) {
            $categories[] = $category->getTitle();
        }

        $articles = $portal->getArticles()->toArray();
        usort($articles, function($a, $b) {
            return strcmp($a->getTitle(), $b->getTitle());
        });

        return $this->render('print/portal.html.twig', [
            'portal' => $portal,
            'categories' => $categories,
            'articles' => $articles,
        ]);
    }

    #[Route('/print/idioms/{slug}', name: 'app_print_idiom')]
    public function idiom(#[MapEntity(expr: 'repository.findIdiomBySlug(slug)')] Idiom $idiom, bool $enableIdiom): Response
    {
        if (false === $enableIdiom) {
            throw $this->createNotFoundException('Not Found');
        }

        $portals = [];
        foreach ($idiom->getPortals() as $portal) {
            $portals[] = $portal->getTitle();
        }

        return $this->render('print/idiom.html.twig',[
            'navigations' => IdiomNavigationHelper::generateNavigation($idiom),
            'idiom' => $idiom,
            'portals' => $portals,
        ]);
    }

    #[Route('/print/scenario/{slug}', name: 'app_print_scenario')]
    public function scenario(Scenario $scenario, bool $enableScenario): Response
    {
        if (false === $enableScenario) {
            throw $this->createNotFoundException('Not Found');
        }

        $portals = [];
        foreach ($scenario->getPortals() as $portal) {
            $portals[] = $portal->getTitle();
        }

        $categories = [];
        foreach ($scenario->getCategories() as $category) {
            $categories[] = $category->getTitle();
        }

        return $this->render('print/scenario.html.twig',[
            'scenario' => $scenario,
            'portals' => $portals,
            'categories' => $categories,
        ]);
    }

    #[Route('/print/idioms/article/{slug}', name: 'app_print_idiom_article')]
    public function idiomArticle(#[MapEntity(expr: 'repository.findOneBySlug(slug)')] IdiomArticle $article): Response
    {
        return $this->render('print/idiom_article.html.twig', [
            'article' => $article,
        ]);
    }

}
