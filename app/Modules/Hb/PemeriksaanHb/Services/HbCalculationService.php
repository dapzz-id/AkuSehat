<?php

namespace App\Modules\Hb\PemeriksaanHb\Services;

use App\Modules\Hb\PemeriksaanHb\Enums\HbStatus;
use App\Modules\User\Enums\UserGender;

class HbCalculationService
{
    public function calculateStatus(float $hbValue, string $gender): array
    {
        $status = HbStatus::NORMAL;

        if (strtoupper($gender) === UserGender::PEREMPUAN->value) {
            if ($hbValue < 12) {
                $status = HbStatus::ANEMIA;
            } elseif ($hbValue > 15) {
                $status = HbStatus::TINGGI;
            }
        } else {
            if ($hbValue < 13) {
                $status = HbStatus::ANEMIA;
            } elseif ($hbValue > 17) {
                $status = HbStatus::TINGGI;
            }
        }

        return [
            'status' => $status->value,
            'pesan' => $status->defaultMessage(),
        ];
    }
}
