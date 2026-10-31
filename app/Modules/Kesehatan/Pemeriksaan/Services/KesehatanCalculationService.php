<?php

namespace App\Modules\Kesehatan\Pemeriksaan\Services;

use App\Modules\Kesehatan\Pemeriksaan\Enums\BmiStatus;
use App\Modules\Kesehatan\Pemeriksaan\Enums\BloodPressureStatus;

class KesehatanCalculationService
{
    public function calculateImt(float $tb, float $bb): array
    {
        $tbMeter = $tb / 100;
        $imt = round($bb / ($tbMeter * $tbMeter), 2);

        if ($imt < 18.5) {
            $status = BmiStatus::UNDERWEIGHT;
        } elseif ($imt < 25) {
            $status = BmiStatus::NORMAL;
        } elseif ($imt < 30) {
            $status = BmiStatus::OVERWEIGHT;
        } elseif ($imt < 35) {
            $status = BmiStatus::OBESITAS_1;
        } elseif ($imt < 40) {
            $status = BmiStatus::OBESITAS_2;
        } else {
            $status = BmiStatus::OBESITAS_3;
        }

        $pesan = match ($status) {
            BmiStatus::UNDERWEIGHT, BmiStatus::KURUS => 'Berat badan kurang. Tingkatkan asupan nutrisi secara seimbang.',
            BmiStatus::OVERWEIGHT, BmiStatus::GEMUK => 'Berat badan berlebih. Disarankan mengatur pola makan dan olahraga rutin.',
            BmiStatus::OBESITAS, BmiStatus::OBESITAS_1, BmiStatus::OBESITAS_2, BmiStatus::OBESITAS_3 => 'Obesitas. Disarankan konsultasi dengan ahli gizi untuk penanganan lebih lanjut.',
            default => 'Berat badan normal. Pertahankan pola hidup sehat.',
        };

        return [
            'imt' => $imt,
            'status' => $status->value,
            'pesan' => $pesan,
        ];
    }

    public function determineBloodPressureStatus(?float $sistol, ?float $diastol): array
    {
        if (is_null($sistol) || is_null($diastol)) {
            return [
                'status' => null,
                'pesan' => null,
            ];
        }

        $sistolVal = intval($sistol);
        $diastolVal = intval($diastol);

        if ($sistolVal < 90 || $diastolVal < 60) {
            $status = BloodPressureStatus::HIPOTENSI;
        } elseif ($sistolVal < 120 && $diastolVal < 80) {
            $status = BloodPressureStatus::NORMAL;
        } elseif ($sistolVal >= 120 && $sistolVal < 130 && $diastolVal < 80) {
            $status = BloodPressureStatus::ELEVASI;
        } elseif (($sistolVal >= 130 && $sistolVal < 140) || ($diastolVal >= 80 && $diastolVal < 90)) {
            $status = BloodPressureStatus::HIPERTENSI_TAHAP_1;
        } elseif (($sistolVal >= 140 && $sistolVal < 180) || ($diastolVal >= 90 && $diastolVal < 120)) {
            $status = BloodPressureStatus::HIPERTENSI_TAHAP_2;
        } else {
            $status = BloodPressureStatus::KRISIS;
        }

        $pesan = match ($status) {
            BloodPressureStatus::HIPOTENSI => 'Tekanan darah rendah. Pastikan hidrasi cukup dan istirahat memadai.',
            BloodPressureStatus::ELEVASI, BloodPressureStatus::PRAHIPERTENSI => 'Tekanan darah sedikit meningkat. Harap perhatikan gaya hidup sehat.',
            BloodPressureStatus::HIPERTENSI_1, BloodPressureStatus::HIPERTENSI_TAHAP_1 => 'Hipertensi tahap 1. Mulai lakukan pengurangan garam dan olahraga teratur.',
            BloodPressureStatus::HIPERTENSI_2, BloodPressureStatus::HIPERTENSI_TAHAP_2 => 'Hipertensi tahap 2. Perlu konsultasi lebih lanjut dengan tenaga medis.',
            BloodPressureStatus::KRISIS => 'Kondisi darurat! Segera hubungi tenaga medis.',
            default => 'Tekanan darah normal. Pertahankan gaya hidup sehat.',
        };

        return [
            'status' => $status->value,
            'pesan' => $pesan,
        ];
    }
}
