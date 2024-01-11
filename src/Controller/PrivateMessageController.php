<?php

namespace App\Controller;

use App\Entity\PrivateMessageReceived;
use App\Entity\PrivateMessageSent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/private-message')]
class PrivateMessageController extends AbstractController
{
    #[Route('', name: 'app_private_message_inbox', methods:['GET'])]
    public function inboxAction(): Response
    {
        return $this->render('private_message/inbox.html.twig');
    }

    #[Route('/create', name: 'app_private_message_create', requirements: ['page' => '\d+'], methods:['GET', 'POST'])]
    public function createAction(): Response
    {
        return $this->render('private_message/create.html.twig');
    }

    #[Route('/{id}', name: 'app_private_message_inbox_show', requirements: ['page' => '\d+'], methods:['GET'])]
    public function inboxShowAction(PrivateMessageReceived $privateMessage): Response
    {
        return $this->render('private_message/inbox_show.html.twig', ['privateMessage' => $privateMessage]);
    }

    #[Route('/{id}/delete', name: 'app_private_message_inbox_delete', requirements: ['page' => '\d+'], methods:['POST'])]
    public function inboxDeleteAction(PrivateMessageReceived $privateMessage): Response
    {
        return $this->render('private_message/inbox_show.html.twig');
    }

    #[Route('/sendbox', name: 'app_private_message_sendbox', methods:['GET'])]
    public function sendboxAction(): Response
    {
        return $this->render('private_message/sendbox.html.twig');
    }

    #[Route('/sendbox/{id}', name: 'app_private_message_sendbox', requirements: ['page' => '\d+'], methods:['GET'])]
    public function sendboxShowAction(PrivateMessageSent $privateMessage): Response
    {
        return $this->render('private_message/sendbox_show.html.twig', ['privateMessage' => $privateMessage]);
    }

    #[Route('/sendbox/{id}/delete', name: 'app_private_message_sendbox_delete', requirements: ['page' => '\d+'], methods:['POST'])]
    public function sendboxDeleteAction(PrivateMessageSent $privateMessage): Response
    {
        return $this->render('private_message/sendbox_show.html.twig');
    }
}
