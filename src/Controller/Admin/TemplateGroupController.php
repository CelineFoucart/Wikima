<?php

namespace App\Controller\Admin;

use App\Entity\TemplateGroup;
use App\Form\Admin\TemplateGroupType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TemplateGroupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;

#[Route('/template/group')]
class TemplateGroupController extends AbstractAdminController
{
    protected string $entityName = "template_group";

    #[Route('/', name: 'admin_app_template_group_list', methods: ['GET'])]
    public function index(TemplateGroupRepository $templateGroupRepository): Response
    {
        return $this->render('Admin/template_group/index.html.twig', [
            'template_groups' => $templateGroupRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_template_group_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $templateGroup = new TemplateGroup();
        $form = $this->createForm(TemplateGroupType::class, $templateGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($templateGroup);
            $entityManager->flush();

            return $this->redirectTo($request, $templateGroup->getId());
        }

        return $this->render('Admin/template_group/new.html.twig', [
            'template_group' => $templateGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_template_group_show', methods: ['GET'])]
    public function show(TemplateGroup $templateGroup): Response
    {
        return $this->render('Admin/template_group/show.html.twig', [
            'template_group' => $templateGroup,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_template_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TemplateGroup $templateGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TemplateGroupType::class, $templateGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectTo($request, $templateGroup->getId());
        }

        return $this->render('Admin/template_group/edit.html.twig', [
            'template_group' => $templateGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_template_group_delete', methods: ['POST'])]
    public function delete(Request $request, TemplateGroup $templateGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$templateGroup->getId(), $request->request->get('_token'))) {
            $entityManager->remove($templateGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_app_template_group_list', [], Response::HTTP_SEE_OTHER);
    }
}
