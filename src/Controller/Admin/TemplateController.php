<?php

namespace App\Controller\Admin;

use App\Entity\Template;
use App\Form\Admin\TemplateType;
use App\Repository\TemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/template')]
class TemplateController extends AbstractAdminController
{
    protected string $entityName = "template";

    #[Route('/', name: 'admin_app_template_list', methods: ['GET'])]
    public function index(TemplateRepository $templateRepository): Response
    {
        return $this->render('Admin/template/index.html.twig', [
            'templates' => $templateRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_template_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $template = new Template();
        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($template);
            $entityManager->flush();

            return $this->redirectTo($request, $template->getId());
        }

        return $this->render('Admin/template/new.html.twig', [
            'template' => $template,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_template_show', methods: ['GET'])]
    public function show(Template $template): Response
    {
        return $this->render('Admin/template/show.html.twig', [
            'template' => $template,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_template_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Template $template, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TemplateType::class, $template);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectTo($request, $template->getId());
        }

        return $this->render('Admin/template/edit.html.twig', [
            'template' => $template,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_template_delete', methods: ['POST'])]
    public function delete(Request $request, Template $template, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$template->getId(), $request->request->get('_token'))) {
            $entityManager->remove($template);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_app_template_list', [], Response::HTTP_SEE_OTHER);
    }
}
