<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/verify-login', name: 'app_verify_login')]
    public function verifyLogin(\Symfony\Component\HttpFoundation\Request $request, \Doctrine\ORM\EntityManagerInterface $em): Response
    {
        if (!$request->getSession()->get('_2fa_pending')) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $code = $request->request->get('verification_code');
            /** @var \App\Entity\User $user */
            $user = $this->getUser();

            if ($user->getLoginCode() === $code && $user->getLoginCodeExpiresAt() > new \DateTimeImmutable()) {
                $request->getSession()->remove('_2fa_pending');
                $user->setLoginCode(null);
                $user->setLoginCodeExpiresAt(null);
                $em->flush();

                return $this->redirectToRoute('user_dashboard');
            }

            $this->addFlash('error', 'Invalid or expired verification code.');
        }

        return $this->render('security/verify_login.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]

    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
