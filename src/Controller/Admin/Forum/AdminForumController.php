<?php

namespace App\Controller\Admin\Forum;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/forum')]
#[Security("is_granted('ROLE_ADMIN')")]
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