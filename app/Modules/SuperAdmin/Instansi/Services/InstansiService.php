<?php

namespace App\Modules\SuperAdmin\Instansi\Services;

use App\Models\Instansi;
use App\Models\LicenseKey;
use App\Models\User;
use App\Modules\User\Enums\UserGender;
use App\Modules\User\Enums\UserRole;
use App\Modules\SuperAdmin\Instansi\Repositories\InstansiRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class InstansiService
{
    public function __construct(
        protected InstansiRepository $instansiRepository
    ) {}

    public function createWithStarterLicenseAndAdmin(array $data): Instansi
    {
        if (empty($data['npsn'])) {
            $prefix = match ($data['jenis_instansi'] ?? 'Sekolah') {
                'Perusahaan' => 'CORP',
                'Universitas' => 'UNIV',
                'Pemerintah' => 'GOV',
                'Klinik' => 'MED',
                default => 'INST'
            };
            $data['npsn'] = $prefix . '-' . strtoupper(Str::random(6));
        }

        if (empty($data['jenjang'])) {
            $data['jenjang'] = ($data['jenis_instansi'] ?? 'Sekolah') === 'Sekolah'
                ? 'Umum'
                : (($data['jenis_instansi'] ?? '') === 'Universitas' ? 'Perguruan Tinggi' : 'Non-Pendidikan');
        }

        return DB::transaction(function () use ($data) {
            $instansi = $this->instansiRepository->create($data);

            $starterLicense = LicenseKey::create([
                'instansi_id' => $instansi->id,
                'key' => LicenseKey::generateKey(),
                'kuota_pengguna' => 500,
                'status' => LicenseKey::STATUS_ACTIVE,
                'tanggal_berakhir' => now()->addMonth(),
            ]);

            $instansi->update(['license_id_active' => $starterLicense->id]);

            User::create([
                'nama' => 'Admin ' . $instansi->nama_instansi,
                'username' => strtolower($instansi->npsn),
                'password' => bcrypt('password123'),
                'level' => UserRole::ADMIN_INSTANSI->value,
                'instansi_id' => $instansi->id,
                'jk' => UserGender::LAKI_LAKI->value,
                'tgl' => now(),
                'nomor_induk' => null,
                'id_divisi' => null,
            ]);

            return $instansi;
        });
    }

    public function fetchProvinces(): array
    {
        return Cache::remember('wilayah_provinces', 86400, function () {
            try {
                $response = Http::timeout(5)->get('https://api.datawilayah.com/api/provinsi.json');
                if ($response->failed()) {
                    return [];
                }
                $json = $response->json();
                return $json['data'] ?? [];
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public function fetchRegencies(string|int $provinceId): array
    {
        if (!preg_match('/^[0-9]+$/', (string) $provinceId)) {
            return [];
        }

        return Cache::remember("wilayah_regencies_{$provinceId}", 86400, function () use ($provinceId) {
            try {
                $response = Http::timeout(5)->get("https://api.datawilayah.com/api/kabupaten_kota/{$provinceId}.json");
                if ($response->failed()) {
                    return [];
                }
                $json = $response->json();
                return $json['data'] ?? [];
            } catch (\Throwable $e) {
                return [];
            }
        });
    }
}
