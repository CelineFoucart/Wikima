<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use DateTimeImmutable;
use App\Form\Admin\UserFormType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/admin/user')]
#[Security("is_granted('ROLE_ADMIN')")]
final class AdminUserController extends AbstractAdminController
{
    protected string $entityName = "user";

    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    #[Route('/', name: 'admin_app_user_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/user/list.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_user_create', methods:['GET', 'POST'])]
    public function createAction(Request $request): Response
    {
        $user = new User();
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
            
            $this->userRepository->add($user, true);
            $this->addFlash('success', "L'utilisateur " . $user . " a bien été modifié.");

            return $this->redirectTo($request, $user->getId());
        }

        return $this->render('Admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_user_delete', methods:['POST'])]
    public function deleteAction(Request $request, User $user): Response
    {
        if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            throw new AccessDeniedException('Les administrateurs et les fondateurs ne peuvent être supprimés.');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->userRepository->remove($user, true);
            $this->addFlash('success', "L'utilisateur a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_user_list', [], Response::HTTP_SEE_OTHER);
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