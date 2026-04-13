<?php

namespace App\Service\Risk;

/**
 * Données d'entrée pour l'évaluation du risque de crédit.
 */
final class LoanRiskInput
{
    public function __construct(
        public readonly float $creditAmount,
        public readonly float $monthlyIncome,
        public readonly float $annualInterestRate,
    ) {
    }
}
