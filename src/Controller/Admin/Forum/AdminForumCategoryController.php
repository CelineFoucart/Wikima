<?php

declare(strict_types=1);

namespace App\Controller\Admin\Forum;

use App\Entity\ForumCategory;
use App\Form\Admin\ForumCategoryType;
use App\Repository\ForumCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/forumcategory')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
class AdminForumCategoryController extends AbstractAdminController
{
    protected string $entityName = 'forum_category';

    public function __construct(
        bool $enableForum
    ) {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'admin_app_forum_category_list')]
    public function listAction(ForumCategoryRepository $forumCategoryRepository): Response
    {
        return $this->render('Admin/forum_category/list.html.twig', [
            'categories' => $forumCategoryRepository->findByOrder(),
        ]);
    }

    #[Route('/create', name: 'admin_app_forum_category_create')]
    public function createAction(Request $request, ForumCategoryRepository $forumCategoryRepository): Response
    {
        $category = new ForumCategory();
        $form = $this->createForm(ForumCategoryType::class, $category);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $position = $forumCategoryRepository->findMaxPosition();
            $category->setPosition($position+1);
            $forumCategoryRepository->add($category, true);
            $this->addFlash('success', "La catégorie a été créée.");

            return $this->redirectTo($request, $category->getId());
        }

        return $this->render('Admin/forum_category/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_forum_category_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, ForumCategory $category, ForumCategoryRepository $forumCategoryRepository): Response
    {
        $form = $this->createForm(ForumCategoryType::class, $category);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $forumCategoryRepository->add($category, true);
            $this->addFlash('success', "La catégorie " . $category->getTitle() . " a bien été modifiée.");

            return $this->redirectTo($request, $category->getId());
        }

        return $this->render('Admin/forum_category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route('/{id}', name: 'admin_app_forum_category_show')]
    public function showAction(ForumCategory $category): Response
    {
        return $this->render('Admin/forum_category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_forum_category_delete', methods:['POST'])]
    public function deleteAction(Request $request, ForumCategory $category, ForumCategoryRepository $forumCategoryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $forumCategoryRepository->remove($category, true);
            $this->addFlash('success', "L'élément a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_forum_category_list', [], Response::HTTP_SEE_OTHER);
    }
}