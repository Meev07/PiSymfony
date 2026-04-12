<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Entity\Account;
use App\Entity\Cheque;
use App\Form\ChequeType;
use App\Repository\ChequeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cheque')]
final class ChequeController extends AbstractController
{
    #[Route('/scan', name: 'app_cheque_scan', methods: ['GET', 'POST'])]
    public function scan(Request $request, \App\Service\OcrService $ocrService, EntityManagerInterface $entityManager, AccountRepository $accountRepository): Response
    {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('cheque_image');
            
            if ($file) {
                // Perform OCR Analysis
                $extraction = $ocrService->extractChequeInfo($file->getPathname());
                
                if ($extraction['success']) {
                    return $this->json($extraction);
                }
                
                return $this->json($extraction, Response::HTTP_BAD_REQUEST);
            }

            // If it's a final confirmation request (sent as JSON or standard POST)
            $chequeNumber = $request->request->get('chequeNumber');
            $secureToken = $request->request->get('secureToken');

            if ($chequeNumber && $secureToken) {
                return $this->processRedemption($request, $chequeNumber, $secureToken, $entityManager, $accountRepository);
            }
        }

        return $this->render('cheque/scan.html.twig');
    }

    private function processRedemption(Request $request, string $serial, string $token, EntityManagerInterface $entityManager, AccountRepository $accountRepository): Response
    {
        $cheque = $entityManager->getRepository(Cheque::class)->findOneBy([
            'chequeNumber' => $serial,
            'secureToken' => $token
        ]);

        if (!$cheque) {
            $this->addFlash('error', 'CRITICAL ERROR: Invalid cheque data detected by AI.');
            return $this->redirectToRoute('app_cheque_scan');
        }

        if ($cheque->getStatus() !== 'PENDING') {
            $this->addFlash('error', 'ILLEGAL ACTION: This cheque has already been redeemed or is expired.');
            return $this->redirectToRoute('app_cheque_scan');
        }

        if ($cheque->getSender() === $this->getUser()) {
            $this->addFlash('error', 'ILLEGAL ACTION: You cannot redeem a cheque that you issued yourself.');
            return $this->redirectToRoute('app_cheque_scan');
        }

        $receiver = $this->getUser();
        $accounts = $accountRepository->findBy(['user' => $receiver, 'status' => 'ACTIVE']);
        if (empty($accounts)) {
            $this->addFlash('error', 'CRITICAL ERROR: No active bank account found to receive funds. Please contact support if your account is blocked.');
            return $this->redirectToRoute('app_cheque_scan');
        }

        
        $targetAccount = $accounts[0];
        $targetAccount->setBalance((string)((float)$targetAccount->getBalance() + (float)$cheque->getAmount()));

        $cheque->setReceiver($receiver);
        $cheque->setStatus('REDEEMED');
        
        $entityManager->flush();

        if ($request->isXmlHttpRequest() || $request->headers->get('Accept') === 'application/json') {
             return $this->json(['success' => true, 'message' => 'Success! ' . $cheque->getAmount() . ' TND has been securely transferred.']);
        }

        $this->addFlash('success', 'SUCCESS: ' . $cheque->getAmount() . ' TND has been securely transferred to your wallet via AI Verification.');
        return $this->redirectToRoute('app_cheque_index');
    }

    #[Route('/redeem', name: 'app_cheque_redeem', methods: ['GET', 'POST'])]
    public function redeem(Request $request, EntityManagerInterface $entityManager, AccountRepository $accountRepository): Response
    {
        if ($request->isMethod('POST')) {
            $serial = $request->request->get('serial');
            $token = $request->request->get('token');

            $cheque = $entityManager->getRepository(Cheque::class)->findOneBy([
                'chequeNumber' => $serial,
                'secureToken' => $token
            ]);

            if (!$cheque) {
                $this->addFlash('error', 'CRITICAL ERROR: Invalid cheque serial or security token.');
                return $this->redirectToRoute('app_cheque_redeem');
            }

            if ($cheque->getStatus() !== 'PENDING') {
                $this->addFlash('error', 'ILLEGAL ACTION: This cheque has already been redeemed or is expired.');
                return $this->redirectToRoute('app_cheque_redeem');
            }

            if ($cheque->getSender() === $this->getUser()) {
                $this->addFlash('error', 'ILLEGAL ACTION: You cannot redeem a cheque that you issued yourself.');
                return $this->redirectToRoute('app_cheque_redeem');
            }

            // Sync: Locate receiver's account
            $receiver = $this->getUser();
            $accounts = $accountRepository->findBy(['user' => $receiver, 'status' => 'ACTIVE']);
            if (empty($accounts)) {
                $this->addFlash('error', 'CRITICAL ERROR: No active bank account found to receive funds. Please contact support if your account is blocked.');
                return $this->redirectToRoute('app_cheque_redeem');
            }

            $targetAccount = $accounts[0];
            $targetAccount->setBalance((string)((float)$targetAccount->getBalance() + (float)$cheque->getAmount()));

            // Logic to transfer funds (Simplification)
            $cheque->setReceiver($receiver);
            $cheque->setStatus('REDEEMED');
            
            $entityManager->flush();

            $this->addFlash('success', 'SUCCESS: ' . $cheque->getAmount() . ' TND has been securely transferred to your wallet.');
            return $this->redirectToRoute('app_cheque_index');
        }

        return $this->render('cheque/redeem.html.twig');
    }

    #[Route('/', name: 'app_cheque_index', methods: ['GET'])]
    public function index(ChequeRepository $chequeRepository): Response
    {
        $user = $this->getUser();
        return $this->render('cheque/index.html.twig', [
            'sent_cheques' => $chequeRepository->findBy(['sender' => $user], ['createdAt' => 'DESC']),
            'received_cheques' => $chequeRepository->findBy(['receiver' => $user], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_cheque_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cheque = new Cheque();
        $cheque->setSender($this->getUser());
        $cheque->setSecureToken(bin2hex(random_bytes(16))); // Automated token generation
        $cheque->setChequeNumber('EW-'.rand(100000, 999999)); // Automatic serial generation
        $cheque->setExpirationDate(new \DateTime('+7 days'));
        
        $form = $this->createForm(ChequeType::class, $cheque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Balance Sync Step: Find sender's account for deduction
            $accounts = $entityManager->getRepository(Account::class)->findBy(['user' => $this->getUser(), 'status' => 'ACTIVE']);
            if (empty($accounts)) {
                $this->addFlash('error', 'ILLEGAL ACTION: You must have an ACTIVE bank account to issue a digital cheque. Your current accounts may be blocked.');
                return $this->redirectToRoute('app_cheque_index');
            }

            
            $sourceAccount = $accounts[0];
            $chequeAmount = (float)$cheque->getAmount();
            $currentBalance = (float)$sourceAccount->getBalance();

            if ($currentBalance < $chequeAmount) {
                $this->addFlash('error', 'INSUFFICIENT FUNDS: Your current balance (' . number_format($currentBalance, 2) . ' TND) is lower than the cheque amount.');
                return $this->render('cheque/new.html.twig', [
                    'cheque' => $cheque,
                    'form' => $form,
                ]);
            }

            // Deduct from balance
            $sourceAccount->setBalance((string)($currentBalance - $chequeAmount));

            $entityManager->persist($cheque);
            $entityManager->flush();

            $this->addFlash('success', 'Digital cheque successfully generated. ' . number_format($chequeAmount, 2) . ' TND has been reserved from your account.');
            return $this->redirectToRoute('app_cheque_show', ['id' => $cheque->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cheque/new.html.twig', [
            'cheque' => $cheque,
            'form' => $form,
        ], new Response(null, $form->isSubmitted() ? Response::HTTP_UNPROCESSABLE_ENTITY : Response::HTTP_OK));
    }

    #[Route('/{id}/receipt', name: 'app_cheque_receipt', methods: ['GET'])]
    public function receipt(Cheque $cheque): Response
    {
        return $this->render('cheque/receipt.html.twig', [
            'cheque' => $cheque,
            'printedAt' => new \DateTime(),
        ]);
    }

    #[Route('/{id}', name: 'app_cheque_show', methods: ['GET'])]
    public function show(Cheque $cheque): Response
    {
        if ($cheque->getSender() !== $this->getUser() && $cheque->getReceiver() !== $this->getUser()) {
             throw $this->createAccessDeniedException('You do not have permission to view this cheque.');
        }

        return $this->render('cheque/show.html.twig', [
            'cheque' => $cheque,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cheque_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cheque $cheque, EntityManagerInterface $entityManager): Response
    {
        if ($cheque->getSender() !== $this->getUser()) {
             throw $this->createAccessDeniedException('You can only edit cheques you have issued.');
        }
        $form = $this->createForm(ChequeType::class, $cheque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cheque_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cheque/edit.html.twig', [
            'cheque' => $cheque,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cheque_delete', methods: ['POST'])]
    public function delete(Request $request, Cheque $cheque, EntityManagerInterface $entityManager): Response
    {
        if ($cheque->getSender() !== $this->getUser()) {
             throw $this->createAccessDeniedException('You can only delete cheques you have issued.');
        }
        if ($this->isCsrfTokenValid('delete'.$cheque->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cheque);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cheque_index', [], Response::HTTP_SEE_OTHER);
    }
}
