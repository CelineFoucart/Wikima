<?php

namespace App\Service;

use App\Service\LogService;
use App\Entity\Data\Contact;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class ContactService
{
    public function __construct(
        private string $contactMail,
        private MailerInterface $mailer,
        private LogService $logService
    ) {  
    }

    public function notify(Contact $contact): bool
    {
        try {
            $this->send($contact);
            return true;
        } catch (TransportExceptionInterface $th) {
            $this->logService->error("Contact", $th->getMessage(), "TransportExceptionInterface");
            return false;
        }
    }

    private function send(Contact $contact): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($contact->getEmail(), $contact->getUsername()))
            ->to(new Address($this->contactMail))
            ->subject($contact->getSubject())
            ->htmlTemplate('home/contact_email.html.twig')
            ->context(['contact' => $contact]);
        $this->mailer->send($email);
    }
}