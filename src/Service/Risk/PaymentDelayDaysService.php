<?php

namespace App\Service\Risk;

/**
 * Service de prédiction du nombre de jours de retard estimé.
 * Port PHP de PaymentDelayDaysService + HeuristicDelayDaysRiskModel (org.example.risk).
 *
 * Mapping score → jours:
 *  LOW    (< 0.33) → ~ 2..5  jours
 *  MEDIUM (< 0.66) → ~ 6..15 jours
 *  HIGH   (>= 0.66)→ ~16..60 jours
 */
final class PaymentDelayDaysService
{
    public function predictDays(float $creditAmount, float $monthlyIncome, float $annualInterestRate): DelayDaysPrediction
    {
        $input = new LoanRiskInput($creditAmount, $monthlyIncome, $annualInterestRate);

        $debtToIncomeAnnual = $input->creditAmount / ($input->monthlyIncome * 12.0);
        $score = 0.0;
        $reasons = [];

        if ($debtToIncomeAnnual <= 1.0) {
            $score += 0.10;
            $reasons[] = 'Montant de crédit modéré vs revenu';
        } elseif ($debtToIncomeAnnual <= 2.5) {
            $score += 0.40;
            $reasons[] = 'Montant de crédit significatif vs revenu';
        } else {
            $score += 0.75;
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

        $riskScore = $this->clamp($score, 0.01, 0.99);
        $level = $riskScore < 0.33 ? RiskLevel::LOW : ($riskScore < 0.66 ? RiskLevel::MEDIUM : RiskLevel::HIGH);

        $predictedDays = match ($level) {
            RiskLevel::LOW    => (int) round(2 + ($riskScore / 0.33) * 3),
            RiskLevel::MEDIUM => (int) round(6 + (($riskScore - 0.33) / 0.33) * 9),
            RiskLevel::HIGH   => (int) round(16 + (($riskScore - 0.66) / 0.34) * 44),
        };
        $predictedDays = max(0, min(60, $predictedDays));

        $explanation = implode('. ', $reasons);
        if ('' !== $explanation) {
            $explanation .= '.';
        }

        return new DelayDaysPrediction($predictedDays, $riskScore, $level, $explanation);
    }

    private function clamp(float $v, float $min, float $max): float
    {
        return max($min, min($max, $v));
    }
}
