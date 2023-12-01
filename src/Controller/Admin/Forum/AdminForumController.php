<?php

namespace App\Controller\Admin\Forum;

use App\Entity\Forum;
use App\Form\Admin\ForumType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use App\Entity\Category;
use App\Entity\ForumCategory;
use App\Repository\ForumRepository;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/forum')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
class AdminForumController extends AbstractAdminController
{
    protected string $entityName = 'forum';

    public function __construct(
        bool $enableForum
    ) {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/{id}/create', name: 'admin_app_forum_create', methods:['GET', 'POST'])]
    public function createAction(ForumCategory $category, Request $request, ForumRepository $forumRepository): Response
    {
        $forum = (new Forum())->setCategory($category);

        if ($category->getForums()->isEmpty()) {
            $position = 0;
        } else {
            $positions = [];
            foreach ($category->getForums() as $child) {
                $positions[] = $child->getPosition();
            }
            $position = max($positions);
        }

        $forum->setPosition($position);
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $forumRepository->add($forum, true);

            $this->addFlash('success', 'Le forum a bien été créé');
            
            return $this->redirectForum($request, $forum);
        }

        return $this->render('Admin/forum_category/forum/create.html.twig', [
            'form' => $form->createView(),
            'category' => $forum->getCategory(),
        ]);
    }

    #[Route('/{id}', name: 'admin_app_forum_edit', methods:['GET', 'POST'])]
    public function editAction(Forum $forum, Request $request, ForumRepository $forumRepository): Response
    {
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $forumRepository->add($forum, true);
            $this->addFlash('success', 'Le forum a bien été modifié');
            
            return $this->redirectForum($request, $forum);
        }

        return $this->render('Admin/forum_category/forum/edit.html.twig', [
            'form' => $form->createView(),
            'forum' => $forum,
            'category' => $forum->getCategory(),
        ]);
    }

    private function redirectForum(Request $request, Forum $forum): RedirectResponse
    {
        if (null !== $request->get('btn_save_and_list')) {
            return $this->redirectToRoute('admin_app_forum_category_show', ['id' => $forum->getCategory()->getId()]);
        }

        if (null !== $request->get('btn_save_and_create')) {
            return $this->redirectToRoute('admin_app_forum_create', ['id' => $forum->getCategory()->getId()]);
        }

        return $this->redirectTo($request, $forum->getId());
    }

    #[Route('/{id}/delete', name: 'admin_app_forum_delete', methods:['POST'])]
    public function deleteAction(Request $request, Forum $forum, ForumRepository $forumRepository): Response
    {
        $category = $forum->getCategory();

        if ($this->isCsrfTokenValid('delete'.$forum->getId(), $request->request->get('_token'))) {
            $forumRepository->remove($forum, true);
            $this->addFlash('success', "L'élément a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_forum_category_show', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
    }
}