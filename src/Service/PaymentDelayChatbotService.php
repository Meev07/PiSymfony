<?php

namespace App\Service;

use App\Service\Risk\PaymentDelayDaysService;
use App\Service\Risk\PaymentDelayRiskService;
use App\Service\Risk\RiskLevel;
use Doctrine\DBAL\Connection;

final class PaymentDelayChatbotService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly PaymentDelayRiskService $riskService,
        private readonly PaymentDelayDaysService $daysService,
    ) {
    }

    public function analyzeUser(int $userId, ?float $monthlyIncome): PaymentDelayPredictionResult
    {
        $payments = $this->connection->fetchAssociative(
            'SELECT
                COUNT(p.id_paiement) AS total_payments,
                SUM(CASE WHEN p.statut = \'COMPLETED\' THEN 1 ELSE 0 END) AS completed_payments,
                MAX(p.date_paiement) AS last_payment_date
            FROM paiement p
            JOIN echeance e ON e.id_echeance = p.id_echeance
            JOIN credits c ON c.id_credit = e.id_credit
            JOIN dossier_client d ON d.id_dossier = c.id_dossier
            WHERE d.id_user = :userId',
            ['userId' => $userId]
        );

        $delays = $this->connection->fetchAssociative(
            'SELECT
                COUNT(r.id_retard) AS total_delays,
                SUM(CASE WHEN r.statut = \'ACTIVE\' THEN 1 ELSE 0 END) AS active_delays,
                AVG(r.jours_retard) AS average_delay_days,
                MAX(r.date_detection) AS last_delay_date
            FROM retard r
            JOIN echeance e ON e.id_echeance = r.id_echeance
            JOIN credits c ON c.id_credit = e.id_credit
            JOIN dossier_client d ON d.id_dossier = c.id_dossier
            WHERE d.id_user = :userId',
            ['userId' => $userId]
        );

        $creditInfo = $this->connection->fetchAssociative(
            'SELECT
                COALESCE(SUM(c.montant_credit), 0) AS total_credit_amount,
                COALESCE(AVG(c.taux_interet), 0) AS avg_interest_rate
            FROM credits c
            JOIN dossier_client d ON d.id_dossier = c.id_dossier
            WHERE d.id_user = :userId',
            ['userId' => $userId]
        );

        $totalPayments = $this->toInt($payments['total_payments'] ?? 0);
        $completedPayments = $this->toInt($payments['completed_payments'] ?? 0);
        $totalDelays = $this->toInt($delays['total_delays'] ?? 0);
        $activeDelays = $this->toInt($delays['active_delays'] ?? 0);
        $averageDelayDays = $this->toFloat($delays['average_delay_days']);
        $lastPaymentDate = $this->parseDateTime($payments['last_payment_date'] ?? null);
        $lastDelayDate = $this->parseDateTime($delays['last_delay_date'] ?? null);

        $creditAmount = $this->toFloat($creditInfo['total_credit_amount'] ?? 0.0);
        $averageInterestRate = $this->toFloat($creditInfo['avg_interest_rate'] ?? 0.0);

        $latePaymentRatio = $totalPayments > 0 ? min(1.0, $totalDelays / $totalPayments) : 0.0;

        $riskProbability = $this->computeRiskProbability(
            $latePaymentRatio,
            $activeDelays,
            $averageDelayDays,
            $monthlyIncome,
            $creditAmount,
            $averageInterestRate,
        );

        $riskLevel = $this->pickRiskLevel($riskProbability);
        $predictedDelayDays = $this->computePredictedDelayDays(
            $riskProbability,
            $averageDelayDays,
            $monthlyIncome,
            $creditAmount,
            $averageInterestRate,
        );

        $explanation = $this->buildExplanation(
            $totalPayments,
            $completedPayments,
            $totalDelays,
            $activeDelays,
            $averageDelayDays,
            $monthlyIncome,
            $creditAmount,
            $averageInterestRate,
        );

        return new PaymentDelayPredictionResult(
            $totalPayments,
            $completedPayments,
            $totalDelays,
            $activeDelays,
            $averageDelayDays,
            $lastPaymentDate,
            $lastDelayDate,
            $latePaymentRatio,
            $riskProbability,
            $riskLevel,
            $predictedDelayDays,
            $explanation,
        );
    }

    private function computeRiskProbability(
        float $latePaymentRatio,
        int $activeDelays,
        ?float $averageDelayDays,
        ?float $monthlyIncome,
        float $creditAmount,
        float $averageInterestRate,
    ): float {
        $probability = 0.05;
        $probability += min(0.30, $latePaymentRatio * 0.50);
        $probability += min(0.25, $activeDelays * 0.10);

        if (null !== $averageDelayDays && $averageDelayDays > 0.0) {
            $probability += min(0.20, $averageDelayDays / 60.0 * 0.20);
        }

        if ($monthlyIncome > 0.0 && $creditAmount > 0.0) {
            $financial = $this->riskService->predict($creditAmount, $monthlyIncome, $averageInterestRate > 0.0 ? $averageInterestRate : 12.0)->probability;
            $probability = ($probability + $financial) / 2.0;
        }

        return $this->clamp($probability, 0.01, 0.99);
    }

    private function computePredictedDelayDays(
        float $riskProbability,
        ?float $averageDelayDays,
        ?float $monthlyIncome,
        float $creditAmount,
        float $averageInterestRate,
    ): int {
        if ($monthlyIncome > 0.0 && $creditAmount > 0.0) {
            return $this->daysService->predictDays($creditAmount, $monthlyIncome, $averageInterestRate > 0.0 ? $averageInterestRate : 12.0)->predictedDays;
        }

        if (null !== $averageDelayDays && $averageDelayDays > 0.0) {
            return (int) max(1, round($averageDelayDays));
        }

        return (int) min(60, max(0, round(2 + $riskProbability * 15)));
    }

    private function buildExplanation(
        int $totalPayments,
        int $completedPayments,
        int $totalDelays,
        int $activeDelays,
        ?float $averageDelayDays,
        ?float $monthlyIncome,
        float $creditAmount,
        float $averageInterestRate,
    ): string {
        $parts = [];

        if ($totalPayments <= 0) {
            $parts[] = 'Aucun paiement historique trouvé pour cet utilisateur.';
        } else {
            $parts[] = sprintf('%d paiement(s) enregistrés, dont %d complété(s).', $totalPayments, $completedPayments);
        }

        if ($totalDelays > 0) {
            $parts[] = sprintf('%d retard(s) détecté(s)', $totalDelays);
        }

        if ($activeDelays > 0) {
            $parts[] = sprintf('%d retard(s) actif(s)', $activeDelays);
        }

        if (null !== $averageDelayDays) {
            $parts[] = sprintf('Délai moyen de retard: %.1f jour(s)', $averageDelayDays);
        }

        if ($monthlyIncome > 0.0) {
            $parts[] = sprintf('Revenu mensuel: %.2f DT', $monthlyIncome);
        } else {
            $parts[] = 'Revenu inconnu pour l’analyse financière.';
        }

        if ($creditAmount > 0.0) {
            $parts[] = sprintf('Montant total des crédits: %.2f DT', $creditAmount);
            $parts[] = sprintf('Taux moyen: %.2f %%', $averageInterestRate);
        }

        return implode(' ', array_filter($parts));
    }

    private function pickRiskLevel(float $probability): string
    {
        return match (true) {
            $probability < 0.33 => RiskLevel::LOW->label(),
            $probability < 0.66 => RiskLevel::MEDIUM->label(),
            default => RiskLevel::HIGH->label(),
        };
    }

    private function parseDateTime(?string $value): ?\DateTimeImmutable
    {
        return null === $value ? null : new \DateTimeImmutable($value);
    }

    private function toFloat(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }

    private function toInt(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    private function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
