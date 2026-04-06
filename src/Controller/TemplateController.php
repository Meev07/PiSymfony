<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
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

    #[Route('/template/signin.html', name: 'app_signin', methods: ['GET'])]
    public function signin(Request $request): Response
    {
        return $this->render('inapp/signin.html.twig', [
            'error' => $request->query->getBoolean('error'),
        ]);
    }

    #[Route('/template/signin.html', name: 'app_signin_submit', methods: ['POST'])]
    public function signinSubmit(Request $request, Connection $connection): RedirectResponse
    {
        $email = trim((string) $request->request->get('email', ''));
        $emailNormalized = mb_strtolower($email);
        $password = (string) $request->request->get('password', '');

        if ('' === $email || '' === $password) {
            return $this->redirectToRoute('app_signin', ['error' => 1], 303);
        }

        $user = $connection->fetchAssociative(
            'SELECT id_user, password_hash
             FROM users
             WHERE LOWER(TRIM(REPLACE(REPLACE(email, CHAR(13), \'\'), CHAR(10), \'\'))) = :email
             LIMIT 1',
            ['email' => $emailNormalized]
        );

        if (!$user) {
            return $this->redirectToRoute('app_signin', ['error' => 1], 303);
        }

        $storedPassword = (string) $user['password_hash'];
        $isValidPassword = password_verify($password, $storedPassword) || hash_equals($storedPassword, $password);

        if (!$isValidPassword) {
            return $this->redirectToRoute('app_signin', ['error' => 1], 303);
        }

        $request->getSession()->set('auth_user_id', (int) $user['id_user']);

        return $this->redirectToRoute('app_template_page', ['page' => 'credit'], 303);
    }

    #[Route('/template/index.html', name: 'app_template_index', methods: ['GET'])]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('app_template_page', ['page' => 'credit']);
    }

    #[Route('/template/create-demande.html', name: 'app_create_demande_submit', methods: ['POST'])]
    public function createDemandeSubmit(Request $request, Connection $connection): Response
    {
        $userId = $request->getSession()->get('auth_user_id');
        
        if (!$userId) {
            return $this->redirectToRoute('app_signin');
        }

        // Récupérer et valider les données du formulaire
        $montantDemande = (string) $request->request->get('montant_demande', '');
        $dureeMois = (string) $request->request->get('duree_mois', '');
        $tauxInteret = (string) $request->request->get('taux_interet', '');
        $montantMensuel = (string) $request->request->get('montant_mensuel', '');
        $objetCredit = trim((string) $request->request->get('objet_credit', ''));
        $description = trim((string) $request->request->get('description', ''));

        // Valider les données
        $errors = [];

        if ('' === $montantDemande || !is_numeric($montantDemande)) {
            $errors[] = 'Montant invalide';
        } else {
            $montantDemande = (float) $montantDemande;
            if ($montantDemande < 100 || $montantDemande > 500000) {
                $errors[] = 'Le montant doit être entre 100 et 500 000 DT';
            }
        }

        if ('' === $dureeMois || !is_numeric($dureeMois)) {
            $errors[] = 'Durée invalide';
        } else {
            $dureeMois = (int) $dureeMois;
            if ($dureeMois < 1 || $dureeMois > 360) {
                $errors[] = 'La durée doit être entre 1 et 360 mois';
            }
        }

        if ('' === $tauxInteret || !is_numeric($tauxInteret)) {
            $errors[] = 'Taux invalide';
        } else {
            $tauxInteret = (float) $tauxInteret;
            if ($tauxInteret < 0 || $tauxInteret > 50) {
                $errors[] = 'Le taux doit être entre 0 et 50%';
            }
        }

        if ('' === $objetCredit) {
            $errors[] = 'Veuillez sélectionner un objet du crédit';
        }

        if ('' === $description || strlen($description) < 10 || strlen($description) > 500) {
            $errors[] = 'La description doit contenir entre 10 et 500 caractères';
        }

        // Si erreurs, rediriger avec message d'erreur
        if (!empty($errors)) {
            return $this->render('inapp/create-demande.html.twig', [
                'error' => implode(', ', $errors),
                'success' => false,
            ]);
        }

        try {
            $connection->beginTransaction();

            // 1. Créer un numéro de dossier unique
            $numDossier = 'DOS-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $now = new \DateTime();

            // 2. Insérer dans dossier_client
            $connection->insert('dossier_client', [
                'id_user' => $userId,
                'num_dossier' => $numDossier,
                'montant_demande' => $montantDemande,
                'status' => 0, // 0 = En attente
                'created_at' => $now->format('Y-m-d H:i:s'),
            ]);

            $dossierId = $connection->lastInsertId();

            // 3. Insérer dans credits
            $connection->insert('credits', [
                'id_dossier' => $dossierId,
                'montant_credit' => $montantDemande,
                'taux_interet' => $tauxInteret,
                'duree_mois' => $dureeMois,
                'montant_mensuel' => (float) $montantMensuel,
                'created_at' => $now->format('Y-m-d H:i:s'),
            ]);

            $creditId = $connection->lastInsertId();

            // 4. Générer les échéances
            $dateEcheance = new \DateTime('first day of next month');
            for ($i = 1; $i <= $dureeMois; ++$i) {
                $connection->insert('echeance', [
                    'id_credit' => $creditId,
                    'num_echeance' => $i,
                    'date_echeance' => $dateEcheance->format('Y-m-d'),
                    'montant_echeance' => (float) $montantMensuel,
                    'statut' => 'PENDING',
                    'created_at' => $now->format('Y-m-d H:i:s'),
                ]);

                $dateEcheance->modify('+1 month');
            }

            $connection->commit();

            // Retourner avec message de succès
            return $this->render('inapp/create-demande.html.twig', [
                'success' => true,
                'error' => false,
            ]);

        } catch (\Exception $e) {
            // Vérifier si une transaction est active avant de la rollback
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            return $this->render('inapp/create-demande.html.twig', [
                'error' => 'Erreur lors de la création de la demande: ' . $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    #[Route('/template/edit-demande/{id}.html', name: 'app_edit_demande', methods: ['GET'])]
    public function editDemande(int $id, Request $request, Connection $connection): Response
    {
        $userId = $request->getSession()->get('auth_user_id');
        
        if (!$userId) {
            return $this->redirectToRoute('app_signin');
        }

        // Récupérer les données du dossier et du crédit
        $data = $connection->fetchAssociative(
            'SELECT 
                d.id_dossier,
                d.montant_demande,
                d.num_dossier,
                d.status,
                c.id_credit,
                c.taux_interet,
                c.duree_mois,
                c.montant_mensuel
            FROM dossier_client d
            LEFT JOIN credits c ON c.id_dossier = d.id_dossier
            WHERE d.id_dossier = :id AND d.id_user = :userId
            LIMIT 1',
            ['id' => $id, 'userId' => $userId]
        );

        if (!$data) {
            throw $this->createNotFoundException('Demande non trouvée');
        }

        return $this->render('inapp/edit-demande.html.twig', [
            'demande' => $data,
            'error' => false,
            'success' => false,
        ]);
    }

    #[Route('/template/edit-demande/{id}.html', name: 'app_edit_demande_submit', methods: ['POST'])]
    public function editDemandeSubmit(int $id, Request $request, Connection $connection): Response
    {
        $userId = $request->getSession()->get('auth_user_id');
        
        if (!$userId) {
            return $this->redirectToRoute('app_signin');
        }

        // Vérifier que le dossier appartient à l'utilisateur
        $dossier = $connection->fetchAssociative(
            'SELECT id_dossier FROM dossier_client WHERE id_dossier = :id AND id_user = :userId LIMIT 1',
            ['id' => $id, 'userId' => $userId]
        );

        if (!$dossier) {
            throw $this->createNotFoundException('Demande non trouvée');
        }

        // Récupérer et valider les données
        $montantDemande = (string) $request->request->get('montant_demande', '');
        $dureeMois = (string) $request->request->get('duree_mois', '');
        $tauxInteret = (string) $request->request->get('taux_interet', '');
        $montantMensuel = (string) $request->request->get('montant_mensuel', '');

        // Valider les données
        $errors = [];

        if ('' === $montantDemande || !is_numeric($montantDemande)) {
            $errors[] = 'Montant invalide';
        } else {
            $montantDemande = (float) $montantDemande;
            if ($montantDemande < 100 || $montantDemande > 500000) {
                $errors[] = 'Le montant doit être entre 100 et 500 000 DT';
            }
        }

        if ('' === $dureeMois || !is_numeric($dureeMois)) {
            $errors[] = 'Durée invalide';
        } else {
            $dureeMois = (int) $dureeMois;
            if ($dureeMois < 1 || $dureeMois > 360) {
                $errors[] = 'La durée doit être entre 1 et 360 mois';
            }
        }

        if ('' === $tauxInteret || !is_numeric($tauxInteret)) {
            $errors[] = 'Taux invalide';
        } else {
            $tauxInteret = (float) $tauxInteret;
            if ($tauxInteret < 0 || $tauxInteret > 50) {
                $errors[] = 'Le taux doit être entre 0 et 50%';
            }
        }

        if (!empty($errors)) {
            $data = $connection->fetchAssociative(
                'SELECT d.id_dossier, d.montant_demande, d.num_dossier, d.status, 
                        c.id_credit, c.taux_interet, c.duree_mois, c.montant_mensuel
                 FROM dossier_client d
                 LEFT JOIN credits c ON c.id_dossier = d.id_dossier
                 WHERE d.id_dossier = :id',
                ['id' => $id]
            );

            return $this->render('inapp/edit-demande.html.twig', [
                'demande' => $data,
                'error' => implode(', ', $errors),
                'success' => false,
            ]);
        }

        try {
            $connection->beginTransaction();
            $now = new \DateTime();

            // Mettre à jour dossier_client
            $connection->update('dossier_client', [
                'montant_demande' => $montantDemande,
                'updated_at' => $now->format('Y-m-d H:i:s'),
            ], ['id_dossier' => $id]);

            // Mettre à jour credits
            $connection->update('credits', [
                'montant_credit' => $montantDemande,
                'taux_interet' => $tauxInteret,
                'duree_mois' => $dureeMois,
                'montant_mensuel' => (float) $montantMensuel,
            ], ['id_dossier' => $id]);

            $connection->commit();

            // Récupérer les données mises à jour
            $data = $connection->fetchAssociative(
                'SELECT d.id_dossier, d.montant_demande, d.num_dossier, d.status, 
                        c.id_credit, c.taux_interet, c.duree_mois, c.montant_mensuel
                 FROM dossier_client d
                 LEFT JOIN credits c ON c.id_dossier = d.id_dossier
                 WHERE d.id_dossier = :id',
                ['id' => $id]
            );

            return $this->render('inapp/edit-demande.html.twig', [
                'demande' => $data,
                'error' => false,
                'success' => true,
            ]);

        } catch (\Exception $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            $data = $connection->fetchAssociative(
                'SELECT d.id_dossier, d.montant_demande, d.num_dossier, d.status, 
                        c.id_credit, c.taux_interet, c.duree_mois, c.montant_mensuel
                 FROM dossier_client d
                 LEFT JOIN credits c ON c.id_dossier = d.id_dossier
                 WHERE d.id_dossier = :id',
                ['id' => $id]
            );

            return $this->render('inapp/edit-demande.html.twig', [
                'demande' => $data,
                'error' => 'Erreur lors de la modification: ' . $e->getMessage(),
                'success' => false,
            ]);
        }
    }

    #[Route('/template/delete-demande/{id}.html', name: 'app_delete_demande', methods: ['POST'])]
    public function deleteDemande(int $id, Request $request, Connection $connection): RedirectResponse
    {
        $userId = (int) $request->getSession()->get('auth_user_id', 0);

        if (0 === $userId) {
            return $this->redirectToRoute('app_signin');
        }

        $dossier = $connection->fetchAssociative(
            'SELECT id_dossier FROM dossier_client WHERE id_dossier = :id AND id_user = :userId LIMIT 1',
            ['id' => $id, 'userId' => $userId]
        );

        if (!$dossier) {
            return $this->redirectToRoute('app_template_page', [
                'page' => 'credit',
                'errorMessage' => 'Demande introuvable.',
            ], 303);
        }

        try {
            $connection->beginTransaction();

            $echeanceIds = $connection->fetchFirstColumn(
                'SELECT e.id_echeance
                 FROM echeance e
                 INNER JOIN credits c ON c.id_credit = e.id_credit
                 WHERE c.id_dossier = :id',
                ['id' => $id]
            );

            if (!empty($echeanceIds)) {
                $placeholders = implode(',', array_fill(0, count($echeanceIds), '?'));

                $connection->executeStatement(
                    sprintf('DELETE FROM paiement WHERE id_echeance IN (%s)', $placeholders),
                    $echeanceIds
                );
                $connection->executeStatement(
                    sprintf('DELETE FROM retard WHERE id_echeance IN (%s)', $placeholders),
                    $echeanceIds
                );
            }

            $connection->executeStatement(
                'DELETE e FROM echeance e INNER JOIN credits c ON c.id_credit = e.id_credit WHERE c.id_dossier = :id',
                ['id' => $id]
            );
            $connection->executeStatement('DELETE FROM credits WHERE id_dossier = :id', ['id' => $id]);
            $connection->executeStatement(
                'DELETE FROM dossier_client WHERE id_dossier = :id AND id_user = :userId',
                ['id' => $id, 'userId' => $userId]
            );

            $connection->commit();
        } catch (\Throwable $e) {
            if ($connection->isTransactionActive()) {
                $connection->rollBack();
            }

            return $this->redirectToRoute('app_template_page', [
                'page' => 'credit',
                'errorMessage' => 'Erreur suppression: ' . $e->getMessage(),
            ], 303);
        }

        try {
            // La reindexation utilise du DDL (ALTER TABLE), execute hors transaction.
            $this->resequenceEspritWalletIds($connection);
        } catch (\Throwable $e) {
            return $this->redirectToRoute('app_template_page', [
                'page' => 'credit',
                'errorMessage' => 'Suppression OK, reindexation echouee: ' . $e->getMessage(),
            ], 303);
        }

        return $this->redirectToRoute('app_template_page', [
            'page' => 'credit',
            'successMessage' => 'Demande supprimée avec succès.',
        ], 303);
    }

    #[Route(
        '/template/{page}.html',
        name: 'app_template_page',
        requirements: ['page' => 'credit|create-demande|signup'],
        methods: ['GET']
    )]
    public function page(string $page, Request $request, Connection $connection): Response
    {
        $context = [
            'error' => false,
            'success' => false,
            'successMessage' => trim((string) $request->query->get('successMessage', '')),
            'errorMessage' => trim((string) $request->query->get('errorMessage', '')),
        ];

        if ('credit' === $page) {
            $context['creditRows'] = $connection->fetchAllAssociative(
                'SELECT
                    d.id_dossier,
                    d.montant_demande,
                    d.status,
                    d.created_at AS date_demande,
                    c.montant_mensuel,
                    c.duree_mois
                FROM dossier_client d
                LEFT JOIN credits c ON c.id_dossier = d.id_dossier
                ORDER BY d.created_at DESC, d.id_dossier DESC'
            );
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
