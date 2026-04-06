<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Form\RegistrationType;

class SecurityController extends AbstractController
{
    /**
     * Standard Login Route
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Helper route to redirect users based on their role after successful authentication
     */
    #[Route(path: '/redirect', name: 'app_redirect')]
    public function redirectAfterLogin(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->redirectToRoute('app_dashboard');
    }

    /**
     * Registration / Sign Up Route
     * Uses a Symfony Form for validation (character-only names and secure passwords)
     */
    #[Route(path: '/signup', name: 'app_signup')]
    public function signup(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password from the form
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setBalance(0); // Initialize new member with zero assets
            $user->setRoles(['ROLE_USER']); // Set default security role

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Account created successfully! You can now log in.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/signup.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Initiates the Password Reset process
     * Generates a token and sends a recovery email
     */
    #[Route(path: '/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user) {
                // Generate a one-time secure recovery token
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setResetTokenExpiresAt(new \DateTime('+1 hour')); // Token valid for 1 hour
                $entityManager->flush();

                // Build and send the recovery email
                $resetUrl = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                $emailMessage = (new Email())
                    ->from('no-reply@esprit-banking.tn')
                    ->to($user->getEmail())
                    ->subject('Password Reset - Esprit Banking')
                    ->html($this->renderView('emails/reset_password.html.twig', [
                        'resetUrl' => $resetUrl,
                        'userName' => $user->getName() ?: 'User',
                    ]));

                $mailer->send($emailMessage);
            }

            $this->addFlash('success', 'If an account exists with this email, a reset link has been sent.');
            return $this->redirectToRoute('app_forgot_password');
        }

        return $this->render('security/forgot_password.html.twig');
    }

    /**
     * Finalizes the Password Reset process using the provided token
     */
    #[Route(path: '/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(
        string $token, 
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        // Validate the recovery token
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetToken' => $token]);

        if (!$user || ($user->getResetTokenExpiresAt() && $user->getResetTokenExpiresAt() < new \DateTime())) {
            $this->addFlash('danger', 'The reset link is invalid or has expired.');
            return $this->redirectToRoute('app_forgot_password');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            // Ensure password matching and complexity (enforced by form if one was used, or manual check)
            if ($password !== $confirmPassword) {
                $this->addFlash('danger', 'Passwords do not match.');
            } else {
                // Update and encrypt new password, clear the token
                $user->setPassword($userPasswordHasher->hashPassword($user, $password));
                $user->setResetToken(null);
                $user->setResetTokenExpiresAt(null);
                $entityManager->flush();

                $this->addFlash('success', 'Your password has been reset successfully. You can now log in.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/reset_password.html.twig', ['token' => $token]);
    }

    /**
     * Logout Route (Handled by Symfony Security)
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
