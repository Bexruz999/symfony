<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PostSubmitEvent;

class TicketSubscriber implements EventSubscriberInterface
{
    public function onFormPostSubmit(PostSubmitEvent $event): void
    {
        // ...
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'form.post_submit' => 'onFormPostSubmit',
        ];
    }
}
