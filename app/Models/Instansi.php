<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Scopes\TenantScope;
use App\Modules\User\Enums\UserRole;
use App\Models\Divisi;
use App\Models\User;

class Instansi extends Model
{
    use HasUuids;

    protected $table = 'instansi';

    protected $fillable = [
        'nama_instansi',
        'jenis_instansi',
        'active_modules',
        'module_permissions',
        'custom_role_labels',
        'alamat',
        'email',
        'npsn',
        'invite_code',
        'jenjang',
        'telepon',
        'license_id_active',
        'website',
        'kota',
        'provinsi',
        'kode_pos'
    ];

    protected $appends = [
        'tipe_sebutan',
        'sebutan_spesifik',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($instansi) {
            if (empty($instansi->invite_code)) {
                $instansi->invite_code = self::generateUniqueInviteCode($instansi->nama_instansi ?? 'INST');
            }
        });
    }

    public static function generateUniqueInviteCode($name = 'INST')
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name ?: 'INST'), 0, 4));
        if (strlen($prefix) < 3) {
            $prefix = 'INST';
        }
        do {
            $code = $prefix . '-' . rand(1000, 9999);
        } while (self::where('invite_code', $code)->exists());

        return $code;
    }

    protected $casts = [
        'active_modules' => 'array',
        'module_permissions' => 'array',
        'custom_role_labels' => 'array',
    ];

    public function isModuleActive($moduleKey)
    {
        // 0. SuperAdmin Global Override: Jika fitur disembunyikan/dinonaktifkan oleh SuperAdmin
        if (\App\Models\GlobalModuleConfig::isModuleInvisible($moduleKey)) {
            return false;
        }

        if (is_null($this->active_modules)) {
            return true; // Default all active
        }
        return in_array($moduleKey, $this->active_modules);
    }

    public static function getDefaultRolePresets($jenis = 'Sekolah')
    {
        return match ($jenis) {
            'Universitas' => [
                'Admin' => 'Admin KSR PMI / Satgas Kampus',
                'Member' => 'Mahasiswa / Mahasiswi',
                'Health Monitor' => 'Instruktur Olahraga / Tim Klinik Kampus',
                'Health Consultant' => 'Dosen Pembimbing / Konselor Kampus',
            ],
            'Perusahaan' => [
                'Admin' => 'Admin K3 / Koordinator HSE',
                'Member' => 'Karyawan / Staf',
                'Health Monitor' => 'Petugas HSE / Tim Medis Perusahaan',
                'Health Consultant' => 'HR People Care / Psikolog EAP',
            ],
            'Pemerintah' => [
                'Admin' => 'Admin Poliklinik / PIC Kesehatan ASN',
                'Member' => 'ASN / Pegawai',
                'Health Monitor' => 'Tim Medis / K3 Pegawai',
                'Health Consultant' => 'Tim Kepegawaian / Konselor ASN',
            ],
            'Klinik' => [
                'Admin' => 'Admin Operasional / Resepsionis Klinis',
                'Member' => 'Pasien / Klien',
                'Health Monitor' => 'Perawat / Asisten Medis',
                'Health Consultant' => 'Dokter / Konselor Medis',
            ],
            default => [
                'Admin' => 'Admin PMR / Pengurus UKS',
                'Member' => 'Siswa / Siswi',
                'Health Monitor' => 'Guru Penjaskes / Pembina UKS',
                'Health Consultant' => 'Guru BK / Konselor',
            ],
        };
    }

    public static function getDefaultModulePresets($jenis = 'Sekolah')
    {
        return match ($jenis) {
            'Perusahaan' => ['olahraga', 'vitalitas', 'kesehatan', 'hemoglobin', 'haid_pita', 'konsultasi', 'burnout', 'ergonomi'],
            'Universitas' => ['olahraga', 'vitalitas', 'kesehatan', 'hemoglobin', 'haid_pita', 'konsultasi', 'nutrisi', 'burnout', 'ergonomi'],
            'Pemerintah' => ['olahraga', 'vitalitas', 'kesehatan', 'hemoglobin', 'haid_pita', 'konsultasi', 'burnout', 'ergonomi'],
            'Klinik' => ['vitalitas', 'kesehatan', 'hemoglobin', 'haid_pita', 'konsultasi', 'nutrisi', 'vaksinasi'],
            default => ['olahraga', 'vitalitas', 'kesehatan', 'hemoglobin', 'haid_pita', 'konsultasi', 'nutrisi', 'vaksinasi'],
        };
    }

    public function getRoleLabel($level)
    {
        $custom = $this->custom_role_labels ?? [];
        if (!empty($custom[$level])) {
            return $custom[$level];
        }

        $presets = self::getDefaultRolePresets($this->jenis_instansi ?? 'Sekolah');
        return $presets[$level] ?? $level;
    }

    public function users()
    {
        return $this->hasMany(User::class, 'instansi_id');
    }

    public function licenseKey()
    {
        return $this->belongsTo(LicenseKey::class, 'license_id_active');
    }

    public function licenseKeys()
    {
        return $this->hasMany(LicenseKey::class, 'instansi_id');
    }

    public function divisi()
    {
        return $this->hasMany(Divisi::class, 'instansi_id');
    }

    public function getNamaSekolahAttribute()
    {
        return $this->nama_instansi;
    }

    public function getActiveLicenses()
    {
        return $this->licenseKeys()
            ->where('status', LicenseKey::STATUS_ACTIVE)
            ->whereDate('tanggal_berakhir', '>=', now()->toDateString())
            ->orderBy('tanggal_berakhir', 'asc')
            ->get();
    }

    public function getAllLicenses()
    {
        return $this->licenseKeys()
            ->orderByRaw("CASE WHEN status = 'active' AND (tanggal_berakhir IS NULL OR tanggal_berakhir >= CURRENT_DATE) THEN 0 ELSE 1 END")
            ->orderBy('tanggal_berakhir', 'desc')
            ->get();
    }

    public function getActiveLicense()
    {
        if ($this->license_id_active) {
            $active = $this->licenseKey()
                ->where('status', LicenseKey::STATUS_ACTIVE)
                ->whereDate('tanggal_berakhir', '>=', now()->toDateString())
                ->first();
            if ($active) {
                return $active;
            }
        }

        return $this->getActiveLicenses()->first();
    }

    /**
     * Menghitung total kuota aktif dari seluruh paket lisensi yang berjalan bersamaan (multi-bucket provider).
     * Jika tidak ada paket aktif sama sekali (kedaluwarsa), kembali ke kuota default minimal 1 (khusus Admin Instansi/Institusi).
     */
    public function getTotalActiveQuota(): int
    {
        $activeSum = (int) $this->licenseKeys()
            ->where('status', LicenseKey::STATUS_ACTIVE)
            ->whereDate('tanggal_berakhir', '>=', now()->toDateString())
            ->sum('kuota_pengguna');

        return $activeSum > 0 ? $activeSum : 1;
    }

    /**
     * Memeriksa apakah instansi memiliki setidaknya satu paket lisensi aktif yang belum kedaluwarsa.
     */
    public function hasActiveLicense(): bool
    {
        return $this->licenseKeys()
            ->where('status', LicenseKey::STATUS_ACTIVE)
            ->whereDate('tanggal_berakhir', '>=', now()->toDateString())
            ->exists();
    }

    /**
     * Cek apakah tipe instansi merupakan institusi pendidikan (Sekolah / Universitas)
     */
    public function isInstitusiPendidikan(): bool
    {
        $jenis = $this->jenis_instansi ?? 'Sekolah';
        if (in_array($jenis, ['Perusahaan', 'Pemerintah', 'Klinik'])) {
            return false;
        }
        return in_array($jenis, ['Sekolah', 'Universitas']) || !empty($this->npsn);
    }

    public function isSekolah(): bool
    {
        return ($this->jenis_instansi ?? 'Sekolah') === 'Sekolah';
    }

    public function isUniversitas(): bool
    {
        return ($this->jenis_instansi ?? '') === 'Universitas';
    }

    public function isPerusahaan(): bool
    {
        return ($this->jenis_instansi ?? '') === 'Perusahaan';
    }

    public function isPemerintah(): bool
    {
        return ($this->jenis_instansi ?? '') === 'Pemerintah';
    }

    public function isKlinik(): bool
    {
        return ($this->jenis_instansi ?? '') === 'Klinik';
    }

    /**
     * Label status untuk member siswa/mahasiswa yang dikeluarkan / tanpa divisi aktif
     */
    public function getLabelAlumniAttribute(): string
    {
        return match ($this->jenis_instansi ?? 'Sekolah') {
            'Universitas' => 'Alumni Kampus',
            'Perusahaan' => 'Non-Aktif',
            'Pemerintah' => 'Non-Aktif',
            'Klinik' => 'Selesai Rawat',
            default => 'Alumni',
        };
    }

    /**
     * Label badge untuk member tanpa divisi di tabel
     */
    public function getLabelBadgeAlumniAttribute(): string
    {
        return match ($this->jenis_instansi ?? 'Sekolah') {
            'Universitas' => 'Alumni Kampus',
            'Perusahaan' => 'Non-Aktif / Arsip',
            'Pemerintah' => 'Non-Aktif / Pindah',
            'Klinik' => 'Selesai Rawat / Nonaktif',
            default => 'Alumni / Arsip',
        };
    }

    /**
     * Subjudul halaman data pengguna yang sepenuhnya disesuaikan jenis institusi/instansi
     */
    public function getSubtitleDataUsersAttribute(): string
    {
        return match ($this->jenis_instansi ?? 'Sekolah') {
            'Universitas' => 'Manajemen data sivitas akademika (mahasiswa, dosen, staf), program studi/divisi, dan riwayat kesehatan',
            'Perusahaan' => 'Manajemen data karyawan & manajemen, divisi/departemen, dan riwayat kesehatan kerja',
            'Pemerintah' => 'Manajemen data aparatur sipil negara (ASN), unit kerja/divisi, dan riwayat kesehatan',
            'Klinik' => 'Manajemen data pasien, tim paramedis, dan rekam kesehatan',
            default => 'Manajemen data warga sekolah (siswa, dewan guru, staf), divisi/kelas, dan riwayat kesehatan',
        };
    }

    /**
     * Label tab Semua Pengguna yang disesuaikan
     */
    public function getLabelAllUsersTabAttribute(): string
    {
        return match ($this->jenis_instansi ?? 'Sekolah') {
            'Universitas' => 'Semua Sivitas Kampus',
            'Perusahaan' => 'Semua Karyawan & Staf',
            'Pemerintah' => 'Semua Pegawai & ASN',
            'Klinik' => 'Semua Pasien & Tim Faskes',
            default => 'Semua Warga Sekolah',
        };
    }

    /**
     * Label tab Petugas & Medis yang disesuaikan
     */
    public function getLabelPetugasTabAttribute(): string
    {
        return match ($this->jenis_instansi ?? 'Sekolah') {
            'Universitas' => 'Tim Medis & Satgas Kampus',
            'Perusahaan' => 'Tim Medis & HSE K3',
            'Pemerintah' => 'Tim Medis & K3 ASN',
            'Klinik' => 'Dokter & Paramedis Faskes',
            default => 'Petugas & Pengelola UKS',
        };
    }

    /**
     * Label tombol aksi keluarkan dari divisi default
     */
    public function getLabelTombolKeluarkanAttribute(): string
    {
        if ($this->jenis_instansi === 'Universitas') {
            return 'Keluarkan dari Divisi (Alumni Kampus)';
        }
        return $this->isInstitusiPendidikan() 
            ? 'Keluarkan dari Divisi (Alumni)' 
            : 'Keluarkan dari Divisi (Non-Aktifkan)';
    }

    /**
     * Ikon untuk alumni / non-aktif
     */
    public function getIconAlumniAttribute(): string
    {
        return $this->isInstitusiPendidikan() ? 'fa-user-graduate' : 'fa-user-slash';
    }

    /**
     * Mendapatkan informasi aksi, icon, status, dan deskripsi modal yang kontekstual
     * berdasarkan tab aktif atau objek pengguna (Pimpinan vs Guru vs Siswa vs Non-Pendidikan)
     */
    public function getContextualActionInfo(?string $tab = null, ?User $user = null): array
    {
        $isPimpinan = false;
        $isGuru = false;
        $isPetugas = false;
        $isStudent = false;

        if ($user) {
            if ($user->level === UserRole::ADMIN_INSTANSI->value || ($user->divisi && $user->divisi->kategori === Divisi::KATEGORI_PIMPINAN)) {
                $isPimpinan = true;
            } elseif ($user->divisi && $user->divisi->kategori === Divisi::KATEGORI_STAF) {
                $isGuru = true;
            } elseif (in_array($user->level, [UserRole::ADMIN->value, UserRole::HEALTH_MONITOR->value, UserRole::HEALTH_CONSULTANT->value])) {
                $isPetugas = true;
            } else {
                $isStudent = $this->isInstitusiPendidikan();
            }
        } elseif ($tab) {
            $isPimpinan = ($tab === 'pimpinan');
            $isGuru = in_array($tab, ['guru', 'staf']);
            $isPetugas = ($tab === 'petugas');
            $isStudent = in_array($tab, ['siswa', 'member']) && $this->isInstitusiPendidikan();
        }

        if ($isPimpinan) {
            return [
                'btn_label' => 'Keluarkan dari Divisi',
                'btn_title' => 'Keluarkan dari Divisi (Lepas Penugasan)',
                'icon' => 'fa-arrow-right-from-bracket',
                'status_label' => 'Tanpa Divisi',
                'badge_label' => 'Tanpa Penugasan Divisi',
                'modal_title' => 'Keluarkan Pimpinan dari Divisi',
                'modal_desc' => 'Pengguna pimpinan akan dikeluarkan dari divisi aktif saat ini. Akun tetap tersimpan aman di sistem.',
                'is_alumni' => false,
            ];
        }

        if ($isGuru) {
            $sebutanGuru = $this->jenis_instansi === 'Universitas' ? 'Dosen / Staf' : ($this->isInstitusiPendidikan() ? 'Guru / Tendik' : 'Staf / Manajerial');
            return [
                'btn_label' => 'Keluarkan dari Divisi',
                'btn_title' => "Keluarkan dari Divisi (Nonaktifkan Penugasan {$sebutanGuru})",
                'icon' => 'fa-user-minus',
                'status_label' => 'Tanpa Penugasan',
                'badge_label' => 'Tanpa Divisi / Arsip',
                'modal_title' => "Keluarkan {$sebutanGuru} dari Divisi",
                'modal_desc' => "Pengguna {$sebutanGuru} akan dikeluarkan dari divisi aktif saat ini. Data riwayat akun tetap tersimpan.",
                'is_alumni' => false,
            ];
        }

        if ($isPetugas) {
            return [
                'btn_label' => 'Keluarkan dari Divisi',
                'btn_title' => 'Keluarkan dari Divisi (Petugas)',
                'icon' => 'fa-user-minus',
                'status_label' => 'Tanpa Divisi',
                'badge_label' => 'Tanpa Divisi',
                'modal_title' => 'Keluarkan Petugas dari Divisi',
                'modal_desc' => 'Pengguna petugas akan dikeluarkan dari penugasan divisi aktif.',
                'is_alumni' => false,
            ];
        }

        if ($isStudent) {
            $isKampus = ($this->jenis_instansi === 'Universitas');
            $labelAlum = $isKampus ? 'Alumni Kampus' : 'Alumni';
            return [
                'btn_label' => "Keluarkan dari Divisi ({$labelAlum})",
                'btn_title' => "Keluarkan dari Divisi (Status {$labelAlum})",
                'icon' => 'fa-user-graduate',
                'status_label' => $labelAlum,
                'badge_label' => "{$labelAlum} / Arsip",
                'modal_title' => "Keluarkan dari Divisi (Status {$labelAlum})",
                'modal_desc' => "Siswa/mahasiswa yang dikeluarkan akan berstatus {$labelAlum} / Arsip. Kuota lisensi aktif akan dibebaskan.",
                'is_alumni' => true,
            ];
        }

        $labelNonaktif = match ($this->jenis_instansi ?? 'Sekolah') {
            'Klinik' => 'Selesai Rawat',
            'Pemerintah' => 'Non-Aktif / Pindah',
            default => 'Non-Aktif',
        };
        return [
            'btn_label' => 'Keluarkan dari Divisi (Non-Aktifkan)',
            'btn_title' => 'Keluarkan dari Divisi (Non-Aktifkan)',
            'icon' => 'fa-user-slash',
            'status_label' => $labelNonaktif,
            'badge_label' => "{$labelNonaktif} / Arsip",
            'modal_title' => 'Keluarkan dari Divisi (Non-Aktifkan)',
            'modal_desc' => 'Pengguna yang dikeluarkan akan dinonaktifkan dari divisi aktif. Kuota lisensi akan dibebaskan.',
            'is_alumni' => false,
        ];
    }

    /**
     * Sebutan utama organisasi: 'Institusi' atau 'Instansi'
     */
    public function getTipeSebutanAttribute(): string
    {
        return $this->isInstitusiPendidikan() ? 'Institusi' : 'Instansi';
    }

    /**
     * Sebutan spesifik organisasi: 'Institusi / Sekolah', 'Institusi / Kampus', 'Instansi / Korporasi', dll.
     */
    public function getSebutanSpesifikAttribute(): string
    {
        return match ($this->jenis_instansi ?? 'Sekolah') {
            'Universitas' => 'Institusi / Kampus',
            'Perusahaan' => 'Instansi / Korporasi',
            'Pemerintah' => 'Instansi Pemerintah',
            'Klinik' => 'Faskes / Klinik',
            default => 'Institusi / Sekolah',
        };
    }

    /**
     * Label tunggal untuk member (contoh: 'Siswa', 'Mahasiswa', 'Karyawan', 'Pegawai', 'Pasien', 'Anggota')
     */
    public function getLabelMemberSingularAttribute(): string
    {
        return match ($this->jenis_instansi ?? 'Sekolah') {
            'Universitas' => 'Mahasiswa',
            'Perusahaan' => 'Karyawan',
            'Pemerintah' => 'Pegawai',
            'Klinik' => 'Pasien',
            default => 'Siswa',
        };
    }

    /**
     * Label jamak untuk member (contoh: 'Siswa / Siswi', 'Mahasiswa / Mahasiswi', 'Karyawan / Staf', dll.)
     */
    public function getLabelMemberPluralAttribute(): string
    {
        return match ($this->jenis_instansi ?? 'Sekolah') {
            'Universitas' => 'Mahasiswa',
            'Perusahaan' => 'Karyawan / Staf',
            'Pemerintah' => 'Pegawai / ASN',
            'Klinik' => 'Pasien / Klien',
            default => 'Siswa / Anggota',
        };
    }

    /**
     * Sebutan Admin pengelola (contoh: 'Admin Institusi / Sekolah', 'Admin Institusi / Kampus', 'Admin Instansi')
     */
    public function getLabelAdminAttribute(): string
    {
        return match ($this->jenis_instansi ?? 'Sekolah') {
            'Universitas' => 'Admin Institusi / Kampus',
            'Perusahaan' => 'Admin Instansi / HR K3',
            'Pemerintah' => 'Admin Instansi',
            'Klinik' => 'Admin Faskes',
            default => 'Admin Institusi / Sekolah',
        };
    }

    /**
     * Label menu manajemen pengguna di sidebar sesuai konteks instansi
     */
    public function getLabelDataUsersMenuAttribute(): string
    {
        return match ($this->jenis_instansi ?? 'Sekolah') {
            'Sekolah' => 'Data Warga Sekolah',
            'Universitas' => 'Data Sivitas Kampus',
            'Perusahaan' => 'Data Karyawan & Staf',
            'Pemerintah' => 'Data Pegawai & ASN',
            'Klinik' => 'Data Pasien & Tim Medis',
            default => 'Data Pengguna',
        };
    }

    /**
     * Nama preset paket kuota sesuai tipe institusi/instansi
     */
    public function getPresetPaketName(int $qty): string
    {
        $jenis = $this->jenis_instansi ?? 'Sekolah';
        if ($jenis === 'Universitas') {
            return match ($qty) {
                50 => 'Paket Program Studi',
                100 => 'Paket Jurusan',
                250 => 'Paket Fakultas',
                500 => 'Standar Kampus',
                1000 => 'Universitas / Institut',
                default => 'Paket Mahasiswa',
            };
        } elseif ($jenis === 'Perusahaan' || $jenis === 'Pemerintah') {
            return match ($qty) {
                50 => 'Paket Tim / Divisi',
                100 => 'Paket Departemen',
                250 => 'Skala Menengah',
                500 => 'Standar Instansi / Korporasi',
                1000 => 'Perusahaan / BUMN Besar',
                default => 'Paket Pengguna',
            };
        } else {
            return match ($qty) {
                50 => 'Paket Kelas',
                100 => 'Paket Angkatan',
                250 => 'Sekolah Menengah',
                500 => 'Standar Sekolah',
                1000 => 'Sekolah Besar / Yayasan',
                default => 'Paket Siswa',
            };
        }
    }
}
