<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VerifyRegistrationController extends AbstractController
{
    #[Route('/verify-registration', name: 'app_verify_registration')]
    public function verify(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $email = $request->query->get('email');
        if (!$email) {
            return $this->redirectToRoute('app_login');
        }

        $user = $userRepository->findOneBy(['email' => $email]);
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($user->getIsVerified()) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $code = $request->request->get('code');
            
            if ($user->getVerificationCode() === $code && $user->getVerificationCodeExpiresAt() > new \DateTimeImmutable()) {
                $user->setIsVerified(true);
                $user->setVerificationCode(null);
                $user->setVerificationCodeExpiresAt(null);
                $entityManager->flush();

                $this->addFlash('success', 'Your account has been verified! You can now log in.');
                return $this->redirectToRoute('app_login');
            }

            $this->addFlash('error', 'Invalid or expired verification code.');
        }

        return $this->render('registration/verify_email.html.twig', [
            'email' => $email,
        ]);
    }
}
