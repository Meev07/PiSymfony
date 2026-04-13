<?php

namespace App\Service;

final class PaymentDelayPredictionResult
{
    public function __construct(
        public readonly int $totalPayments,
        public readonly int $completedPayments,
        public readonly int $totalDelays,
        public readonly int $activeDelays,
        public readonly ?float $averageDelayDays,
        public readonly ?\DateTimeImmutable $lastPaymentDate,
        public readonly ?\DateTimeImmutable $lastDelayDate,
        public readonly float $latePaymentRatio,
        public readonly float $riskProbability,
        public readonly string $riskLevel,
        public readonly int $predictedDelayDays,
        public readonly string $explanation,
    ) {
    }
}
