<?php


use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSubscriber implements EventSubscriberInterface
{
    private EntityManager $em;
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            LoginSuccessEvent::class => [
                ['onLogin']
            ]
        ];
    }

    public function onLogin(LoginSuccessEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticatedToken()->getUser();
        $user->setLastConnectedAt(new \Datetime());
        $this->em->persist($user);
        $this->em->flush();
    }
}
