<?php

namespace App\Controller\Admin\Forum;

use App\Entity\ForumGroup;
use App\Form\Admin\ForumGroupType;
use App\Repository\ForumGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;

#[Route('/admin/forumgroup')]
class AdminForumGroupController extends AbstractAdminController
{
    protected string $entityName = 'forum_group';

    public function __construct(
        bool $enableForum
    ) {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'admin_app_forum_group_list', methods: ['GET'])]
    public function index(ForumGroupRepository $forumGroupRepository): Response
    {
        return $this->render('Admin/forum_group/index.html.twig', [
            'forum_groups' => $forumGroupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_app_forum_group_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $forumGroup = new ForumGroup();
        $form = $this->createForm(ForumGroupType::class, $forumGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($forumGroup);
            $entityManager->flush();

            return $this->redirectTo($request, $forumGroup->getId());
        }

        return $this->render('Admin/forum_group/new.html.twig', [
            'forum_group' => $forumGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_app_forum_group_show', methods: ['GET'])]
    public function show(ForumGroup $forumGroup): Response
    {
        return $this->render('Admin/forum_group/show.html.twig', [
            'forum_group' => $forumGroup,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_forum_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ForumGroup $forumGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ForumGroupType::class, $forumGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectTo($request, $forumGroup->getId());
        }

        return $this->render('Admin/forum_group/edit.html.twig', [
            'forum_group' => $forumGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_app_forum_group_delete', methods: ['POST'])]
    public function delete(Request $request, ForumGroup $forumGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forumGroup->getId(), $request->request->get('_token'))) {
            $entityManager->remove($forumGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_app_forum_group_list', [], Response::HTTP_SEE_OTHER);
    }
}
