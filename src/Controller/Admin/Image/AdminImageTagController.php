<?php

declare(strict_types=1);

namespace App\Controller\Admin\Image;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use App\Entity\ImageTag;
use App\Form\Admin\ImageTagFormType;
use App\Repository\ImageTagRepository;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/admin/image/type')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class AdminImageTagController extends AbstractAdminController
{
    protected string $entityName = "imagetype";

    #[Route('/', name: 'admin_app_imagetype_list', methods:['GET'])]
    public function listAction(ImageTagRepository $imageTagRepository): Response
    {
        return $this->render('Admin/image_type/list.html.twig', [
            'imageTypes' => $imageTagRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_imagetype_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, ImageTagRepository $imageTagRepository): Response
    {
        $imageTag = new ImageTag();
        $form = $this->createForm(ImageTagFormType::class, $imageTag);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $imageTagRepository->save($imageTag, true);
            $this->addFlash('success', "Le tag " . $imageTag->getTitle() . " a bien été créé.");

            return $this->redirectTo($request, $imageTag->getId());
        }

        return $this->render('Admin/image_type/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_imagetype_show', methods:['GET'])]
    public function showAction(ImageTag $imageTag): Response
    {
        return $this->render('Admin/image_type/show.html.twig', [
            'imageType' => $imageTag,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_imagetype_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, ImageTag $imageTag, ImageTagRepository $imageTagRepository): Response
    {
        $form = $this->createForm(ImageTagFormType::class, $imageTag);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $imageTagRepository->save($imageTag, true);
            $this->addFlash('success', "Le type " . $imageTag->getTitle() . " a bien été modifié.");

            return $this->redirectTo($request, $imageTag->getId());
        }

        return $this->render('Admin/image_type/edit.html.twig', [
            'form' => $form->createView(),
            'imageType' => $imageTag,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_imagetype_delete', methods:['POST'])]
    public function deleteAction(Request $request, ImageTag $imageTag, ImageTagRepository $imageTagRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$imageTag->getId(), $request->request->get('_token'))) {
            $imageTagRepository->remove($imageTag, true);
            $this->addFlash('success', "L'élément a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_imagetype_list', [], Response::HTTP_SEE_OTHER);
    }
}