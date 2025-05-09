<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Event;
use App\Entity\Timeline;
use App\Form\Admin\TimelineEventType;
use App\Service\ReorderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/timeline')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class TimelineController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/{id}', name: 'api_timeline_show', methods: ['GET'])]
    public function showAction(Timeline $timeline): JsonResponse
    {
        return $this->json($timeline, Response::HTTP_OK, [], ['groups' => 'timeline:show']);
    }

    #[Route('/{id}/events', name: 'api_timeline_event_add', methods: ['POST'])]
    public function appendEventAction(Timeline $timeline, Request $request): JsonResponse
    {
        $event = new Event();
        $event->setTimeline($timeline);
        $form = $this->createForm(TimelineEventType::class, $event);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json('Invalid Form', Response::HTTP_BAD_REQUEST);
        }

        $event->setCreatedAt(new \DateTimeImmutable())->setUpdatedAt(new \DateTime());
        $lastEvent = $timeline->getEvents()->last();
        if ($lastEvent instanceof Event) {
            $timelineOrder = $lastEvent->getTimelineOrder() + 1;
        } else {
            $timelineOrder = 0;
        }
        $event->setTimelineOrder($timelineOrder);

        $this->entityManager->persist($event);
        $this->entityManager->flush();
        $timeline->addEvent($event);

        return $this->json($timeline, Response::HTTP_CREATED, [], ['groups' => 'timeline:show']);
    }

    #[Route('/{id}/events/{eventId}', name: 'api_timeline_event_edit', methods: ['PUT'])]
    public function editEventAction(#[MapEntity(id: 'id')] Timeline $timeline, #[MapEntity(id: 'eventId')] Event $event, Request $request): JsonResponse
    {
        if ($event->getTimeline()->getId() !== $timeline->getId()) {
            return $this->json('Invalid data', Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(TimelineEventType::class, $event);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json('Invalid Form', Response::HTTP_BAD_REQUEST);
        }

        $event->setUpdatedAt(new \DateTime());
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $this->json($timeline, Response::HTTP_OK, [], ['groups' => 'timeline:show']);
    }

    #[Route('/{id}/events/{eventId}/position', name: 'api_timeline_event_position', methods: ['PUT'])]
    public function sortEventAction(
        #[MapEntity(id: 'id')] Timeline $timeline,
        #[MapEntity(id: 'eventId')] Event $event,
        ReorderService $reorderService,
        Request $request
    ): JsonResponse {
        if ($event->getTimeline()->getId() !== $timeline->getId()) {
            return $this->json('Invalid data', Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        $position = (isset($data['position'])) ? $data['position'] : $timeline->getEvents()->count();
        $reorderService->setElements($timeline->getEvents()->toArray())->insertToNewPosition($event->getId(), $position);
        $this->entityManager->refresh($timeline);

        return $this->json($timeline, Response::HTTP_OK, [], ['groups' => 'timeline:show']);
    }

    #[Route('/{id}/events/{eventId}', name: 'api_timeline_event_delete', methods: ['DELETE'])]
    public function deleteEventAction(#[MapEntity(id: 'id')] Timeline $timeline, #[MapEntity(id: 'eventId')] Event $event): JsonResponse
    {
        if ($event->getTimeline()->getId() !== $timeline->getId()) {
            return $this->json('Invalid data', Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->remove($event);
        $this->entityManager->flush();

        return $this->json($timeline, Response::HTTP_OK, [], ['groups' => 'timeline:show']);
    }
}
