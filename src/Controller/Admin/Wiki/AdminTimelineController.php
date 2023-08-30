<?php

declare(strict_types=1);

namespace App\Controller\Admin\Wiki;

use App\Entity\Event;
use App\Entity\Timeline;
use App\Repository\EventRepository;
use App\Form\Admin\TimelineFormType;
use App\Repository\PortalRepository;
use App\Form\Admin\TimelineEventType;
use App\Repository\CategoryRepository;
use App\Repository\TimelineRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/timeline')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class AdminTimelineController extends AbstractAdminController
{
    protected string $entityName = "timeline";

    public function __construct(
        private EventRepository $eventRepository,
        private TimelineRepository $timelineRepository
    ) {
    }

    #[Route('/', name: 'admin_app_timeline_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/timeline/list.html.twig', [
            'timelines' => $this->timelineRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_timeline_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, CategoryRepository $categoryRepository, PortalRepository $portalRepository): Response
    {
        $timeline = new Timeline();

        $categoryId = $request->query->getInt('category');
        if (0 !== $categoryId) {
            $category = $categoryRepository->find($categoryId);
            if ($category) {
                $timeline->addCategory($category);
            }
        }

        $portalId = $request->query->getInt('portal');
        if (0 !== $portalId) {
            $portal = $portalRepository->find($portalId);
            if ($portal) {
                $timeline->addPortal($portal);
            }
        }
        
        $form = $this->createForm(TimelineFormType::class, $timeline);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $timeline->setCreatedAt(new \DateTimeImmutable());
            $this->timelineRepository->add($timeline, true);
            $this->addFlash('success', "La chronologie " . $timeline->getTitle() . " a bien été créée.");

            return $this->redirectTo($request, $timeline->getId());
        }

        return $this->render('Admin/timeline/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_timeline_show', methods:['GET'])]
    public function showAction(Timeline $timeline): Response
    {
        return $this->render('Admin/timeline/show.html.twig', [
            'timeline' => $timeline,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_timeline_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, Timeline $timeline): Response
    {
        $form = $this->createForm(TimelineFormType::class, $timeline);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $timeline->setUpdatedAt(new \DateTime());
            $this->timelineRepository->add($timeline, true);
            $this->addFlash('success', "Le portail " . $timeline->getTitle() . " a bien été créée.");

            return $this->redirectTo($request, $timeline->getId());
        }

        return $this->render('Admin/timeline/edit.html.twig', [
            'form' => $form->createView(),
            'timeline' => $timeline,
        ]);
    }

    #[Route('/{id}/event', name: 'admin_app_timeline_event', methods:['GET', 'POST'])]
    public function eventAction(Request $request, Timeline $timeline): Response
    {
        $event = $this->getEvent($timeline, $request->query->getInt('event'));
        $form = $this->createForm(TimelineEventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $event->getCreatedAt()) {
                $event->setCreatedAt(new \DateTimeImmutable());
                $lastEvent = $timeline->getEvents()->last();
                if ($lastEvent instanceof Event) {
                    $timelineOrder = $lastEvent->getTimelineOrder() + 1;
                } else {
                    $timelineOrder = 0;
                }

                $event->setTimelineOrder($timelineOrder);
            }
            $event->setUpdatedAt(new \DateTime());

            $this->eventRepository->add($event, true);
            $this->addFlash('success', 'Les modifications ont bien été enregistrées.');

            return $this->redirectToRoute('admin_app_timeline_event', ['id' => $timeline->getId()]);
        }

        return $this->render('Admin/timeline/event.html.twig', [
            'timeline' => $timeline,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_timeline_delete', methods:['POST'])]
    public function deleteAction(Request $request, Timeline $timeline): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timeline->getId(), $request->request->get('_token'))) {
            $this->timelineRepository->remove($timeline, true);
            $this->addFlash('success', "L'élément a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_timeline_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/event/{id}/delete', name: 'admin_app_event_delete', methods: ['POST'])]
    public function deleteEvent(Event $event, Request $request, EventRepository $eventRepository): Response
    {
        $timeline = $event->getTimeline();

        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $eventRepository->remove($event, true);
            $this->addFlash('success', 'La section a bien été supprimée.');
        } else {
            $this->addFlash('error', 'La suppression a échouée.');
        }

        return $this->redirectToRoute('admin_app_timeline_event', ['id' => $timeline->getId()]);
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

}