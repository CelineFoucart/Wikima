<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\UserGroup;
use App\Service\UserService;
use App\Form\Admin\UserFormType;
use App\Repository\UserRepository;
use App\Repository\ForumGroupRepository;
use App\Repository\PrivateMessageReceivedRepository;
use App\Repository\PrivateMessageSentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/user')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class AdminUserController extends AbstractAdminController
{
    protected string $entityName = "user";

    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $em
    ) {
    }

    #[Route('/', name: 'admin_app_user_list', methods:['GET'])]
    public function listAction(Request $request, UserService $userService): Response
    {
        $roles = $userService->getAvailableRoles();
        $data = array_values($roles);
        $form = $this->createFormBuilder(['roles' => $data])
            ->setMethod('GET')
            ->add('roles', ChoiceType::class, [
                'choices' => $roles,
                'multiple' => true,
                'attr' => ['data-choices' => 'choices']
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $data = $form->get('roles')->getData();
        }

        return $this->render('Admin/user/list.html.twig', [
            'users' => $this->userRepository->findByRoles($data),
            'form' => $form,
        ]);
    }

    #[Route('/create', name: 'admin_app_user_create', methods:['GET', 'POST'])]
    public function createAction(Request $request): Response
    {
        $user = (new User())->setRoles(["ROLE_USER"]);
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setCreatedAt(new DateTimeImmutable());
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $plainPassword
                )
            );
            $this->userRepository->add($user, true);
            $this->addFlash('success', "L'utilisateur " . $user . " a bien été créé.");

            return $this->redirectTo($request, $user->getId());
        }

        return $this->render('Admin/user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_user_show', methods:['GET'])]
    public function showAction(User $user): Response
    {
        return $this->render('Admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_user_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, User $user): Response
    {
        if ($user instanceof User && in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $this->canHandleFounderUser($user);
        }

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            
            if ($plainPassword !== null) {
                $user->setPassword(
                    $this->userPasswordHasher->hashPassword(
                        $user,
                        $plainPassword
                    )
                );
            }

            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', "L'utilisateur " . $user . " a bien été modifié.");

            return $this->redirectTo($request, $user->getId());
        }

        return $this->render('Admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_user_delete', methods:['POST'])]
    public function deleteAction(Request $request, User $user, PrivateMessageSentRepository $sendRepository, PrivateMessageReceivedRepository $receiveRepository): Response
    {
        if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            throw new AccessDeniedException('Les administrateurs et les fondateurs ne peuvent être supprimés.');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->clearPrivateMessages($sendRepository, $receiveRepository, $user);
            $this->userRepository->remove($user, true);
            $this->addFlash('success', "L'utilisateur a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_user_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/group', name: 'admin_app_user_group', methods:['GET', 'POST'])]
    public function groupAction(Request $request, User $user, bool $enableForum, ForumGroupRepository $forumGroupRepository): Response
    {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }

        $userGroups = [];
        foreach ($user->getUserGroups() as $group) {
            $userGroups[] = $group->getForumGroup()->getId();
        }

        $groupId = $request->request->getInt('group', 0);
        $defaultGroup = $request->request->getInt('defaultGroup', 0);

        if ($request->isMethod('POST') && $groupId !== 0) {
            if ($groupId === 0 || in_array($groupId, $userGroups)) {
                $this->addFlash('error', 'Cet utilisateur est déjà membre de ce groupe.');

                return $this->redirectToRoute('admin_app_user_group', ['id' => $user->getId()]);
            }

            $group = $forumGroupRepository->find($groupId);

            if (null === $group) {
                $this->addFlash('error', "Ce groupe n'existe pas.");

                return $this->redirectToRoute('admin_app_user_group', ['id' => $user->getId()]);
            }

            $isDefault = (empty($userGroups));
            $userGroup = (new UserGroup())->setDefaultGroup($isDefault)->setMember($user)->setForumGroup($group);
            $this->em->persist($userGroup);
            $this->em->flush();
            $this->addFlash('success', "Le membre a bien été ajouté au groupe.");

            return $this->redirectToRoute('admin_app_user_group', ['id' => $user->getId()]);
        }

        if ($request->isMethod('POST')) {
            $toDelete = isset($request->request->all()['delete']) ? $request->request->all()['delete'] : [];

            foreach ($user->getUserGroups() as $userGroup) {
                if (in_array($userGroup->getId(), $toDelete, false)) {
                    $user->removeUserGroup($userGroup);
                } elseif ($userGroup->getForumGroup()->getId() !== $defaultGroup) {
                    $userGroup->setDefaultGroup(false);
                    $this->em->persist($userGroup);
                } else {
                    $userGroup->setDefaultGroup(true);
                    $this->em->persist($userGroup);
                }
            }
            
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', "Les modifications ont bien été enregistrés.");

            return $this->redirectToRoute('admin_app_user_group', ['id' => $user->getId()]);
        }

        $groups = $forumGroupRepository->findAll();

        $filteredGroups = [];
        foreach ($groups as $group) {
            if (!in_array($group->getId(), $userGroups)) {
                $filteredGroups[] = $group;
            }
        }

        return $this->render('Admin/user/group.html.twig', [
            'user' => $user,
            'groups' => $filteredGroups,
        ]);
    }

    private function clearPrivateMessages(
        PrivateMessageSentRepository $sendRepository, 
        PrivateMessageReceivedRepository $receiveRepository,
        User $user
    ): void {
        $sends = $sendRepository->getReferenced($user);
        foreach ($sends as $pm) {
            if ($pm->getAddressee() === $user) {
                $pm->setAddressee(null);
            }

            if ($pm->getAuthor() === $user) {
                $pm->setAuthor(null);
            }

            $this->em->persist($pm);
        }
        
        $receiveds = $receiveRepository->getReferenced($user);
        foreach ($receiveds as $pm) {
            if ($pm->getAddressee() === $user) {
                $pm->setAddressee(null);
            }

            if ($pm->getAuthor() === $user) {
                $pm->setAuthor(null);
            }

            $this->em->persist($pm);
        }

        $this->em->flush();
    }

    private function canHandleFounderUser(User $user): void
    {
        $currentUser = $this->getUser();

        if (!in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return;
        }
        if ($currentUser instanceof User && !in_array('ROLE_SUPER_ADMIN', $currentUser->getRoles())) {
            throw new AccessDeniedException('You cannot edit or create a Founder Administrator.');
        }
    }
}