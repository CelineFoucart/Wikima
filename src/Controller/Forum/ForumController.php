<?php

namespace App\Controller\Forum;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum')]
class ForumController extends AbstractController
{
    public function __construct(bool $enableForum)
    {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'app_forum_index')]
    public function index(): Response
    {
        return $this->render('forum/index.html.twig', [
        ]);
    }

    #[Route('/category-{slug}', name: 'app_forum_category_show')]
    public function category(string $slug): Response
    {
        return $this->render('forum/category.html.twig', [
        ]);
    }

    #[Route('/forum-{slug}', name: 'app_forum_forum_show')]
    public function forum(string $slug): Response
    {
        return $this->render('forum/forum.html.twig', [
        ]);
    }
}
