<?php

namespace App\Modules\Billing\Subscription\Enums;

enum PaymentMethod: string
{
    case GOOGLE_PAY = 'google_pay';
    case GOOGLE_PLAY = 'google_play';
    case GOOGLE_PLAY_INAPP = 'google_play_inapp';
    case MANUAL_TRANSFER = 'manual_transfer';
    case MIDTRANS = 'midtrans';

    public function label(): string
    {
        return match($this) {
            self::GOOGLE_PAY => 'Google Pay',
            self::GOOGLE_PLAY, self::GOOGLE_PLAY_INAPP => 'Google Play In-App',
            self::MANUAL_TRANSFER => 'Transfer Manual BCA',
            self::MIDTRANS => 'Midtrans (Snap Gateway)',
        };
    }
}
