<?php

namespace App\Controller;

use App\Entity\PrivateMessageReceived;
use App\Entity\PrivateMessageSent;
use App\Entity\User;
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
            $privateMessageReceived = $this->setPrivateMessageReceived($privateMessage);
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
    public function inboxShowAction(PrivateMessageReceived $privateMessage, EntityManagerInterface $entityManager): Response
    {
        if ($privateMessage->getAddressee() === null) {
            throw $this->createAccessDeniedException();
        } else if ($privateMessage->getAddressee() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if (!$privateMessage->isReadStatus()) {
            $privateMessage->setReadStatus(true);
            $entityManager->persist($privateMessage);
            $entityManager->flush();
        }

        return $this->render('private_message/inbox_show.html.twig', ['privateMessage' => $privateMessage]);
    }

    #[Route('/{id}/delete', name: 'app_private_message_inbox_delete', methods:['POST'])]
    public function inboxDeleteAction(PrivateMessageReceived $privateMessage, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$privateMessage->getId(), $request->request->get('_token'))) {
            $privateMessage->setPrivateMessageSent(null);
            $entityManager->persist($privateMessage);

            $entityManager->remove($privateMessage);
            $entityManager->flush();
            $this->addFlash('success', "Le message a été supprimée avec succès.");
        }

        return $this->redirectToRoute('app_private_message_inbox', [], Response::HTTP_SEE_OTHER);
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
        if ($privateMessage->getAuthor() === null) {
            throw $this->createAccessDeniedException();
        } else if ($privateMessage->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('private_message/sendbox_show.html.twig', ['privateMessage' => $privateMessage]);
    }

    #[Route('/sendbox/{id}/delete', name: 'app_private_message_sendbox_delete', methods:['POST'])]
    public function sendboxDeleteAction(PrivateMessageSent $privateMessage, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$privateMessage->getId(), $request->request->get('_token'))) {
            $privateMessage->setPrivateMessageReceived(null);
            $entityManager->persist($privateMessage);
            $entityManager->flush();

            $entityManager->remove($privateMessage);
            $entityManager->flush();
            $this->addFlash('success', "Le message a été supprimée avec succès.");
        }

        return $this->redirectToRoute('app_private_message_sendbox', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/conversation/{id}', name: 'app_private_message_conversation', methods:['GET', 'POST'])]
    public function conversationAction(
        User $addressee, 
        PrivateMessageReceivedRepository $receivedRepository, 
        PrivateMessageSentRepository $sendRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $privateMessage = (new PrivateMessageSent())->setAuthor($this->getUser())->setAddressee($addressee);
        $form = $this->createForm(PrivateMessageType::class, $privateMessage);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $privateMessage->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($privateMessage);
            $privateMessageReceived = $this->setPrivateMessageReceived($privateMessage);
            $entityManager->persist($privateMessageReceived);
            $entityManager->flush();
            $this->addFlash('success','Le message a été envoyé.');

            return $this->redirectToRoute('app_private_message_conversation', ['id' => $addressee->getId()]);
        }

        $received = $receivedRepository->findForConversation($this->getUser(), $addressee);
        
        foreach ($received as $privateMessage) {
            if (!$privateMessage->isReadStatus()) {
                $privateMessage->setReadStatus(true);
                $entityManager->persist($privateMessage);
            }
        }
        $entityManager->flush();

        $sent = $sendRepository->findForConversation($addressee, $this->getUser());
        $privateMessages = [...$received, ...$sent];

        usort($privateMessages, function($a, $b) {
            if ($a->getCreatedAt() == $b->getCreatedAt()) {
                return 0;
            }
            return ($a->getCreatedAt() > $b->getCreatedAt()) ? -1 : 1;
        });

        return $this->render('private_message/conversation.html.twig', [
            'addressee' => $addressee,
            'privateMessages' => $privateMessages,
            'form' => $form,
        ]);
    }

    private function setPrivateMessageReceived(PrivateMessageSent $privateMessage): PrivateMessageReceived
    {
        return (new PrivateMessageReceived())
                ->setReadStatus(false)
                ->setAuthor($this->getUser())
                ->setAddressee($privateMessage->getAddressee())
                ->setTitle($privateMessage->getTitle())
                ->setContent($privateMessage->getContent())
                ->setCreatedAt($privateMessage->getCreatedAt())
                ->setPrivateMessageSent($privateMessage)
            ;
    }
}
