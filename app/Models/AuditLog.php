<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AuditLog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'instansi_id',
        'instansi_name',
        'action',
        'category',
        'description',
        'properties',
        'ip_address',
        'user_agent',
        'device',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function instansi()
    {
        return $this->belongsTo(Instansi::class, 'instansi_id');
    }

    /**
     * Badge color mapping based on category.
     */
    public function getCategoryBadgeClass(): string
    {
        return match ($this->category) {
            'auth' => 'bg-blue-100 text-blue-700 border-blue-200',
            'billing' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'user' => 'bg-purple-100 text-purple-700 border-purple-200',
            'instansi' => 'bg-amber-100 text-amber-800 border-amber-200',
            'maintenance' => 'bg-rose-100 text-rose-700 border-rose-200',
            'module' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }

    /**
     * Category human-readable label.
     */
    public function getCategoryLabel(): string
    {
        return match ($this->category) {
            'auth' => 'Otentikasi Akun',
            'billing' => 'Pembayaran & Lisensi',
            'user' => 'Manajemen Pengguna',
            'instansi' => 'Institusi / Instansi',
            'maintenance' => 'Pemeliharaan Sistem',
            'module' => 'Kontrol Fitur & Modul',
            default => 'Sistem Terpadu',
        };
    }
}
