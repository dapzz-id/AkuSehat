<?php

namespace App\Modules\Billing\Subscription\Services;

use App\Models\BillingTransaction;
use App\Models\Instansi;
use App\Models\LicenseKey;
use App\Models\User;
use App\Models\PaymentMethodSetting;
use App\Services\MidtransService;
use App\Modules\Billing\Subscription\Repositories\BillingRepository;
use App\Modules\Billing\Subscription\Enums\BillingRate;
use App\Modules\Billing\Subscription\Enums\BillingTransactionType;
use App\Modules\Billing\Subscription\Enums\BillingTransactionStatus;
use App\Modules\Billing\Subscription\Enums\PaymentMethod;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class BillingService
{
    public const HARGA_PER_USER_BULAN = BillingRate::HARGA_PER_USER_BULAN->value;
    public const HARGA_PER_USER_TAHUN = BillingRate::HARGA_PER_USER_BULAN->value;

    public const BANK_BCA = 'BCA';
    public const NO_REK_BCA = '842-171-3359';
    public const ATAS_NAMA_BCA = 'Kadavi Raditya Alvino';
    public const WA_KONFIRMASI = '+62 895-3831-07479';
    public const WA_RAW = '62895383107479';

    public function __construct(
        protected BillingRepository $billingRepository
    ) {}

    public function calculateTotalAmount(string|BillingTransactionType $type, int $qty, int $months = 1): float
    {
        $rate = BillingRate::HARGA_PER_USER_BULAN;
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
                ? $primaryLicense->tanggal_berakhir->copy()->addMonths(1) 
                : now()->addMonths(1))
            : now()->addMonths(1);

        $subtotalPerpanjang = $kuotaPerpanjangDefault * self::HARGA_PER_USER_BULAN;
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
            'hargaPerUser' => self::HARGA_PER_USER_BULAN,
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
            'paymentMethods' => PaymentMethodSetting::getActiveMethods(),
            'midtransConfig' => [
                'is_active' => PaymentMethodSetting::isMethodActive('midtrans'),
                'client_key' => (new MidtransService())->getClientKey(),
                'snap_js' => (new MidtransService())->getSnapJsUrl(),
                'environment' => (new MidtransService())->getEnvironment(),
            ],
            'googlePayConfig' => [
                'is_active' => PaymentMethodSetting::isMethodActive('google_pay'),
                'environment' => PaymentMethodSetting::getMethodConfig('google_pay', 'environment', 'TEST'),
                'merchant_id' => PaymentMethodSetting::getMethodConfig('google_pay', 'merchant_id', ''),
                'merchant_name' => PaymentMethodSetting::getMethodConfig('google_pay', 'merchant_name', 'AkuSehat - Neura Cakrawira Solusi'),
            ],
            'bankDetails' => [
                'bank' => PaymentMethodSetting::getMethodConfig('manual_bca', 'bank_name', self::BANK_BCA),
                'account_number' => PaymentMethodSetting::getMethodConfig('manual_bca', 'account_number', self::NO_REK_BCA),
                'account_holder' => PaymentMethodSetting::getMethodConfig('manual_bca', 'account_name', self::ATAS_NAMA_BCA),
                'wa_phone' => PaymentMethodSetting::getMethodConfig('manual_bca', 'whatsapp_number', self::WA_KONFIRMASI),
                'wa_raw' => preg_replace('/[^0-9]/', '', (string) PaymentMethodSetting::getMethodConfig('manual_bca', 'whatsapp_number', self::WA_RAW)),
            ],
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
        $durasiBulan = (int) ($validated['durasi_bulan'] ?? 1);
        if (!in_array($durasiBulan, [1, 3, 6, 12, 24, 36], true)) {
            $durasiBulan = 1;
        }

        $durasiLabel = match ($durasiBulan) {
            1 => '1 Bulan',
            3 => '3 Bulan',
            6 => '6 Bulan',
            12 => '1 Tahun',
            24 => '2 Tahun',
            36 => '3 Tahun',
            default => "{$durasiBulan} Bulan",
        };

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
            $subtotal = $kuotaTarget * self::HARGA_PER_USER_BULAN * $durasiBulan;
            $tipeTransaksi = BillingTransactionType::PERPANJANG_LISENSI->value;
            $deskripsiItem = "Perpanjangan Paket Lisensi ({$targetLicense->key} - {$kuotaTarget} " . ($instansi->label_member_singular ?? 'User') . " / +{$durasiLabel})";
        } else {
            // Mode TAMBAH KUOTA BARU (Paket Baru Mandiri Sesuai Durasi Pilihan - Model Provider Internet / Bima Tri)
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
            $subtotal = $tambahKuota * self::HARGA_PER_USER_BULAN * $durasiBulan;
            $tipeTransaksi = BillingTransactionType::TAMBAH_KUOTA->value;
            $deskripsiItem = "Paket Kuota Baru (+{$tambahKuota} " . ($instansi->label_member_singular ?? 'User') . " / {$durasiLabel} Mandiri)";
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
            $resultData = DB::transaction(function () use ($instansi, $user, $validated, $modeTransaksi, $durasiBulan, $durasiLabel, $targetLicense, $tambahKuota, $subtotal, $pajak11, $totalNominal, $tipeTransaksi, $deskripsiItem, $orderIdInput) {
                if ($modeTransaksi === 'perpanjang') {
                    $lockedLicense = $this->billingRepository->lockLicenseForUpdate((string) $targetLicense->id) ?? $targetLicense;

                    $baseDate = ($lockedLicense->tanggal_berakhir && $lockedLicense->tanggal_berakhir->isFuture()) 
                        ? $lockedLicense->tanggal_berakhir 
                        : now();
                    $tanggalBerakhirBaru = $baseDate->copy()->addMonths($durasiBulan);

                    $lockedLicense->tanggal_berakhir = $tanggalBerakhirBaru;
                    $lockedLicense->status = LicenseKey::STATUS_ACTIVE;
                    $lockedLicense->save();

                    if (!$instansi->license_id_active) {
                        $instansi->update(['license_id_active' => $lockedLicense->id]);
                    }

                    $affectedLicense = $lockedLicense;
                    $durasiBulanTrx = $durasiBulan;
                } else {
                    // Tambah Slot Baru: Buat LicenseKey baru mandiri aktif sesuai durasi pilihan (seperti paket provider Bima Tri)
                    $newLicense = $this->billingRepository->createNewLicense(
                        instansiId: (string) $instansi->id,
                        quota: $tambahKuota,
                        months: $durasiBulan
                    );

                    if (!$instansi->license_id_active) {
                        $instansi->update(['license_id_active' => $newLicense->id]);
                    }

                    $tanggalBerakhirBaru = $newLicense->tanggal_berakhir;
                    $affectedLicense = $newLicense;
                    $durasiBulanTrx = $durasiBulan;
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
                        'harga_per_user' => self::HARGA_PER_USER_BULAN,
                        'durasi_bulan' => $durasiBulanTrx,
                        'durasi_label' => $durasiLabel,
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

    public function processManualTransfer(User $user, array $validated): array
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
        $durasiBulan = (int) ($validated['durasi_bulan'] ?? 1);
        if (!in_array($durasiBulan, [1, 3, 6, 12, 24, 36], true)) {
            $durasiBulan = 1;
        }

        $durasiLabel = match ($durasiBulan) {
            1 => '1 Bulan',
            3 => '3 Bulan',
            6 => '6 Bulan',
            12 => '1 Tahun',
            24 => '2 Tahun',
            36 => '3 Tahun',
            default => "{$durasiBulan} Bulan",
        };

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
            $subtotal = $kuotaTarget * self::HARGA_PER_USER_BULAN * $durasiBulan;
            $tipeTransaksi = BillingTransactionType::PERPANJANG_LISENSI->value;
            $deskripsiItem = "Perpanjangan Paket Lisensi ({$targetLicense->key} - {$kuotaTarget} " . ($instansi->label_member_singular ?? 'User') . " / +{$durasiLabel})";
        } else {
            // Mode TAMBAH KUOTA BARU
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
            $subtotal = $tambahKuota * self::HARGA_PER_USER_BULAN * $durasiBulan;
            $tipeTransaksi = BillingTransactionType::TAMBAH_KUOTA->value;
            $deskripsiItem = "Paket Kuota Baru (+{$tambahKuota} " . ($instansi->label_member_singular ?? 'User') . " / {$durasiLabel} Mandiri)";
        }

        $pajak11 = BillingRate::calculatePpn($subtotal);
        $totalNominal = $subtotal + $pajak11;

        $orderId = 'BCA-AKU-' . time() . '-' . strtoupper(Str::random(4));

        $bankName = self::BANK_BCA;
        $accountNumber = self::NO_REK_BCA;
        $accountHolder = self::ATAS_NAMA_BCA;
        $waPhone = self::WA_KONFIRMASI;
        $waPhoneRaw = self::WA_RAW;

        $namaInstansiFormatted = $instansi->nama_instansi ?? ($instansi->nama_sekolah ?? 'Instansi');
        $namaUserFormatted = $user->nama ?? ($user->name ?? 'Admin');

        $waText = "Halo Admin Aku Sehat, saya ingin konfirmasi pembayaran langganan:\n\n"
            . "• No. Order: {$orderId}\n"
            . "• Instansi: {$namaInstansiFormatted}\n"
            . "• Pemesan: {$namaUserFormatted}\n"
            . "• Rincian: {$deskripsiItem}\n"
            . "• Total Transfer: Rp " . number_format($totalNominal, 0, ',', '.') . "\n"
            . "• Bank Tujuan: BCA ({$accountNumber} a.n {$accountHolder})\n\n"
            . "Berikut saya lampirkan bukti transfernya. Mohon bantuannya untuk verifikasi dan aktivasi lisensi. Terima kasih!";

        $waUrl = "https://wa.me/{$waPhoneRaw}?text=" . rawurlencode($waText);

        try {
            $trx = $this->billingRepository->create([
                'instansi_id' => $instansi->id,
                'user_id' => $user->id,
                'order_id' => $orderId,
                'tipe_transaksi' => $tipeTransaksi,
                'durasi_bulan' => $durasiBulan,
                'tambah_kuota' => $tambahKuota,
                'nominal' => $totalNominal,
                'payment_method' => PaymentMethod::MANUAL_TRANSFER->value,
                'payment_token' => null,
                'status' => BillingTransactionStatus::PENDING->value,
                'payload' => [
                    'gateway' => 'Manual Transfer Bank BCA',
                    'bank_name' => $bankName,
                    'account_number' => $accountNumber,
                    'account_holder' => $accountHolder,
                    'confirmation_wa' => $waPhone,
                    'confirmation_wa_url' => $waUrl,
                    'timestamp' => now()->toIso8601String(),
                    'mode_transaksi' => $modeTransaksi,
                    'license_id_target' => $targetLicense ? $targetLicense->id : null,
                    'license_key' => $targetLicense ? $targetLicense->key : null,
                    'kuota_paket' => $modeTransaksi === 'perpanjang' ? $kuotaTarget : $tambahKuota,
                    'subtotal' => $subtotal,
                    'ppn_11' => $pajak11,
                    'harga_per_user' => self::HARGA_PER_USER_BULAN,
                    'durasi_bulan' => $durasiBulan,
                    'durasi_label' => $durasiLabel,
                    'deskripsi_item' => $deskripsiItem,
                ],
            ]);

            AuditLogger::billing(
                'manual_transfer_order_created',
                "Pesanan transfer manual BCA dibuat oleh {$user->name} untuk institusi {$namaInstansiFormatted} (Order: {$orderId}) senilai Rp " . number_format($totalNominal, 0, ',', '.'),
                [
                    'order_id' => $orderId,
                    'nominal' => $totalNominal,
                    'mode' => $modeTransaksi,
                    'bank' => $bankName,
                    'account_number' => $accountNumber,
                ]
            );

            return [
                'status_code' => 200,
                'response' => [
                    'success' => true,
                    'message' => 'Pesanan transfer manual BCA berhasil dibuat. Silakan transfer dan kirim bukti transaksi ke WhatsApp Admin.',
                    'data' => [
                        'transaction_id' => $trx->id,
                        'order_id' => $trx->order_id,
                        'mode_transaksi' => $modeTransaksi,
                        'total_dibayar' => number_format($totalNominal, 0, ',', '.'),
                        'nominal_raw' => $totalNominal,
                        'bank_name' => $bankName,
                        'account_number' => $accountNumber,
                        'account_holder' => $accountHolder,
                        'wa_phone' => $waPhone,
                        'wa_url' => $waUrl,
                        'deskripsi_item' => $deskripsiItem,
                        'durasi_label' => $durasiLabel,
                    ],
                ],
            ];
        } catch (\Throwable $e) {
            return [
                'status_code' => 500,
                'response' => [
                    'success' => false,
                    'message' => 'Gagal membuat pesanan transfer: ' . $e->getMessage(),
                ],
            ];
        }
    }

    public function approveManualTransfer(int|string $transactionId, User $approver): array
    {
        if (!$approver->isSuperAdmin()) {
            abort(403, 'Hanya SuperAdmin yang berhak menyetujui transaksi manual transfer.');
        }

        $trx = $this->billingRepository->findTransactionWithDetails($transactionId);
        if ($trx->status === BillingTransactionStatus::SUCCESS->value || $trx->status === BillingTransactionStatus::PAID->value) {
            return [
                'status_code' => 400,
                'response' => ['success' => false, 'message' => 'Transaksi ini sudah lunas / aktif sebelumnya.'],
            ];
        }

        try {
            DB::transaction(function () use ($trx, $approver) {
                $this->activateLicenseForTransaction($trx, 'manual_transfer', $approver);
            });

            return [
                'status_code' => 200,
                'response' => ['success' => true, 'message' => "Order {$trx->order_id} berhasil diverifikasi dan lisensi telah aktif."],
            ];
        } catch (\Throwable $e) {
            return [
                'status_code' => 500,
                'response' => ['success' => false, 'message' => 'Gagal menyetujui transaksi: ' . $e->getMessage()],
            ];
        }
    }

    public function rejectManualTransfer(int|string $transactionId, User $rejector, ?string $reason = null): array
    {
        if (!$rejector->isSuperAdmin()) {
            abort(403, 'Hanya SuperAdmin yang berhak menolak transaksi manual transfer.');
        }

        $trx = $this->billingRepository->findTransactionWithDetails($transactionId);
        if ($trx->status === BillingTransactionStatus::SUCCESS->value || $trx->status === BillingTransactionStatus::PAID->value) {
            return [
                'status_code' => 400,
                'response' => ['success' => false, 'message' => 'Transaksi ini sudah lunas, tidak dapat dibatalkan/ditolak.'],
            ];
        }

        $trx->status = BillingTransactionStatus::FAILED->value;
        $payload = is_array($trx->payload) ? $trx->payload : [];
        $payload['rejection_reason'] = $reason ?: 'Ditolak oleh SuperAdmin (Bukti transfer tidak valid/dana tidak masuk)';
        $payload['rejected_by'] = $rejector->name;
        $payload['rejected_at'] = now()->toIso8601String();
        $trx->payload = $payload;
        $trx->save();

        AuditLogger::billing(
            'manual_transfer_rejected',
            "Pesanan manual transfer BCA untuk Order {$trx->order_id} ditolak oleh SuperAdmin {$rejector->name}: " . ($reason ?: 'Bukti transfer tidak valid'),
            ['order_id' => $trx->order_id, 'nominal' => $trx->nominal, 'reason' => $reason]
        );

        return [
            'status_code' => 200,
            'response' => ['success' => true, 'message' => "Order {$trx->order_id} berhasil ditolak / dibatalkan."],
        ];
    }

    /**
     * Aktivasi lisensi atau perpanjangan durasi untuk transaksi yang telah berhasil dibayar.
     */
    public function activateLicenseForTransaction(BillingTransaction $trx, string $triggerSource = 'midtrans', ?User $actor = null): void
    {
        $instansi = $trx->instansi;
        $modeTransaksi = $trx->payload['mode_transaksi'] ?? ($trx->tipe_transaksi === 'perpanjang_lisensi' ? 'perpanjang' : 'tambah_kuota');
        $durasiBulan = (int) ($trx->durasi_bulan ?: 1);

        if ($modeTransaksi === 'perpanjang') {
            $licenseId = $trx->payload['license_id_target'] ?? null;
            $targetLicense = $licenseId ? $this->billingRepository->findLicenseById($licenseId) : null;
            if (!$targetLicense || $targetLicense->instansi_id !== $instansi->id) {
                $targetLicense = $instansi->getActiveLicense() ?? $this->billingRepository->getActiveOrLatestLicense((string) $instansi->id);
            }
            if (!$targetLicense) {
                $targetLicense = $this->billingRepository->createDefaultLicense((string) $instansi->id, 8);
            }

            $locked = $this->billingRepository->lockLicenseForUpdate((string) $targetLicense->id) ?? $targetLicense;
            $baseDate = ($locked->tanggal_berakhir && $locked->tanggal_berakhir->isFuture()) ? $locked->tanggal_berakhir : now();
            $locked->tanggal_berakhir = $baseDate->copy()->addMonths($durasiBulan);
            $locked->status = LicenseKey::STATUS_ACTIVE;
            $locked->save();

            if (!$instansi->license_id_active) {
                $instansi->update(['license_id_active' => $locked->id]);
            }
        } else {
            $newLicense = $this->billingRepository->createNewLicense(
                instansiId: (string) $instansi->id,
                quota: $trx->tambah_kuota,
                months: $durasiBulan
            );

            if (!$instansi->license_id_active) {
                $instansi->update(['license_id_active' => $newLicense->id]);
            }
        }

        $trx->status = BillingTransactionStatus::SUCCESS->value;
        $trx->save();

        $actorName = $actor ? $actor->name : 'Sistem Gateway (' . ucfirst($triggerSource) . ')';
        AuditLogger::billing(
            "{$triggerSource}_success",
            "Pembayaran Order {$trx->order_id} via {$trx->payment_method} berhasil diverifikasi dan diaktifkan oleh {$actorName}",
            ['order_id' => $trx->order_id, 'nominal' => $trx->nominal, 'trigger' => $triggerSource]
        );
    }

    /**
     * Proses pembuatan sesi Snap Midtrans untuk pembayaran instan
     */
    public function processMidtransSnap(User $user, array $validated): array
    {
        try {
            if (!PaymentMethodSetting::isMethodActive('midtrans')) {
                return [
                    'status_code' => 403,
                    'response' => [
                        'success' => false,
                        'message' => 'Metode pembayaran Midtrans sedang dinonaktifkan oleh Administrator.',
                    ],
                ];
            }

            $instansi = $user->sekolah ?: Instansi::find($user->instansi_id);
            if (!$instansi) {
                return [
                    'status_code' => 404,
                    'response' => ['success' => false, 'message' => 'Instansi Anda tidak ditemukan.'],
                ];
            }

            $modeTransaksi = $validated['mode_transaksi'] ?? 'perpanjang';
            $durasiBulan = (int) ($validated['durasi_bulan'] ?? 1);
            $tambahKuota = (int) ($validated['tambah_kuota'] ?? 0);

            if ($modeTransaksi === 'perpanjang') {
                $licenseId = $validated['license_id'] ?? null;
                $targetLicense = $licenseId ? $this->billingRepository->findLicenseById($licenseId) : null;
                if (!$targetLicense || $targetLicense->instansi_id !== $instansi->id) {
                    $targetLicense = $instansi->getActiveLicense() ?? $this->billingRepository->getActiveOrLatestLicense((string) $instansi->id);
                }
                if (!$targetLicense) {
                    return [
                        'status_code' => 400,
                        'response' => ['success' => false, 'message' => 'Tidak ditemukan paket lisensi untuk diperpanjang.'],
                    ];
                }
                $kuotaTarget = (int) $targetLicense->kuota_pengguna;
                $subtotal = $this->calculateTotalAmount(BillingTransactionType::PERPANJANG_LISENSI, $kuotaTarget, $durasiBulan);
                $tipeTransaksi = BillingTransactionType::PERPANJANG_LISENSI->value;
                $deskripsiItem = "Perpanjangan Lisensi ({$kuotaTarget} Siswa, {$durasiBulan} Bulan)";
                $targetLicenseId = $targetLicense->id;
            } else {
                if ($tambahKuota <= 0) {
                    return [
                        'status_code' => 400,
                        'response' => ['success' => false, 'message' => 'Jumlah kuota tambahan harus lebih dari 0.'],
                    ];
                }
                $subtotal = $this->calculateTotalAmount(BillingTransactionType::TAMBAH_KUOTA, $tambahKuota, $durasiBulan);
                $tipeTransaksi = BillingTransactionType::TAMBAH_KUOTA->value;
                $deskripsiItem = "Tambah Kuota Baru (+{$tambahKuota} Siswa, {$durasiBulan} Bulan)";
                $targetLicenseId = null;
            }

            $pajak11 = round($subtotal * 0.11);
            $totalNominal = $subtotal + $pajak11;
            $orderId = 'MID-AKU-' . time() . '-' . strtoupper(Str::random(4));

            $midtransService = new MidtransService();
            $userEmail = !empty($user->email)
                ? $user->email
                : ('admin.' . ($user->username ?: 'instansi') . '@akusehat.neuracakrawira.asia');
            $userPhone = !empty($user->no_hp) ? $user->no_hp : '081234567890';
            $customerName = !empty($instansi->nama_instansi) ? $instansi->nama_instansi : ($user->nama ?: 'Admin Instansi');

            $snapParams = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) round($totalNominal),
                ],
                'customer_details' => [
                    'first_name' => Str::limit($customerName, 45, ''),
                    'email' => $userEmail,
                    'phone' => $userPhone,
                ],
                'item_details' => [
                    [
                        'id' => 'AKU-' . strtoupper($modeTransaksi),
                        'price' => (int) round($totalNominal),
                        'quantity' => 1,
                        'name' => Str::limit($deskripsiItem, 50, ''),
                    ],
                ],
            ];

            $snapResult = $midtransService->createSnapTransaction($snapParams);

            if (!$snapResult['success']) {
                return [
                    'status_code' => 400,
                    'response' => [
                        'success' => false,
                        'message' => $snapResult['message'] ?? 'Gagal membuat transaksi Midtrans Snap.',
                    ],
                ];
            }

            $snapToken = $snapResult['token'];
            $redirectUrl = $snapResult['redirect_url'];

            $trx = $this->billingRepository->create([
                'instansi_id' => $instansi->id,
                'user_id' => $user->id,
                'order_id' => $orderId,
                'tipe_transaksi' => $tipeTransaksi,
                'durasi_bulan' => $durasiBulan,
                'tambah_kuota' => $modeTransaksi === 'perpanjang' ? 0 : $tambahKuota,
                'nominal' => $totalNominal,
                'payment_method' => PaymentMethod::MIDTRANS->value,
                'payment_token' => $snapToken,
                'status' => BillingTransactionStatus::PENDING->value,
                'payload' => [
                    'gateway' => 'Midtrans Snap',
                    'environment' => $midtransService->getEnvironment(),
                    'mode_transaksi' => $modeTransaksi,
                    'license_id_target' => $targetLicenseId,
                    'subtotal' => $subtotal,
                    'ppn_11' => $pajak11,
                    'durasi_bulan' => $durasiBulan,
                    'deskripsi_item' => $deskripsiItem,
                    'redirect_url' => $redirectUrl,
                    'snap_token' => $snapToken,
                    'created_at' => now()->toIso8601String(),
                ],
            ]);

            return [
                'status_code' => 200,
                'response' => [
                    'success' => true,
                    'order_id' => $orderId,
                    'snap_token' => $snapToken,
                    'redirect_url' => $redirectUrl,
                    'total_nominal' => $totalNominal,
                    'total_formatted' => 'Rp ' . number_format($totalNominal, 0, ',', '.'),
                    'deskripsi_item' => $deskripsiItem,
                    'message' => 'Sesi pembayaran Midtrans berhasil dibuat.',
                ],
            ];
        } catch (\Throwable $e) {
            Log::error('processMidtransSnap exception: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return [
                'status_code' => 500,
                'response' => [
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat menghubungi Midtrans: ' . $e->getMessage(),
                ],
            ];
        }
    }

    /**
     * Webhook Notifikasi dari Midtrans (S2S Callback)
     */
    public function handleMidtransNotification(array $payload): array
    {
        $midtransService = new MidtransService();

        // Validasi signature dari Midtrans
        if (!$midtransService->verifySignature($payload)) {
            Log::warning('Midtrans Webhook: Signature tidak valid', $payload);
            return [
                'status_code' => 403,
                'response' => ['success' => false, 'message' => 'Invalid signature key.'],
            ];
        }

        $orderId = $payload['order_id'] ?? '';
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? null;

        $trx = BillingTransaction::where('order_id', $orderId)->first();
        if (!$trx) {
            Log::warning("Midtrans Webhook: Order {$orderId} tidak ditemukan di database.");
            return [
                'status_code' => 404,
                'response' => ['success' => false, 'message' => 'Order tidak ditemukan.'],
            ];
        }

        if ($trx->status === BillingTransactionStatus::SUCCESS->value) {
            return [
                'status_code' => 200,
                'response' => ['success' => true, 'message' => 'Order sudah lunas sebelumnya.'],
            ];
        }

        try {
            if ($midtransService->isTransactionSettled($transactionStatus, $fraudStatus)) {
                DB::transaction(function () use ($trx, $payload) {
                    $this->activateLicenseForTransaction($trx, 'midtrans_webhook');
                    $existingPayload = is_array($trx->payload) ? $trx->payload : [];
                    $existingPayload['midtrans_notification'] = $payload;
                    $existingPayload['settled_at'] = now()->toIso8601String();
                    $trx->payload = $existingPayload;
                    $trx->save();
                });
            } elseif (in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'])) {
                $trx->status = BillingTransactionStatus::FAILED->value;
                $existingPayload = is_array($trx->payload) ? $trx->payload : [];
                $existingPayload['midtrans_failure'] = $payload;
                $trx->payload = $existingPayload;
                $trx->save();
            }

            return [
                'status_code' => 200,
                'response' => ['success' => true, 'message' => 'Notifikasi Midtrans berhasil diproses.'],
            ];
        } catch (\Throwable $e) {
            Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return [
                'status_code' => 500,
                'response' => ['success' => false, 'message' => 'Error: ' . $e->getMessage()],
            ];
        }
    }

    /**
     * Cek status pembayaran Midtrans dari frontend setelah modal Snap ditutup
     */
    public function checkMidtransStatus(User $user, string $orderId): array
    {
        $trx = BillingTransaction::where('order_id', $orderId)->first();
        if (!$trx) {
            return [
                'status_code' => 404,
                'response' => ['success' => false, 'message' => 'Transaksi tidak ditemukan.'],
            ];
        }

        $buildSuccessData = function (BillingTransaction $transaction) {
            $instansi = $transaction->instansi ?: Instansi::find($transaction->instansi_id);
            $activeLicense = $instansi ? $instansi->getActiveLicense() : null;
            $totalActiveQuota = $instansi ? $instansi->getTotalActiveQuota() : 0;
            $tanggalBerakhir = $activeLicense ? $activeLicense->tanggal_berakhir : null;

            return [
                'transaction_id' => $transaction->id,
                'order_id' => $transaction->order_id,
                'total_dibayar' => number_format($transaction->nominal, 0, ',', '.'),
                'kuota_sekarang' => $totalActiveQuota,
                'license_key' => $activeLicense ? $activeLicense->key : ($transaction->payload['license_key'] ?? '-'),
                'tanggal_berakhir' => $tanggalBerakhir ? $tanggalBerakhir->format('d/m/Y') : '-',
            ];
        };

        if ($trx->status === BillingTransactionStatus::SUCCESS->value || $trx->status === BillingTransactionStatus::PAID->value) {
            return [
                'status_code' => 200,
                'response' => [
                    'success' => true,
                    'status' => 'success',
                    'message' => 'Pembayaran telah berhasil diverifikasi dan lisensi aktif!',
                    'data' => $buildSuccessData($trx),
                ],
            ];
        }

        $midtransService = new MidtransService();
        $check = $midtransService->getTransactionStatus($orderId);

        if ($check['success'] && isset($check['data'])) {
            $data = $check['data'];
            $status = $data['transaction_status'] ?? '';
            $fraud = $data['fraud_status'] ?? null;

            if ($midtransService->isTransactionSettled($status, $fraud)) {
                DB::transaction(function () use ($trx, $data, $user) {
                    $this->activateLicenseForTransaction($trx, 'midtrans_check', $user);
                    $existingPayload = is_array($trx->payload) ? $trx->payload : [];
                    $existingPayload['midtrans_check'] = $data;
                    $trx->payload = $existingPayload;
                    $trx->save();
                });

                $trx->refresh();

                return [
                    'status_code' => 200,
                    'response' => [
                        'success' => true,
                        'status' => 'success',
                        'message' => 'Pembayaran Midtrans berhasil dikonfirmasi dan lisensi telah aktif!',
                        'data' => $buildSuccessData($trx),
                    ],
                ];
            }

            return [
                'status_code' => 200,
                'response' => [
                    'success' => true,
                    'status' => $status,
                    'message' => 'Status pembayaran Midtrans: ' . $status,
                ],
            ];
        }

        return [
            'status_code' => 200,
            'response' => [
                'success' => false,
                'status' => $trx->status,
                'message' => 'Menunggu konfirmasi pembayaran.',
            ],
        ];
    }

    /**
     * SuperAdmin: Toggle aktif/nonaktif metode pembayaran
     */
    public function togglePaymentMethod(User $actor, string $methodKey): array
    {
        if (!$actor->isSuperAdmin()) {
            abort(403, 'Hanya SuperAdmin yang dapat mengelola metode pembayaran.');
        }

        $method = PaymentMethodSetting::where('method_key', $methodKey)->first();
        if (!$method) {
            return [
                'status_code' => 404,
                'response' => ['success' => false, 'message' => "Metode pembayaran '{$methodKey}' tidak ditemukan."],
            ];
        }

        $method->is_active = !$method->is_active;
        $method->save();

        $statusStr = $method->is_active ? 'Diaktifkan' : 'Dinonaktifkan';
        AuditLogger::billing(
            'payment_method_toggled',
            "Metode pembayaran {$method->name} ({$methodKey}) berhasil {$statusStr} oleh SuperAdmin {$actor->name}",
            ['method_key' => $methodKey, 'is_active' => $method->is_active]
        );

        return [
            'status_code' => 200,
            'response' => [
                'success' => true,
                'is_active' => $method->is_active,
                'message' => "Metode pembayaran {$method->name} berhasil {$statusStr}.",
            ],
        ];
    }

    /**
     * SuperAdmin: Update konfigurasi kredensial metode pembayaran
     */
    public function updatePaymentMethodSetting(User $actor, string $methodKey, array $data): array
    {
        if (!$actor->isSuperAdmin()) {
            abort(403, 'Hanya SuperAdmin yang dapat mengelola metode pembayaran.');
        }

        $method = PaymentMethodSetting::where('method_key', $methodKey)->first();
        if (!$method) {
            return [
                'status_code' => 404,
                'response' => ['success' => false, 'message' => "Metode pembayaran '{$methodKey}' tidak ditemukan."],
            ];
        }

        if (isset($data['is_active'])) {
            $method->is_active = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
        }

        $currentConfig = is_array($method->config) ? $method->config : [];

        if ($methodKey === 'manual_bca') {
            if (isset($data['bank_name'])) {
                $currentConfig['bank_name'] = trim($data['bank_name']);
            }
            if (isset($data['account_number'])) {
                $currentConfig['account_number'] = trim($data['account_number']);
            }
            if (isset($data['account_name'])) {
                $currentConfig['account_name'] = trim($data['account_name']);
            }
            if (isset($data['whatsapp_number'])) {
                $currentConfig['whatsapp_number'] = trim($data['whatsapp_number']);
            }
            $method->config = $currentConfig;
        } else {
            // Midtrans & Google Pay menggunakan kredensial .env (tidak disimpan di database)
            $method->config = [];
        }

        $method->save();

        AuditLogger::billing(
            'payment_method_updated',
            "Konfigurasi metode pembayaran {$method->name} ({$methodKey}) berhasil diperbarui oleh SuperAdmin {$actor->name}",
            ['method_key' => $methodKey, 'is_active' => $method->is_active]
        );

        return [
            'status_code' => 200,
            'response' => [
                'success' => true,
                'message' => "Pengaturan {$method->name} berhasil disimpan.",
                'method' => $method,
            ],
        ];
    }

    public function getSuperAdminBillingData(array|string|null $filters = null, int $perPage = 15): array
    {
        $filterArr = is_array($filters) ? $filters : ['status' => $filters];
        $transactions = $this->billingRepository->getSuperAdminTransactions($filterArr, $perPage);
        $stats = $this->billingRepository->getTransactionSummaryStats();

        return [
            'transactions' => $transactions,
            'stats' => $stats,
            'currentStatus' => $filterArr['status'] ?? 'all',
            'paymentMethodsSettings' => PaymentMethodSetting::getAllSettings(),
            'midtransService' => new MidtransService(),
            'filters' => [
                'search' => $filterArr['search'] ?? $filterArr['q'] ?? '',
                'payment_method' => $filterArr['payment_method'] ?? 'all',
                'start_date' => $filterArr['start_date'] ?? '',
                'end_date' => $filterArr['end_date'] ?? '',
                'status' => $filterArr['status'] ?? 'all',
            ],
        ];
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

        $durasiBulan = 1;
        $subtotal = $tambahKuota * self::HARGA_PER_USER_BULAN;
        $nominal = $subtotal + (int) round($subtotal * 0.11);
        $tipe = BillingTransactionType::LISENSI_BULANAN->value;

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

    /**
     * Alihkan metode pembayaran untuk transaksi yang masih pending (misal Manual BCA <-> Midtrans Snap).
     */
    public function switchPaymentMethod(User $actor, int|string $id, string $targetMethod): array
    {
        $trx = BillingTransaction::find($id);
        if (!$trx) {
            return [
                'status_code' => 404,
                'response' => ['success' => false, 'message' => 'Transaksi tidak ditemukan.'],
            ];
        }

        if (!$actor->isSuperAdmin() && $trx->instansi_id !== $actor->instansi_id) {
            return [
                'status_code' => 403,
                'response' => ['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk transaksi ini.'],
            ];
        }

        if ($trx->status !== BillingTransactionStatus::PENDING->value) {
            return [
                'status_code' => 400,
                'response' => ['success' => false, 'message' => 'Hanya transaksi berstatus Menunggu Pembayaran (Pending) yang dapat dialihkan.'],
            ];
        }

        $instansi = $trx->instansi ?: ($actor->sekolah ?: Instansi::find($trx->instansi_id));
        $user = $trx->user ?: $actor;
        $namaInstansiFormatted = $instansi ? ($instansi->nama_instansi ?? ($instansi->nama_sekolah ?? 'Instansi')) : 'Instansi';
        $namaUserFormatted = $user->nama ?? ($user->name ?? 'Admin');
        $deskripsiItem = $trx->payload['deskripsi_item'] ?? ($trx->tipe_transaksi === 'perpanjang_lisensi' ? 'Perpanjangan Lisensi' : 'Tambah Paket Kuota');

        if ($targetMethod === 'midtrans') {
            if (!PaymentMethodSetting::isMethodActive('midtrans')) {
                return [
                    'status_code' => 400,
                    'response' => ['success' => false, 'message' => 'Metode Midtrans Snap sedang dinonaktifkan oleh Administrator.'],
                ];
            }

            $midtransService = new MidtransService();
            $newOrderId = 'MID-AKU-' . time() . '-' . strtoupper(Str::random(4));

            $customerDetails = [
                'first_name' => $user->name,
                'email' => $user->email ?: ('billing.' . ($instansi->id ?? 'org') . '@akusehat.id'),
                'phone' => $user->no_telp ?: '081234567890',
            ];

            $snapResult = $midtransService->createSnapToken(
                $newOrderId,
                (int) round($trx->nominal),
                $customerDetails,
                [
                    [
                        'id' => $trx->tipe_transaksi,
                        'price' => (int) round($trx->nominal),
                        'quantity' => 1,
                        'name' => mb_strimwidth($deskripsiItem, 0, 48, '...'),
                    ],
                ]
            );

            if (!$snapResult['success']) {
                return [
                    'status_code' => 400,
                    'response' => [
                        'success' => false,
                        'message' => $snapResult['message'] ?? 'Gagal membuat sesi pembayaran Midtrans Snap.',
                    ],
                ];
            }

            $snapToken = $snapResult['token'];
            $redirectUrl = $snapResult['redirect_url'];

            $payload = is_array($trx->payload) ? $trx->payload : [];
            $payload['previous_order_id'] = $trx->order_id;
            $payload['previous_method'] = $trx->payment_method;
            $payload['gateway'] = 'Midtrans Snap';
            $payload['environment'] = $midtransService->getEnvironment();
            $payload['snap_token'] = $snapToken;
            $payload['redirect_url'] = $redirectUrl;
            $payload['switched_at'] = now()->toIso8601String();
            $payload['switched_by'] = $actor->name;

            $trx->update([
                'order_id' => $newOrderId,
                'payment_method' => PaymentMethod::MIDTRANS->value,
                'payment_token' => $snapToken,
                'payload' => $payload,
            ]);

            AuditLogger::billing(
                'billing_method_switched',
                "Metode transaksi {$payload['previous_order_id']} dialihkan ke Midtrans Snap (Order: {$newOrderId}) oleh {$actor->name}."
            );

            return [
                'status_code' => 200,
                'response' => [
                    'success' => true,
                    'message' => 'Metode pembayaran berhasil dialihkan ke Midtrans Snap.',
                    'order_id' => $newOrderId,
                    'payment_method' => 'midtrans',
                    'snap_token' => $snapToken,
                    'redirect_url' => $redirectUrl,
                ],
            ];
        }

        if ($targetMethod === 'manual_transfer' || $targetMethod === 'manual_bca') {
            if (!PaymentMethodSetting::isMethodActive('manual_transfer')) {
                return [
                    'status_code' => 400,
                    'response' => ['success' => false, 'message' => 'Metode Transfer Bank BCA sedang dinonaktifkan oleh Administrator.'],
                ];
            }

            $newOrderId = 'BCA-AKU-' . time() . '-' . strtoupper(Str::random(4));

            $bankSetting = PaymentMethodSetting::getSetting('manual_transfer');
            $config = $bankSetting?->config ?? [];
            $bankName = $config['bank_name'] ?? self::BANK_BCA;
            $accountNumber = $config['account_number'] ?? self::NO_REK_BCA;
            $accountHolder = $config['account_holder'] ?? self::ATAS_NAMA_BCA;
            $waPhone = $config['confirmation_wa'] ?? self::WA_KONFIRMASI;
            $waPhoneRaw = preg_replace('/[^0-9]/', '', $waPhone) ?: self::WA_RAW;

            $waText = "Halo Admin Aku Sehat, saya ingin konfirmasi pembayaran langganan:\n\n"
                . "• No. Order: {$newOrderId}\n"
                . "• Instansi: {$namaInstansiFormatted}\n"
                . "• Pemesan: {$namaUserFormatted}\n"
                . "• Rincian: {$deskripsiItem}\n"
                . "• Total Transfer: Rp " . number_format($trx->nominal, 0, ',', '.') . "\n"
                . "• Bank Tujuan: {$bankName} ({$accountNumber} a.n {$accountHolder})\n\n"
                . "Berikut saya lampirkan bukti transfernya. Mohon bantuannya untuk verifikasi dan aktivasi lisensi. Terima kasih!";

            $waUrl = "https://wa.me/{$waPhoneRaw}?text=" . rawurlencode($waText);

            $payload = is_array($trx->payload) ? $trx->payload : [];
            $payload['previous_order_id'] = $trx->order_id;
            $payload['previous_method'] = $trx->payment_method;
            $payload['gateway'] = 'Manual Transfer Bank BCA';
            $payload['bank_name'] = $bankName;
            $payload['account_number'] = $accountNumber;
            $payload['account_holder'] = $accountHolder;
            $payload['confirmation_wa'] = $waPhone;
            $payload['confirmation_wa_url'] = $waUrl;
            $payload['switched_at'] = now()->toIso8601String();
            $payload['switched_by'] = $actor->name;

            $trx->update([
                'order_id' => $newOrderId,
                'payment_method' => PaymentMethod::MANUAL_TRANSFER->value,
                'payment_token' => null,
                'payload' => $payload,
            ]);

            AuditLogger::billing(
                'billing_method_switched',
                "Metode transaksi {$payload['previous_order_id']} dialihkan ke Transfer Manual BCA (Order: {$newOrderId}) oleh {$actor->name}."
            );

            return [
                'status_code' => 200,
                'response' => [
                    'success' => true,
                    'message' => 'Metode pembayaran berhasil dialihkan ke Transfer Bank BCA.',
                    'order_id' => $newOrderId,
                    'payment_method' => 'manual_transfer',
                    'wa_url' => $waUrl,
                    'bank_info' => [
                        'bank_name' => $bankName,
                        'account_number' => $accountNumber,
                        'account_holder' => $accountHolder,
                        'wa_phone' => $waPhone,
                    ],
                ],
            ];
        }

        return [
            'status_code' => 400,
            'response' => ['success' => false, 'message' => 'Metode pembayaran tujuan tidak didukung.'],
        ];
    }

    /**
     * Batalkan pesanan billing yang masih berstatus pending.
     */
    public function cancelBillingTransaction(User $actor, int|string $id, ?string $reason = null): array
    {
        $trx = BillingTransaction::find($id);
        if (!$trx) {
            return [
                'status_code' => 404,
                'response' => ['success' => false, 'message' => 'Transaksi tidak ditemukan.'],
            ];
        }

        if (!$actor->isSuperAdmin() && $trx->instansi_id !== $actor->instansi_id) {
            return [
                'status_code' => 403,
                'response' => ['success' => false, 'message' => 'Akses ditolak.'],
            ];
        }

        if ($trx->status !== BillingTransactionStatus::PENDING->value) {
            return [
                'status_code' => 400,
                'response' => ['success' => false, 'message' => 'Hanya transaksi berstatus Menunggu Pembayaran (Pending) yang dapat dibatalkan.'],
            ];
        }

        $payload = is_array($trx->payload) ? $trx->payload : [];
        $payload['cancelled_at'] = now()->toIso8601String();
        $payload['cancelled_by'] = $actor->name;
        if ($reason) {
            $payload['cancel_reason'] = $reason;
        }

        $trx->update([
            'status' => BillingTransactionStatus::CANCELLED->value,
            'payload' => $payload,
        ]);

        AuditLogger::billing(
            'billing_order_cancelled',
            "Pesanan {$trx->order_id} dibatalkan oleh {$actor->name}."
        );

        return [
            'status_code' => 200,
            'response' => [
                'success' => true,
                'message' => "Pesanan #{$trx->order_id} berhasil dibatalkan.",
            ],
        ];
    }
}
