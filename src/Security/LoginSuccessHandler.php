<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Twig\Environment;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private Environment $twig
    ) {}

    /**
     * Intercepts successful logins to trigger the Two-Factor Authentication (2FA) flow.
     * Generates a 6-digit OTP, sends it via email, and redirects to the verification page.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        /** @var User $user */
        $user = $token->getUser();

        // 1. Generate a secure random 6-digit OTP code
        $otpCode = (string) random_int(100000, 999999);
        $user->setOtpCode($otpCode);
        $user->setOtpExpiresAt(new \DateTime('+5 minutes')); // OTP is valid for 5 minutes

        // Persist the OTP to the database
        $this->entityManager->flush();

        // 2. Prepare and send the professional branded OTP email
        $email = (new Email())
            ->from('security@esprit-banking.tn')
            ->to($user->getEmail())
            ->subject('Your one-time password (OTP) - Esprit Banking')
            ->html($this->twig->render('emails/otp.html.twig', [
                'otpCode' => $otpCode,
                'userName' => $user->getName() ?: 'User',
            ]));

        $this->mailer->send($email);

        // Debug Bypass: Store code in session for dev environments where MAILER_DSN is null
        $request->getSession()->set('dev_otp_code', $otpCode);

        // 3. Mark the session as pending 2FA verification
        $request->getSession()->set('_2fa_pending', true);

        // Redirect the user to the OTP verification form
        return new RedirectResponse($this->urlGenerator->generate('app_verify_otp'));
    }
}
