<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Generate Verification Code
            $code = sprintf('%06d', mt_rand(0, 999999));
            $user->setVerificationCode($code);
            $user->setVerificationCodeExpiresAt(new \DateTimeImmutable('+24 hours'));
            $user->setIsVerified(false);

            $entityManager->persist($user);
            $entityManager->flush();

            // Send Verification Email
            $email = (new \Symfony\Bridge\Twig\Mime\TemplatedEmail())
                ->from(new \Symfony\Component\Mime\Address('sahlimouhib118@gmail.com', 'Esprit Wallet Security'))
                ->to($user->getEmail())
                ->subject('Verify Your ESPRIT Wallet Account')
                ->htmlTemplate('emails/registration_verification.html.twig')
                ->context(['code' => $code, 'user' => $user]);

            $mailer->send($email);

            return $this->redirectToRoute('app_verify_registration', ['email' => $user->getEmail()]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
