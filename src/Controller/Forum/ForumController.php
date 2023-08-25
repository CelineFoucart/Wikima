<?php

namespace App\Controller\Forum;

use App\Entity\Forum;
use App\Entity\ForumCategory;
use App\Repository\ForumCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function index(ForumCategoryRepository $forumCategoryRepository): Response
    {
        return $this->render('forum/index.html.twig', [
            'categories' => $forumCategoryRepository->findByOrder(),
        ]);
    }

    #[Route('/category-{slug}', name: 'app_forum_category_show')]
    #[Entity('category', expr: 'repository.findBySlug(slug)')]
    public function category(ForumCategory $category): Response
    {
        return $this->render('forum/category.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/forum-{slug}', name: 'app_forum_forum_show')]
    public function forum(Forum $forum): Response
    {
        return $this->render('forum/forum.html.twig', [
            'forum' => $forum
        ]);
    }
}
