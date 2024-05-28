<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;

class FormListenerFactory
{
    public function __construct(private Security $security)
    {
        $this->security = $security;
    }

    public function autoSlug(string $field): callable
    {
        return function (PreSubmitEvent $event) use ($field) {
            $data = $event->getData();

            if (empty($data['slug'])) {
                $slugger = new AsciiSlugger();
                $data['slug'] = $slugger->slug($data[$field])->toString();
                $event->setData($data);
            }
        };
    }

    public function timestamps(): callable
    {
        return function (PostSubmitEvent $event) {
            $data = $event->getData();
            $data->setUpdatedAt(new \DateTimeImmutable());
            if (!$data->getId()) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }
        };
    }

    public function setUser(): callable
    {
        return function (PostSubmitEvent $event) {

            $data = $event->getData();

            if(empty($data->getUser())) {
                $data->setUser($this->security->getUser());
            }
        };
    }
}