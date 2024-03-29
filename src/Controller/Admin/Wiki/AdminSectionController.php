<?php

declare(strict_types=1);

namespace App\Controller\Admin\Wiki;

use App\Entity\Article;
use App\Entity\Section;
use App\Form\Admin\SectionConvertType;
use App\Form\SectionType;
use App\Repository\ArticleRepository;
use App\Repository\SectionRepository;
use App\Security\Voter\VoterHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
#[Route('/admin/section')]
class AdminSectionController extends AbstractController
{
    public function __construct(
        private SectionRepository $sectionRepository
    ) {
    }

    #[Route('/', name: 'admin_app_section_list')]
    public function indexAction(): Response
    {
        return $this->render('Admin/section/index.html.twig');
    }

    #[Route('/{id}/show', name: 'admin_app_section_show')]
    public function showAction(Section $section, Request $request, ArticleRepository $articleRepository): Response
    {
        if ($request->isMethod('POST')) {
            $this->denyAccessUnlessGranted(VoterHelper::EDIT, $section->getArticle(), 'Access Denied.');
            $id = $request->request->get('article');
            $article = (null !== $id) ? $articleRepository->find($id) : null;

            if ($article) {
                $section->setArticle($article);
                $lastSection = $article->getSections()->last();
                if ($lastSection instanceof Section) {
                    $position = $lastSection->getPosition() + 1;
                } else {
                    $position = 0;
                }
                $section->setPosition($position);

                $this->sectionRepository->add($section, true);
                $this->addFlash('success', "L'article a bien été lié à la langue.");
            }

            return $this->redirectToRoute('admin_app_section_show', ['id' => $section->getId()]);
        }

        return $this->render('Admin/section/show.html.twig', [
            'section' => $section,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_section_edit')]
    public function editAction(Section $section, Request $request): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $section->getArticle());

        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $section->setUpdatedAt(new \DateTime());
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
            $article->setAuthor($this->getUser())->setCreatedAt(new \DateTime());
            $articleRepository->add($article, true);
            $action = $form->get('actions')->getData();

            if (0 == $action) {
                $this->sectionRepository->remove($section, true);

                return $this->redirectToRoute('app_article_show', ['slug' => $article->getSlug()]);
            } elseif (1 == $action) {
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

        if ('POST' === $request->getMethod()) {
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
