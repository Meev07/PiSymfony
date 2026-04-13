<?php

namespace App\Service\Risk;

/**
 * Service d'évaluation du risque de retard de paiement.
 * Implémentation heuristique basée sur le ratio dette/revenu et le taux d'intérêt.
 * Port PHP de PaymentDelayRiskService + HeuristicDelayRiskModel (org.example.risk).
 *
 * Règles:
 *  - Ratio annuel montant/revenu (P / (revenu × 12))
 *    ≤ 1.0  → +0.10  (risque modéré)
 *    ≤ 2.5  → +0.35  (risque significatif)
 *    > 2.5  → +0.65  (risque élevé)
 *  - Taux au-delà de 10% → ajout jusqu'à +0.20
 *  - Revenu < 1 200 DT/mois → +0.10
 */
final class PaymentDelayRiskService
{
    /**
     * Évalue le risque de retard à partir des paramètres du crédit et du profil emprunteur.
     *
     * @param float $creditAmount        Montant emprunté (en DT)
     * @param float $monthlyIncome       Revenu mensuel de l'emprunteur (en DT)
     * @param float $annualInterestRate  Taux annuel en pourcentage (ex: 7.5 pour 7,5%)
     */
    public function predict(float $creditAmount, float $monthlyIncome, float $annualInterestRate): LoanRiskPrediction
    {
        $input = new LoanRiskInput($creditAmount, $monthlyIncome, $annualInterestRate);

        $debtToIncomeAnnual = $input->creditAmount / ($input->monthlyIncome * 12.0);
        $score = 0.0;
        $reasons = [];

        if ($debtToIncomeAnnual <= 1.0) {
            $score += 0.10;
            $reasons[] = 'Montant de crédit modéré vs revenu';
        } elseif ($debtToIncomeAnnual <= 2.5) {
            $score += 0.35;
            $reasons[] = 'Montant de crédit significatif vs revenu';
        } else {
            $score += 0.65;
            $reasons[] = 'Montant de crédit élevé vs revenu';
        }

        $rateAdj = $this->clamp(($input->annualInterestRate - 10.0) / 50.0, 0.0, 0.20);
        if ($rateAdj > 0.0) {
            $reasons[] = "Taux d'intérêt augmente la charge";
        }
        $score += $rateAdj;

        if ($input->monthlyIncome < 1200.0) {
            $score += 0.10;
            $reasons[] = 'Revenu mensuel faible';
        }

        $probability = $this->clamp($score, 0.01, 0.99);
        $level = $probability < 0.33 ? RiskLevel::LOW : ($probability < 0.66 ? RiskLevel::MEDIUM : RiskLevel::HIGH);

        $explanation = implode('. ', $reasons);
        if ('' !== $explanation) {
            $explanation .= '.';
        }

        return new LoanRiskPrediction($probability, $level, $explanation);
    }

    private function clamp(float $v, float $min, float $max): float
    {
        return max($min, min($max, $v));
    }
}
