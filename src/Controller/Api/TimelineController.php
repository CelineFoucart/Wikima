<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Event;
use App\Entity\Timeline;
use App\Form\Admin\TimelineEventType;
use App\Repository\EventRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/timeline')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class TimelineController extends AbstractController
{
    #[Route('/{id}', name: 'api_timeline_show', methods:['GET'])]
    public function showAction(Timeline $timeline): JsonResponse
    {
        return $this->json($timeline, Response::HTTP_OK, [], ['groups' => 'timeline-admin']);
    }

    #[Route('/{id}/events', name: 'api_timeline_event_add', methods:['POST'])]
    public function appendEventAction(Timeline $timeline, Request $request, EventRepository $eventRepository): JsonResponse
    {
        $event = new Event();
        $event->setTimeline($timeline);
        $form = $this->createForm(TimelineEventType::class, $event);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json("Invalid Form", Response::HTTP_BAD_REQUEST);
        }

        $event->setCreatedAt(new \DateTimeImmutable())->setUpdatedAt(new \DateTime());
        $lastEvent = $timeline->getEvents()->last();
        if ($lastEvent instanceof Event) {
            $timelineOrder = $lastEvent->getTimelineOrder() + 1;
        } else {
            $timelineOrder = 0;
        }
        $event->setTimelineOrder($timelineOrder);
        $eventRepository->add($event, true);
        $timeline->addEvent($event);

        return $this->json($timeline, Response::HTTP_CREATED, [], ['groups' => 'timeline-admin']);
    }

    #[Route('/{id}/events/{eventId}', name: 'api_timeline_event_edit', methods:['PUT'])]
    public function editEventAction(
        #[MapEntity(id: 'id')] Timeline $timeline, 
        #[MapEntity(id: 'eventId')] Event $event,
        Request $request, 
        EventRepository $eventRepository
    ): JsonResponse {
        if ($event->getTimeline()->getId() !== $timeline->getId()) {
            return $this->json("Invalid data", Response::HTTP_BAD_REQUEST);
        }

        $form = $this->createForm(TimelineEventType::class, $event);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->json("Invalid Form", Response::HTTP_BAD_REQUEST);
        }

        $event->setUpdatedAt(new \DateTime());
        $eventRepository->add($event, true);

        return $this->json($timeline, Response::HTTP_OK, [], ['groups' => 'timeline-admin']);
    }

    #[Route('/{id}/events/{eventId}', name: 'api_timeline_event_delete', methods:['DELETE'])]
    public function deleteEventAction(
        #[MapEntity(id: 'id')] Timeline $timeline, 
        #[MapEntity(id: 'eventId')] Event $event, 
        EventRepository $eventRepository
    ): JsonResponse {
        if ($event->getTimeline()->getId() !== $timeline->getId()) {
            return $this->json("Invalid data", Response::HTTP_BAD_REQUEST);
        }

        $timeline->removeEvent($event);
        $eventRepository->remove($event, true);

        return $this->json($timeline, Response::HTTP_OK, [], ['groups' => 'timeline-admin']);
    }
}
