<?php

declare(strict_types=1);

namespace App\Controller\Admin\Wiki;

use App\Entity\Portal;
use App\Form\Admin\PortalFormType;
use App\Repository\ImageRepository;
use App\Repository\PortalRepository;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/admin/portal')]
#[Security("is_granted('ROLE_ADMIN')")]
final class AdminPortalController extends AbstractAdminController
{
    protected string $entityName = "portal";

    public function __construct(
        private PortalRepository $portalRepository,
        private ImageRepository $imageRepository
    ) {
    }

    #[Route('/', name: 'admin_app_portal_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/portal/list.html.twig', [
            'portals' => $this->portalRepository->findAllWithCategory(),
        ]);
    }

    #[Route('/create', name: 'admin_app_portal_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, CategoryRepository $categoryRepository): Response
    {
        $portal = new Portal();

        $categoryId = $request->query->getInt('category');
        if (0 !== $categoryId) {
            $category = $categoryRepository->find($categoryId);
            if ($category) {
                $portal->addCategory($category);
            }
        }
        
        $form = $this->createForm(PortalFormType::class, $portal);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $portal->setCreatedAt(new \DateTimeImmutable());
            $this->portalRepository->add($portal, true);
            $this->addFlash('success', "Le portail " . $portal->getTitle() . " a bien été créé.");

            return $this->redirectTo($request, $portal->getId());
        }

        return $this->render('Admin/portal/create.html.twig', [
            'form' => $form->createView(),
            'images' => $this->imageRepository->findAll(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_portal_show', methods:['GET'])]
    public function showAction(Portal $portal): Response
    {
        return $this->render('Admin/portal/show.html.twig', [
            'portal' => $portal,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_portal_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, Portal $portal): Response
    {
        $form = $this->createForm(PortalFormType::class, $portal);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $portal->setUpdatedAt(new \DateTime());
            $this->portalRepository->add($portal, true);
            $this->addFlash('success', "Le portail " . $portal->getTitle() . " a bien été modifié.");

            return $this->redirectTo($request, $portal->getId());
        }

        return $this->render('Admin/portal/edit.html.twig', [
            'form' => $form->createView(),
            'portal' => $portal,
            'images' => $this->imageRepository->findAll(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_portal_delete', methods:['POST'])]
    public function deleteAction(Request $request, Portal $portal): Response
    {
        if ($this->isCsrfTokenValid('delete'.$portal->getId(), $request->request->get('_token'))) {
            $this->portalRepository->remove($portal, true);
            $this->addFlash('success', "L'élément a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_portal_list', [], Response::HTTP_SEE_OTHER);
    }

}