<?php

namespace App\Controller\Forum;

use App\Entity\Data\SearchForumData;
use App\Entity\Forum;
use App\Entity\ForumCategory;
use App\Form\Search\SearchTopicType;
use App\Repository\ForumCategoryRepository;
use App\Repository\TopicRepository;
use App\Service\ForumHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/forum')]
class ForumController extends AbstractController
{
    public function __construct(private ForumHelper $forumHelper, bool $enableForum)
    {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'app_forum_index')]
    public function index(ForumCategoryRepository $forumCategoryRepository): Response
    {
        return $this->render('forum/index.html.twig', [
            'categories' => $forumCategoryRepository->findByOrder($this->forumHelper->getCurrentUserRoles($this->getUser())),
        ]);
    }

    #[Route('/category-{slug}', name: 'app_forum_category_show')]
    public function category(#[MapEntity(expr: 'repository.findBySlug(slug)')] ForumCategory $category): Response
    {
        $groups = $this->forumHelper->getCurrentUserRoles($this->getUser());
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
    public function forum(Forum $forum, TopicRepository $topicRepository, Request $request, int $perPageOdd): Response
    {
        $hasAccess = $this->hasAccess($this->forumHelper->getCurrentUserRoles($this->getUser()), $forum);

        if (!$hasAccess) {
            throw $this->createAccessDeniedException('Access Denied');
        }

        $page = $request->query->getInt('page', 1);
        $topics = $topicRepository->findPaginated($forum->getId(), $page, $perPageOdd);
        $stickies = $topicRepository->findStickies($forum->getId());

        return $this->render('forum/forum.html.twig', [
            'forum' => $forum,
            'topics' => $topics,
            'stickies' => $stickies,
        ]);
    }

    #[Route('/search', name: 'app_forum_search')]
    public function search(Request $request, TopicRepository $topicRepository, int $perPageOdd): Response
    {
        $searchData = (new SearchForumData())->setPage($request->query->getInt('page', 1));
        $userRoles = $this->forumHelper->getCurrentUserRoles($this->getUser());
        $form = $this->createForm(SearchTopicType::class, $searchData, ['user_roles' => $userRoles]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $topics = $topicRepository->findSearchResultPaginated($searchData, $userRoles, $perPageOdd);
        } else {
            $topics = [];
        }

        return $this->render('forum/search.html.twig', [
            'form' => $form,
            'topics' => $topics,
        ]);
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
