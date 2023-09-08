<?php

namespace App\Controller\Forum;

use App\Entity\Forum;
use App\Entity\ForumCategory;
use App\Entity\ForumGroup;
use App\Entity\User;
use App\Repository\ForumCategoryRepository;
use App\Repository\ForumGroupRepository;
use App\Repository\TopicRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/forum')]
class ForumController extends AbstractController
{
    public function __construct(private ForumGroupRepository $forumGroupRepository, bool $enableForum)
    {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'app_forum_index')]
    public function index(ForumCategoryRepository $forumCategoryRepository): Response
    {
        return $this->render('forum/index.html.twig', [
            'categories' => $forumCategoryRepository->findByOrder($this->getCurrentUserRoles()),
        ]);
    }

    #[Route('/category-{slug}', name: 'app_forum_category_show')]
    public function category(#[MapEntity(expr: 'repository.findBySlug(slug)')] ForumCategory $category): Response
    {
        $groups = $this->getCurrentUserRoles();
        $hasAccess = $this->hasAccess($groups, $category);
        
        if (!$hasAccess) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        foreach ($category->getForums() as $forum) {
            $hasAccess = $this->hasAccess($groups, $forum);

            if (!$hasAccess) {
                $category->removeForum($forum);
            }
        }

        return $this->render('forum/category.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/forum-{slug}', name: 'app_forum_forum_show')]
    public function forum(Forum $forum, TopicRepository $topicRepository, Request $request): Response
    {
        $hasAccess = $this->hasAccess($this->getCurrentUserRoles(), $forum);

        if (!$hasAccess) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        $page = $request->query->getInt('page', 1);
        $topics = $topicRepository->findPaginated($forum->getId(), $page);

        return $this->render('forum/forum.html.twig', [
            'forum' => $forum,
            'topics' => $topics,
        ]);
    }

    /**
     * @return ForumGroup[]
     */
    private function getCurrentUserRoles(): array
    {
        $anonymous = $this->forumGroupRepository->findByRoleName(['roleName' => 'PUBLIC_ACCESS']);
        
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $anonymous;
        }

        $userGroups = $user->getUserGroups();
        if ($userGroups->isEmpty()) {
            return $anonymous;
        }

        $groups = [];

        foreach ($userGroups as $userGroup) {
            $groups[] = $userGroup->getForumGroup();
        }

        return $groups;
    }

    /**
     * @param array $groups
     * @param Forum|ForumCategory $element
     * 
     * @return bool
     */
    private function hasAccess(array $groups, $element): bool
    {
        $hasAccess = false;

        foreach ($groups as $group) {
            $access = $element->getGroupAccess()->contains($group);
            if ($access) {
                $hasAccess = $access;
            }
        }

        return $hasAccess;
    }
}
