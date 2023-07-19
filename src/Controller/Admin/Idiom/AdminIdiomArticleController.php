<?php

namespace App\Controller\Admin\Idiom;

use App\Entity\Idiom;
use App\Entity\IdiomArticle;
use App\Form\IdiomArticleType;
use App\Repository\IdiomArticleRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/admin/idiom')]
#[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')")]
class AdminIdiomArticleController extends AbstractController
{
    public function __construct(
        private IdiomArticleRepository $idiomArticleRepository
    ) {
    }

    #[Route('/{id}/articles/create', name: 'admin_app_idiom_article_create', methods: ['GET', 'POST'])]
    public function createAction(Idiom $idiom, Request $request): Response
    {
        $article = (new IdiomArticle())->setIdiom($idiom);
        $form = $this->createForm(IdiomArticleType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $article->setCreatedAt(new DateTimeImmutable());
            $this->idiomArticleRepository->add($article, true);
            $this->addFlash('success', "L'article a été ajouté.");

            return $this->redirectAction($article, $request);
        }

        return $this->render('Admin/idiom_article/create.html.twig', [
            'idiom' => $idiom,
            'section_active' => true,
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/articles/{id}/edit', name: 'admin_app_idiom_article_edit', methods: ['GET', 'POST'])]
    public function editAction(IdiomArticle $article, Request $request): Response
    {
        $form = $this->createForm(IdiomArticleType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $article->setUpdatedAt(new \DateTime());
            $this->idiomArticleRepository->add($article, true);
            $this->addFlash('success', "L'article a été modifié.");

            return $this->redirectAction($article, $request);
        }

        return $this->render('Admin/idiom_article/create.html.twig', [
            'idiom' => $article->getIdiom(),
            'section_active' => true,
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_idiom_article_delete', methods: ['POST'])]
    public function deleteAction(Request $request, IdiomArticle $idiomArticle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$idiomArticle->getId(), $request->request->get('_token'))) {
            $this->idiomArticleRepository->remove($idiomArticle, true);

            $this->addFlash('success', "L'article a été supprimé.");
        }

        return $this->redirectToRoute('admin_app_idiom_article', ['id' => $idiomArticle->getIdiom()->getId()], Response::HTTP_SEE_OTHER);
    }

    private function redirectAction(IdiomArticle $idiomArticle, Request $request): RedirectResponse
    {
        if (null !== $request->get('btn_save_and_list')) {
            return $this->redirectToRoute('admin_app_idiom_article', ['id' => $idiomArticle->getIdiom()->getId()]);
        }

        if (null !== $request->get('btn_save_and_create')) {
            return $this->redirectToRoute('admin_app_idiom_article_create', ['id' => $idiomArticle->getIdiom()->getId()]);
        }

        if (null !== $request->get('btn_save_and_edit')) {
            return $this->redirectToRoute('admin_app_idiom_article_edit', ['id' => $idiomArticle->getId()]);
        }
    }
}