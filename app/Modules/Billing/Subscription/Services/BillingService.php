<?php

namespace App\Modules\Billing\Subscription\Services;

use App\Models\BillingTransaction;
use App\Models\Instansi;
use App\Models\LicenseKey;
use App\Models\User;
use App\Modules\Billing\Subscription\Repositories\BillingRepository;
use App\Modules\Billing\Subscription\Enums\BillingRate;
use App\Modules\Billing\Subscription\Enums\BillingTransactionType;
use App\Modules\Billing\Subscription\Enums\BillingTransactionStatus;
use App\Modules\Billing\Subscription\Enums\PaymentMethod;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class BillingService
{
    public const HARGA_PER_USER_TAHUN = BillingRate::HARGA_PER_USER_TAHUN->value;

    public function __construct(
        protected BillingRepository $billingRepository
    ) {}

    public function calculateTotalAmount(string|BillingTransactionType $type, int $qty, int $months = 12): float
    {
        $rate = BillingRate::HARGA_PER_USER_TAHUN;
        $typeVal = $type instanceof BillingTransactionType ? $type->value : $type;

        if ($typeVal === BillingTransactionType::QUOTA_TOPUP->value || $typeVal === BillingTransactionType::TAMBAH_KUOTA->value) {
            return $rate->calculateSubtotal($qty, $months);
        }

        return $rate->calculateSubtotal($qty, $months);
    }

    public function getBillingDashboardData(User $user): array
    {
        $instansi = $user->sekolah ?: Instansi::find($user->instansi_id);
        if (!$instansi) {
            throw new Exception('Instansi Anda belum terdaftar.');
        }

        $activeLicenses = $this->billingRepository->getActiveLicensesForInstansi((string) $instansi->id);
        $allLicenses = $this->billingRepository->getAllLicensesForInstansi((string) $instansi->id);

        $totalActiveQuota = $instansi->getTotalActiveQuota();
        $totalPengguna = $this->billingRepository->countActiveDivisionalUsers((string) $instansi->id);

        // Paket acuan untuk form perpanjangan default (prioritaskan paket aktif yang masa kedaluwarsanya paling dekat)
        $primaryLicense = $activeLicenses->first() 
            ?? $this->billingRepository->getActiveOrLatestLicense((string) $instansi->id);

        if (!$primaryLicense && $allLicenses->isNotEmpty()) {
            $primaryLicense = $allLicenses->first();
        }

        if (!$primaryLicense) {
            $primaryLicense = $this->billingRepository->createDefaultLicense((string) $instansi->id, 500);
            $instansi->update(['license_id_active' => $primaryLicense->id]);
            $allLicenses = collect([$primaryLicense]);
            $activeLicenses = collect([$primaryLicense]);
            $totalActiveQuota = $primaryLicense->kuota_pengguna;
        }

        $kuotaMaksimal = $totalActiveQuota;
        $sisaHari = $primaryLicense ? $primaryLicense->getDaysRemaining() : 0;

        $transactions = $this->billingRepository->getTransactionsForInstansi((string) $instansi->id, 10);

        // Hitungan perpanjangan untuk paket acuan
        $kuotaPerpanjangDefault = $primaryLicense ? (int) $primaryLicense->kuota_pengguna : $kuotaMaksimal;
        $tanggalBerakhirBaruPerpanjang = $primaryLicense 
            ? ($primaryLicense->tanggal_berakhir && $primaryLicense->tanggal_berakhir->isFuture() 
                ? $primaryLicense->tanggal_berakhir->copy()->addMonths(12) 
                : now()->addMonths(12))
            : now()->addMonths(12);

        $subtotalPerpanjang = $kuotaPerpanjangDefault * self::HARGA_PER_USER_TAHUN;
        $ppnPerpanjang = (int) round($subtotalPerpanjang * 0.11);
        $totalPerpanjang = $subtotalPerpanjang + $ppnPerpanjang;

        $packagesPayload = $allLicenses->map(function ($p) {
            return [
                'id' => (string) $p->id,
                'key' => (string) $p->key,
                'quota' => (int) $p->kuota_pengguna,
                'expiry_formatted' => $p->tanggal_berakhir ? $p->tanggal_berakhir->format('d M Y') : 'Hari Ini',
                'raw_expiry' => $p->tanggal_berakhir ? $p->tanggal_berakhir->format('Y-m-d') : null,
                'is_expired' => $p->isExpired(),
                'days_remaining' => $p->getDaysRemaining(),
            ];
        })->values()->all();

        return [
            'instansi' => $instansi,
            'license' => $primaryLicense,
            'primaryLicense' => $primaryLicense,
            'activePackages' => $activeLicenses,
            'allPackages' => $allLicenses,
            'packagesPayload' => $packagesPayload,
            'totalPengguna' => $totalPengguna,
            'kuotaMaksimal' => $kuotaMaksimal,
            'sisaHari' => (int) floor($sisaHari),
            'transactions' => $transactions,
            'hargaPerUser' => self::HARGA_PER_USER_TAHUN,
            'tipeSebutan' => $instansi->tipe_sebutan,
            'sebutanSpesifik' => $instansi->sebutan_spesifik,
            'labelMember' => $instansi->label_member_singular,
            'labelMemberPlural' => $instansi->label_member_plural,
            'labelAdmin' => $instansi->label_admin,
            'kuotaPerpanjangDefault' => $kuotaPerpanjangDefault,
            'tanggalBerakhirBaruPerpanjang' => $tanggalBerakhirBaruPerpanjang,
            'subtotalPerpanjang' => $subtotalPerpanjang,
            'ppnPerpanjang' => $ppnPerpanjang,
            'totalPerpanjang' => $totalPerpanjang,
        ];
    }

    public function processGooglePay(User $user, array $validated): array
    {
        $instansi = $user->sekolah ?: Instansi::find($user->instansi_id);
        if (!$instansi) {
            return [
                'status_code' => 404,
                'response' => [
                    'success' => false,
                    'message' => 'Instansi tidak ditemukan.',
                ],
            ];
        }

        $modeTransaksi = $validated['mode_transaksi'] ?? 'perpanjang';

        if ($modeTransaksi === 'perpanjang') {
            $licenseIdInput = $validated['license_id'] ?? null;
            $targetLicense = null;
            if ($licenseIdInput) {
                $targetLicense = $this->billingRepository->findLicenseById($licenseIdInput);
            }
            if (!$targetLicense || $targetLicense->instansi_id !== $instansi->id) {
                $targetLicense = $instansi->getActiveLicense() 
                    ?? $this->billingRepository->getActiveOrLatestLicense((string) $instansi->id);
            }

            if (!$targetLicense) {
                $targetLicense = $this->billingRepository->createDefaultLicense((string) $instansi->id, 8);
                $instansi->update(['license_id_active' => $targetLicense->id]);
            }

            $kuotaTarget = (int) ($targetLicense->kuota_pengguna ?: 8);
            $tambahKuota = 0;
            $subtotal = $kuotaTarget * self::HARGA_PER_USER_TAHUN;
            $tipeTransaksi = BillingTransactionType::PERPANJANG_LISENSI->value;
            $deskripsiItem = "Perpanjangan Paket Lisensi ({$targetLicense->key} - {$kuotaTarget} " . ($instansi->label_member_singular ?? 'User') . " / +1 Tahun)";
        } else {
            // Mode TAMBAH KUOTA BARU (Paket Baru Mandiri 1 Tahun Penuh - Model Provider Internet / Bima Tri)
            $tambahKuota = (int) ($validated['tambah_kuota'] ?? 0);
            if ($tambahKuota <= 0) {
                return [
                    'status_code' => 422,
                    'response' => [
                        'success' => false,
                        'message' => 'Tentukan jumlah kuota tambahan (minimal 1 user).',
                    ],
                ];
            }

            $targetLicense = null;
            $subtotal = $tambahKuota * self::HARGA_PER_USER_TAHUN;
            $tipeTransaksi = BillingTransactionType::TAMBAH_KUOTA->value;
            $deskripsiItem = "Paket Kuota Baru (+{$tambahKuota} " . ($instansi->label_member_singular ?? 'User') . " / 1 Tahun Mandiri)";
        }

        $pajak11 = BillingRate::calculatePpn($subtotal);
        $totalNominal = $subtotal + $pajak11;

        $orderIdInput = $validated['order_id'] ?? null;
        if ($orderIdInput && $this->billingRepository->findByOrderId($orderIdInput)) {
            return [
                'status_code' => 200,
                'response' => [
                    'success' => true,
                    'is_duplicate' => true,
                    'message' => 'Transaksi dengan Order ID ini telah berhasil dicatat sebelumnya.',
                ],
            ];
        }

        try {
            $resultData = DB::transaction(function () use ($instansi, $user, $validated, $modeTransaksi, $targetLicense, $tambahKuota, $subtotal, $pajak11, $totalNominal, $tipeTransaksi, $deskripsiItem, $orderIdInput) {
                if ($modeTransaksi === 'perpanjang') {
                    $lockedLicense = $this->billingRepository->lockLicenseForUpdate((string) $targetLicense->id) ?? $targetLicense;

                    $baseDate = ($lockedLicense->tanggal_berakhir && $lockedLicense->tanggal_berakhir->isFuture()) 
                        ? $lockedLicense->tanggal_berakhir 
                        : now();
                    $tanggalBerakhirBaru = $baseDate->copy()->addMonths(12);

                    $lockedLicense->tanggal_berakhir = $tanggalBerakhirBaru;
                    $lockedLicense->status = LicenseKey::STATUS_ACTIVE;
                    $lockedLicense->save();

                    if (!$instansi->license_id_active) {
                        $instansi->update(['license_id_active' => $lockedLicense->id]);
                    }

                    $affectedLicense = $lockedLicense;
                    $durasiBulanTrx = 12;
                } else {
                    // Tambah Slot Baru: Buat LicenseKey baru mandiri aktif 1 tahun (seperti paket provider Bima Tri)
                    $newLicense = $this->billingRepository->createNewLicense(
                        instansiId: (string) $instansi->id,
                        quota: $tambahKuota,
                        months: 12
                    );

                    if (!$instansi->license_id_active) {
                        $instansi->update(['license_id_active' => $newLicense->id]);
                    }

                    $tanggalBerakhirBaru = $newLicense->tanggal_berakhir;
                    $affectedLicense = $newLicense;
                    $durasiBulanTrx = 12;
                }

                $orderId = $orderIdInput ?: ('GPAY-AKU-' . time() . '-' . strtoupper(Str::random(4)));

                $trx = $this->billingRepository->create([
                    'instansi_id' => $instansi->id,
                    'user_id' => $user->id,
                    'order_id' => $orderId,
                    'tipe_transaksi' => $tipeTransaksi,
                    'durasi_bulan' => $durasiBulanTrx,
                    'tambah_kuota' => $tambahKuota,
                    'nominal' => $totalNominal,
                    'payment_method' => PaymentMethod::GOOGLE_PAY->value,
                    'payment_token' => $validated['payment_token'] ?? null,
                    'status' => BillingTransactionStatus::SUCCESS->value,
                    'payload' => [
                        'gateway' => 'Google Pay Web API',
                        'merchant_id' => config('services.google_pay.merchant_id') ?: env('GOOGLE_PAY_MERCHANT_ID', 'BCR2DN4TX7W7XXXX'),
                        'card_details' => $validated['card_details'] ?? 'Google Pay',
                        'card_network' => $validated['card_network'] ?? 'CARD',
                        'timestamp' => now()->toIso8601String(),
                        'mode_transaksi' => $modeTransaksi,
                        'license_key' => $affectedLicense->key,
                        'kuota_paket' => $affectedLicense->kuota_pengguna,
                        'total_kuota_aktif' => $instansi->fresh()->getTotalActiveQuota(),
                        'tanggal_berakhir_paket' => $tanggalBerakhirBaru ? $tanggalBerakhirBaru->format('Y-m-d H:i:s') : null,
                        'subtotal' => $subtotal,
                        'ppn_11' => $pajak11,
                        'harga_per_user' => self::HARGA_PER_USER_TAHUN,
                        'durasi_tahun' => 1,
                        'deskripsi_item' => $deskripsiItem,
                    ],
                ]);

                return [
                    'trx' => $trx,
                    'affectedLicense' => $affectedLicense,
                    'totalActiveQuota' => $instansi->fresh()->getTotalActiveQuota(),
                    'tanggalBerakhir' => $tanggalBerakhirBaru,
                ];
            });

            $trx = $resultData['trx'];
            $affectedLicense = $resultData['affectedLicense'];
            $totalActiveQuota = $resultData['totalActiveQuota'];
            $tanggalBerakhir = $resultData['tanggalBerakhir'];

            AuditLogger::billing(
                'payment_google_pay',
                "Pembayaran Google Pay berhasil untuk institusi {$instansi->nama_instansi} (Order: {$trx->order_id}, Paket: {$affectedLicense->key}) senilai Rp " . number_format($totalNominal, 0, ',', '.'),
                [
                    'order_id' => $trx->order_id,
                    'nominal' => $totalNominal,
                    'mode' => $modeTransaksi,
                    'license_key' => $affectedLicense->key,
                    'total_kuota_aktif' => $totalActiveQuota,
                ]
            );

            return [
                'status_code' => 200,
                'response' => [
                    'success' => true,
                    'message' => 'Pembayaran Google Pay berhasil diproses. Paket lisensi Anda telah aktif.',
                    'data' => [
                        'transaction_id' => $trx->id,
                        'order_id' => $trx->order_id,
                        'mode_transaksi' => $modeTransaksi,
                        'total_dibayar' => number_format($totalNominal, 0, ',', '.'),
                        'kuota_paket' => $affectedLicense->kuota_pengguna,
                        'kuota_sekarang' => $totalActiveQuota,
                        'license_key' => $affectedLicense->key,
                        'tanggal_berakhir' => $tanggalBerakhir ? $tanggalBerakhir->format('d/m/Y') : '-',
                    ],
                ],
            ];
        } catch (\Throwable $e) {
            return [
                'status_code' => 500,
                'response' => [
                    'success' => false,
                    'message' => 'Gagal memproses transaksi Google Pay: ' . $e->getMessage() . '. Seluruh perubahan dibatalkan (Rollback).',
                ],
            ];
        }
    }

    public function processGooglePlayInApp(User $user, array $validated): array
    {
        $instansi = $user->sekolah ?: Instansi::find($user->instansi_id);
        if (!$instansi) {
            return [
                'status_code' => 404,
                'response' => ['status' => false, 'message' => 'Instansi tidak ditemukan untuk user ini.'],
            ];
        }

        $productId = $validated['product_id'];
        $orderId = $validated['order_id'];

        if (str_contains($productId, 'user_100') || str_contains($productId, 'quota_100')) {
            $tambahKuota = 100;
        } elseif (str_contains($productId, 'user_50') || str_contains($productId, 'quota_50')) {
            $tambahKuota = 50;
        } elseif (str_contains($productId, 'user_25') || str_contains($productId, 'quota_25')) {
            $tambahKuota = 25;
        } else {
            $tambahKuota = 10;
        }

        $durasiBulan = 12;
        $subtotal = $tambahKuota * self::HARGA_PER_USER_TAHUN;
        $nominal = $subtotal + (int) round($subtotal * 0.11);
        $tipe = BillingTransactionType::LISENSI_TAHUNAN->value;

        $existingTrx = $this->billingRepository->findByOrderId($orderId);
        if ($existingTrx) {
            $currentLicense = $instansi->getActiveLicense() ?? $this->billingRepository->getActiveOrLatestLicense((string) $instansi->id);
            return [
                'status_code' => 200,
                'response' => [
                    'status' => true,
                    'message' => 'Langganan Google Play In-App ini telah diproses sebelumnya (Idempotent).',
                    'license' => [
                        'kuota_pengguna' => $currentLicense?->kuota_pengguna,
                        'tanggal_berakhir' => $currentLicense?->tanggal_berakhir?->format('Y-m-d'),
                        'status' => $currentLicense?->status,
                    ],
                ],
            ];
        }

        $license = $instansi->getActiveLicense() ?? $this->billingRepository->getActiveOrLatestLicense((string) $instansi->id);

        try {
            DB::transaction(function () use ($instansi, $user, $orderId, $tipe, $durasiBulan, $tambahKuota, $nominal, $validated, &$license) {
                if ($license) {
                    $license = $this->billingRepository->lockLicenseForUpdate((string) $license->id) ?? $license;
                } else {
                    $license = $this->billingRepository->createDefaultLicense((string) $instansi->id, 500);
                    $instansi->update(['license_id_active' => $license->id]);
                }

                if ($durasiBulan > 0) {
                    $baseDate = ($license->tanggal_berakhir && $license->tanggal_berakhir->isFuture()) 
                        ? $license->tanggal_berakhir 
                        : now();
                    $license->tanggal_berakhir = $baseDate->copy()->addMonths($durasiBulan);
                    $license->status = LicenseKey::STATUS_ACTIVE;
                }

                if ($tambahKuota > 0) {
                    $license->kuota_pengguna = (int) $license->kuota_pengguna + $tambahKuota;
                }

                $license->save();

                $this->billingRepository->create([
                    'instansi_id' => $instansi->id,
                    'user_id' => $user->id,
                    'order_id' => $orderId,
                    'tipe_transaksi' => $tipe,
                    'durasi_bulan' => $durasiBulan,
                    'tambah_kuota' => $tambahKuota,
                    'nominal' => $nominal,
                    'payment_method' => PaymentMethod::GOOGLE_PLAY_INAPP->value,
                    'payment_token' => $validated['purchase_token'] ?? null,
                    'status' => BillingTransactionStatus::SUCCESS->value,
                    'payload' => $validated,
                ]);
            });

            AuditLogger::billing(
                'payment_google_play_inapp',
                "Pembayaran In-App Google Play berhasil untuk institusi {$instansi->nama_instansi} (Order: {$orderId})",
                [
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'kuota_akhir' => $license->kuota_pengguna,
                ]
            );

            return [
                'status_code' => 200,
                'response' => [
                    'status' => true,
                    'message' => 'Langganan Google Play In-App berhasil diverifikasi.',
                    'license' => [
                        'kuota_pengguna' => $license->kuota_pengguna,
                        'tanggal_berakhir' => $license->tanggal_berakhir?->format('Y-m-d'),
                        'status' => $license->status,
                    ],
                ],
            ];
        } catch (\Throwable $e) {
            return [
                'status_code' => 500,
                'response' => [
                    'status' => false,
                    'message' => 'Gagal memproses langganan Google Play: ' . $e->getMessage(),
                ],
            ];
        }
    }

    public function generateInvoicePdf(int|string $id, User $actor, bool $isPreview = false): Response
    {
        $trx = $this->billingRepository->findTransactionWithDetails($id);

        if (!$actor->isSuperAdmin() && $trx->instansi_id != $actor->instansi_id) {
            abort(403, 'Akses tidak sah ke struk pembayaran instansi lain.');
        }

        $instansi = $trx->instansi ?: ($actor->sekolah ?: Instansi::find($trx->instansi_id));
        $trxUser = $trx->user ?: $actor;

        $logoBase64 = null;
        $logoPath = public_path('src/aku_sehat_icon.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('src/aku_sehat_black_icon.png');
        }
        if (file_exists($logoPath)) {
            $logoBase64 = base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('admin.billing.invoice-pdf', [
            'trx' => $trx,
            'instansi' => $instansi,
            'user' => $trxUser,
            'logoBase64' => $logoBase64,
            'tipeSebutan' => $instansi->tipe_sebutan,
            'labelMember' => $instansi->label_member_singular,
            'labelAdmin' => $instansi->label_admin,
        ]);

        $pdf->setPaper([0, 0, 240, 720], 'portrait');
        $fileName = 'STRUK-INVOICE-' . $trx->order_id . '.pdf';

        if ($isPreview) {
            return $pdf->stream($fileName);
        }

        return $pdf->download($fileName);
    }
}
