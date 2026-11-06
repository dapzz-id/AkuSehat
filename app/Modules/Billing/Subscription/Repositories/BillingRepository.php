<?php

namespace App\Modules\Billing\Subscription\Repositories;

use App\Models\BillingTransaction;
use App\Models\LicenseKey;
use App\Models\User;
use App\Modules\Billing\Subscription\Enums\BillingTransactionStatus;
use App\Modules\Billing\Subscription\Enums\PaymentMethod;
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

    public function createNewLicense(string $instansiId, int $quota, int $months = 1): LicenseKey
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
        $instansi = Instansi::find($instansiId);
        if ($instansi) {
            return $instansi->getCountableUsersCount();
        }

        return User::where('instansi_id', $instansiId)
            ->whereNotIn('level', [
                UserRole::ADMIN_INSTANSI->value,
                UserRole::SUPERADMIN->value,
                'Admin Instansi',
                'SuperAdmin',
            ])
            ->count();
    }

    public function getSuperAdminTransactions(array|string|null $filters = null, int $perPage = 15): LengthAwarePaginator
    {
        $query = BillingTransaction::with(['instansi', 'user'])->latest();

        $filterArr = is_array($filters) ? $filters : ['status' => $filters];

        // 1. Status Filter
        $status = $filterArr['status'] ?? null;
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // 2. Keyword Search (Order ID, Nama Instansi, Pemesan)
        $search = trim($filterArr['search'] ?? $filterArr['q'] ?? '');
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('instansi', function ($iq) use ($search) {
                      $iq->where('nama_instansi', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nama', 'like', "%{$search}%")
                         ->orWhere('username', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // 3. Payment Method Filter
        $method = $filterArr['payment_method'] ?? null;
        if ($method && $method !== 'all') {
            if ($method === 'manual_bca' || $method === 'manual_transfer' || $method === 'bca') {
                $query->where(function ($mq) {
                    $mq->where('payment_method', 'like', '%bca%')
                       ->orWhere('payment_method', PaymentMethod::MANUAL_TRANSFER->value);
                });
            } elseif ($method === 'google_pay' || $method === 'gpay') {
                $query->where(function ($mq) {
                    $mq->where('payment_method', 'like', '%google%')
                       ->orWhere('payment_method', 'like', '%gpay%');
                });
            } else {
                $query->where('payment_method', $method);
            }
        }

        // 4. Date Range Filter
        $startDate = $filterArr['start_date'] ?? null;
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        $endDate = $filterArr['end_date'] ?? null;
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $perPageCount = (int) ($filterArr['per_page'] ?? $perPage);
        if ($perPageCount < 1) $perPageCount = 15;

        return $query->paginate($perPageCount);
    }

    public function getTransactionSummaryStats(): array
    {
        return [
            'total_revenue' => (float) BillingTransaction::whereIn('status', [
                BillingTransactionStatus::SUCCESS->value, 
                BillingTransactionStatus::PAID->value
            ])->sum('nominal'),
            'pending_count' => BillingTransaction::where('status', BillingTransactionStatus::PENDING->value)->count(),
            'success_count' => BillingTransaction::whereIn('status', [
                BillingTransactionStatus::SUCCESS->value, 
                BillingTransactionStatus::PAID->value
            ])->count(),
            'total_count' => BillingTransaction::count(),
        ];
    }
}
