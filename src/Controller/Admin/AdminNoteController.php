<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Note;
use App\Form\Admin\NoteFormType;
use App\Repository\NoteRepository;
use App\Repository\PortalRepository;
use App\Repository\CategoryRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/note')]
final class AdminNoteController extends AbstractAdminController
{
    protected string $entityName = "note";

    public function __construct(
        private NoteRepository $noteRepository
    ) {
    }

    #[Route('/', name: 'admin_app_note_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/note/list.html.twig', [
            'notes' => $this->noteRepository->findForAdminList(),
        ]);
    }

    #[Route('/archive', name: 'admin_app_note_archive_index', methods:['GET'])]
    public function archiveIndexAction(): Response
    {
        return $this->render('Admin/note/archive.html.twig', [
            'notes' => $this->noteRepository->findForAdminList(true),
        ]);
    }

    #[Route('/create', name: 'admin_app_note_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, CategoryRepository $categoryRepository, PortalRepository $portalRepository): Response
    {
        $note = new Note();

        $categoryId = $request->query->getInt('category');
        if (0 !== $categoryId) {
            $category = $categoryRepository->find($categoryId);
            if ($category) {
                $note->setCategory($category);
            }
        }

        $portalId = $request->query->getInt('portal');
        if (0 !== $portalId) {
            $portal = $portalRepository->find($portalId);
            if ($portal) {
                $note->setPortal($portal);
            }
        }

        $form = $this->createForm(NoteFormType::class, $note);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $note->setCreatedAt(new DateTimeImmutable()); 
            $this->noteRepository->save($note, true);
            $this->addFlash('success', "La note " . $note . " a bien été créé.");

            return $this->redirectTo($request, $note->getId());
        }

        return $this->render('Admin/note/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_note_show', methods:['GET'])]
    public function showAction(Note $note): Response
    {
        return $this->render('Admin/note/show.html.twig', [
            'note' => $note,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_note_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, Note $note): Response
    {
        $referer = $request->query->get('referer');
        
        $form = $this->createForm(NoteFormType::class, $note);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $note->setUpdatedAt(new DateTime());
            $this->noteRepository->save($note, true);
            $this->addFlash('success', "La note " . $note . " a bien été modifiée.");

            if (null !== $referer) {
                return $this->redirect($referer);
            }

            return $this->redirectTo($request, $note->getId());
        }

        return $this->render('Admin/note/edit.html.twig', [
            'form' => $form->createView(),
            'note' => $note,
            'referer' => $referer,
        ]);
    }

    #[Route('/{id}/archive', name: 'admin_app_note_archive', methods:['POST'])]
    public function archiveAction(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid('archive'.$note->getId(), $request->request->get('_token'))) {
            $isArchived = (bool) $note->getIsArchived();
            $message = $isArchived ? "désarchivée" : "archivée";
            $note->setIsArchived(!$isArchived);
            $this->noteRepository->save($note, true);

            $this->addFlash('success', "La note a été {$message} avec succès.");
        }

        return $this->redirectToRoute('admin_app_note_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'admin_app_note_delete', methods:['POST'])]
    public function deleteAction(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->request->get('_token'))) {
            $this->noteRepository->remove($note, true);
            $this->addFlash('success', "La note a été supprimée avec succès.");
        }

        return $this->redirectToRoute('admin_app_note_list', [], Response::HTTP_SEE_OTHER);
    }
}