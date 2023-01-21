<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Timeline;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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

    #[Route('api/admin/article/{id}/section', 'api_article_section', methods: ['POST'])]
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
}
