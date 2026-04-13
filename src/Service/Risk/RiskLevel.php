<?php

namespace App\Service\Risk;

enum RiskLevel: string
{
    case LOW = 'LOW';
    case MEDIUM = 'MEDIUM';
    case HIGH = 'HIGH';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Faible risque',
            self::MEDIUM => 'Risque modéré',
            self::HIGH => 'Risque élevé',
        };
    }
}
