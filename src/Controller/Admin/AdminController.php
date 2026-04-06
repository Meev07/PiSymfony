<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard')]
    public function index(UserRepository $userRepository): Response
    {
        // Calculate real total balance from all users
        $users = $userRepository->findAll();
        $totalBalance = array_reduce($users, function($carry, $user) {
            return $carry + $user->getBalance();
        }, 0);

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => count($users),
            'totalBalance' => $totalBalance,
            'latestUsers' => $userRepository->findBy([], ['id' => 'DESC']),
        ]);
    }
}
