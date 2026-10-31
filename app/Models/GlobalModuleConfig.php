<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Modules\SuperAdmin\ModulePermission\Enums\ModuleAccessStatus;

class GlobalModuleConfig extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'global_module_configs';

    protected $fillable = [
        'module_key',
        'name',
        'status', // 'active', 'maintenance', 'invisible', 'tester_only'
        'maintenance_message',
        'tester_user_ids',
        'updated_by',
    ];

    protected $casts = [
        'tester_user_ids' => 'array',
    ];

    /**
     * Definisi lengkap 10 modul sistem AkuSehat.
     */
    public static function getModuleCatalog(): array
    {
        return [
            'haid_pita' => [
                'name' => 'Siklus Menstruasi & Kesehatan Reproduksi Wanita',
                'description' => 'Kalender siklus menstruasi, prediksi ovulasi/masa subur, log nyeri PMS & dismenore, fasilitas izin cuti haid atau inventaris pita pembalut.',
                'icon' => 'fas fa-calendar-heart',
                'badge' => "Women's Health",
                'color' => 'pink',
            ],
            'olahraga' => [
                'name' => 'Pelacakan Aktivitas Fisik & Run Tracker',
                'description' => 'Pelacakan GPS lari, jalan santai, pace, estimasi kalori terbakar, pedometer langkah, dan papan peringkat kebugaran institusi.',
                'icon' => 'fas fa-person-running',
                'badge' => 'Universal',
                'color' => 'emerald',
            ],
            'vitalitas' => [
                'name' => 'Vitalitas, Kualitas Tidur & Hidrasi Harian',
                'description' => 'Pencatatan asupan air minum (+250ml target 2.000ml), durasi & kualitas tidur malam, evaluasi mood harian, dan biomarker vital mandiri.',
                'icon' => 'fas fa-heart-pulse',
                'badge' => 'Lifestyle',
                'color' => 'sky',
            ],
            'kesehatan' => [
                'name' => 'Rekam Medis Fisik & Tanda Vital Klinis',
                'description' => 'Pencatatan berkala tinggi badan, berat badan, IMT, tekanan darah, dan riwayat klinis berkala oleh Health Monitor.',
                'icon' => 'fas fa-notes-medical',
                'badge' => 'Klinis',
                'color' => 'teal',
            ],
            'hemoglobin' => [
                'name' => 'Skrining Hemoglobin (HB) & Pencegahan Anemia',
                'description' => 'Pemantauan kadar HB, skrining anemia berkala, deteksi dini kelelahan kronis, dan verifikasi pemberian Tablet Tambah Darah (TTD).',
                'icon' => 'fas fa-droplet',
                'badge' => 'Medis',
                'color' => 'rose',
            ],
            'konsultasi' => [
                'name' => 'Konseling Mental & SafeSpace Konfidensial',
                'description' => 'Layanan konseling privat 1-on-1 bersama Health Consultant (Guru BK / HR People Care / Psikolog EAP) dengan jaminan privasi.',
                'icon' => 'fas fa-comments',
                'badge' => 'Mental Health',
                'color' => 'indigo',
            ],
            'nutrisi' => [
                'name' => 'Nutrisi & Pemantau Pola Makan Sehat',
                'description' => 'Pencatatan menu makanan, estimasi kalori harian, keseimbangan makronutrisi, serta panduan pola makan sehat pencegah obesitas & anemia.',
                'icon' => 'fas fa-apple-whole',
                'badge' => 'Gizi Seimbang',
                'color' => 'amber',
            ],
            'burnout' => [
                'name' => 'Skrining Stres, Burnout & Well-being',
                'description' => 'Kuesioner berkala evaluasi stres kerja/akademik, deteksi dini kelelahan mental (burnout), dan rekomendasi pemulihan energi.',
                'icon' => 'fas fa-brain',
                'badge' => 'Kesehatan Kerja',
                'color' => 'purple',
            ],
            'ergonomi' => [
                'name' => 'Ergonomi Kerja & Peringatan Sedentary (Duduk Lama)',
                'description' => 'Pengingat peregangan postur (active stretch break), pemantauan durasi duduk terus-menerus, cegah nyeri punggung & leher.',
                'icon' => 'fas fa-chair',
                'badge' => 'Occupational',
                'color' => 'cyan',
            ],
            'vaksinasi' => [
                'name' => 'Paspor Vaksinasi & Imunisasi Digital',
                'description' => 'Buku vaksin digital (HPV pencegah kanker serviks, Hepatitis B, Influenza, Tetanus), riwayat alergi obat, dan pengingat jadwal vaksin.',
                'icon' => 'fas fa-syringe',
                'badge' => 'Preventif',
                'color' => 'blue',
            ],
        ];
    }

    protected static ?array $allConfigsCache = null;

    protected static function booted(): void
    {
        static::saved(function () {
            self::clearConfigCache();
        });

        static::deleted(function () {
            self::clearConfigCache();
        });
    }

    /**
     * Dapatkan semua konfigurasi modul dari cache.
     */
    public static function getAllConfigs(): array
    {
        if (self::$allConfigsCache !== null) {
            return self::$allConfigsCache;
        }

        self::$allConfigsCache = \Illuminate\Support\Facades\Cache::remember('global_module_configs_all', 300, function () {
            return self::all()->keyBy('module_key')->all();
        });

        return self::$allConfigsCache ?? [];
    }

    /**
     * Dapatkan konfigurasi untuk modul tertentu (cached).
     */
    public static function getConfig(string $moduleKey): ?self
    {
        $all = self::getAllConfigs();
        return $all[$moduleKey] ?? null;
    }

    /**
     * Bersihkan cache internal konfigurasi modul.
     */
    public static function clearConfigCache(): void
    {
        self::$allConfigsCache = null;
        \Illuminate\Support\Facades\Cache::forget('global_module_configs_all');
    }

    /**
     * Alias untuk membersihkan cache konfigurasi modul.
     */
    public static function clearCache(): void
    {
        self::clearConfigCache();
    }

    /**
     * Cek apakah modul invisible (disembunyikan secara global oleh SuperAdmin).
     */
    public static function isModuleInvisible(string $moduleKey): bool
    {
        $config = self::getConfig($moduleKey);
        return $config && $config->status === ModuleAccessStatus::INVISIBLE->value;
    }

    /**
     * Cek apakah modul sedang dalam perbaikan (maintenance) oleh SuperAdmin.
     */
    public static function isModuleInMaintenance(string $moduleKey): bool
    {
        $config = self::getConfig($moduleKey);
        return $config && $config->status === ModuleAccessStatus::MAINTENANCE->value;
    }

    /**
     * Cek apakah modul berstatus tester only oleh SuperAdmin.
     */
    public static function isModuleTesterOnly(string $moduleKey): bool
    {
        $config = self::getConfig($moduleKey);
        return $config && $config->status === ModuleAccessStatus::TESTER_ONLY->value;
    }

    /**
     * Evaluasi apakah pengguna tertentu diizinkan mengakses modul ini berdasarkan Master Feature Flag SuperAdmin.
     */
    public static function isUserAllowedToAccess(string $moduleKey, ?User $user): bool
    {
        // SuperAdmin SELALU memiliki akses penuh (Master Bypass)
        if ($user && $user->isSuperAdmin()) {
            return true;
        }

        $config = self::getConfig($moduleKey);
        if (!$config) {
            return true; // Default aktif normal
        }

        // 1. Jika Invisible: tertutup total untuk semua pengguna non-superadmin
        if ($config->status === ModuleAccessStatus::INVISIBLE->value) {
            return false;
        }

        // 2. Jika Tester Only: hanya tester yang diizinkan
        if ($config->status === ModuleAccessStatus::TESTER_ONLY->value) {
            if (!$user) {
                return false;
            }
            if ($user->isTester()) {
                return true;
            }
            if (is_array($config->tester_user_ids) && in_array($user->id, $config->tester_user_ids)) {
                return true;
            }
            return false;
        }

        // 3. Maintenance: modul tetap visible/dapat dibuka, namun menampilkan pesan maintenance
        // (Diizinkan lolos di level visibilitas)
        return true;
    }

    /**
     * Dapatkan pesan pemeliharaan khusus modul.
     */
    public static function getMaintenanceNotice(string $moduleKey): string
    {
        $config = self::getConfig($moduleKey);
        if ($config && !empty($config->maintenance_message)) {
            return $config->maintenance_message;
        }
        $catalog = self::getModuleCatalog();
        $name = $catalog[$moduleKey]['name'] ?? 'Fitur ini';
        return "{$name} saat ini sedang dalam peningkatan performa atau perbaikan teknis oleh Super Administrator. Fitur akan segera aktif kembali.";
    }
}
