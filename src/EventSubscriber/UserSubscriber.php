<?php

namespace App\EventSubscriber;

use App\Entity\User;
use ApiPlatform\Symfony\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setPassword', EventPriorities::PRE_WRITE],
        ];
    }

    public function setPassword(ViewEvent $event)
    {
        $user = $event->getControllerResult();
        if ($user instanceof User && $user->getPassword() && $this->userPasswordHasher->needsRehash($user)) {
            $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPassword()));
        }
    }

}