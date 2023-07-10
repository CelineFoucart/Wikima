<?php

declare(strict_types=1);

namespace App\Controller\Admin\Wiki;

use App\Entity\Section;
use App\Form\SectionType;
use App\Repository\ArticleRepository;
use App\Repository\ImageRepository;
use App\Repository\SectionRepository;
use App\Security\Voter\VoterHelper;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_EDITOR')")]
class AdminSectionController extends AbstractController
{
    public function __construct(
        private SectionRepository $sectionRepository,
        private ImageRepository $imageRepository
    ) {
    }

    #[Route('/admin/section/{id}/edit', name: 'admin_app_section_edit')]
    public function editAction(Section $section, Request $request): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $section->getArticle());

        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->sectionRepository->add($section, true);
            $this->addFlash('success', 'Les modifications ont bien été enregistrées.');

            return $this->redirectToRoute('admin_app_section_edit', ['id' => $section->getId()]);
        }

        return $this->render('Admin/section/edit.html.twig', [
            'form' => $form->createView(),
            'section' => $section,
            'images' => $this->imageRepository->findAll(),
        ]);
    }

    #[Route('/admin/section/{id}/delete', name: 'admin_app_section_delete')]
    public function delete(Section $section, SectionRepository $sectionRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $section->getArticle());
        
        $article = $section->getArticle();

        if ($request->getMethod() === 'POST') {
            if ($this->isCsrfTokenValid('delete'.$section->getId(), $request->request->get('_token'))) {
                $sectionRepository->remove($section, true);
    
                return $this->redirectToRoute('admin_app_article_section', ['id' => $article->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('Admin/section/delete.html.twig', [
            'section' => $section,
        ]);
    }
}