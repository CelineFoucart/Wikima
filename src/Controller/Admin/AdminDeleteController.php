<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Entity\Section;
use App\Repository\EventRepository;
use App\Repository\SectionRepository;
use App\Security\Voter\VoterHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminDeleteController extends AbstractController
{
    #[Route('/admin/app/event/{id}/delete', name: 'admin_app_event_delete', methods: ['POST'])]
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
}
