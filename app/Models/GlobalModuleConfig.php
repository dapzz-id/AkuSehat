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

    /**
     * Label kontekstual hak akses CRUD: Wewenang Tugas (Operasional/Siswa) vs Hak Akses Personal (Diri Sendiri).
     */
    public static function getModulePermissionLabels(): array
    {
        return [
            'haid_pita' => [
                'duty' => [
                    'r' => ['title' => 'Monitoring Rekap', 'desc' => 'Pantau siklus haid siswi & guru, peringatan pita UKS >7 hari'],
                    'u' => ['title' => 'Kelola Pita & Medis', 'desc' => 'Verifikasi pita UKS & hentikan siklus siswa karena kondisi medis'],
                    'c' => ['title' => 'Entri Siklus Anggota', 'desc' => 'Input pencatatan siklus atas nama anggota UKS'],
                    'd' => ['title' => 'Hapus Rekap', 'desc' => 'Hapus data catatan siklus anggota'],
                ],
                'personal' => [
                    'c' => ['title' => 'Mulai Siklus Pribadi', 'desc' => 'Mulai siklus haid diri sendiri di HP & ajukan pita UKS'],
                    'r' => ['title' => 'Kalender Haid Saya', 'desc' => 'Buka riwayat siklus & prediksi ovulasi pribadi'],
                    'u' => ['title' => 'Edit Gejala Pribadi', 'desc' => 'Catat keluhan PMS, kram, dan durasi mandiri'],
                    'd' => ['title' => 'Hapus Log Pribadi', 'desc' => 'Hapus catatan siklus haid pribadi'],
                ],
            ],
            'olahraga' => [
                'duty' => [
                    'r' => ['title' => 'Monitoring Leaderboard', 'desc' => 'Pantau aktivitas & peringkat lari siswa se-sekolah'],
                    'u' => ['title' => 'Validasi Rekap', 'desc' => 'Koreksi & validasi catatan kebugaran anggota'],
                    'c' => ['title' => 'Penilaian Manual', 'desc' => 'Input penilaian lari/olahraga siswa secara manual'],
                    'd' => ['title' => 'Hapus Rekap Siswa', 'desc' => 'Hapus catatan lari anggota'],
                ],
                'personal' => [
                    'c' => ['title' => 'GPS Run Tracker Pribadi', 'desc' => 'Rekam lari, jalan, pace & kalori mandiri di HP'],
                    'r' => ['title' => 'Statistik Lari Sendiri', 'desc' => 'Lihat jarak tempuh, rute GPS & riwayat sendiri'],
                    'u' => ['title' => 'Edit Catatan Lari', 'desc' => 'Sesuaikan catatan latihan mandiri'],
                    'd' => ['title' => 'Hapus Riwayat Pribadi', 'desc' => 'Hapus sesi lari pribadi'],
                ],
            ],
            'kesehatan' => [
                'duty' => [
                    'c' => ['title' => 'Input Pemeriksaan Fisik', 'desc' => 'Catat TB, BB, IMT, tensi darah siswa di UKS'],
                    'r' => ['title' => 'Rekam Medis Sekolah', 'desc' => 'Monitoring riwayat fisik & klinis seluruh siswa'],
                    'u' => ['title' => 'Perbarui Data Medis', 'desc' => 'Koreksi hasil pemeriksaan fisik siswa'],
                    'd' => ['title' => 'Hapus Rekap Medis', 'desc' => 'Hapus data pemeriksaan siswa'],
                ],
                'personal' => [
                    'r' => ['title' => 'Riwayat Kesehatan Diri', 'desc' => 'Lihat status gizi IMT & tensi diri sendiri'],
                    'c' => ['title' => 'Catat Vital Mandiri', 'desc' => 'Input tanda vital mandiri jika diizinkan'],
                    'u' => ['title' => 'Edit Catatan Sendiri', 'desc' => 'Perbarui catatan fisik mandiri'],
                    'd' => ['title' => 'Hapus Catatan Sendiri', 'desc' => 'Hapus log fisik mandiri'],
                ],
            ],
            'konsultasi' => [
                'duty' => [
                    'r' => ['title' => 'Inbox SafeSpace Siswa', 'desc' => 'Baca pengajuan konseling & pesan privat siswa'],
                    'u' => ['title' => 'Bimbing Konseling', 'desc' => 'Respons pesan, dampingi & chat sesi SafeSpace siswa'],
                    'c' => ['title' => 'Buka Sesi Terjadwal', 'desc' => 'Inisiasi sesi konseling terjadwal untuk siswa'],
                    'd' => ['title' => 'Tutup / Arsipkan Sesi', 'desc' => 'Selesaikan atau arsipkan sesi bimbingan siswa'],
                ],
                'personal' => [
                    'c' => ['title' => 'Booking SafeSpace', 'desc' => 'Buat pengajuan konseling untuk diri sendiri'],
                    'r' => ['title' => 'Chat Konseling Pribadi', 'desc' => 'Buka riwayat obrolan bimbingan diri sendiri'],
                    'u' => ['title' => 'Ubah Janji Temu', 'desc' => 'Sesuaikan jadwal konseling pribadi'],
                    'd' => ['title' => 'Batalkan Pengajuan', 'desc' => 'Batalkan sesi konseling diri sendiri'],
                ],
            ],
            'vitalitas' => [
                'duty' => [
                    'r' => ['title' => 'Monitoring Hidrasi Siswa', 'desc' => 'Pantau kepatuhan minum & jam tidur anggota'],
                    'u' => ['title' => 'Evaluasi Gaya Hidup', 'desc' => 'Beri catatan evaluasi hidrasi anggota'],
                    'c' => ['title' => 'Input Log Anggota', 'desc' => 'Entri rekap vitalitas atas nama siswa'],
                    'd' => ['title' => 'Hapus Log Anggota', 'desc' => 'Hapus catatan hidrasi siswa'],
                ],
                'personal' => [
                    'c' => ['title' => 'Catat Minum & Tidur', 'desc' => 'Tombol cepat +250ml air & log durasi tidur di HP'],
                    'r' => ['title' => 'Statistik Vitalitas Saya', 'desc' => 'Lihat capaian target 2000ml & evaluasi mood'],
                    'u' => ['title' => 'Ubah Target Mandiri', 'desc' => 'Sesuaikan target air minum & jam tidur harian'],
                    'd' => ['title' => 'Hapus Log Harian', 'desc' => 'Hapus data hidrasi hari ini'],
                ],
            ],
            'hemoglobin' => [
                'duty' => [
                    'c' => ['title' => 'Input Skrining HB', 'desc' => 'Catat kadar HB, deteksi anemia & sebar TTD siswa'],
                    'r' => ['title' => 'Rekap Anemia Institusi', 'desc' => 'Monitoring status anemia siswi & guru perempuan'],
                    'u' => ['title' => 'Perbarui Data HB', 'desc' => 'Koreksi kadar HB & status minum TTD siswa'],
                    'd' => ['title' => 'Hapus Rekap HB', 'desc' => 'Hapus riwayat skrining HB anggota'],
                ],
                'personal' => [
                    'r' => ['title' => 'Lihat Kadar HB Saya', 'desc' => 'Cek status anemia & riwayat HB diri sendiri'],
                    'c' => ['title' => 'Konfirmasi Minum TTD', 'desc' => 'Catat konsumsi Tablet Tambah Darah di HP'],
                    'u' => ['title' => 'Edit Log Mandiri', 'desc' => 'Perbarui catatan mandiri konsumsi TTD'],
                    'd' => ['title' => 'Hapus Log Mandiri', 'desc' => 'Hapus catatan minum TTD pribadi'],
                ],
            ],
            'nutrisi' => [
                'duty' => [
                    'r' => ['title' => 'Pantau Gizi Anggota', 'desc' => 'Monitoring pola makan & asupan kalori siswa'],
                    'u' => ['title' => 'Beri Saran Nutrisi', 'desc' => 'Koreksi atau beri anjuran menu makanan sehat'],
                    'c' => ['title' => 'Input Panduan Menu', 'desc' => 'Buat rekomendasi menu sehat institusi'],
                    'd' => ['title' => 'Hapus Panduan Menu', 'desc' => 'Hapus menu rekomendasi'],
                ],
                'personal' => [
                    'c' => ['title' => 'Catat Menu Makanan', 'desc' => 'Input makanan harian, hitung kalori & makronutrisi'],
                    'r' => ['title' => 'Riwayat Makan Sendiri', 'desc' => 'Lihat grafik kalori harian & nutrisi pribadi'],
                    'u' => ['title' => 'Edit Catatan Makan', 'desc' => 'Perbarui gramasi / menu yang dicatat'],
                    'd' => ['title' => 'Hapus Catatan Makan', 'desc' => 'Hapus entri menu makanan pribadi'],
                ],
            ],
            'burnout' => [
                'duty' => [
                    'r' => ['title' => 'Pantau Stres Kelas/Siswa', 'desc' => 'Monitoring agregat tingkat stres & risiko burnout'],
                    'u' => ['title' => 'Tindak Lanjut Pemulihan', 'desc' => 'Berikan catatan intervensi pemulihan beban belajar'],
                    'c' => ['title' => 'Jadwalkan Kuesioner', 'desc' => 'Rilis evaluasi stres serentak untuk divisi/kelas'],
                    'd' => ['title' => 'Hapus Data Evaluasi', 'desc' => 'Hapus rekap evaluasi stres'],
                ],
                'personal' => [
                    'c' => ['title' => 'Skrining Stres Mandiri', 'desc' => 'Isi kuesioner evaluasi beban & kejenuhan di HP'],
                    'r' => ['title' => 'Lihat Tingkat Stres Saya', 'desc' => 'Buka hasil analisis burnout & tips relaksasi diri'],
                    'u' => ['title' => 'Ubah Jawaban Skrining', 'desc' => 'Perbarui respons kuesioner mandiri'],
                    'd' => ['title' => 'Hapus Riwayat Skrining', 'desc' => 'Hapus riwayat evaluasi stres diri sendiri'],
                ],
            ],
            'ergonomi' => [
                'duty' => [
                    'r' => ['title' => 'Monitoring Duduk Anggota', 'desc' => 'Pantau kepatuhan peregangan postur institusi'],
                    'u' => ['title' => 'Atur Standar Ergonomi', 'desc' => 'Sesuaikan rekomendasi interval istirahat'],
                    'c' => ['title' => 'Buat Panduan Gerak', 'desc' => 'Input materi peregangan postur tubuh'],
                    'd' => ['title' => 'Hapus Panduan', 'desc' => 'Hapus materi gerakan'],
                ],
                'personal' => [
                    'c' => ['title' => 'Pengingat Postur di HP', 'desc' => 'Aktifkan alarm jeda peregangan duduk lama'],
                    'r' => ['title' => 'Riwayat Duduk Sendiri', 'desc' => 'Lihat durasi duduk vs waktu aktif harian'],
                    'u' => ['title' => 'Ubah Interval Pengingat', 'desc' => 'Sesuaikan timer pengingat berdiri di HP'],
                    'd' => ['title' => 'Nonaktifkan Pengingat', 'desc' => 'Matikan alarm postur diri sendiri'],
                ],
            ],
            'vaksinasi' => [
                'duty' => [
                    'c' => ['title' => 'Input Imunisasi Siswa', 'desc' => 'Verifikasi & catat vaksin HPV/Hepatitis/TTD anggota'],
                    'r' => ['title' => 'Monitoring Buku Vaksin', 'desc' => 'Cek cakupan imunisasi seluruh siswa & staf'],
                    'u' => ['title' => 'Perbarui Jadwal Vaksin', 'desc' => 'Update jadwal dosis lanjutan vaksin siswa'],
                    'd' => ['title' => 'Hapus Catatan Vaksin', 'desc' => 'Hapus data imunisasi anggota'],
                ],
                'personal' => [
                    'r' => ['title' => 'Buku Vaksin Digital Saya', 'desc' => 'Akses sertifikat & jadwal vaksinasi diri sendiri'],
                    'c' => ['title' => 'Unggah Bukti Vaksin', 'desc' => 'Kirim foto bukti imunisasi mandiri'],
                    'u' => ['title' => 'Update Alergi Mandiri', 'desc' => 'Catat alergi obat pribadi di akun sendiri'],
                    'd' => ['title' => 'Hapus Bukti Unggahan', 'desc' => 'Hapus sertifikat yang diunggah mandiri'],
                ],
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
