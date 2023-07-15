<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Note;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Place;
use App\Entity\Timeline;
use App\Service\DataFilterService;
use App\Repository\PlaceRepository;
use App\Repository\PersonRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminApiController extends AbstractController
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

    #[Route('/api/admin/note/{id}/processed', 'api_note_processed', methods: ['POST'])]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')")]
    public function updateNoteProcessed(Note $note): JsonResponse
    {
        $currentStatus = $note->getIsProcessed();
        $note->setIsProcessed(!$currentStatus);
        $this->em->persist($note);
        $this->em->flush();

        return new JsonResponse(
            $this->serializer->serialize(['id' => $note->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/admin/articles', name: 'api_article_index', methods: ['GET'])]
    public function articleAction(ArticleRepository $articleRepository, Request $request): JsonResponse
    {
        $parameters = $request->query->all();
        $recordsFiltered = $articleRepository->countSearchTotal($parameters);
        $recordsTotal = $articleRepository->countSearchTotal([]);

        $data = $articleRepository->searchPaginatedItems($parameters);

        $data = [
            'draw' => isset($parameters['draw']) ? (int)$parameters['draw'] : 0,
            'recordsFiltered' => isset($recordsFiltered['recordsFiltered']) ? $recordsFiltered['recordsFiltered'] : 0,
            "data" => $data,
            'recordsTotal' => isset($recordsTotal['recordsFiltered']) ? $recordsTotal['recordsFiltered'] : 0,
        ];

        return $this->json($data, 200, [], ['groups' => 'index']);
    }

    #[Route('/api/admin/persons', name: 'api_person_index', methods: ['GET'])]
    public function personAction(PersonRepository $personRepository, Request $request): JsonResponse
    {
        $parameters = $request->query->all();
        $recordsFiltered = $personRepository->countSearchTotal($parameters);
        $recordsTotal = $personRepository->countSearchTotal([]);
        $data = [
            'draw' => isset($parameters['draw']) ? (int)$parameters['draw'] : 0,
            'recordsFiltered' => isset($recordsFiltered['recordsFiltered']) ? $recordsFiltered['recordsFiltered'] : 0,
            "data" => $personRepository->searchPaginatedItems($parameters),
            'recordsTotal' =>isset($recordsTotal['recordsFiltered']) ? $recordsTotal['recordsFiltered'] : 0,
        ];

        return $this->json($data, 200, [], ['groups' => 'index']);
    }

    #[Route('/api/admin/place', name: 'api_place_index', methods: ['GET'])]
    public function placeAction(PlaceRepository $placeRepository, Request $request): JsonResponse
    {
        $parameters = $request->query->all();
        $recordsFiltered = $placeRepository->countSearchTotal($parameters);
        $recordsTotal = $placeRepository->countSearchTotal([]);

        $data = [
            'draw' => isset($parameters['draw']) ? (int)$parameters['draw'] : 0,
            'recordsFiltered' => isset($recordsFiltered['recordsFiltered']) ? $recordsFiltered['recordsFiltered'] : 0,
            "data" => $placeRepository->searchPaginatedItems($parameters),
            'recordsTotal' => isset($recordsTotal['recordsFiltered']) ? $recordsTotal['recordsFiltered'] : 0,
        ];
        
        return $this->json($data, 200, [], ['groups' => 'index']);
        
    }
}
