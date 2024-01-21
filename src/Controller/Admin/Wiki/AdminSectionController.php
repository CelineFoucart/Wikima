<?php

declare(strict_types=1);

namespace App\Controller\Admin\Wiki;

use DateTime;
use App\Entity\Article;
use App\Entity\Section;
use App\Form\SectionType;
use App\Security\Voter\VoterHelper;
use App\Repository\ArticleRepository;
use App\Repository\SectionRepository;
use App\Form\Admin\SectionConvertType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
#[Route('/admin/section')]
class AdminSectionController extends AbstractController
{
    public function __construct(
        private SectionRepository $sectionRepository
    ) {
    }

    #[Route('/{id}/edit', name: 'admin_app_section_edit')]
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
        ]);
    }

    #[Route('/{id}/conversion', name: 'admin_app_section_conversion')]
    public function conversionAction(Section $section, Request $request, SluggerInterface $slugger, ArticleRepository $articleRepository): Response
    {
        $article = (new Article())
            ->setTitle($section->getTitle())
            ->setSlug($slugger->slug(strtolower($section->getTitle()), '-')->toString())
            ->setContent($section->getContent())
            ->setKeywords($section->getArticle()->getKeywords())
            ->setType($section->getArticle()->getType())
            ->setDescription($section->getTitle())
            ->setEnableComment(true);

        foreach ($section->getArticle()->getPortals() as $portal) {
            $article->addPortal($portal);
        }

        $form = $this->createForm(SectionConvertType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $article->setAuthor($this->getUser())->setCreatedAt(new DateTime());
            $articleRepository->add($article, true);
            $action = $form->get('actions')->getData();

            if ($action == 0) {
                $this->sectionRepository->remove($section, true);
                
                return $this->redirectToRoute('app_article_show', ['slug' => $article->getSlug()]);
            } elseif ($action == 1) {
                return $this->redirectToRoute('admin_app_section_edit', ['id' => $section->getId()]);
            } else {
                return $this->redirectToRoute('app_article_show', ['slug' => $article->getSlug()]);
            }
        }

        return $this->render('Admin/section/conversion.html.twig', [
            'section' => $section,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_section_delete')]
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