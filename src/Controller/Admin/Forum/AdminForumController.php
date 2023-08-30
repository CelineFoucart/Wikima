<?php

namespace App\Controller\Admin\Forum;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/admin/forum')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
class AdminForumController extends AbstractController
{
    public function __construct(
        bool $enableForum
    ) {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'admin_app_idiom_forum_list')]
    public function action(): Response
    {
        return $this->render('template.html.twig');
    }
}