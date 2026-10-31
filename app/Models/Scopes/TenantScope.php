<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Flag untuk mencegah rekursi tak terbatas saat Auth::user() memicu query Model::find()
     */
    protected static bool $isApplying = false;
    protected static array $hasColumnCache = [];

    /**
     * Terapkan pembatasan isolasi data tenant (instansi/sekolah) ke query Eloquent.
     * Mencegah Sekolah A melihat atau mengakses data milik Sekolah B.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (static::$isApplying) {
            return;
        }

        static::$isApplying = true;

        try {
            // Jangan filter jika pengguna belum terautentikasi (misal public login/register)
            if (!Auth::check()) {
                return;
            }

            $currentUser = Auth::user();
            if (!$currentUser) {
                return;
            }

            // SuperAdmin memiliki wewenang akses lintas instansi
            if ($currentUser->isSuperAdmin()) {
                return;
            }

            $instansiId = $currentUser->instansi_id;
            $table = $model->getTable();

            // Jika user tidak memiliki instansi_id dan bukan SuperAdmin, blokir akses total
            if (!$instansiId) {
                $builder->whereRaw('1 = 0');
                return;
            }

            // 1. Model Instansi itu sendiri
            if ($table === 'instansi') {
                $builder->where($table . '.id', $instansiId);
                return;
            }

            // 2. Model yang memiliki kolom instansi_id langsung
            if (in_array($table, [
                'users', 'divisi', 'billing_transactions', 'counseling_sessions',
                'kesehatan', 'hb', 'peminjaman_pita', 'data_haid',
                'user_activities', 'user_vital_logs', 'nutrition_logs',
                'burnout_screenings', 'ergonomics_logs', 'vaccine_records',
                'v_instansi_health_summary', 'v_divisi_dashboard_stats'
            ])) {
                $builder->where($table . '.instansi_id', $instansiId);
                return;
            }

            // 3. Fallback jika ada tabel log tanpa instansi_id langsung (relasi via user)
            $userColumn = in_array($table, ['kesehatan', 'hb', 'peminjaman_pita', 'data_haid']) ? 'id_user' : 'user_id';
            $cacheKey = $table . '.' . $userColumn;
            if (!isset(static::$hasColumnCache[$cacheKey])) {
                static::$hasColumnCache[$cacheKey] = \Illuminate\Support\Facades\Schema::hasColumn($table, $userColumn);
            }

            if (static::$hasColumnCache[$cacheKey]) {
                $builder->whereExists(function ($query) use ($table, $userColumn, $instansiId) {
                    $query->selectRaw(1)
                        ->from('users')
                        ->whereColumn('users.id', $table . '.' . $userColumn)
                        ->where('users.instansi_id', $instansiId);
                });
            }
        } finally {
            static::$isApplying = false;
        }
    }
}

