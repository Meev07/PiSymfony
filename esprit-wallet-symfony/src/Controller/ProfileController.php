<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profile/settings', name: 'app_profile_settings')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger, \Symfony\Component\Mailer\MailerInterface $mailer, \Symfony\Bundle\SecurityBundle\Security $security): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $infoForm = $this->createForm(ProfileType::class, $user);
        $passwordForm = $this->createForm(ChangePasswordType::class);

        $infoForm->handleRequest($request);
        if ($infoForm->isSubmitted() && $infoForm->isValid()) {
            $entityManager->flush();
            $security->login($user); // Re-authenticate to prevent logout
            $this->addFlash('success', 'Profile information updated successfully!');
            return $this->redirectToRoute('app_profile_settings');
        }

        $passwordForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $newPassword = $passwordForm->get('newPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $entityManager->flush();
            $this->addFlash('success', 'Password changed successfully!');
            return $this->redirectToRoute('app_profile_settings');
        }

        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');

            if ($action === 'upload_photo') {
                $photoFile = $request->files->get('profile_photo');
                if ($photoFile) {
                    $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                    try {
                        $photoFile->move(
                            $this->getParameter('kernel.project_dir').'/public/uploads/profiles',
                            $newFilename
                        );
                        $user->setProfileImage($newFilename);
                        $entityManager->flush();
                        $this->addFlash('success', 'Profile photo uploaded successfully!');
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Failed to upload photo.');
                    }
                }
                return $this->redirectToRoute('app_profile_settings');
            } elseif ($action === 'toggle_2fa') {
                $user->setIs2faEnabled(!$user->isIs2faEnabled());
                
                // If 2FA is enabled, disable Face ID
                if ($user->isIs2faEnabled()) {
                    $user->setIsFaceIdEnabled(false);
                }
                
                $entityManager->flush();
                
                $status = $user->isIs2faEnabled() ? 'enabled' : 'disabled';
                $this->addFlash('success', "2FA has been $status successfully!");

                if ($user->isIs2faEnabled()) {
                    $this->send2faCode($user, $entityManager, $mailer);
                    $request->getSession()->set('_2fa_pending', true);
                    return $this->redirectToRoute('app_verify_login');
                }
                return $this->redirectToRoute('app_profile_settings');
            }
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'infoForm' => $infoForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ]);
    }

    private function send2faCode(User $user, EntityManagerInterface $entityManager, \Symfony\Component\Mailer\MailerInterface $mailer): void
    {
        $code = sprintf('%06d', mt_rand(0, 999999));
        $user->setLoginCode($code);
        $user->setLoginCodeExpiresAt(new \DateTimeImmutable('+10 minutes'));
        $entityManager->flush();

        $email = (new \Symfony\Bridge\Twig\Mime\TemplatedEmail())
            ->from(new \Symfony\Component\Mime\Address('sahlimouhib118@gmail.com', 'Esprit Wallet Security'))
            ->to($user->getEmail())
            ->subject('2FA Activation Code')
            ->htmlTemplate('emails/login_verification.html.twig')
            ->context(['code' => $code, 'user' => $user]);

        $mailer->send($email);
    }
}
