<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\AccountRepository;
use App\Repository\ChequeRepository;
use App\Repository\ComplaintRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Complaint;
use App\Entity\User;
use App\Entity\Cheque;
use App\Form\UserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
    public function index(
        UserRepository $userRepo,
        AccountRepository $accountRepo,
        ChequeRepository $chequeRepo,
        ComplaintRepository $complaintRepo
    ): Response {
        $stats = [
            'users' => $userRepo->count([]),
            'accounts' => $accountRepo->count([]),
            'cheques' => $chequeRepo->count([]),
            'complaints' => $complaintRepo->count([])
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats
        ]);
    }

    #[Route('/users', name: 'users')]
    public function users(UserRepository $userRepo): Response
    {
        return $this->render('admin/users.html.twig', [
            'users' => $userRepo->findAll()
        ]);
    }

    #[Route('/user/new', name: 'user_new')]
    #[Route('/user/edit/{id}', name: 'user_edit')]
    public function userForm(?User $user, \Symfony\Component\HttpFoundation\Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $isEdit = true;
        if (!$user) {
            $user = new User();
            $isEdit = false;
        }

        $form = $this->createForm(UserType::class, $user, [
            'is_new' => !$isEdit
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$isEdit) {
                /** @var string $plainPassword */
                $plainPassword = $form->get('plainPassword')->getData();
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
                $em->persist($user);
            }

            $em->flush();

            $this->addFlash('success', 'User ' . ($isEdit ? 'updated' : 'created') . ' successfully.');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/user_form.html.twig', [
            'user' => $user,
            'userForm' => $form->createView(),
            'isEdit' => $isEdit
        ], new Response(null, $form->isSubmitted() && !$form->isValid() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK));
    }


    
    #[Route('/user/delete/{id}', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'User deleted successfully.');
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/user/toggle-status/{id}', name: 'user_toggle_status', methods: ['POST'])]
    public function toggleUserStatus(User $user, EntityManagerInterface $em): Response
    {
        $user->setIsActive(!$user->getIsActive());
        $em->flush();
        
        $status = $user->getIsActive() ? 'unblocked' : 'blocked';
        $this->addFlash('success', sprintf('User %s successfully.', $status));
        
        return $this->redirectToRoute('admin_users');
    }


    #[Route('/accounts', name: 'accounts')]
    public function accounts(AccountRepository $accountRepo): Response
    {
        return $this->render('admin/accounts.html.twig', [
            'accounts' => $accountRepo->findAll()
        ]);
    }

    #[Route('/cheques', name: 'cheques')]
    public function cheques(ChequeRepository $chequeRepo): Response
    {
        return $this->render('admin/cheques.html.twig', [
            'cheques' => $chequeRepo->findBy([], ['createdAt' => 'DESC'])
        ]);
    }
    
    #[Route('/cheque/status/{id}/{status}', name: 'cheque_status', methods: ['POST'])]
    public function updateChequeStatus(Cheque $cheque, string $status, EntityManagerInterface $em): Response
    {
        $cheque->setStatus($status);
        $em->flush();
        $this->addFlash('success', 'Cheque status updated to ' . $status);
        return $this->redirectToRoute('admin_cheques');
    }

    #[Route('/complaints', name: 'complaints')]
    public function complaints(ComplaintRepository $complaintRepo): Response
    {
        return $this->render('admin/complaints.html.twig', [
            'complaints' => $complaintRepo->findBy([], ['createdAt' => 'DESC'])
        ]);
    }

    #[Route('/complaint/resolve/{id}', name: 'complaint_resolve', methods: ['POST'])]
    public function resolveComplaint(Complaint $complaint, EntityManagerInterface $em): Response
    {
        $complaint->setStatus('RESOLVED');
        $em->flush();
        $this->addFlash('success', 'Complaint marked as resolved.');
        return $this->redirectToRoute('admin_complaints');
    }

    #[Route('/complaint/{id}/chat', name: 'complaint_chat', methods: ['GET', 'POST'])]
    public function complaintChat(Complaint $complaint, \Symfony\Component\HttpFoundation\Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $reply = $request->request->get('replyMessage');
            if ($reply) {
                // If it already has a reply, we can append to it
                $existingReply = $complaint->getReplyMessage();
                if ($existingReply) {
                    $complaint->setReplyMessage($existingReply . "\n\nAGENT::" . $reply);
                } else {
                    $complaint->setReplyMessage("AGENT::" . $reply);
                }
                
                $em->flush();
                $this->addFlash('success', 'Reply sent successfully. Ticket remains pending until manually resolved.');
            }
            return $this->redirectToRoute('admin_complaint_chat', ['id' => $complaint->getId()]);
        }

        return $this->render('admin/complaint_chat.html.twig', [
            'complaint' => $complaint
        ]);
    }

    #[Route('/account/delete/{id}', name: 'account_delete', methods: ['POST'])]
    public function deleteAccount(App\Entity\Account $account, EntityManagerInterface $em): Response
    {
        $em->remove($account);
        $em->flush();
        $this->addFlash('success', 'Account deleted successfully.');
        return $this->redirectToRoute('admin_accounts');
    }

    #[Route('/account/toggle-status/{id}', name: 'account_toggle_status', methods: ['POST'])]
    public function toggleAccountStatus(App\Entity\Account $account, EntityManagerInterface $em): Response
    {
        $newStatus = ($account->getStatus() === 'ACTIVE') ? 'BLOCKED' : 'ACTIVE';
        $account->setStatus($newStatus);
        $em->flush();
        
        $this->addFlash('success', sprintf('Account %s successfully.', strtolower($newStatus)));
        
        return $this->redirectToRoute('admin_accounts');
    }


    #[Route('/cheque/delete/{id}', name: 'cheque_delete', methods: ['POST'])]
    public function deleteCheque(Cheque $cheque, EntityManagerInterface $em): Response
    {
        $em->remove($cheque);
        $em->flush();
        $this->addFlash('success', 'Cheque deleted successfully.');
        return $this->redirectToRoute('admin_cheques');
    }

    #[Route('/complaint/delete/{id}', name: 'complaint_delete', methods: ['POST'])]
    public function deleteComplaint(Complaint $complaint, EntityManagerInterface $em): Response
    {
        $em->remove($complaint);
        $em->flush();
        $this->addFlash('success', 'Complaint deleted successfully.');
        return $this->redirectToRoute('admin_complaints');
    }
}
