<?php

namespace App\Modules\Kesehatan\Ergonomics\Enums;

enum ErgonomicsScoreCategory: string
{
    case PRIMA = 'prima';
    case WASPADA = 'waspada';
    case BERISIKO = 'berisiko';

    public function label(): string
    {
        return match($this) {
            self::PRIMA => 'Prima / Baik (≥75)',
            self::WASPADA => 'Waspada (50-74)',
            self::BERISIKO => 'Berisiko (<50)',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PRIMA => '#10b981',
            self::WASPADA => '#f59e0b',
            self::BERISIKO => '#ef4444',
        };
    }

    public static function fromScore(float $score): self
    {
        if ($score >= 75) {
            return self::PRIMA;
        }
        if ($score >= 50) {
            return self::WASPADA;
        }
        return self::BERISIKO;
    }
}
