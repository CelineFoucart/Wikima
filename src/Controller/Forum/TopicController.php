<?php

namespace App\Controller\Forum;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum')]
class TopicController extends AbstractController
{
    public function __construct(bool $enableForum)
    {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/topic-{slug}', name: 'app_forum_topic_show')]
    public function topic(string $slug): Response
    {
        return $this->render('forum/topic.html.twig', [
        ]);
    }

    // new

    // edit

    // delete post
}
