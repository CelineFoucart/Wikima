<?php

declare(strict_types=1);

namespace App\Controller\Admin\Wiki;

use App\Controller\Admin\AbstractAdminController;
use App\Entity\Event;
use App\Entity\Timeline;
use App\Form\Admin\TimelineEventType;
use App\Form\Admin\TimelineFormType;
use App\Repository\CategoryRepository;
use App\Repository\EventRepository;
use App\Repository\PortalRepository;
use App\Repository\TimelineRepository;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/timeline')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class AdminTimelineController extends AbstractAdminController
{
    protected string $entityName = 'timeline';

    public function __construct(
        private EventRepository $eventRepository,
        private TimelineRepository $timelineRepository
    ) {
    }

    #[Route('/', name: 'admin_app_timeline_list', methods: ['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/timeline/list.html.twig', [
            'timelines' => $this->timelineRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_timeline_create', methods: ['GET', 'POST'])]
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
            $this->addFlash('success', 'La chronologie '.$timeline->getTitle().' a bien été créée.');

            return $this->redirectTo($request, $timeline->getId());
        }

        return $this->render('Admin/timeline/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_timeline_show', methods: ['GET'])]
    public function showAction(Timeline $timeline): Response
    {
        return $this->render('Admin/timeline/show.html.twig', [
            'timeline' => $timeline,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_timeline_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Timeline $timeline): Response
    {
        $form = $this->createForm(TimelineFormType::class, $timeline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $timeline->setUpdatedAt(new \DateTime());
            $this->timelineRepository->add($timeline, true);
            $this->addFlash('success', 'Le portail '.$timeline->getTitle().' a bien été créée.');

            return $this->redirectTo($request, $timeline->getId());
        }

        return $this->render('Admin/timeline/edit.html.twig', [
            'form' => $form->createView(),
            'timeline' => $timeline,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_timeline_delete', methods: ['POST'])]
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
