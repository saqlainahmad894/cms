<?php

namespace App\EventSubscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTCreatedSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            JWTCreatedEvent::class => 'onJWTCreated',
        ];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        // Make sure it's your User class
        if (method_exists($user, 'getRoles')) {
            $payload = $event->getData();
            $payload['roles'] = $user->getRoles(); // from User::getRoles()
            $event->setData($payload);
        }
    }
}
