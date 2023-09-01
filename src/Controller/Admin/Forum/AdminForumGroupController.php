<?php

namespace App\Controller\Admin\Forum;

use App\Entity\ForumGroup;
use App\Form\Admin\ForumGroupType;
use App\Repository\ForumGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/forumgroup')]
class AdminForumGroupController extends AbstractController
{
    #[Route('/', name: 'app_admin_forum_group_index', methods: ['GET'])]
    public function index(ForumGroupRepository $forumGroupRepository): Response
    {
        return $this->render('Admin/forum_group/index.html.twig', [
            'forum_groups' => $forumGroupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_forum_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $forumGroup = new ForumGroup();
        $form = $this->createForm(ForumGroupType::class, $forumGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($forumGroup);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_forum_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/forum_group/new.html.twig', [
            'forum_group' => $forumGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_forum_group_show', methods: ['GET'])]
    public function show(ForumGroup $forumGroup): Response
    {
        return $this->render('Admin/forum_group/show.html.twig', [
            'forum_group' => $forumGroup,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_forum_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ForumGroup $forumGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ForumGroupType::class, $forumGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_forum_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/forum_group/edit.html.twig', [
            'forum_group' => $forumGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_forum_group_delete', methods: ['POST'])]
    public function delete(Request $request, ForumGroup $forumGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forumGroup->getId(), $request->request->get('_token'))) {
            $entityManager->remove($forumGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_forum_group_index', [], Response::HTTP_SEE_OTHER);
    }
}
