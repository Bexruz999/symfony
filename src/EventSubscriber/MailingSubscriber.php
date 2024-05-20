<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\ContactRequestEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class MailingSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function onContactRequestEvent(ContactRequestEvent $event): void
    {
        $data = $event->data;

        $mail = (new TemplatedEmail())
            ->to($data->service)
            ->from($data->email)
            ->subject('New message from contact@example.com')
            ->htmlTemplate('mails/contact.html.twig')
            ->context(['data' => $data,]);
        $this->mailer->send($mail);
    }

    public function onInteractiveLoginEvent(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if (!$user instanceof User) {
            return;
        }

        $mail = (new Email())
            ->to($user->getEmail())
            ->from('support@example.com')
            ->subject('Contextion')
            ->text('vi voshli');
        $this->mailer->send($mail);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContactRequestEvent::class => 'onContactRequestEvent',
            InteractiveLoginEvent::class => 'onInteractiveLoginEvent',
        ];
    }
}
