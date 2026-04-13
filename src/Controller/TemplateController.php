<?php

namespace App\Controller;

use App\Entity\Credit;
use App\Entity\DossierClient;
use App\Entity\Echeance;
use App\Form\CreditSearchData;
use App\Form\CreditSearchType;
use App\Form\DecisionDemandeData;
use App\Form\DecisionDemandeType;
use App\Form\DeleteDemandeData;
use App\Form\DeleteDemandeType;
use App\Form\DossierCreditData;
use App\Form\RetardPredictionQueryData;
use App\Form\RetardPredictionQueryType;
use App\Form\DossierCreditType;
use App\Form\EditDossierCreditData;
use App\Form\EditDossierCreditType;
use App\Repository\CreditRepository;
use App\Repository\DossierClientRepository;
use App\Repository\UserRepository;
use App\Service\CreditNotificationService;
use App\Service\PaymentDelayChatbotService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TemplateController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(): RedirectResponse
    {
        return $this->redirectToRoute('app_signin');
    }

    #[Route('/template', name: 'app_template_root', methods: ['GET'])]
    public function templateRoot(): RedirectResponse
    {
        return $this->redirectToRoute('app_signin');
    }

    #[Route('/template/signin.html.twig', name: 'app_signin', methods: ['GET'])]
    #[Route('/template/signin.html', name: 'app_signin_legacy', methods: ['GET'])]
    public function signin(Request $request): Response
    {
        return $this->render('inapp/signin.html.twig', [
            'error' => $request->query->getBoolean('error'),
        ]);
    }

    #[Route('/template/signin.html.twig', name: 'app_signin_submit', methods: ['POST'])]
    #[Route('/template/signin.html', name: 'app_signin_submit_legacy', methods: ['POST'])]
    public function signinSubmit(Request $request, UserRepository $userRepository): RedirectResponse
    {
        $email    = trim((string) $request->request->get('email', ''));
        $password = (string) $request->request->get('password', '');

        if ('' === $email || '' === $password) {
            return $this->redirectToRoute('app_signin', ['error' => 1], 303);
        }

        $user = $userRepository->findOneByEmail($email);

        if (!$user) {
            return $this->redirectToRoute('app_signin', ['error' => 1], 303);
        }

        $storedPassword  = (string) $user->getPasswordHash();
        $isValidPassword = password_verify($password, $storedPassword)
            || hash_equals($storedPassword, $password);

        if (!$isValidPassword) {
            return $this->redirectToRoute('app_signin', ['error' => 1], 303);
        }

        $request->getSession()->set('auth_user_id', $user->getIdUser());

        return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
    }

    #[Route('/template/index.html.twig', name: 'app_template_index', methods: ['GET'])]
    #[Route('/template/index.html', name: 'app_template_index_legacy', methods: ['GET'])]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_template_page', ['page' => 'credit']);
    }

    #[Route('/template/credit-search.html.twig', name: 'app_credit_search_submit', methods: ['POST'])]
    #[Route('/template/credit-search.html', name: 'app_credit_search_submit_legacy', methods: ['POST'])]
    public function creditSearchSubmit(Request $request): RedirectResponse
    {
        $userId = (int) $request->getSession()->get('auth_user_id', 0);
        if (0 === $userId) {
            return $this->redirectToRoute('app_signin');
        }

        $data = new CreditSearchData();
        $form = $this->createForm(CreditSearchType::class, $data);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('errorMessage', 'Recherche invalide.');

            return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
        }

        if (null === $data->search_duree) {
            $request->getSession()->remove('credit_search_duree');
        } else {
            $request->getSession()->set('credit_search_duree', (int) $data->search_duree);
        }

        return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
    }

    #[Route('/template/credit-search-reset.html.twig', name: 'app_credit_search_reset', methods: ['GET'])]
    #[Route('/template/credit-search-reset.html', name: 'app_credit_search_reset_legacy', methods: ['GET'])]
    public function creditSearchReset(Request $request): RedirectResponse
    {
        $userId = (int) $request->getSession()->get('auth_user_id', 0);
        if (0 === $userId) {
            return $this->redirectToRoute('app_signin');
        }

        $request->getSession()->remove('credit_search_duree');

        return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
    }

    #[Route('/template/create-demande.html.twig', name: 'app_create_demande_submit', methods: ['POST'])]
    #[Route('/template/create-demande.html', name: 'app_create_demande_submit_legacy', methods: ['POST'])]
    public function createDemandeSubmit(
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $userId = $request->getSession()->get('auth_user_id');

        if (!$userId) {
            return $this->redirectToRoute('app_signin');
        }

        $data = new DossierCreditData();
        $form = $this->createForm(DossierCreditType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $montantDemande = (float) $data->montant_demande;
            $dureeMois      = (int)   $data->duree_mois;
            $tauxInteret    = (float) $data->taux_interet;
            $submissionFingerprint = hash('sha256', implode('|', [
                (int) $userId,
                number_format($montantDemande, 2, '.', ''),
                $dureeMois,
                number_format($tauxInteret, 2, '.', ''),
                (string) $data->objet_credit,
                trim((string) $data->description),
            ]));

            $lastCreateSubmission = $request->getSession()->get('create_demande_last_submission');
            if (is_array($lastCreateSubmission)
                && ($lastCreateSubmission['fingerprint'] ?? null) === $submissionFingerprint
                && isset($lastCreateSubmission['timestamp'])
                && (time() - (int) $lastCreateSubmission['timestamp']) < 30
            ) {
                $this->addFlash('successMessage', 'Votre demande de crédit a déjà été enregistrée.');

                return $this->redirectToRoute('app_template_page', [
                    'page' => 'create-demande',
                ], 303);
            }

            // Mensualité calculée via méthode domaine (Credit::calculerMensualiteStatic)
            $montantMensuel = Credit::calculerMensualiteStatic($montantDemande, $tauxInteret, $dureeMois);

            try {
                $now        = new \DateTime();
                $numDossier = 'DOS-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

                // Entité DossierClient
                $dossier = (new DossierClient())
                    ->setIdUser((int) $userId)
                    ->setNumDossier($numDossier)
                    ->setMontantDemande((string) $montantDemande)
                    ->setStatus(DossierClient::STATUS_ANALYSE)
                    ->setCreatedAt($now);

                $em->persist($dossier);

                // Entité Credit liée au dossier
                $credit = (new Credit())
                    ->setDossier($dossier)
                    ->setMontantCredit((string) $montantDemande)
                    ->setTauxInteret((string) $tauxInteret)
                    ->setDureeMois($dureeMois)
                    ->setMontantMensuel((string) round($montantMensuel, 2))
                    ->setCreatedAt($now);

                $em->persist($credit);

                // Une seule échéance initiale est créée à la soumission.
                $dateEcheance = new \DateTime('first day of next month');
                $echeance = (new Echeance())
                    ->setCredit($credit)
                    ->setNumEcheance(1)
                    ->setDateEcheance($dateEcheance)
                    ->setMontantEcheance((string) round($montantMensuel, 2))
                    ->setStatut('Analyse')
                    ->setCreatedAt($now);

                $em->persist($echeance);

                $em->flush();

                $request->getSession()->set('create_demande_last_submission', [
                    'fingerprint' => $submissionFingerprint,
                    'timestamp' => time(),
                ]);

                $this->addFlash('successMessage', 'Votre demande de crédit a été créée avec succès.');

                return $this->redirectToRoute('app_template_page', [
                    'page' => 'create-demande',
                ], 303);

            } catch (\Exception $e) {
                return $this->render('inapp/create-demande.html.twig', [
                    'form'    => $form,
                    'error'   => 'Erreur lors de la création de la demande: ' . $e->getMessage(),
                    'success' => false,
                ]);
            }
        }

        return $this->render('inapp/create-demande.html.twig', [
            'form'    => $form,
            'error'   => false,
            'success' => false,
        ]);
    }

    #[Route('/template/edit-demande.html.twig', name: 'app_edit_demande', methods: ['GET'])]
    #[Route('/template/edit-demande.html', name: 'app_edit_demande_legacy', methods: ['GET'])]
    #[Route('/template/edit-demande/{id}.html.twig', name: 'app_edit_demande_open', methods: ['GET'])]
    #[Route('/template/edit-demande/{id}.html', name: 'app_edit_demande_open_legacy', methods: ['GET'])]
    public function editDemande(Request $request, EntityManagerInterface $em, CreditRepository $creditRepository, UserRepository $userRepository): Response
    {
        $session = $request->getSession();
        $userId = $session->get('auth_user_id');
        $idFromQuery = $request->query->getInt('id', 0);
        $idFromPath = (int) $request->attributes->get('id', 0);
        $id = $idFromQuery > 0 ? $idFromQuery : $idFromPath;

        if (!$userId) {
            return $this->redirectToRoute('app_signin');
        }

        $user = $userRepository->find((int) $userId);
        $isAdmin = 'ADMIN' === strtoupper((string) ($user?->getRole() ?? 'USER'));

        // Normalize URL to /template/edit-demande.html while keeping selected dossier id in session.
        if ($id > 0) {
            $session->set('edit_demande_id', $id);
            if ($idFromQuery > 0 || $idFromPath > 0) {
                return $this->redirectToRoute('app_edit_demande', [], 303);
            }
        }

        if ($id <= 0) {
            $id = (int) $session->get('edit_demande_id', 0);
        }

        if ($id <= 0) {
            return $this->redirectToRoute('app_template_page', ['page' => 'credit']);
        }

        /** @var DossierClient|null $dossier */
        $dossier = $em->find(DossierClient::class, $id);

        if (!$dossier || (!$isAdmin && $dossier->getIdUser() !== (int) $userId)) {
            throw $this->createNotFoundException('Demande non trouvée');
        }

        $credit = $creditRepository->findOneByDossierId($id);
        $decisionStatus = $this->findCreditDecisionStatus($em->getConnection(), $credit?->getIdCredit());

        $data = [
            'id_dossier'      => $dossier->getIdDossier(),
            'montant_demande' => $dossier->getMontantDemande(),
            'num_dossier'     => $dossier->getNumDossier(),
            'status'          => $dossier->getStatus(),
            'decision_status' => $decisionStatus ?? DossierClient::normalizeStatus($dossier->getStatus()),
            'id_credit'       => $credit?->getIdCredit(),
            'taux_interet'    => $credit?->getTauxInteret(),
            'duree_mois'      => $credit?->getDureeMois(),
            'montant_mensuel' => $credit?->getMontantMensuel(),
        ];

        $editData = new EditDossierCreditData();
        $editData->montant_demande = (float) $dossier->getMontantDemande();
        $editData->duree_mois      = $credit?->getDureeMois();
        $editData->taux_interet    = (float) ($credit?->getTauxInteret() ?? '13');

        $form = $this->createForm(EditDossierCreditType::class, $editData);

        return $this->render('inapp/edit-demande.html.twig', [
            'form'    => $form,
            'demande' => $data,
            'error'   => false,
            'success' => false,
        ]);
    }

    #[Route('/template/edit-demande.html.twig', name: 'app_edit_demande_submit', methods: ['POST'])]
    #[Route('/template/edit-demande.html', name: 'app_edit_demande_submit_legacy', methods: ['POST'])]
    public function editDemandeSubmit(
        Request $request,
        EntityManagerInterface $em,
        CreditRepository $creditRepository,
        UserRepository $userRepository,
    ): Response {
        $session = $request->getSession();
        $userId  = $session->get('auth_user_id');
        $id      = $request->request->getInt('id', 0);

        if (!$userId) {
            return $this->redirectToRoute('app_signin');
        }

        $user = $userRepository->find((int) $userId);
        $isAdmin = 'ADMIN' === strtoupper((string) ($user?->getRole() ?? 'USER'));

        if ($id <= 0) {
            $id = (int) $session->get('edit_demande_id', 0);
        }

        if ($id <= 0) {
            return $this->redirectToRoute('app_template_page', ['page' => 'credit']);
        }

        $session->set('edit_demande_id', $id);

        /** @var DossierClient|null $dossier */
        $dossier = $em->find(DossierClient::class, $id);

        if (!$dossier || (!$isAdmin && $dossier->getIdUser() !== (int) $userId)) {
            throw $this->createNotFoundException('Demande non trouvée');
        }

        $credit = $creditRepository->findOneByDossierId($id);
        $decisionStatus = $this->findCreditDecisionStatus($em->getConnection(), $credit?->getIdCredit());

        // Helper pour construire le tableau attendu par le template
        $buildDataArray = static function (DossierClient $d, ?Credit $c, ?string $decisionStatus): array {
            return [
                'id_dossier'      => $d->getIdDossier(),
                'montant_demande' => $d->getMontantDemande(),
                'num_dossier'     => $d->getNumDossier(),
                'status'          => $d->getStatus(),
                'decision_status' => $decisionStatus ?? DossierClient::normalizeStatus($d->getStatus()),
                'id_credit'       => $c?->getIdCredit(),
                'taux_interet'    => $c?->getTauxInteret(),
                'duree_mois'      => $c?->getDureeMois(),
                'montant_mensuel' => $c?->getMontantMensuel(),
            ];
        };

            $editData = new EditDossierCreditData();
            $form = $this->createForm(EditDossierCreditType::class, $editData);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $montantDemande = (float) $editData->montant_demande;
                $dureeMois      = (int)   $editData->duree_mois;
                $tauxInteret    = (float) $editData->taux_interet;

                // Recalcul de la mensualité via la méthode domaine
                $montantMensuel = Credit::calculerMensualiteStatic($montantDemande, $tauxInteret, $dureeMois);

                try {
                    $now = new \DateTime();

                    $dossier
                        ->setMontantDemande((string) $montantDemande)
                        ->setUpdatedAt($now);

                    if (null !== $credit) {
                        $credit
                            ->setMontantCredit((string) $montantDemande)
                            ->setTauxInteret((string) $tauxInteret)
                            ->setDureeMois($dureeMois)
                            ->setMontantMensuel((string) round($montantMensuel, 2));
                    }

                    $em->flush();

                    return $this->render('inapp/edit-demande.html.twig', [
                        'form'    => $form,
                        'demande' => $buildDataArray($dossier, $credit),
                        'error'   => false,
                        'success' => true,
                    ]);

                } catch (\Exception $e) {
                    return $this->render('inapp/edit-demande.html.twig', [
                        'form'    => $form,
                        'demande' => $buildDataArray($dossier, $credit, $decisionStatus),
                        'error'   => 'Erreur lors de la modification: ' . $e->getMessage(),
                        'success' => false,
                    ]);
                }
            }

            return $this->render('inapp/edit-demande.html.twig', [
                'form'    => $form,
                'demande' => $buildDataArray($dossier, $credit, $decisionStatus),
                'error'   => false,
                'success' => false,
            ]);
    }

    #[Route('/template/retard-prediction.html.twig', name: 'app_retard_prediction', methods: ['GET', 'POST'])]
    #[Route('/template/retard-prediction.html', name: 'app_retard_prediction_legacy', methods: ['GET', 'POST'])]
    public function retardPrediction(
        Request $request,
        UserRepository $userRepository,
        PaymentDelayChatbotService $chatbotService,
    ): Response {
        $userId = (int) $request->getSession()->get('auth_user_id', 0);
        if (0 === $userId) {
            return $this->redirectToRoute('app_signin');
        }

        $user = $userRepository->find($userId);
        $isAdmin = 'ADMIN' === strtoupper((string) ($user?->getRole() ?? 'USER'));
        if (!$isAdmin) {
            $this->addFlash('errorMessage', 'Accès refusé: action réservée à l\'administrateur.');

            return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
        }

        $queryData = new RetardPredictionQueryData();
        $form = $this->createForm(RetardPredictionQueryType::class, $queryData);
        $form->handleRequest($request);

        $prediction = null;
        $targetUser = null;
        $errorMessage = false;

        if ($form->isSubmitted() && $form->isValid()) {
            $search = trim((string) $queryData->query);
            if ('' === $search) {
                $errorMessage = 'Veuillez saisir un ID ou un email d\'utilisateur.';
            } else {
                $targetUser = ctype_digit($search)
                    ? $userRepository->find((int) $search)
                    : $userRepository->findOneBy(['email' => $search]);

                if (null === $targetUser) {
                    $errorMessage = 'Utilisateur introuvable. Vérifiez l\'ID ou l\'email.';
                } else {
                    $prediction = $chatbotService->analyzeUser(
                        (int) $targetUser->getIdUser(),
                        $targetUser->getRevenu(),
                    );
                }
            }
        }

        return $this->render('inapp/retard-prediction.html.twig', [
            'form' => $form->createView(),
            'prediction' => $prediction,
            'targetUser' => $targetUser,
            'errorMessage' => $errorMessage,
            'successMessage' => false,
            'isAdmin' => $isAdmin,
        ]);
    }

    #[Route('/template/delete-demande/{id}.html.twig', name: 'app_delete_demande', methods: ['POST'])]
    #[Route('/template/delete-demande/{id}.html', name: 'app_delete_demande_legacy', methods: ['POST'])]
    public function deleteDemande(int $id, Request $request, EntityManagerInterface $em, UserRepository $userRepository): RedirectResponse
    {
        $deleteData = new DeleteDemandeData();
        $form = $this->createForm(DeleteDemandeType::class, $deleteData);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('errorMessage', 'Requête de suppression invalide.');

            return $this->redirectToRoute('app_template_page', [
                'page' => 'credit',
            ], 303);
        }

        $submittedId = (int) $deleteData->id;
        if ($submittedId <= 0 || $submittedId !== $id) {
            $this->addFlash('errorMessage', 'Identifiant de suppression invalide.');

            return $this->redirectToRoute('app_template_page', [
                'page' => 'credit',
            ], 303);
        }

        $userId = (int) $request->getSession()->get('auth_user_id', 0);

        if (0 === $userId) {
            return $this->redirectToRoute('app_signin');
        }

        $user = $userRepository->find($userId);
        $isAdmin = 'ADMIN' === strtoupper((string) ($user?->getRole() ?? 'USER'));

        /** @var DossierClient|null $dossier */
        $dossier = $em->find(DossierClient::class, $id);

        if (!$dossier || (!$isAdmin && $dossier->getIdUser() !== $userId)) {
            $this->addFlash('errorMessage', 'Demande introuvable.');

            return $this->redirectToRoute('app_template_page', [
                'page' => 'credit',
            ], 303);
        }

        try {
            $em->remove($dossier);
            $em->flush();
        } catch (\Throwable $e) {
            $this->addFlash('errorMessage', 'Erreur suppression: ' . $e->getMessage());

            return $this->redirectToRoute('app_template_page', [
                'page' => 'credit',
            ], 303);
        }

        try {
            // La reindexation utilise du DDL (ALTER TABLE), exécutée hors transaction.
            $this->resequenceEspritWalletIds($em->getConnection());
        } catch (\Throwable $e) {
            $this->addFlash('errorMessage', 'Suppression OK, reindexation echouee: ' . $e->getMessage());

            return $this->redirectToRoute('app_template_page', [
                'page' => 'credit',
            ], 303);
        }

        $this->addFlash('successMessage', 'Demande supprimée avec succès.');

        return $this->redirectToRoute('app_template_page', [
            'page' => 'credit',
        ], 303);
    }

    #[Route('/template/decision-demande/{id}.html.twig', name: 'app_decision_demande', methods: ['POST'])]
    #[Route('/template/decision-demande/{id}.html', name: 'app_decision_demande_legacy', methods: ['POST'])]
    public function decisionDemande(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        CreditRepository $creditRepository,
        UserRepository $userRepository,
        CreditNotificationService $notificationService,
    ): RedirectResponse {
        $userId = (int) $request->getSession()->get('auth_user_id', 0);
        if (0 === $userId) {
            return $this->redirectToRoute('app_signin');
        }

        $user = $userRepository->find($userId);
        $isAdmin = 'ADMIN' === strtoupper((string) ($user?->getRole() ?? 'USER'));
        if (!$isAdmin) {
            $this->addFlash('errorMessage', 'Accès refusé: action réservée à l\'administrateur.');

            return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
        }

        $decisionData = new DecisionDemandeData();
        $form = $this->createForm(DecisionDemandeType::class, $decisionData);
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('errorMessage', 'Requête de décision invalide.');

            return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
        }

        $submittedId = (int) $decisionData->id_dossier;
        $newStatut = (string) $decisionData->statut;

        if ($submittedId <= 0 || $submittedId !== $id) {
            $this->addFlash('errorMessage', 'Identifiant de demande invalide.');

            return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
        }

        /** @var DossierClient|null $dossier */
        $dossier = $em->find(DossierClient::class, $id);
        if (!$dossier) {
            $this->addFlash('errorMessage', 'Demande introuvable.');

            return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
        }

        $credit = $creditRepository->findOneByDossierId($id);
        if (null === $credit) {
            $this->addFlash('errorMessage', 'Aucun crédit associé à cette demande.');

            return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
        }

        try {
            $updatedRows = $em->getConnection()->executeStatement(
                'UPDATE echeance SET statut = :statut WHERE id_credit = :idCredit',
                [
                    'statut' => $newStatut,
                    'idCredit' => (int) $credit->getIdCredit(),
                ]
            );

            if ($updatedRows <= 0) {
                $this->addFlash('errorMessage', 'Aucune échéance mise à jour pour cette demande.');

                return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
            }

            $mappedStatus = DossierClient::mapDecisionToStatus($newStatut);
            if (null !== $mappedStatus) {
                $dossier->setStatus($mappedStatus);
            }

            $dossier->setUpdatedAt(new \DateTime());
            $em->flush();

            // Récupérer l'utilisateur propriétaire du dossier pour envoyer l'email
            $requestUser = $userRepository->find($dossier->getIdUser());
            if ($requestUser) {
                try {
                    if ('Accept' === $newStatut) {
                        $notificationService->sendCreditApprovedNotification($requestUser, $dossier, $credit);
                    } else {
                        $notificationService->sendCreditRejectedNotification($requestUser, $dossier, $credit);
                    }
                } catch (\Throwable $emailError) {
                    // Log l'erreur d'email mais ne bloque pas le flux
                    error_log('Erreur lors de l\'envoi de l\'email de notification: ' . $emailError->getMessage());
                }
            }

            $this->addFlash('successMessage', sprintf('Demande #%d %s.', $id, 'Accept' === $newStatut ? 'acceptée' : 'rejetée'));

            return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
        } catch (\Throwable $e) {
            $this->addFlash('errorMessage', 'Erreur lors de la mise à jour du statut: ' . $e->getMessage());

            return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
        }
    }

    #[Route(
        '/template/{page}.html.twig',
        name: 'app_template_page',
        requirements: ['page' => 'credit|create-demande|signup'],
        methods: ['GET']
    )]
    #[Route(
        '/template/{page}.html',
        name: 'app_template_page_legacy',
        requirements: ['page' => 'credit|create-demande|signup'],
        methods: ['GET']
    )]
    public function page(
        string $page,
        Request $request,
        DossierClientRepository $dossierClientRepository,
        UserRepository $userRepository,
    ): Response
    {
        $userId = (int) $request->getSession()->get('auth_user_id', 0);
        if (0 === $userId) {
            return $this->redirectToRoute('app_signin');
        }

        $user = $userRepository->find($userId);
        $isAdmin = 'ADMIN' === strtoupper((string) ($user?->getRole() ?? 'USER'));

        $successMessage = trim((string) $request->query->get('successMessage', ''));
        if ('' === $successMessage) {
            $successMessage = (string) ($request->getSession()->getFlashBag()->get('successMessage')[0] ?? '');
        }

        $errorMessage = trim((string) $request->query->get('errorMessage', ''));
        if ('' === $errorMessage) {
            $errorMessage = (string) ($request->getSession()->getFlashBag()->get('errorMessage')[0] ?? '');
        }

        $context = [
            'error' => false,
            'success' => false,
            'successMessage' => $successMessage,
            'errorMessage' => $errorMessage,
            'isAdmin' => $isAdmin,
        ];

        if ('credit' === $page) {
            $searchDureeSession = $request->getSession()->get('credit_search_duree');
            $dureeMois = is_int($searchDureeSession)
                ? $searchDureeSession
                : (ctype_digit((string) $searchDureeSession) ? (int) $searchDureeSession : null);

            $searchData = new CreditSearchData();
            $searchData->search_duree = $dureeMois;

            $context['searchForm'] = $this->createForm(CreditSearchType::class, $searchData)->createView();
            $context['search_duree'] = null !== $dureeMois ? (string) $dureeMois : '';

            $context['creditRows'] = $dossierClientRepository->findCreditRows($dureeMois, $userId, $isAdmin);

            $context['deleteForms'] = [];
            $context['decisionForms'] = [];
            foreach ($context['creditRows'] as $row) {
                $idDossier = (int) ($row['id_dossier'] ?? 0);
                if ($idDossier <= 0) {
                    continue;
                }

                $deleteData = new DeleteDemandeData();
                $deleteData->id = $idDossier;
                $context['deleteForms'][$idDossier] = $this->createForm(DeleteDemandeType::class, $deleteData)->createView();

                if ($isAdmin) {
                    $acceptData = new DecisionDemandeData();
                    $acceptData->id_dossier = $idDossier;
                    $acceptData->statut = 'Accept';

                    $rejectData = new DecisionDemandeData();
                    $rejectData->id_dossier = $idDossier;
                    $rejectData->statut = 'Rejet';

                    $context['decisionForms'][$idDossier]['Accept'] = $this->createForm(DecisionDemandeType::class, $acceptData)->createView();
                    $context['decisionForms'][$idDossier]['Rejet'] = $this->createForm(DecisionDemandeType::class, $rejectData)->createView();
                }
            }
        }

        if ('create-demande' === $page) {
            $context['form'] = $this->createForm(DossierCreditType::class, new DossierCreditData());
        }

        $response = $this->render(sprintf('inapp/%s.html.twig', $page), $context);

        return $response;
    }

    private function resequenceEspritWalletIds(Connection $connection): void
    {
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');

        try {
            $this->resequencePrimaryKey($connection, 'dossier_client', 'id_dossier', 'tmp_map_dossier');
            $this->resequenceForeignKey($connection, 'credits', 'id_dossier', 'tmp_map_dossier');

            $this->resequencePrimaryKey($connection, 'credits', 'id_credit', 'tmp_map_credit');
            $this->resequenceForeignKey($connection, 'echeance', 'id_credit', 'tmp_map_credit');

            $this->resequencePrimaryKey($connection, 'echeance', 'id_echeance', 'tmp_map_echeance');
            $this->resequenceForeignKey($connection, 'paiement', 'id_echeance', 'tmp_map_echeance');
            $this->resequenceForeignKey($connection, 'retard', 'id_echeance', 'tmp_map_echeance');

            $this->resequencePrimaryKey($connection, 'paiement', 'id_paiement', 'tmp_map_paiement');
            $this->resequencePrimaryKey($connection, 'retard', 'id_retard', 'tmp_map_retard');
        } finally {
            $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
        }
    }

    private function findCreditDecisionStatus(Connection $connection, ?int $creditId): ?string
    {
        if (null === $creditId || $creditId <= 0) {
            return null;
        }

        $status = $connection->fetchOne(
            'SELECT e.statut
             FROM echeance e
             WHERE e.id_credit = :idCredit
             ORDER BY e.num_echeance ASC, e.id_echeance ASC
             LIMIT 1',
            ['idCredit' => $creditId]
        );

        return false === $status ? null : (string) $status;
    }

    private function resequencePrimaryKey(Connection $connection, string $table, string $primaryKey, string $mapTable): void
    {
        $connection->executeStatement(sprintf('DROP TEMPORARY TABLE IF EXISTS %s', $mapTable));
        $connection->executeStatement(
            sprintf('CREATE TEMPORARY TABLE %s (old_id INT NOT NULL PRIMARY KEY, new_id INT NOT NULL)', $mapTable)
        );
        $connection->executeStatement('SET @rownum := 0');
        $connection->executeStatement(
            sprintf(
                'INSERT INTO %s (old_id, new_id)
                 SELECT %s, (@rownum := @rownum + 1)
                 FROM %s
                 ORDER BY %s',
                $mapTable,
                $primaryKey,
                $table,
                $primaryKey
            )
        );

        $connection->executeStatement(
            sprintf(
                'UPDATE %s t
                 INNER JOIN %s m ON t.%s = m.old_id
                 SET t.%s = m.new_id',
                $table,
                $mapTable,
                $primaryKey,
                $primaryKey
            )
        );

        $nextAutoIncrement = (int) $connection->fetchOne(
            sprintf('SELECT COALESCE(MAX(%s), 0) + 1 FROM %s', $primaryKey, $table)
        );

        $connection->executeStatement(
            sprintf('ALTER TABLE %s AUTO_INCREMENT = %d', $table, max(1, $nextAutoIncrement))
        );
    }

    private function resequenceForeignKey(Connection $connection, string $table, string $foreignKey, string $mapTable): void
    {
        $connection->executeStatement(
            sprintf(
                'UPDATE %s t
                 INNER JOIN %s m ON t.%s = m.old_id
                 SET t.%s = m.new_id',
                $table,
                $mapTable,
                $foreignKey,
                $foreignKey
            )
        );
    }
}
