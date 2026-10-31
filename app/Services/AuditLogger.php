<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use App\Enums\AuditCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Kategori log yang diizinkan (bersumber dari AuditCategory Enum)
     */
    public const ALLOWED_CATEGORIES = [
        'auth',
        'billing',
        'user',
        'instansi',
        'maintenance',
        'module',
        'system',
        'security',
        'health',
    ];

    public static function log(
        string $action,
        string|AuditCategory $category = AuditCategory::SYSTEM,
        string $description = '',
        array $properties = [],
        ?User $user = null
    ): ?AuditLog {
        try {
            $catValue = $category instanceof AuditCategory ? $category->value : strtolower($category);
            $categoryStr = in_array($catValue, AuditCategory::values(), true) ? $catValue : AuditCategory::SYSTEM->value;
            // Sanitasi aksi (hanya izinkan huruf, angka, underscore, hyphen, max 60 karakter)
            $action = substr(preg_replace('/[^a-zA-Z0-9_\-]/', '_', $action), 0, 60);

            $currentUser = $user ?? (auth('sanctum')->user() ?? Auth::user());
            $request = request();

            $userAgent = $request ? $request->userAgent() : null;
            $device = self::resolveDevice($userAgent);

            return AuditLog::create([
                'user_id' => $currentUser?->id,
                'user_name' => $currentUser?->name ?? 'Tamu / Sistem',
                'user_email' => $currentUser?->email,
                'user_role' => $currentUser?->role_label ?? ($currentUser?->level ?? 'Guest'),
                'instansi_id' => $currentUser?->instansi_id,
                'instansi_name' => $currentUser?->instansi?->nama_instansi,
                'action' => $action,
                'category' => $categoryStr,
                'description' => $description,
                'properties' => !empty($properties) ? $properties : null,
                'ip_address' => $request ? $request->ip() : '127.0.0.1',
                'user_agent' => $userAgent,
                'device' => $device,
            ]);
        } catch (\Throwable $e) {
            // Logging tidak boleh mematahkan proses utama aplikasi
            \Illuminate\Support\Facades\Log::error('AuditLogger Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Shortcut untuk log otentikasi (login, logout, registrasi).
     */
    public static function auth(string $action, string $description, array $properties = [], ?User $user = null): ?AuditLog
    {
        return self::log($action, AuditCategory::AUTH, $description, $properties, $user);
    }

    /**
     * Shortcut untuk log pembayaran, langganan & lisensi.
     */
    public static function billing(string $action, string $description, array $properties = [], ?User $user = null): ?AuditLog
    {
        return self::log($action, AuditCategory::BILLING, $description, $properties, $user);
    }

    /**
     * Shortcut untuk log manajemen pengguna (buat akun, ban, suspend, aktivasi).
     */
    public static function userAction(string $action, string $description, array $properties = [], ?User $user = null): ?AuditLog
    {
        return self::log($action, AuditCategory::USER, $description, $properties, $user);
    }

    /**
     * Shortcut untuk log pemeliharaan sistem (maintenance mode).
     */
    public static function maintenance(string $action, string $description, array $properties = [], ?User $user = null): ?AuditLog
    {
        return self::log($action, AuditCategory::MAINTENANCE, $description, $properties, $user);
    }

    /**
     * Shortcut untuk log kontrol modul & perbaikan fitur.
     */
    public static function module(string $action, string $description, array $properties = [], ?User $user = null): ?AuditLog
    {
        return self::log($action, AuditCategory::MODULE, $description, $properties, $user);
    }

    /**
     * Deteksi tipe perangkat dari User-Agent string secara ringkas.
     */
    private static function resolveDevice(?string $userAgent): string
    {
        if (empty($userAgent)) {
            return 'Desktop';
        }
        $ua = strtolower($userAgent);
        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet') || (str_contains($ua, 'android') && !str_contains($ua, 'mobile'))) {
            return 'Tablet';
        }
        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone') || str_contains($ua, 'ipod')) {
            return 'Mobile';
        }
        return 'Desktop';
    }
}
