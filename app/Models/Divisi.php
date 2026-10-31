<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Traits\BelongsToTenant;

class Divisi extends Model
{
    use HasFactory, HasUuids, BelongsToTenant;

    protected $table = 'divisi';

    public const KATEGORI_MEMBER = 'member';
    public const KATEGORI_STAF = 'staf';
    public const KATEGORI_PIMPINAN = 'pimpinan';

    protected $fillable = [
        'instansi_id',
        'tgl',
        'divisi_name',
        'kategori',
        'color_cover',
        'active_modules',
        'module_permissions',
        'description',
    ];

    public static function getKategoriOptions(?string $jenisInstansi = 'Sekolah'): array
    {
        return match ($jenisInstansi) {
            'Universitas' => [
                self::KATEGORI_MEMBER => [
                    'label' => 'Mahasiswa / Kelas Akademik',
                    'sublabel' => 'Sivitas akademika & mahasiswa program studi',
                    'icon' => 'fa-user-graduate',
                    'color' => 'emerald',
                    'badge' => 'Mahasiswa',
                ],
                self::KATEGORI_STAF => [
                    'label' => 'Dosen & Tenaga Pengajar',
                    'sublabel' => 'Dosen tetap, dosen luar biasa, asisten lab',
                    'icon' => 'fa-chalkboard-teacher',
                    'color' => 'blue',
                    'badge' => 'Dosen & Tendik',
                ],
                self::KATEGORI_PIMPINAN => [
                    'label' => 'Rektorat & Pimpinan Kampus',
                    'sublabel' => 'Rektor, Dekan, Senat Akademik, Pengurus Kampus',
                    'icon' => 'fa-building-columns',
                    'color' => 'purple',
                    'badge' => 'Rektorat / Pimpinan',
                ],
            ],
            'Perusahaan' => [
                self::KATEGORI_MEMBER => [
                    'label' => 'Karyawan / Staf Operasional',
                    'sublabel' => 'Pegawai reguler departemen / divisi operasional',
                    'icon' => 'fa-user-tie',
                    'color' => 'emerald',
                    'badge' => 'Karyawan',
                ],
                self::KATEGORI_STAF => [
                    'label' => 'Manajerial & Tim Khusus',
                    'sublabel' => 'Manager, supervisor, team lead, project manager',
                    'icon' => 'fa-briefcase',
                    'color' => 'blue',
                    'badge' => 'Manajerial',
                ],
                self::KATEGORI_PIMPINAN => [
                    'label' => 'Pimpinan & Direksi (Direktur / Bos / C-Level)',
                    'sublabel' => 'Board of Directors, Direktur Utama, Komisaris, Owner',
                    'icon' => 'fa-crown',
                    'color' => 'purple',
                    'badge' => 'Pimpinan & Direksi',
                ],
            ],
            'Pemerintah' => [
                self::KATEGORI_MEMBER => [
                    'label' => 'Pegawai / ASN Reguler',
                    'sublabel' => 'Aparatur sipil negara & staf dinas',
                    'icon' => 'fa-user-tie',
                    'color' => 'emerald',
                    'badge' => 'ASN / Pegawai',
                ],
                self::KATEGORI_STAF => [
                    'label' => 'Staf Teknis & Fungsional',
                    'sublabel' => 'Pejabat fungsional, tenaga ahli, konsultan dinas',
                    'icon' => 'fa-id-badge',
                    'color' => 'blue',
                    'badge' => 'Fungsional',
                ],
                self::KATEGORI_PIMPINAN => [
                    'label' => 'Pimpinan Lembaga / Eselon / Kepala Dinas',
                    'sublabel' => 'Kepala dinas, sekretaris dinas, pimpinan instansi',
                    'icon' => 'fa-landmark',
                    'color' => 'purple',
                    'badge' => 'Pimpinan Instansi',
                ],
            ],
            'Klinik' => [
                self::KATEGORI_MEMBER => [
                    'label' => 'Pasien / Klien Faskes',
                    'sublabel' => 'Masyarakat, pasien, atau klien penerima layanan',
                    'icon' => 'fa-user',
                    'color' => 'emerald',
                    'badge' => 'Pasien',
                ],
                self::KATEGORI_STAF => [
                    'label' => 'Staf Paramedis & Resepsionis',
                    'sublabel' => 'Perawat, apoteker, analis lab, staf administrasi',
                    'icon' => 'fa-stethoscope',
                    'color' => 'blue',
                    'badge' => 'Paramedis & Staf',
                ],
                self::KATEGORI_PIMPINAN => [
                    'label' => 'Direktur Medis / Kepala Faskes',
                    'sublabel' => 'Kepala puskesmas, direktur klinik, penanggung jawab medis',
                    'icon' => 'fa-hospital-user',
                    'color' => 'purple',
                    'badge' => 'Direktur Faskes',
                ],
            ],
            default => [
                self::KATEGORI_MEMBER => [
                    'label' => 'Siswa / Kelas Pembelajaran',
                    'sublabel' => 'Peserta didik dan rombongan belajar (rombel/kelas)',
                    'icon' => 'fa-user-graduate',
                    'color' => 'emerald',
                    'badge' => 'Siswa / Kelas',
                ],
                self::KATEGORI_STAF => [
                    'label' => 'Dewan Guru & Tenaga Kependidikan (Tendik)',
                    'sublabel' => 'Guru mapel, wali kelas, tata usaha, laboran, satpam',
                    'icon' => 'fa-chalkboard-teacher',
                    'color' => 'blue',
                    'badge' => 'Guru & Tendik',
                ],
                self::KATEGORI_PIMPINAN => [
                    'label' => 'Pimpinan Sekolah / Yayasan / Komite',
                    'sublabel' => 'Kepala Sekolah, Wakil Kepala Sekolah, Pembina Yayasan',
                    'icon' => 'fa-school',
                    'color' => 'purple',
                    'badge' => 'Pimpinan / Yayasan',
                ],
            ],
        };
    }

    public function getKategoriMetaAttribute(): array
    {
        $options = self::getKategoriOptions($this->instansi?->jenis_instansi);
        $kategoriKey = $this->kategori ?: self::KATEGORI_MEMBER;
        return $options[$kategoriKey] ?? [
            'label' => ucfirst($kategoriKey),
            'sublabel' => '',
            'icon' => 'fa-folder',
            'color' => 'gray',
            'badge' => ucfirst($kategoriKey),
        ];
    }

    public function getKategoriLabelAttribute(): string
    {
        return $this->kategori_meta['badge'];
    }

    protected $casts = [
        'active_modules' => 'array',
        'module_permissions' => 'array',
    ];

    public function isModuleActive($moduleKey)
    {
        if (!is_null($this->active_modules)) {
            return in_array($moduleKey, $this->active_modules);
        }
        return $this->instansi ? $this->instansi->isModuleActive($moduleKey) : true;
    }

    public function getNamaDivisiAttribute()
    {
        return $this->divisi_name;
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_divisi');
    }

     public function hb()
    {
        return $this->hasMany(Hb::class, 'id_divisi');
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    public function kesehatan()
    {
        return $this->hasManyThrough(
            Kesehatan::class,
            User::class,
            'id_divisi', // foreign key di users
            'id_user',   // foreign key di kesehatan
            'id',        // local key Divisi
            'id'         // local key User
        );
    }
}
