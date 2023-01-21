<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Portal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}
