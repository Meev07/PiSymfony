<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private \Doctrine\ORM\EntityManagerInterface $entityManager,
        private \Symfony\Component\Mailer\MailerInterface $mailer
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username', '');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('_password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        /** @var \App\Entity\User $user */
        $user = $token->getUser();

        // 1. Check Face ID 2FA (Biometric Priority)
        if ($user->isFaceIdEnabled() && $user->getFaceIdDescriptor()) {
            $request->getSession()->set('_face_id_pending', true);
            return new RedirectResponse($this->urlGenerator->generate('app_face_id_verify_flow'));
        }
        
        // 2. Check Email 2FA
        if ($user->isIs2faEnabled()) {
            // Generate a 6-digit code
            $code = sprintf('%06d', mt_rand(0, 999999));
            $user->setLoginCode($code);
            $user->setLoginCodeExpiresAt(new \DateTimeImmutable('+10 minutes'));
            
            $this->entityManager->flush();

            // Send Email
            $email = (new \Symfony\Bridge\Twig\Mime\TemplatedEmail())
                ->from(new \Symfony\Component\Mime\Address('sahlimouhib118@gmail.com', 'Esprit Wallet Security'))
                ->to($user->getEmail())
                ->subject('Security Verification Code')
                ->htmlTemplate('emails/login_verification.html.twig')
                ->context(['code' => $code, 'user' => $user]);

            $this->mailer->send($email);

            // Mark as pending 2FA in session
            $request->getSession()->set('_2fa_pending', true);

            return new RedirectResponse($this->urlGenerator->generate('app_verify_login'));
        }

        return new RedirectResponse($this->urlGenerator->generate('user_dashboard'));
    }


    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
