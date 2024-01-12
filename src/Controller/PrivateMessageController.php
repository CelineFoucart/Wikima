<?php

namespace App\Controller;

use App\Entity\PrivateMessageReceived;
use App\Entity\PrivateMessageSent;
use App\Form\PrivateMessageType;
use App\Repository\PrivateMessageReceivedRepository;
use App\Repository\PrivateMessageSentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/private-message')]
class PrivateMessageController extends AbstractController
{
    public function __construct(bool $enablePrivateMessage)
    {
        if (false === $enablePrivateMessage) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('', name: 'app_private_message_inbox', methods:['GET'])]
    public function inboxAction(PrivateMessageReceivedRepository $repository): Response
    {
        return $this->render('private_message/inbox.html.twig', [
            'privateMessages' => $repository->findByAddressee($this->getUser()),
        ]);
    }

    #[Route('/create', name: 'app_private_message_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $privateMessage = (new PrivateMessageSent())->setAuthor($this->getUser());
        $form = $this->createForm(PrivateMessageType::class, $privateMessage);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $privateMessage->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($privateMessage);

            $privateMessageReceived = (new PrivateMessageReceived())
                ->setReadStatus(false)
                ->setAuthor($this->getUser())
                ->setAddressee($privateMessage->getAddressee())
                ->setTitle($privateMessage->getTitle())
                ->setContent($privateMessage->getContent())
                ->setCreatedAt($privateMessage->getCreatedAt())
                ->setPrivateMessageSent($privateMessage)
            ;

            $entityManager->persist($privateMessageReceived);
            $entityManager->flush();
            $this->addFlash('success','Le message a été envoyé.');

            return $this->redirectToRoute('app_private_message_inbox');
        }

        return $this->render('private_message/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_private_message_inbox_show', methods:['GET'])]
    public function inboxShowAction(PrivateMessageReceived $privateMessage): Response
    {
        return $this->render('private_message/inbox_show.html.twig', ['privateMessage' => $privateMessage]);
    }

    #[Route('/{id}/delete', name: 'app_private_message_inbox_delete', methods:['POST'])]
    public function inboxDeleteAction(PrivateMessageReceived $privateMessage): Response
    {
        return $this->render('private_message/inbox_show.html.twig');
    }

    #[Route('/sendbox', name: 'app_private_message_sendbox', methods:['GET'])]
    public function sendboxAction(PrivateMessageSentRepository $repository): Response
    {
        return $this->render('private_message/sendbox.html.twig', [
            'privateMessages' => $repository->findByAuthor($this->getUser()),
        ]);
    }

    #[Route('/sendbox/{id}/show', name: 'app_private_message_sendbox_show', methods:['GET'])]
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
