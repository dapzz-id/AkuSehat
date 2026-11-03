@extends('layouts.app')

@section('title', 'Manajemen Billing & Transaksi - SuperAdmin')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 flex items-center gap-2.5">
                <i class="fas fa-receipt text-emerald-600"></i>
                <span>Manajemen Billing & Transaksi</span>
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Kelola dan verifikasi pembayaran manual transfer BCA, aktivasi lisensi instansi, dan pantau seluruh transaksi aplikasi.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('superadmin.license.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors shadow-xs text-xs sm:text-sm font-semibold flex items-center gap-2">
                <i class="fas fa-key text-amber-500"></i>
                <span>Daftar Lisensi Aktif</span>
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-xl shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
                <span class="text-sm font-semibold">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Statistic Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Pendapatan --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Pendapatan</span>
                <div class="text-xl font-black text-gray-900 mt-1">
                    Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
                </div>
                <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1 mt-0.5">
                    <i class="fas fa-circle-check"></i> Transaksi Lunas
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-sack-dollar"></i>
            </div>
        </div>

        {{-- Card 2: Menunggu Verifikasi --}}
        <div class="bg-white p-5 rounded-2xl border {{ $stats['pending_count'] > 0 ? 'border-amber-300 ring-2 ring-amber-500/20' : 'border-gray-100' }} shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Menunggu Verifikasi</span>
                <div class="text-xl font-black text-amber-600 mt-1">
                    {{ $stats['pending_count'] }} Pesanan
                </div>
                <span class="text-[11px] {{ $stats['pending_count'] > 0 ? 'text-amber-700 font-bold' : 'text-gray-400' }} mt-0.5 block">
                    {{ $stats['pending_count'] > 0 ? 'Perlu tindakan SuperAdmin' : 'Semua transaksi terverifikasi' }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-hourglass-half"></i>
            </div>
        </div>

        {{-- Card 3: Transaksi Berhasil --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Berhasil / Lunas</span>
                <div class="text-xl font-black text-gray-900 mt-1">
                    {{ $stats['success_count'] }}
                </div>
                <span class="text-[11px] text-gray-500 mt-0.5 block">
                    Lisensi telah aktif
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-circle-check"></i>
            </div>
        </div>

        {{-- Card 4: Total Transaksi --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-soft flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Semua Order</span>
                <div class="text-xl font-black text-gray-900 mt-1">
                    {{ $stats['total_count'] }}
                </div>
                <span class="text-[11px] text-gray-500 mt-0.5 block">
                    Sejak sistem beroperasi
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-xs">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
    </div>

    <!-- MASTER CONTROL: PENGATURAN METODE PEMBAYARAN (AKTIF / NONAKTIF) -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-soft p-5 sm:p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-gray-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h2 class="text-base sm:text-lg font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                        <i class="fas fa-toggle-on text-emerald-600"></i>
                        <span>Master Kontrol Metode Pembayaran</span>
                    </h2>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">
                    Aktifkan atau nonaktifkan metode pembayaran yang muncul di halaman checkout sekolah/instansi secara instan.
                </p>
            </div>
            <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-3 py-1 rounded-xl self-start sm:self-auto flex items-center gap-1.5 border border-slate-200">
                <i class="fas fa-mobile-screen-button text-emerald-600"></i>
                <span>Tampilan Responsif</span>
            </span>
        </div>

        @php
            $bcaSetting = $paymentMethodsSettings->firstWhere('method_key', 'manual_bca');
            $midtransSetting = $paymentMethodsSettings->firstWhere('method_key', 'midtrans');
            $gpaySetting = $paymentMethodsSettings->firstWhere('method_key', 'google_pay');

            $midtransConfig = \App\Models\PaymentMethodSetting::getMethodConfig('midtrans');
            $bcaConfig = is_array($bcaSetting?->config) ? $bcaSetting->config : [];
            $gpayConfig = \App\Models\PaymentMethodSetting::getMethodConfig('google_pay');

            $rawServerKey = $midtransConfig['server_key'] ?? '';
            $rawClientKey = $midtransConfig['client_key'] ?? '';
            $hasServerKey = !empty($rawServerKey) && !str_contains($rawServerKey, 'xxxx');
            $hasClientKey = !empty($rawClientKey) && !str_contains($rawClientKey, 'xxxx');
            $maskedClientKey = !empty($rawClientKey)
                ? (strlen($rawClientKey) > 12 ? substr($rawClientKey, 0, 10) . '••••' . substr($rawClientKey, -4) : '••••••••')
                : 'Belum diatur di .env';
            $maskedServerKey = !empty($rawServerKey)
                ? (strlen($rawServerKey) > 12 ? substr($rawServerKey, 0, 10) . '••••••••••••' : '••••••••••••')
                : 'Belum diatur di .env';

            $rawGpayMerchantId = $gpayConfig['merchant_id'] ?? '';
            $hasGpayMerchantId = !empty($rawGpayMerchantId) && !str_contains($rawGpayMerchantId, 'XXXX');
            $maskedGpayMerchantId = !empty($rawGpayMerchantId)
                ? (strlen($rawGpayMerchantId) > 8 ? substr($rawGpayMerchantId, 0, 6) . '••••' . substr($rawGpayMerchantId, -3) : $rawGpayMerchantId)
                : 'Belum diatur di .env';

            $vaultService = app(\App\Services\Security\DataVaultEncryptionService::class);
            $hasDedicatedDataSecret = $vaultService->hasDedicatedSecretKey();
            $bcaRekPlain = $bcaConfig['account_number'] ?? '842-171-3359';
            $bcaNamePlain = $bcaConfig['account_name'] ?? 'Kadavi Raditya Alvino';
            $bcaWaPlain = $bcaConfig['whatsapp_number'] ?? '+62 895-3831-07479';
            $bcaRekMasked = \App\Services\Security\DataVaultEncryptionService::maskAccountNumber($bcaRekPlain);
            $bcaNameMasked = \App\Services\Security\DataVaultEncryptionService::maskAccountName($bcaNamePlain);
            $bcaWaMasked = \App\Services\Security\DataVaultEncryptionService::maskPhoneNumber($bcaWaPlain);
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- KARTU 1: TRANSFER BANK BCA (MANUAL) --}}
            <div class="rounded-2xl border p-4 transition-all duration-200 flex flex-col justify-between {{ $bcaSetting?->is_active ? 'border-blue-200 bg-blue-50/40 shadow-xs' : 'border-gray-200 bg-gray-50/70 opacity-75' }}">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                BCA
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-gray-900">Transfer Bank BCA</h3>
                                <span class="text-[10px] text-gray-500">Transfer Manual & WA</span>
                            </div>
                        </div>

                        {{-- Toggle Switch --}}
                        <form method="POST" action="{{ route('superadmin.billing.payment-methods.toggle', 'manual_bca') }}">
                            @csrf
                            <button type="submit" 
                                    title="Klik untuk mengaktifkan / menonaktifkan"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden {{ $bcaSetting?->is_active ? 'bg-emerald-600' : 'bg-gray-300' }}">
                                <span class="sr-only">Toggle BCA</span>
                                <span style="transform: translateX({{ $bcaSetting?->is_active ? '20px' : '0px' }});"
                                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $bcaSetting?->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </form>
                    </div>

                    <p class="text-[11px] text-gray-600 leading-relaxed">
                        Pengguna membayar ke rekening BCA Anda dan mengirimkan konfirmasi manual via WhatsApp.
                    </p>

                    <div class="bg-white rounded-xl p-2.5 border border-blue-100/80 text-[11px] space-y-1.5 font-mono text-gray-700">
                        <div class="flex items-center justify-between pb-1 border-b border-blue-50 font-sans">
                            <span class="text-[10px] font-bold text-blue-700 flex items-center gap-1">
                                <i class="fas fa-shield-halved text-[10px]"></i>
                                <span>AES-256-GCM Encrypted</span>
                            </span>
                            <button type="button" onclick="toggleBcaCardReveal()"
                                    title="Klik untuk mendekripsi / menyembunyikan identitas rekening"
                                    class="text-[10px] font-bold text-gray-500 hover:text-blue-700 flex items-center gap-1 px-1.5 py-0.5 rounded bg-gray-50 hover:bg-blue-50 border border-gray-200 transition-colors cursor-pointer">
                                <i id="bcaCardEyeIcon" class="fas fa-eye text-blue-600"></i>
                                <span id="bcaCardEyeLabel">Lihat</span>
                            </button>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 font-sans">Rek:</span>
                            <span id="bcaCardRekText"
                                  data-masked="{{ $bcaRekMasked }}"
                                  data-plain="{{ $bcaRekPlain }}"
                                  class="font-bold text-blue-900">{{ $bcaRekMasked }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 font-sans">A.N:</span>
                            <span id="bcaCardNameText"
                                  data-masked="{{ $bcaNameMasked }}"
                                  data-plain="{{ $bcaNamePlain }}"
                                  class="font-semibold text-gray-800">{{ $bcaNameMasked }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 font-sans">WA:</span>
                            <span id="bcaCardWaText"
                                  data-masked="{{ $bcaWaMasked }}"
                                  data-plain="{{ $bcaWaPlain }}"
                                  class="text-emerald-700 font-semibold">{{ $bcaWaMasked }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 mt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $bcaSetting?->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">
                        {{ $bcaSetting?->is_active ? '✓ AKTIF DI CHECKOUT' : '✕ NONAKTIF' }}
                    </span>
                    <button type="button" onclick="openBcaConfigModal()"
                            class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-[11px] font-bold text-gray-700 transition-colors shadow-2xs flex items-center gap-1 cursor-pointer">
                        <i class="fas fa-pen-to-square text-blue-600"></i>
                        <span>Edit Data Rekening</span>
                    </button>
                </div>
            </div>

            {{-- KARTU 2: MIDTRANS SNAP GATEWAY --}}
            <div class="rounded-2xl border p-4 transition-all duration-200 flex flex-col justify-between {{ $midtransSetting?->is_active ? 'border-emerald-300 bg-emerald-50/40 shadow-xs ring-1 ring-emerald-400/20' : 'border-gray-200 bg-gray-50/70 opacity-75' }}">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-700 to-indigo-800 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-gray-900 flex items-center gap-1">
                                    <span>Midtrans Snap</span>
                                    @if(($midtransConfig['environment'] ?? 'sandbox') === 'production')
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-blue-600 text-white">PROD</span>
                                    @else
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black bg-amber-500 text-white">SANDBOX</span>
                                    @endif
                                </h3>
                                <span class="text-[10px] text-gray-500">QRIS, VA Bank, E-Wallet, Kartu</span>
                            </div>
                        </div>

                        {{-- Toggle Switch --}}
                        <form method="POST" action="{{ route('superadmin.billing.payment-methods.toggle', 'midtrans') }}">
                            @csrf
                            <button type="submit" 
                                    title="Klik untuk mengaktifkan / menonaktifkan"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden {{ $midtransSetting?->is_active ? 'bg-emerald-600' : 'bg-gray-300' }}">
                                <span class="sr-only">Toggle Midtrans</span>
                                <span style="transform: translateX({{ $midtransSetting?->is_active ? '20px' : '0px' }});"
                                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $midtransSetting?->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </form>
                    </div>

                    <p class="text-[11px] text-gray-600 leading-relaxed">
                        Pembayaran instan online. Setelah bayar via QRIS atau Virtual Account, lisensi langsung aktif otomatis.
                    </p>

                    <div class="bg-white rounded-xl p-2.5 border border-emerald-100 text-[11px] space-y-1 text-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Environment (.env):</span>
                            <span class="font-bold uppercase text-[10px] {{ ($midtransConfig['environment'] ?? 'sandbox') === 'production' ? 'text-blue-700' : 'text-amber-600' }}">
                                {{ $midtransConfig['environment'] ?? 'sandbox' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Client Key (.env):</span>
                            @if($hasClientKey)
                                <span class="text-emerald-700 font-bold flex items-center gap-1">
                                    <i class="fas fa-check-circle text-[10px]"></i> Terdeteksi
                                </span>
                            @else
                                <span class="text-amber-600 font-bold flex items-center gap-1">
                                    <i class="fas fa-triangle-exclamation text-[10px]"></i> Dummy / Kosong
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Server Key (.env):</span>
                            @if($hasServerKey)
                                <span class="text-emerald-700 font-bold flex items-center gap-1">
                                    <i class="fas fa-check-circle text-[10px]"></i> Terdeteksi
                                </span>
                            @else
                                <span class="text-amber-600 font-bold flex items-center gap-1">
                                    <i class="fas fa-triangle-exclamation text-[10px]"></i> Dummy / Kosong
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-3 mt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $midtransSetting?->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">
                        {{ $midtransSetting?->is_active ? '✓ AKTIF DI CHECKOUT' : '✕ NONAKTIF' }}
                    </span>
                    <button type="button" onclick="openMidtransConfigModal()"
                            class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-[11px] font-bold text-gray-700 transition-colors shadow-2xs flex items-center gap-1 cursor-pointer">
                        <i class="fas fa-shield-halved text-emerald-600"></i>
                        <span>Status .env & Webhook</span>
                    </button>
                </div>
            </div>

            {{-- KARTU 3: GOOGLE PAY --}}
            <div class="rounded-2xl border p-4 transition-all duration-200 flex flex-col justify-between {{ $gpaySetting?->is_active ? 'border-purple-200 bg-purple-50/40 shadow-xs' : 'border-gray-200 bg-gray-50/70 opacity-75' }}">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-9 h-9 rounded-xl bg-black text-white flex items-center justify-center font-bold text-sm shadow-xs">
                                <i class="fab fa-google-pay text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-gray-900">Google Pay (Web)</h3>
                                <span class="text-[10px] text-gray-500">Dompet Digital Google</span>
                            </div>
                        </div>

                        {{-- Toggle Switch --}}
                        <form method="POST" action="{{ route('superadmin.billing.payment-methods.toggle', 'google_pay') }}">
                            @csrf
                            <button type="submit" 
                                    title="Klik untuk mengaktifkan / menonaktifkan"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-hidden {{ $gpaySetting?->is_active ? 'bg-emerald-600' : 'bg-gray-300' }}">
                                <span class="sr-only">Toggle Google Pay</span>
                                <span style="transform: translateX({{ $gpaySetting?->is_active ? '20px' : '0px' }});"
                                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-sm ring-0 transition duration-200 ease-in-out {{ $gpaySetting?->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </form>
                    </div>

                    <p class="text-[11px] text-gray-600 leading-relaxed">
                        Pembayaran via kartu kredit yang terdaftar di akun Google. Memerlukan Payment Gateway tokenisasi untuk produksi.
                    </p>

                    <div class="bg-white rounded-xl p-2.5 border border-purple-100 text-[11px] space-y-1 text-gray-700">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Mode (.env):</span>
                            <span class="font-bold text-[10px] {{ ($gpayConfig['environment'] ?? 'TEST') === 'PRODUCTION' ? 'text-blue-700' : 'text-amber-600' }}">
                                {{ $gpayConfig['environment'] ?? 'TEST' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Merchant ID (.env):</span>
                            @if($hasGpayMerchantId)
                                <span class="text-emerald-700 font-bold flex items-center gap-1">
                                    <i class="fas fa-check-circle text-[10px]"></i> Terdeteksi
                                </span>
                            @else
                                <span class="text-amber-600 font-bold flex items-center gap-1">
                                    <i class="fas fa-triangle-exclamation text-[10px]"></i> Dummy / Kosong
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Merchant Name:</span>
                            <span class="font-medium text-gray-800 truncate max-w-[140px]">{{ $gpayConfig['merchant_name'] ?? 'AkuSehat Portal' }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 mt-3 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $gpaySetting?->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">
                        {{ $gpaySetting?->is_active ? '✓ AKTIF DI CHECKOUT' : '✕ NONAKTIF' }}
                    </span>
                    <button type="button" onclick="openGpayConfigModal()"
                            class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-[11px] font-bold text-gray-700 transition-colors shadow-2xs flex items-center gap-1 cursor-pointer">
                        <i class="fas fa-shield-halved text-purple-600"></i>
                        <span>Status .env</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs & Main Table -->
    <div class="bg-white rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
        {{-- Tabs Status Filter --}}
        <div class="p-4 sm:p-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                @php
                    $baseParams = request()->except(['status', 'page']);
                @endphp
                <a href="{{ route('superadmin.billing.index', array_merge($baseParams, ['status' => 'all'])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ empty($currentStatus) || $currentStatus === 'all' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Semua ({{ $stats['total_count'] }})
                </a>
                <a href="{{ route('superadmin.billing.index', array_merge($baseParams, ['status' => 'pending'])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 {{ $currentStatus === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-800 hover:bg-amber-100' }}">
                    <span>Menunggu Verifikasi</span>
                    @if($stats['pending_count'] > 0)
                        <span class="w-5 h-5 rounded-full {{ $currentStatus === 'pending' ? 'bg-white text-amber-700' : 'bg-amber-500 text-white' }} text-[10px] font-black flex items-center justify-center">
                            {{ $stats['pending_count'] }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('superadmin.billing.index', array_merge($baseParams, ['status' => 'success'])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $currentStatus === 'success' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Berhasil / Lunas ({{ $stats['success_count'] }})
                </a>
                <a href="{{ route('superadmin.billing.index', array_merge($baseParams, ['status' => 'failed'])) }}" 
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $currentStatus === 'failed' ? 'bg-rose-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                    Gagal / Dibatalkan
                </a>
            </div>

            <div class="text-xs text-gray-400 font-medium">
                Menampilkan halaman {{ $transactions->currentPage() }} dari {{ $transactions->lastPage() }}
            </div>
        </div>

        {{-- Filter & Search Toolbar (Searching by Instansi/Order ID, Date Picker, Dropdown Metode) --}}
        <div class="p-4 sm:p-5 bg-gray-50/80 border-b border-gray-100">
            <form method="GET" action="{{ route('superadmin.billing.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                {{-- Maintain current status tab --}}
                <input type="hidden" name="status" value="{{ $currentStatus ?? 'all' }}">

                {{-- Kolom Searching: Order ID atau Nama Instansi/Sekolah --}}
                <div class="sm:col-span-2 lg:col-span-4">
                    <label class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1.5">
                        <i class="fas fa-magnifying-glass text-gray-400"></i>
                        <span>Pencarian Cepat</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                               placeholder="Cari Order ID, nama sekolah/instansi, atau pemesan..."
                               class="w-full h-10 pl-9 pr-3.5 border border-gray-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white shadow-2xs">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 text-xs">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>

                {{-- Dropdown Metode Pembayaran --}}
                <div class="sm:col-span-1 lg:col-span-3">
                    <label class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1.5">
                        <i class="fas fa-wallet text-gray-400"></i>
                        <span>Metode Pembayaran</span>
                    </label>
                    <select name="payment_method" 
                            class="w-full h-10 px-3 border border-gray-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white shadow-2xs">
                        <option value="all" {{ ($filters['payment_method'] ?? 'all') === 'all' ? 'selected' : '' }}>Semua Metode</option>
                        <option value="manual_bca" {{ in_array(($filters['payment_method'] ?? ''), ['manual_bca', 'manual_transfer', 'bca']) ? 'selected' : '' }}>Transfer BCA (Manual)</option>
                        <option value="midtrans" {{ in_array(($filters['payment_method'] ?? ''), ['midtrans', 'snap']) ? 'selected' : '' }}>Midtrans (Snap Otomatis)</option>
                        <option value="google_pay" {{ in_array(($filters['payment_method'] ?? ''), ['google_pay', 'gpay']) ? 'selected' : '' }}>Google Pay (Otomatis)</option>
                    </select>
                </div>

                {{-- Date Picker: Tanggal Mulai --}}
                <div class="sm:col-span-1 lg:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1.5">
                        <i class="far fa-calendar-alt text-gray-400"></i>
                        <span>Dari Tanggal</span>
                    </label>
                    <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                           class="w-full h-10 px-3 border border-gray-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white shadow-2xs">
                </div>

                {{-- Date Picker: Tanggal Akhir --}}
                <div class="sm:col-span-1 lg:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 mb-1 flex items-center gap-1.5">
                        <i class="far fa-calendar-alt text-gray-400"></i>
                        <span>Sampai Tanggal</span>
                    </label>
                    <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                           class="w-full h-10 px-3 border border-gray-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white shadow-2xs">
                </div>

                {{-- Tombol Filter & Reset --}}
                <div class="sm:col-span-1 lg:col-span-1 flex items-center gap-1.5">
                    <button type="submit" 
                            class="w-full h-10 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white text-xs font-bold transition-all shadow-xs flex items-center justify-center gap-1 cursor-pointer"
                            title="Terapkan Filter">
                        <i class="fas fa-filter"></i>
                        <span class="sm:inline lg:hidden">Filter</span>
                    </button>
                    @if(!empty($filters['search']) || (!empty($filters['payment_method']) && $filters['payment_method'] !== 'all') || !empty($filters['start_date']) || !empty($filters['end_date']))
                        <a href="{{ route('superadmin.billing.index', ['status' => $currentStatus ?? 'all']) }}" 
                           class="h-10 px-3 rounded-xl border border-gray-300 bg-white hover:bg-gray-100 active:scale-95 text-gray-600 text-xs font-bold transition-all flex items-center justify-center shrink-0 cursor-pointer"
                           title="Reset Semua Filter">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>

            @if(!empty($filters['search']) || (!empty($filters['payment_method']) && $filters['payment_method'] !== 'all') || !empty($filters['start_date']) || !empty($filters['end_date']))
                <div class="mt-3 pt-3 border-t border-gray-200/60 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-1.5 flex-wrap text-gray-600">
                        <span class="font-bold text-gray-700">Filter Aktif:</span>
                        @if(!empty($filters['search']))
                            <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-semibold text-[11px]">
                                Kata Kunci: "{{ $filters['search'] }}"
                            </span>
                        @endif
                        @if(!empty($filters['payment_method']) && $filters['payment_method'] !== 'all')
                            <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 font-semibold text-[11px]">
                                Metode: {{ $filters['payment_method'] === 'manual_bca' ? 'Transfer BCA' : 'Google Pay' }}
                            </span>
                        @endif
                        @if(!empty($filters['start_date']) || !empty($filters['end_date']))
                            <span class="px-2 py-0.5 rounded-md bg-purple-100 text-purple-800 font-semibold text-[11px]">
                                Tanggal: {{ $filters['start_date'] ?: 'Awal' }} s/d {{ $filters['end_date'] ?: 'Sekarang' }}
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('superadmin.billing.index', ['status' => $currentStatus ?? 'all']) }}" 
                       class="text-xs font-bold text-rose-600 hover:text-rose-700 flex items-center gap-1">
                        <i class="fas fa-times-circle"></i> Hapus Filter
                    </a>
                </div>
            @endif
        </div>

        {{-- Desktop Table View --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-gray-500 text-[11px] font-bold uppercase tracking-wider text-left">
                        <th class="px-5 py-3.5">Order ID & Waktu</th>
                        <th class="px-5 py-3.5">Instansi / Sekolah</th>
                        <th class="px-5 py-3.5">Rincian Paket</th>
                        <th class="px-5 py-3.5">Total Nominal</th>
                        <th class="px-5 py-3.5">Metode</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $trx)
                        @php
                            $payload = is_array($trx->payload) ? $trx->payload : [];
                            $isPending = $trx->status === 'pending';
                            $isSuccess = in_array($trx->status, ['success', 'paid']);
                            $isFailed = in_array($trx->status, ['failed', 'expired']);
                            $isBca = str_contains(strtolower($trx->payment_method ?? ''), 'bca') || str_contains(strtolower($trx->payment_method ?? ''), 'manual') || str_starts_with($trx->order_id, 'BCA-');
                            $isMidtrans = str_contains(strtolower($trx->payment_method ?? ''), 'midtrans') || str_starts_with($trx->order_id, 'MID-');
                            $isGooglePlay = str_contains(strtolower($trx->payment_method ?? ''), 'google_play');
                            $namaInstansi = $trx->instansi ? ($trx->instansi->nama_instansi ?? $trx->instansi->nama_sekolah ?? 'Instansi') : 'Instansi';
                            $deskripsiItem = $payload['deskripsi_item'] ?? ($trx->tipe_transaksi === 'perpanjang_lisensi' ? 'Perpanjangan Lisensi' : 'Tambah Paket Kuota');
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-colors {{ $isPending ? 'bg-amber-50/30' : '' }}">
                            {{-- Order ID & Waktu --}}
                            <td class="px-5 py-4">
                                <div class="font-mono font-bold text-gray-900 text-xs">
                                    {{ $trx->order_id }}
                                </div>
                                <div class="text-[11px] text-gray-400 mt-0.5 flex items-center gap-1">
                                    <i class="far fa-clock"></i>
                                    <span>{{ $trx->created_at ? $trx->created_at->format('d/m/Y H:i') : '-' }}</span>
                                </div>
                            </td>

                            {{-- Instansi / Sekolah --}}
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-800 text-sm">
                                    {{ $namaInstansi }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-2">
                                    <span>Pemesan: <strong>{{ $trx->user ? $trx->user->name : '-' }}</strong></span>
                                </div>
                            </td>

                            {{-- Rincian Paket --}}
                            <td class="px-5 py-4">
                                <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $trx->tipe_transaksi === 'perpanjang_lisensi' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                                    <i class="fas {{ $trx->tipe_transaksi === 'perpanjang_lisensi' ? 'fa-rotate' : 'fa-user-plus' }} text-[10px]"></i>
                                    <span>{{ $deskripsiItem }}</span>
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    Durasi: <strong>{{ $trx->durasi_bulan ?? 1 }} Bulan</strong>
                                    @if($trx->tambah_kuota > 0)
                                        • +{{ $trx->tambah_kuota }} User
                                    @endif
                                </div>
                            </td>

                            {{-- Total Nominal --}}
                            <td class="px-5 py-4">
                                <span class="font-black text-gray-900 text-sm whitespace-nowrap">
                                    Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                </span>
                                <div class="text-[10px] text-gray-400">Termasuk PPN 11%</div>
                            </td>

                            {{-- Metode Pembayaran --}}
                            <td class="px-5 py-4">
                                @if($isBca)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-[#0c2e68] text-white">
                                        <i class="fas fa-building-columns text-[10px] text-blue-300"></i> Transfer BCA
                                    </span>
                                @elseif($isMidtrans)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-gradient-to-r from-blue-700 to-indigo-800 text-white shadow-2xs">
                                        <i class="fas fa-bolt text-[10px] text-amber-300"></i> Midtrans Snap
                                    </span>
                                @elseif($isGooglePlay)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-900 text-white shadow-2xs">
                                        <i class="fab fa-google-play text-xs text-emerald-300"></i> Google Play
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-neutral-900 text-white shadow-2xs">
                                        <i class="fab fa-google-pay text-sm text-neutral-300"></i> Google Pay
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4">
                                @if($isPending)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                                        <i class="fas fa-hourglass-half text-[10px]"></i> Menunggu Verifikasi
                                    </span>
                                @elseif($isSuccess)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-circle-check text-[10px]"></i> Berhasil / Lunas
                                    </span>
                                @elseif($trx->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                        <i class="fas fa-ban text-[10px]"></i> Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        <i class="fas fa-circle-xmark text-[10px]"></i> Gagal / Ditolak
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Tombol Struk PDF --}}
                                    @if($trx->status !== 'cancelled' && $trx->status !== 'failed')
                                        <a href="{{ route('superadmin.billing.invoice.pdf', $trx->id) }}" target="_blank"
                                           class="px-2.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-colors flex items-center gap-1"
                                           title="Buka Struk Resmi PDF">
                                            <i class="fas fa-file-pdf"></i>
                                            <span class="hidden xl:inline">Struk</span>
                                        </a>
                                    @elseif(!$isPending)
                                        <span class="text-xs text-gray-400 font-medium py-1 px-2">-</span>
                                    @endif

                                    @if($isPending)
                                        {{-- Tombol Setujui / Verifikasi --}}
                                        <button type="button" 
                                                onclick="openApproveModal('{{ $trx->id }}', '{{ $trx->order_id }}', '{{ addslashes($namaInstansi) }}', 'Rp {{ number_format($trx->nominal, 0, ',', '.') }}', '{{ addslashes($deskripsiItem) }}')"
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-95 transition-all shadow-xs flex items-center gap-1 cursor-pointer">
                                            <i class="fas fa-check"></i>
                                            <span>Verifikasi</span>
                                        </button>

                                        {{-- Tombol Tolak --}}
                                        <button type="button" 
                                                onclick="openRejectModal('{{ $trx->id }}', '{{ $trx->order_id }}')"
                                                class="px-2.5 py-1.5 rounded-lg text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 active:scale-95 transition-all cursor-pointer"
                                                title="Tolak Pesanan">
                                            <i class="fas fa-xmark"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                                <i class="fas fa-receipt text-4xl mb-3 text-gray-300"></i>
                                <p class="text-sm font-medium">Belum ada transaksi ditemukan untuk filter ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="p-4 sm:p-5 border-t border-gray-100">
                {{ $transactions->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

{{-- MODAL KONFIRMASI APPROVE / VERIFIKASI PEMBAYARAN --}}
<div id="approveModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in duration-150">
        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                <i class="fas fa-check-double"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-800">Verifikasi Pembayaran BCA</h3>
                <p class="text-xs text-gray-500">Konfirmasi penerimaan dana transfer</p>
            </div>
        </div>

        <p class="text-xs text-gray-600 leading-relaxed">
            Apakah Anda yakin telah mengecek mutasi rekening BCA dan dana dari pesanan berikut sudah benar-benar masuk?
        </p>

        <div class="bg-gray-50 p-4 rounded-2xl border border-gray-200 text-xs space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-500">Order ID:</span>
                <span class="font-mono font-bold text-gray-900" id="approveModalOrderId">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Instansi:</span>
                <span class="font-bold text-gray-800" id="approveModalInstansi">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Rincian Paket:</span>
                <span class="font-bold text-emerald-700" id="approveModalDeskripsi">-</span>
            </div>
            <div class="flex justify-between pt-2 border-t border-gray-200">
                <span class="text-gray-600 font-bold">Total Diterima:</span>
                <span class="font-extrabold text-emerald-800 text-sm" id="approveModalTotal">-</span>
            </div>
        </div>

        <div class="bg-emerald-50/70 p-3 rounded-xl border border-emerald-200 text-[11px] text-emerald-900 leading-relaxed">
            <i class="fas fa-info-circle text-emerald-600 mr-1"></i>
            Setelah disetujui, sistem akan <strong>otomatis memperpanjang masa aktif lisensi</strong> atau <strong>membuat paket kuota baru</strong> untuk instansi tersebut tanpa perlu tindakan manual lainnya.
        </div>

        <form id="approveForm" method="POST" action="">
            @csrf
            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeApproveModal()"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSubmitApprove"
                        class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-check"></i>
                    <span>Ya, Verifikasi & Aktifkan Paket</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL TOLAK / BATALKAN PESANAN --}}
<div id="rejectModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in duration-150">
        <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-lg">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-gray-800">Tolak Pesanan Transfer</h3>
                <p class="text-xs text-gray-500">Batalkan pesanan yang tidak valid</p>
            </div>
        </div>

        <p class="text-xs text-gray-600 leading-relaxed">
            Pesanan <strong id="rejectModalOrderId" class="font-mono"></strong> akan ditandai sebagai gagal. Masukkan alasan penolakan jika diperlukan:
        </p>

        <form id="rejectForm" method="POST" action="">
            @csrf
            <div>
                <label for="rejectReason" class="block text-xs font-bold text-gray-700 mb-1.5">Alasan Penolakan</label>
                <textarea id="rejectReason" name="reason" rows="3"
                          placeholder="Misal: Bukti transfer palsu, dana tidak masuk mutasi BCA, nominal tidak sesuai..."
                          class="w-full p-3 border border-gray-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-rose-500 focus:border-transparent"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-4">
                <button type="button" onclick="closeRejectModal()"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <i class="fas fa-ban"></i>
                    <span>Tolak Pesanan Ini</span>
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KONFIGURASI BCA --}}
<div id="bcaConfigModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                    BCA
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Pengaturan Rekening BCA</h3>
                    <p class="text-[11px] text-gray-500">Dienkripsi di database (AES-256-GCM + HKDF-SHA256)</p>
                </div>
            </div>
            <button type="button" onclick="closeBcaConfigModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-3 rounded-xl {{ $hasDedicatedDataSecret ? 'bg-blue-50/80 border-blue-200 text-blue-900' : 'bg-amber-50 border-amber-200 text-amber-900' }} border text-[11px] flex items-start justify-between gap-2">
            <div class="flex items-start gap-2">
                <i class="fas fa-lock {{ $hasDedicatedDataSecret ? 'text-blue-600' : 'text-amber-600' }} mt-0.5"></i>
                <div>
                    <span class="font-bold block">
                        {{ $hasDedicatedDataSecret ? 'Enkripsi Perbankan Aktif (DATA_SECRET_KEY)' : 'Enkripsi Aktif (Mode Fallback Key)' }}
                    </span>
                    <span>
                        {{ $hasDedicatedDataSecret ? 'Menggunakan kunci DATA_SECRET_KEY yang terpisah dari APP_KEY.' : 'Tambahkan DATA_SECRET_KEY di .env untuk memisahkan kunci data dari APP_KEY.' }}
                    </span>
                </div>
            </div>
            <button type="button" onclick="toggleAllBcaModalEyes()"
                    class="shrink-0 px-2 py-1 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 text-[10px] font-bold text-gray-700 flex items-center gap-1 cursor-pointer">
                <i id="bcaModalMasterEyeIcon" class="fas fa-eye text-blue-600"></i>
                <span id="bcaModalMasterEyeLabel">Lihat Semua</span>
            </button>
        </div>

        <form method="POST" action="{{ route('superadmin.billing.payment-methods.update', 'manual_bca') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Bank</label>
                <input type="text" name="bank_name" value="{{ $bcaConfig['bank_name'] ?? 'BANK CENTRAL ASIA' }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nomor Rekening</label>
                <div class="relative">
                    <input type="password" id="bcaInputRek" name="account_number" value="{{ $bcaRekPlain }}" required
                           class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-xl text-xs font-mono font-bold focus:ring-2 focus:ring-blue-500">
                    <button type="button" onclick="toggleSingleBcaInput('bcaInputRek', 'bcaEyeRek')"
                            title="Dekripsi / Tampilkan Nomor Rekening"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 cursor-pointer">
                        <i id="bcaEyeRek" class="fas fa-eye text-xs"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Atas Nama (A.N)</label>
                <div class="relative">
                    <input type="password" id="bcaInputName" name="account_name" value="{{ $bcaNamePlain }}" required
                           class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-500">
                    <button type="button" onclick="toggleSingleBcaInput('bcaInputName', 'bcaEyeName')"
                            title="Dekripsi / Tampilkan Atas Nama"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 cursor-pointer">
                        <i id="bcaEyeName" class="fas fa-eye text-xs"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nomor WhatsApp Konfirmasi</label>
                <div class="relative">
                    <input type="password" id="bcaInputWa" name="whatsapp_number" value="{{ $bcaWaPlain }}" required
                           class="w-full pl-3 pr-10 py-2 border border-gray-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-blue-500">
                    <button type="button" onclick="toggleSingleBcaInput('bcaInputWa', 'bcaEyeWa')"
                            title="Dekripsi / Tampilkan Nomor WhatsApp"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-600 cursor-pointer">
                        <i id="bcaEyeWa" class="fas fa-eye text-xs"></i>
                    </button>
                </div>
                <span class="text-[10px] text-gray-400 mt-0.5 block">Format: +62 8xx atau 08xx (Klik ikon mata untuk melihat/mengedit)</span>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                <button type="button" onclick="closeBcaConfigModal()"
                        class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                    Simpan & Enkripsi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL STATUS .ENV & WEBHOOK MIDTRANS --}}
<div id="midtransConfigModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in duration-150 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-700 to-indigo-800 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Status Kredensial Midtrans (.env)</h3>
                    <p class="text-[11px] text-gray-500">Kredensial dikelola secara aman melalui file .env server</p>
                </div>
            </div>
            <button type="button" onclick="closeMidtransConfigModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-200 text-[11px] text-emerald-800 flex items-start gap-2.5">
            <i class="fas fa-lock text-emerald-600 mt-0.5"></i>
            <div>
                <span class="font-bold block">Keamanan Kredensial Aktif (Strict .env Mode)</span>
                Kunci rahasia Midtrans dibaca langsung dari variabel <code class="font-mono font-bold">.env</code> server dan tidak disimpan di database demi keamanan maksimal.
            </div>
        </div>

        <div class="space-y-2.5 text-xs">
            <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono text-gray-400 block">MIDTRANS_ENVIRONMENT</span>
                    <span class="font-bold text-gray-800">Mode Environment</span>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-black uppercase {{ ($midtransConfig['environment'] ?? 'sandbox') === 'production' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $midtransConfig['environment'] ?? 'sandbox' }}
                </span>
            </div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono text-gray-400 block">MIDTRANS_CLIENT_KEY</span>
                    <span class="font-mono font-bold text-gray-700 text-[11px]">{{ $maskedClientKey }}</span>
                </div>
                @if($hasClientKey)
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">✓ Terbaca</span>
                @else
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">Placeholder / Kosong</span>
                @endif
            </div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono text-gray-400 block">MIDTRANS_SERVER_KEY</span>
                    <span class="font-mono font-bold text-gray-700 text-[11px]">{{ $maskedServerKey }}</span>
                </div>
                @if($hasServerKey)
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">✓ Terbaca</span>
                @else
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">Placeholder / Kosong</span>
                @endif
            </div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono text-gray-400 block">MIDTRANS_MERCHANT_ID</span>
                    <span class="font-mono font-bold text-gray-700 text-[11px]">{{ !empty($midtransConfig['merchant_id']) ? $midtransConfig['merchant_id'] : '-' }}</span>
                </div>
                <span class="text-[10px] text-gray-500 font-semibold">Opsional</span>
            </div>
        </div>

        {{-- Info URL Webhook Midtrans --}}
        <div class="rounded-xl p-3 bg-slate-50 border border-slate-200 text-xs space-y-1">
            <span class="font-bold text-slate-800 flex items-center gap-1.5 text-[11px]">
                <i class="fas fa-link text-emerald-600"></i>
                <span>Webhook Notification URL (Salin ke Midtrans Dashboard)</span>
            </span>
            <p class="text-[10px] text-slate-500 leading-relaxed">
                Buka Dashboard Midtrans &gt; Settings &gt; Configuration &gt; Payment Notification URL, lalu tempel tautan ini:
            </p>
            <div class="flex items-center gap-1.5 pt-1">
                <input type="text" readonly value="{{ url('billing/midtrans/notification') }}"
                       id="midtransWebhookUrl"
                       class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded-lg text-[11px] font-mono font-bold text-slate-700 select-all">
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('midtransWebhookUrl').value); alert('URL Notifikasi Webhook berhasil disalin!');"
                        class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-[10px] font-bold shrink-0 cursor-pointer">
                    Salin
                </button>
            </div>
        </div>

        <div class="flex items-center justify-end pt-3 border-t border-gray-100">
            <button type="button" onclick="closeMidtransConfigModal()"
                    class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- MODAL STATUS .ENV GOOGLE PAY --}}
<div id="gpayConfigModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 animate-in fade-in duration-150">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-black text-white flex items-center justify-center font-bold text-xs">
                    <i class="fab fa-google-pay text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Status Kredensial Google Pay (.env)</h3>
                    <p class="text-[11px] text-gray-500">Konfigurasi dibaca langsung dari file .env</p>
                </div>
            </div>
            <button type="button" onclick="closeGpayConfigModal()" class="text-gray-400 hover:text-gray-600 cursor-pointer">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="space-y-2.5 text-xs">
            <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono text-gray-400 block">GOOGLE_PAY_ENVIRONMENT</span>
                    <span class="font-bold text-gray-800">Mode Environment</span>
                </div>
                <span class="px-2.5 py-1 rounded-lg text-[11px] font-black uppercase {{ ($gpayConfig['environment'] ?? 'TEST') === 'PRODUCTION' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ $gpayConfig['environment'] ?? 'TEST' }}
                </span>
            </div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono text-gray-400 block">GOOGLE_PAY_MERCHANT_ID</span>
                    <span class="font-mono font-bold text-gray-700 text-[11px]">{{ $maskedGpayMerchantId }}</span>
                </div>
                @if($hasGpayMerchantId)
                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">✓ Terbaca</span>
                @else
                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">Placeholder / Kosong</span>
                @endif
            </div>

            <div class="p-3 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-mono text-gray-400 block">GOOGLE_PAY_MERCHANT_NAME</span>
                    <span class="font-bold text-gray-800 text-[11px]">{{ $gpayConfig['merchant_name'] ?? 'AkuSehat Portal' }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end pt-3 border-t border-gray-100">
            <button type="button" onclick="closeGpayConfigModal()"
                    class="px-5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    let bcaCardRevealed = false;
    function toggleBcaCardReveal() {
        bcaCardRevealed = !bcaCardRevealed;
        const fields = ['bcaCardRekText', 'bcaCardNameText', 'bcaCardWaText'];
        fields.forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = bcaCardRevealed ? el.getAttribute('data-plain') : el.getAttribute('data-masked');
            }
        });
        const icon = document.getElementById('bcaCardEyeIcon');
        const label = document.getElementById('bcaCardEyeLabel');
        if (icon) {
            icon.className = bcaCardRevealed ? 'fas fa-eye-slash text-amber-600' : 'fas fa-eye text-blue-600';
        }
        if (label) {
            label.textContent = bcaCardRevealed ? 'Sembunyikan' : 'Lihat';
        }
    }

    function toggleSingleBcaInput(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash text-xs text-blue-600';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye text-xs';
        }
    }

    let bcaModalAllRevealed = false;
    function toggleAllBcaModalEyes() {
        bcaModalAllRevealed = !bcaModalAllRevealed;
        const pairs = [
            ['bcaInputRek', 'bcaEyeRek'],
            ['bcaInputName', 'bcaEyeName'],
            ['bcaInputWa', 'bcaEyeWa'],
        ];
        pairs.forEach(function(pair) {
            const input = document.getElementById(pair[0]);
            const icon = document.getElementById(pair[1]);
            if (input && icon) {
                input.type = bcaModalAllRevealed ? 'text' : 'password';
                icon.className = bcaModalAllRevealed ? 'fas fa-eye-slash text-xs text-blue-600' : 'fas fa-eye text-xs';
            }
        });
        const masterIcon = document.getElementById('bcaModalMasterEyeIcon');
        const masterLabel = document.getElementById('bcaModalMasterEyeLabel');
        if (masterIcon) {
            masterIcon.className = bcaModalAllRevealed ? 'fas fa-eye-slash text-amber-600' : 'fas fa-eye text-blue-600';
        }
        if (masterLabel) {
            masterLabel.textContent = bcaModalAllRevealed ? 'Sembunyikan' : 'Lihat Semua';
        }
    }

    function openBcaConfigModal() {
        document.getElementById('bcaConfigModal').classList.remove('hidden');
    }
    function closeBcaConfigModal() {
        document.getElementById('bcaConfigModal').classList.add('hidden');
    }

    function openMidtransConfigModal() {
        document.getElementById('midtransConfigModal').classList.remove('hidden');
    }
    function closeMidtransConfigModal() {
        document.getElementById('midtransConfigModal').classList.add('hidden');
    }

    function openGpayConfigModal() {
        document.getElementById('gpayConfigModal').classList.remove('hidden');
    }
    function closeGpayConfigModal() {
        document.getElementById('gpayConfigModal').classList.add('hidden');
    }

    function openApproveModal(id, orderId, instansi, total, deskripsi) {
        document.getElementById('approveModalOrderId').textContent = orderId;
        document.getElementById('approveModalInstansi').textContent = instansi;
        document.getElementById('approveModalTotal').textContent = total;
        document.getElementById('approveModalDeskripsi').textContent = deskripsi;
        
        const form = document.getElementById('approveForm');
        form.action = "{{ url('superadmin/billing/transactions') }}/" + id + "/approve";
        
        document.getElementById('approveModal').classList.remove('hidden');
    }

    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
    }

    function openRejectModal(id, orderId) {
        document.getElementById('rejectModalOrderId').textContent = orderId;
        
        const form = document.getElementById('rejectForm');
        form.action = "{{ url('superadmin/billing/transactions') }}/" + id + "/reject";
        
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }

    // Close on click outside
    window.addEventListener('click', function(e) {
        const approveModal = document.getElementById('approveModal');
        const rejectModal = document.getElementById('rejectModal');
        const bcaModal = document.getElementById('bcaConfigModal');
        const midtransModal = document.getElementById('midtransConfigModal');
        const gpayModal = document.getElementById('gpayConfigModal');

        if (e.target === approveModal) closeApproveModal();
        if (e.target === rejectModal) closeRejectModal();
        if (e.target === bcaModal) closeBcaConfigModal();
        if (e.target === midtransModal) closeMidtransConfigModal();
        if (e.target === gpayModal) closeGpayConfigModal();
    });
</script>
@endsection

