<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Repository\ChequeRepository;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'user_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function dashboard(AccountRepository $accountRepo, TransactionRepository $transactionRepo, ChequeRepository $chequeRepo): Response
    {
        $user = $this->getUser();
        $accounts = $accountRepo->findBy(['user' => $user]);
        
        $totalBalance = 0;
        foreach ($accounts as $account) {
            $totalBalance += (float)$account->getBalance();
        }

        $recentTransactions = $transactionRepo->findBy(['sender' => $user], ['createdAt' => 'DESC'], 5);
        $recentCheques = $chequeRepo->findBy(['sender' => $user], ['createdAt' => 'DESC'], 5);

        return $this->render('dashboard/user.html.twig', [
            'totalBalance' => number_format($totalBalance, 2),
            'accounts' => $accounts,
            'recentTransactions' => $recentTransactions,
            'recentCheques' => $recentCheques,
        ]);
    }



    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('user_dashboard');
        }
        return $this->redirectToRoute('app_login');
    }
}
