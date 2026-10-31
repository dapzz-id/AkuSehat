<?php

namespace App\Modules\Billing\Subscription\Enums;

enum PaymentMethod: string
{
    case GOOGLE_PAY = 'google_pay';
    case GOOGLE_PLAY = 'google_play';
    case MANUAL_TRANSFER = 'manual_transfer';

    public function label(): string
    {
        return match($this) {
            self::GOOGLE_PAY => 'Google Pay',
            self::GOOGLE_PLAY => 'Google Play In-App',
            self::MANUAL_TRANSFER => 'Transfer Manual',
        };
    }
}
