<?php

namespace App\Controller;

use App\Entity\Idiom;
use App\Entity\Portal;
use App\Entity\Article;
use App\Entity\IdiomArticle;
use App\Service\IdiomNavigationHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PrintController extends AbstractController
{
    #[Route('/print/article/{slug}', name: 'app_print_article')]
    #[Entity('article', expr: 'repository.findBySlug(slug)')]
    public function article(Article $article): Response
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

    #[Route('/print/portal/{slug}', name: 'app_print_portal')]
    #[Entity('article', expr: 'repository.findBySlug(slug)')]
    public function portal(Portal $portal): Response
    {
        $categories = [];
        foreach ($portal->getCategories() as $category) {
            $categories[] = $category->getTitle();
        }

        return $this->render('print/portal.html.twig', [
            'portal' => $portal,
            'categories' => $categories,
        ]);
    }

    #[Route('/print/idioms/{slug}', name: 'app_print_idiom')]
    #[Entity('idiom', expr: 'repository.findIdiomBySlug(slug)')]
    public function idiom(Idiom $idiom, bool $enableIdiom)
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

    #[Route('/print/idioms/article/{slug}', name: 'app_print_idiom_article')]
    #[Entity('article', expr: 'repository.findOneBySlug(slug)')]
    public function idiomArticle(IdiomArticle $article): Response
    {
        return $this->render('print/idiom_article.html.twig', [
            'article' => $article,
        ]);
    }

}
