<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\User\AccountType;
use App\Form\User\EditPasswordType;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository
    ) {  
    }

    #[Route('/users', name: 'app_user_index')]
    public function list(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('user/list.html.twig', [
            'users' => $this->userRepository->findPaginated($page)
        ]);
    }

    #[Route('/users/{id}', name: 'app_user_show')]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
