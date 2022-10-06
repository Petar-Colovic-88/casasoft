<?php

namespace App\EventSubscriber;

use App\Entity\Property;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PropertySubscriber implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setUser', EventPriorities::PRE_WRITE],
        ];
    }

    public function setUser(ViewEvent $event)
    {
        $property = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ($property instanceof Property && $method === 'POST') {
            $user = $this->tokenStorage->getToken()?->getUser();
            if ($user instanceof User) {
                $property->setUser($user);
            }
        }
    }

}