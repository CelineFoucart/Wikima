<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Idiom;
use App\Entity\Timeline;
use App\Repository\IdiomCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminApiSortController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer, private EntityManagerInterface $em)
    {
    }

    #[Route('/api/admin/timeline/{id}/event', 'api_timeline_event', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function updateTimelineEvents(Timeline $timeline, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($timeline->getEvents() as $event) {
            $result = array_filter($data, function ($item) use ($event) {
                return $event->getId() === (int) $item['id'];
            });

            if (!empty($result)) {
                $result = array_values($result)[0];
                $timelineOrder = $result['position'];
                $event->setTimelineOrder($timelineOrder);
                $this->em->persist($event);
            }
        }

        $this->em->flush();

        return new JsonResponse(
            $this->serializer->serialize(['id' => $timeline->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/admin/article/{id}/section', 'api_article_section', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function updateArticleSections(Article $article, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($article->getSections() as $section) {
            $result = array_filter($data, function ($item) use ($section) {
                return $section->getId() === (int) $item['id'];
            });

            if (!empty($result)) {
                $result = array_values($result)[0];
                $position = $result['position'];
                $section->setPosition($position);
                $this->em->persist($section);
            }
        }

        $this->em->flush();

        return new JsonResponse(
            $this->serializer->serialize(['id' => $article->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/admin/category/{id}/portals', 'api_category_portal_order', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function updateCategoryPortalOrder(Category $category, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($category->getPortals() as $portal) {
            $result = array_filter($data, function ($item) use ($portal) {
                return $portal->getId() === (int) $item['id'];
            });

            if (!empty($result)) {
                $result = array_values($result)[0];
                $position = $result['position'];
                $portal->setPosition($position);
                $this->em->persist($portal);
            }
        }

        $this->em->flush();

        return new JsonResponse(
            $this->serializer->serialize(['id' => $category->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/admin/category/{id}/timelines', 'api_category_timeline_order', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function updateCategoryTimelinesOrder(Category $category, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($category->getTimelines() as $timeline) {
            $result = array_filter($data, function ($item) use ($timeline) {
                return $timeline->getId() === (int) $item['id'];
            });

            if (!empty($result)) {
                $result = array_values($result)[0];
                $position = $result['position'];
                $timeline->setPosition($position);
                $this->em->persist($timeline);
            }
        }

        $this->em->flush();

        return new JsonResponse(
            $this->serializer->serialize(['id' => $category->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/admin/idiom/categories/sort', 'api_idiom_category_sort', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function sortIdiomCategories(Request $request, IdiomCategoryRepository $idiomCategoryRepository, bool $enableIdiom): JsonResponse
    {
        if (false === $enableIdiom) {
            throw $this->createNotFoundException('Not Found');
        }
        
        $data = json_decode($request->getContent(), true);
        $categories = $idiomCategoryRepository->findAll();

        foreach ($categories as $category) {
            $result = array_filter($data, function ($item) use ($category) {
                return $category->getId() === (int) $item['id'];
            });

            if (!empty($result)) {
                $result = array_values($result)[0];
                $position = $result['position'];
                $category->setPosition($position);
                $this->em->persist($category);
            }
        }

        $this->em->flush();

        return $this->json(['message'=>'success'], Response::HTTP_OK);
    }

    #[Route('/api/admin/idiom/articles/{id}/order', 'api_idiom_article_order', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN')")]
    public function updateIdiomArticleOrder(Idiom $idiom, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        foreach ($idiom->getIdiomArticles() as $article) {
            $result = array_filter($data, function ($item) use ($article) {
                return $article->getId() === (int) $item['id'];
            });

            if (!empty($result)) {
                $result = array_values($result)[0];
                $position = $result['position'];
                $article->setPosition($position);
                $this->em->persist($article);
            }
        }

        $this->em->flush();

        return new JsonResponse(
            $this->serializer->serialize(['id' => $idiom->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
