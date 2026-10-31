<?php

namespace App\Models\Traits;

use App\Models\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    /**
     * Boot trait BelongsToTenant.
     * Mengaktifkan TenantScope secara global dan mengisi instansi_id otomatis saat creating.
     */
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if (!$user->isSuperAdmin() && $user->instansi_id) {
                    $table = $model->getTable();
                    if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'instansi_id') && empty($model->instansi_id)) {
                        $model->instansi_id = $user->instansi_id;
                    }
                }
            }
        });
    }

    /**
     * Relasi ke model Instansi
     */
    public function instansi()
    {
        return $this->belongsTo(\App\Models\Instansi::class, 'instansi_id');
    }
}
