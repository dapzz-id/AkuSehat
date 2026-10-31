<?php

namespace App\Modules\Billing\Subscription\Repositories;

use App\Models\BillingTransaction;
use App\Models\LicenseKey;
use App\Models\User;
use App\Modules\User\Enums\UserRole;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BillingRepository
{
    public function getTransactionsForInstansi(string $instansiId, int $perPage = 10): LengthAwarePaginator
    {
        return BillingTransaction::where('instansi_id', $instansiId)
            ->latest()
            ->paginate($perPage);
    }

    public function findByOrderNumber(string $orderNumber): ?BillingTransaction
    {
        return BillingTransaction::where('order_number', $orderNumber)->first();
    }

    public function findByOrderId(string $orderId): ?BillingTransaction
    {
        return BillingTransaction::where('order_id', $orderId)->first();
    }

    public function create(array $data): BillingTransaction
    {
        return BillingTransaction::create($data);
    }

    public function updateStatus(BillingTransaction $transaction, string $status, ?string $paymentType = null): bool
    {
        $payload = ['status' => $status];
        if ($paymentType) {
            $payload['payment_type'] = $paymentType;
        }
        return $transaction->update($payload);
    }

    public function findTransactionWithDetails(int|string $id): BillingTransaction
    {
        return BillingTransaction::with(['instansi', 'user'])->findOrFail($id);
    }

    public function getActiveLicensesForInstansi(string $instansiId)
    {
        return LicenseKey::where('instansi_id', $instansiId)
            ->where('status', LicenseKey::STATUS_ACTIVE)
            ->whereDate('tanggal_berakhir', '>=', now()->toDateString())
            ->orderBy('tanggal_berakhir', 'asc')
            ->get();
    }

    public function getAllLicensesForInstansi(string $instansiId)
    {
        return LicenseKey::where('instansi_id', $instansiId)
            ->orderByRaw("CASE WHEN status = 'active' AND (tanggal_berakhir IS NULL OR tanggal_berakhir >= CURRENT_DATE) THEN 0 ELSE 1 END")
            ->orderBy('tanggal_berakhir', 'desc')
            ->get();
    }

    public function findLicenseById(string $licenseId): ?LicenseKey
    {
        return LicenseKey::where('id', $licenseId)->first();
    }

    public function getActiveOrLatestLicense(string $instansiId): ?LicenseKey
    {
        return LicenseKey::where('instansi_id', $instansiId)
            ->orderByRaw("CASE WHEN status = 'active' AND (tanggal_berakhir IS NULL OR tanggal_berakhir >= CURRENT_DATE) THEN 0 ELSE 1 END")
            ->latest('created_at')
            ->first();
    }

    public function createDefaultLicense(string $instansiId, int $quota = 500): LicenseKey
    {
        return LicenseKey::create([
            'instansi_id' => $instansiId,
            'key' => LicenseKey::generateKey(),
            'kuota_pengguna' => $quota,
            'status' => LicenseKey::STATUS_ACTIVE,
            'tanggal_berakhir' => now(),
        ]);
    }

    public function createNewLicense(string $instansiId, int $quota, int $months = 12): LicenseKey
    {
        return LicenseKey::create([
            'instansi_id' => $instansiId,
            'key' => LicenseKey::generateKey(),
            'kuota_pengguna' => $quota,
            'status' => LicenseKey::STATUS_ACTIVE,
            'tanggal_berakhir' => now()->addMonths($months),
        ]);
    }

    public function lockLicenseForUpdate(string $licenseId): ?LicenseKey
    {
        return LicenseKey::where('id', $licenseId)->lockForUpdate()->first();
    }

    public function countActiveDivisionalUsers(string $instansiId): int
    {
        return User::where('instansi_id', $instansiId)
            ->where('level', '!=', UserRole::ADMIN_INSTANSI->value)
            ->whereNotNull('id_divisi')
            ->count();
    }
}
