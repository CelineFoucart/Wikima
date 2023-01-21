<?php

namespace App\Service;

use App\Entity\Data\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class ContactService
{
    public function __construct(
        private string $contactMail,
        private MailerInterface $mailer
    ) {  
    }

    public function notify(Contact $contact): bool
    {
        try {
            $this->send($contact);
            return true;
        } catch (TransportExceptionInterface $th) {
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