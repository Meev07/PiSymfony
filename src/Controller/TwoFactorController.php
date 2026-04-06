<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TwoFactorController extends AbstractController
{
    #[Route('/verify-otp', name: 'app_verify_otp', methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function verify(Request $request, EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        if (!$session->get('_2fa_pending')) {
            return $this->redirectToRoute('app_redirect');
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            $otp = $request->request->get('otp');

            if ($user->getOtpCode() === $otp && $user->getOtpExpiresAt() > new \DateTime()) {
                // Clear OTP and set 2FA as complete
                $user->setOtpCode(null);
                $user->setOtpExpiresAt(null);
                $entityManager->flush();

                $session->remove('_2fa_pending');
                $session->set('_2fa_complete', true);

                $this->addFlash('success', 'Two-factor authentication successful.');
                return $this->redirectToRoute('app_redirect');
            }

            $this->addFlash('danger', 'Invalid or expired OTP code.');
        }

        return $this->render('security/verify_otp.html.twig');
    }
}
