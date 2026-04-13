<?php

namespace App\Service\Risk;

/**
 * Résultat de la prédiction du risque de crédit.
 */
final class LoanRiskPrediction
{
    public function __construct(
        public readonly float $probability,
        public readonly RiskLevel $level,
        public readonly string $explanation,
    ) {
    }
}
