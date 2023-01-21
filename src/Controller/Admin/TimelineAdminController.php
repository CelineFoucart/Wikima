<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\Timeline;
use App\Form\TimelineEventType;
use App\Repository\EventRepository;
use App\Repository\TimelineRepository;
use DateTime;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TimelineAdminController extends CRUDController
{
    public function __construct(
        private EventRepository $eventRepository,
        private TimelineRepository $timelineRepository
    ) {
    }

    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")]
    public function eventAction(int $id, Request $request): Response
    {
        $timeline = $this->getTimeline($id);
        $event = $this->getEvent($timeline, $request->query->getInt('event'));
        $form = $this->createForm(TimelineEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $event->getCreatedAt()) {
                $event->setCreatedAt(new DateTimeImmutable());
                $lastEvent = $timeline->getEvents()->last();
                if ($lastEvent instanceof Event) {
                    $timelineOrder = $lastEvent->getTimelineOrder() + 1;
                } else {
                    $timelineOrder = 0;
                }

                $event->setTimelineOrder($timelineOrder);
            }
            $event->setUpdatedAt(new DateTime());

            $this->eventRepository->add($event, true);
            $this->addFlash('success', 'Les modifications ont bien été enregistrées.');
            $url = $this->admin->generateObjectUrl('event', $timeline);

            return $this->redirect($url);
        }

        return $this->render('Admin/timeline_event.html.twig', [
            'timeline' => $timeline,
            'form' => $form->createView(),
        ]);
    }

    private function getEvent(Timeline $timeline, int $eventId): Event
    {
        $event = null;
        if (0 !== $eventId) {
            $event = $this->eventRepository->find($eventId);
        }

        if (null === $event) {
            $event = (new Event())->setTimeline($timeline);
        }

        return $event;
    }

    private function getTimeline(int $id): Timeline
    {
        $timeline = $this->timelineRepository->findTimelineEventsById($id);

        if (!$timeline instanceof Timeline) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        return $timeline;
    }
}
