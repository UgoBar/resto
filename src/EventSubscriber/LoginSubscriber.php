<?php

namespace App\EventSubscriber;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em)
    {

    }
    public static function getSubscribedEvents(){
        return
            [
                LoginSuccessEvent::class=> [ ['onLogin']]
            ];
    }

    public function onLogin(LoginSuccessEvent $event){
        /**@var User $user */
        $user = $event->getAuthenticatedToken()->getUser();
        $user->setLastConnectedAt(new DateTime());
        $this->em->persist($user);
        $this->em->flush();
    }
}
