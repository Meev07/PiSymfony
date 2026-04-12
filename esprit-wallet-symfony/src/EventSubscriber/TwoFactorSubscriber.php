<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TwoFactorSubscriber implements EventSubscriberInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();

        if ($session) {
            $route = $request->attributes->get('_route');
            $allowedRoutes = ['app_verify_login', 'app_face_id_verify_flow', 'app_face_id_verify_check', 'app_logout', 'app_login'];

            if ($session->get('_face_id_pending')) {
                if (!in_array($route, $allowedRoutes)) {
                    $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_face_id_verify_flow')));
                }
            } elseif ($session->get('_2fa_pending')) {
                if (!in_array($route, $allowedRoutes)) {
                    $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_verify_login')));
                }
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
