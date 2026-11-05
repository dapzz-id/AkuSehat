<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SoftwareApp;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwarePlatform;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwareStatus;
use Carbon\Carbon;

class AndroidAppReleaseSeeder extends Seeder
{
    /**
     * Seed data rilis aplikasi Android versi saat ini ke tabel software_apps.
     * Idempotent: Menggunakan updateOrCreate sehingga aman dijalankan berulang kali.
     */
    public function run(): void
    {
        $version = '1.0.6';
        $platform = SoftwarePlatform::ANDROID->value;

        $changelog = <<<MARKDOWN
### Apa yang Baru di Versi 1.0.6 (Build 7):
* **Health Consultant Portal**:
  - Perbaikan visual metrik status IMT (Obesitas, Kurus, Normal) dan daftar anggota per divisi.
  - Perbaikan tombol Detail pada divisi agar data anggota dan riwayat rekam medis tampil stabil tanpa force close.
  - Perbaikan pemuatan profil konselor institusi dan sinkronisasi sesi pengguna.
* **Tampilan & Antarmuka**:
  - Pembaruan tampilan pembuka (Splash Screen): Menghapus prefix PT pada identitas pengembang.
* **Google Play Compliance & Performance**:
  - Pengoptimalan kode DEX melalui R8 Code Shrinking dan Proguard Optimization sehingga ukuran file lebih ringkas dan memori lebih efisien.
  - Penerapan tata letak layar penuh edge-to-edge untuk kepatuhan Android 15.
  - Integrasi native Google Sign-In dengan verifikasi keamanan Google Play Integrity.
* **Health Monitor & Operasional UKS**:
  - Sinkronisasi payload penolakan permohonan peminjaman pita UKS agar alasan penolakan tercatat akurat.
MARKDOWN;

        $release = SoftwareApp::updateOrCreate(
            [
                'platform' => $platform,
                'version' => $version,
            ],
            [
                'name' => 'Aku Sehat Android',
                'min_version' => '1.0.0',
                'description' => 'Aplikasi pemantauan kesehatan holistik, operasional UKS, konseling SafeSpace, dan pencatatan vitalitas institusi.',
                'changelog' => $changelog,
                'developer' => 'Neura Cakrawira Solusi',
                'link' => 'https://play.google.com/store/apps/details?id=neuracakrawira.asia.akusehat',
                'status' => SoftwareStatus::ACTIVE->value,
                'icon' => null,
                'is_mandatory' => false,
                'release_date' => Carbon::now()->toDateString(),
            ]
        );

        if ($this->command) {
            $this->command->info("✅ Versi Android {$release->version} (Build 7) berhasil di-seed ke software_apps!");
            $this->command->table(
                ['Field', 'Nilai'],
                [
                    ['Platform', $release->platform],
                    ['Nama', $release->name],
                    ['Versi', $release->version],
                    ['Min Versi', $release->min_version],
                    ['Developer', $release->developer],
                    ['Status', $release->status],
                    ['Wajib Update (Mandatory)', $release->is_mandatory ? 'Ya' : 'Tidak'],
                    ['Tanggal Rilis', $release->release_date?->format('Y-m-d')],
                    ['Link Unduhan', $release->link],
                ]
            );
        }
    }
}
