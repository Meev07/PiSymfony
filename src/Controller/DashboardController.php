<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\UserSettingsType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractController
{
    /**
     * Display the main user dashboard (Profile View)
     * Handles profile image uploads via POST
     */
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(Request $request, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Check if the request is a POST for profile image upload
        if ($request->isMethod('POST')) {
            $profileFile = $request->files->get('profile_image');

            if ($profileFile) {
                // Generate a unique filename for the uploaded image
                $originalFilename = pathinfo($profileFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profileFile->guessExtension();

                try {
                    // Move the file to the profile pictures directory
                    $profileFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/profiles',
                        $newFilename
                    );
                    
                    // Update user's profile image in the database
                    $user->setProfileImage($newFilename);
                    $entityManager->flush();

                    $this->addFlash('success', 'Profile image updated successfully.');
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Profile image upload failed.');
                }
            }
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * User Settings Page
     * Allows updating Name, Email, and Password
     */
    #[Route('/settings', name: 'app_settings', methods: ['GET', 'POST'])]
    public function settings(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        // Initialize the Settings Form mapped to the User entity
        $form = $this->createForm(UserSettingsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If a new password is provided, hash it before saving
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            // Save changes to database
            $entityManager->flush();

            $this->addFlash('success', 'Your settings have been updated successfully.');
            return $this->redirectToRoute('app_settings');
        }

        return $this->render('dashboard/settings.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
