<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\AccountType;
use App\Form\User\EditPasswordType;
use App\Repository\CommentRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        $user = $this->getUser();
        assert($user instanceof User);

        $accountForm = $this->createForm(AccountType::class, $user);
        $accountForm->handleRequest($request);

        if ($accountForm->isSubmitted() && $accountForm->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Vos informations ont été mises à jour.');

            return $this->redirectToRoute('app_profile');
        }

        $passwordForm = $this->createForm(EditPasswordType::class, $user);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $passwordForm->get('plainPassword')->getData()
                )
            );
            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a été mis à jour.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('user/profile.html.twig', [
            'accountForm' => $accountForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/profile/comments', name: 'app_profile_comments')]
    public function userComments(CommentRepository $commentRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('user/user_comments.html.twig', [
            'comments' => $commentRepository->findByAuthor($this->getUser(), $page),
        ]);
    }

    #[Route('/profile/confirmation', name: 'app_profile_confirmation')]
    public function confirmation(EmailVerifier $emailVerifier, string $contactMail, string $contactName, Request $request): Response
    {
        if ($request->isMethod('POST')) {
            /** @var User */
            $user = $this->getUser();

            $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address($contactMail, $contactName))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre email')
                    ->htmlTemplate('user/confirmation_email.html.twig')
            );

            $this->addFlash('success', 'Un nouvel email de confirmation a été envoyé.');

            return $this->redirectToRoute('app_profile_confirmation');
        }

        return $this->render('user/confirmation.html.twig', []);
    }
}
