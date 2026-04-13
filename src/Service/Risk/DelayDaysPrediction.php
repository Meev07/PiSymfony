<?php

namespace App\Service\Risk;

/**
 * Résultat de la prédiction du nombre de jours de retard.
 */
final class DelayDaysPrediction
{
    public function __construct(
        public readonly int $predictedDays,
        public readonly float $riskScore,
        public readonly RiskLevel $level,
        public readonly string $explanation,
    ) {
    }
}
