<?php

declare(strict_types=1);

namespace App\Controller\Admin\Wiki;

use App\Entity\ArticleType;
use App\Form\Admin\ArticleTypeFormType;
use App\Repository\ArticleTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/admin/article/type')]
#[Security("is_granted('ROLE_ADMIN')")]
final class AdminArticleTypeController extends AbstractAdminController
{
    protected string $entityName = "articletype";

    #[Route('/', name: 'admin_app_articletype_list', methods:['GET'])]
    public function listAction(ArticleTypeRepository $articleTypeRepository): Response
    {
        return $this->render('Admin/article_type/list.html.twig', [
            'articleTypes' => $articleTypeRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_articletype_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, ArticleTypeRepository $articleTypeRepository): Response
    {
        $articleType = new ArticleType();
        $form = $this->createForm(ArticleTypeFormType::class, $articleType);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $articleTypeRepository->save($articleType, true);
            $this->addFlash('success', "Le type " . $articleType->getTitle() . " a bien été créé.");

            return $this->redirectTo($request, $articleType->getId());
        }

        return $this->render('Admin/article_type/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_articletype_show', methods:['GET'])]
    public function showAction(ArticleType $articleType): Response
    {
        return $this->render('Admin/article_type/show.html.twig', [
            'articleType' => $articleType,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_articletype_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, ArticleType $articleType, ArticleTypeRepository $articleTypeRepository): Response
    {
        $form = $this->createForm(ArticleTypeFormType::class, $articleType);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $articleTypeRepository->save($articleType, true);
            $this->addFlash('success', "Le type " . $articleType->getTitle() . " a bien été modifié.");

            return $this->redirectTo($request, $articleType->getId());
        }

        return $this->render('Admin/article_type/edit.html.twig', [
            'form' => $form->createView(),
            'articleType' => $articleType,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_articletype_delete', methods:['POST'])]
    public function deleteAction(Request $request, ArticleType $articleType, ArticleTypeRepository $articleTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$articleType->getId(), $request->request->get('_token'))) {
            $articleTypeRepository->remove($articleType, true);
            $this->addFlash('success', "L'élément a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_articletype_list', [], Response::HTTP_SEE_OTHER);
    }
}