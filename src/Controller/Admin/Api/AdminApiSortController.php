<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Idiom;
use App\Entity\Scenario;
use App\Entity\Timeline;
use App\Repository\IdiomCategoryRepository;
use App\Repository\MenuItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

class AdminApiSortController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer, private EntityManagerInterface $em)
    {
    }

    #[Route('/api/admin/timeline/{id}/event', 'api_timeline_event', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
    public function updateTimelineEvents(Timeline $timeline, Request $request): JsonResponse
    {
        $this->sortItems($timeline->getEvents()->toArray(), json_decode($request->getContent(), true));

        return new JsonResponse(
            $this->serializer->serialize(['id' => $timeline->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/admin/article/{id}/section', 'api_article_section', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
    public function updateArticleSections(Article $article, Request $request): JsonResponse
    {
        $this->sortItems($article->getSections()->toArray(), json_decode($request->getContent(), true));

        return new JsonResponse(
            $this->serializer->serialize(['id' => $article->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/admin/category/{id}/portals', 'api_category_portal_order', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
    public function updateCategoryPortalOrder(Category $category, Request $request): JsonResponse
    {
        $this->sortItems($category->getPortals()->toArray(), json_decode($request->getContent(), true));

        return new JsonResponse(
            $this->serializer->serialize(['id' => $category->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/admin/category/{id}/timelines', 'api_category_timeline_order', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
    public function updateCategoryTimelinesOrder(Category $category, Request $request): JsonResponse
    {
        $this->sortItems($category->getTimelines()->toArray(), json_decode($request->getContent(), true));

        return new JsonResponse(
            $this->serializer->serialize(['id' => $category->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/admin/menuitem/sort', 'api_menu_item_sort', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
    public function sortMenuItem(Request $request, MenuItemRepository $menuItemRepository): JsonResponse
    {
        $this->sortItems($menuItemRepository->findAll(), json_decode($request->getContent(), true));

        return $this->json(['message'=>'success'], Response::HTTP_OK);
    }

    #[Route('/api/admin/idiom/categories/sort', 'api_idiom_category_sort', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
    public function sortIdiomCategories(Request $request, IdiomCategoryRepository $idiomCategoryRepository, bool $enableIdiom): JsonResponse
    {
        if (false === $enableIdiom) {
            throw $this->createNotFoundException('Not Found');
        }

        $this->sortItems($idiomCategoryRepository->findAll(), json_decode($request->getContent(), true));

        return $this->json(['message'=>'success'], Response::HTTP_OK);
    }

    #[Route('/api/admin/idiom/articles/{id}/order', 'api_idiom_article_order', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
    public function updateIdiomArticleOrder(Idiom $idiom, Request $request, bool $enableIdiom): JsonResponse
    {
        if (false === $enableIdiom) {
            throw $this->createNotFoundException('Not Found');
        }

        $this->sortItems($idiom->getIdiomArticles()->toArray(), json_decode($request->getContent(), true));

        return new JsonResponse(
            $this->serializer->serialize(['id' => $idiom->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/admin/scenario/episode/{id}/order', 'api_scenario_episodes', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function updateEpisodeOrder(Scenario $scenario, Request $request, bool $enableScenario): JsonResponse
    {
        if (false === $enableScenario) {
            throw $this->createNotFoundException('Not Found');
        }

        $this->sortItems($scenario->getEpisodes()->toArray(), json_decode($request->getContent(), true));

        return new JsonResponse(
            $this->serializer->serialize(['id' => $scenario->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    private function sortItems(array $elements, array $data): void
    {
        foreach ($elements as $element) {
            $result = array_filter($data, function ($item) use ($element) {
                return $element->getId() === (int) $item['id'];
            });

            if (!empty($result)) {
                $result = array_values($result)[0];
                $position = $result['position'];

                if ($element instanceof Event) {
                    $element->setTimelineOrder($position);
                } else {
                    $element->setPosition($position);
                }

                $this->em->persist($element);
            }
        }

        $this->em->flush();
    }
}
