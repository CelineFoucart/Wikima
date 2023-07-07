<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Form\Admin\PageFormType;
use App\Repository\PageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/admin/page')]
#[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")]
final class AdminPageController extends AbstractAdminController
{
    protected string $entityName = "page";

    public function __construct(
        private PageRepository $pageRepository
    ) {
    }

    #[Route('/', name: 'admin_app_page_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/page/list.html.twig', [
            'pages' => $this->pageRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_page_create', methods:['GET', 'POST'])]
    public function createAction(Request $request): Response
    {
        $page = new Page();
        $form = $this->createForm(PageFormType::class, $page);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->pageRepository->add($page, true);
            $this->addFlash('success', "La page " . $page . " a bien été créé.");

            return $this->redirectTo($request, $page->getId());
        }

        return $this->render('Admin/page/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_page_show', methods:['GET'])]
    public function showAction(Page $page): Response
    {
        return $this->render('Admin/page/show.html.twig', [
            'page' => $page,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_page_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, Page $page): Response
    {
        $form = $this->createForm(PageFormType::class, $page);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->pageRepository->add($page, true);
            $this->addFlash('success', "La page " . $page . " a bien été modifiée.");

            return $this->redirectTo($request, $page->getId());
        }

        return $this->render('Admin/page/edit.html.twig', [
            'form' => $form->createView(),
            'page' => $page,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_page_delete', methods:['POST'])]
    public function deleteAction(Request $request, Page $page): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $this->pageRepository->remove($page, true);
            $this->addFlash('success', "La page a été supprimée avec succès.");
        }

        return $this->redirectToRoute('admin_app_page_list', [], Response::HTTP_SEE_OTHER);
    }
}