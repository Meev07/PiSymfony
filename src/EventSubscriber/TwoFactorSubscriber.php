<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TwoFactorSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private TokenStorageInterface $tokenStorage,
        private AuthorizationCheckerInterface $authorizationChecker
    ) {}

    /**
     * Intercepts every incoming request to enforce Two-Factor Authentication (2FA).
     * If a user is logged in but has a pending 2FA verification, they are forcibly
     * redirected to the OTP verification page unless they are accessing an exempt route.
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        // Only process main requests (skip sub-requests like fragments or toolbar)
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        // Define routes that are exempt from the 2FA enforcement check
        $exemptRoutes = [
            'app_verify_otp',      // The verification page itself
            'app_logout',          // Allow users to logout even if 2FA is pending
            'app_login',           // Login page
            'app_signup',          // Registration page
            'app_forgot_password', // Password recovery
            'app_reset_password'   // Password recovery reset
        ];

        // Skip if the current route is in the exempt list
        if (in_array($route, $exemptRoutes)) {
            return;
        }

        // Check if a user is currently authenticated
        $token = $this->tokenStorage->getToken();
        if (!$token || !$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return;
        }

        // Verify if the session indicates a 2FA verification is still required
        $session = $request->getSession();
        if ($session->get('_2fa_pending')) {
            // Force redirection to the OTP verification page
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_verify_otp')));
        }
    }

    /**
     * Register this subscriber to the Kernel Request event
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
