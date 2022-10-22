<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationSuccessListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }
        if ($user instanceof User) {

            $data['data'] = array(
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            );
            $event->setData($data);
            $response = $event->getResponse();
            $response->headers->setCookie(
                new Cookie(
                    "USERID",
                    $user->getId(),
                    new \DateTime("+1 day"),
                    "/",
                    null,
                    true,
                    true,
                    false,
                    'strict'
                )
            );
            $response->headers->setCookie(
                new Cookie(
                    "BEARER",
                    $data['token'],
                    new \DateTime("+1 day"),
                    "/",
                    null,
                    true,
                    true,
                    false,
                    'strict'
                )
            );
        }
    }
}
