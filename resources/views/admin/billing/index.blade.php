@extends('layouts.app')

@section('title', 'Langganan & Billing Layanan')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Langganan & Kuota {{ $tipeSebutan }}</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Kelola perpanjangan masa aktif sistem dan kuota {{ strtolower($labelMemberPlural) }} untuk <span class="font-semibold text-emerald-800">{{ $instansi->nama_instansi ?? $instansi->nama_sekolah }}</span>.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200 shadow-xs">
                <i class="fas fa-shield-check text-emerald-600"></i> Pembayaran Terverifikasi
            </span>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Card 1: Status Layanan & Paket Berjalan --}}
        <div class="bg-white rounded-2xl shadow-soft p-5 border border-gray-100 relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Status Layanan</span>
                @if($instansi->hasActiveLicense())
                    <span class="px-2.5 py-1 text-xs rounded-full font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                        <i class="fas fa-check-circle text-emerald-600"></i> Aktif (Multi-Paket)
                    </span>
                @else
                    <span class="px-2.5 py-1 text-xs rounded-full font-bold bg-rose-100 text-rose-800 border border-rose-200 flex items-center gap-1">
                        <i class="fas fa-exclamation-circle text-rose-600"></i> Default Kuota 1 (Admin)
                    </span>
                @endif
            </div>

            <div class="space-y-2">
                <div class="flex items-baseline justify-between">
                    <div>
                        <span class="text-2xl font-black text-gray-800">{{ $activePackages->count() }}</span>
                        <span class="text-xs text-gray-500 font-medium ml-1">Paket Aktif</span>
                    </div>
                    <span class="text-xs text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-md">
                        {{ (int) floor($sisaHari) }} Hari Sisa Terdekat
                    </span>
                </div>
                <div class="text-xs text-gray-500 pt-1 border-t border-gray-100 flex items-center justify-between">
                    <span class="text-gray-400">Paket Terdekat Berakhir:</span>
                    <span class="font-semibold text-gray-700">
                        {{ $primaryLicense && $primaryLicense->tanggal_berakhir ? $primaryLicense->tanggal_berakhir->format('d M Y') : 'Belum Ditentukan' }}
                    </span>
                </div>
                <div class="text-[11px] font-mono text-gray-400 truncate">
                    Key Acuan: {{ $primaryLicense->key ?? 'Belum Ada' }}
                </div>
            </div>
        </div>

        {{-- Card 2: Kuota Pengguna Total --}}
        <div class="bg-white rounded-2xl shadow-soft p-5 border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Kuota {{ $labelMemberPlural }}</span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $kuotaMaksimal > 1 ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                    {{ $kuotaMaksimal > 1 ? 'Akumulasi Multi-Paket' : 'Default Kuota 1 (Admin)' }}
                </span>
            </div>

            @php
                $pct = $kuotaMaksimal > 0 ? min(100, round(($totalPengguna / $kuotaMaksimal) * 100)) : 0;
            @endphp

            <div class="space-y-2">
                <div class="flex items-baseline justify-between">
                    <span class="text-2xl font-black text-gray-800">{{ $totalPengguna }} <span class="text-sm font-normal text-gray-400">/ {{ $kuotaMaksimal }}</span></span>
                    <span class="text-xs font-semibold text-emerald-700">{{ $pct }}%</span>
                </div>

                {{-- Progress Bar --}}
                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full {{ $pct > 90 ? 'bg-rose-500' : ($pct > 75 ? 'bg-amber-500' : 'bg-emerald-600') }}" style="width: {{ $pct }}%"></div>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-500 pt-1">
                    <span>Sisa Slot:</span>
                    <span class="font-semibold text-gray-800">{{ max(0, $kuotaMaksimal - $totalPengguna) }} {{ $labelMember }} Tersedia</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Skema Tarif Lisensi Provider --}}
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl shadow-soft p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Lisensi {{ $tipeSebutan }} (Model Provider)</span>
                    <i class="fab fa-google-play text-emerald-400 text-base"></i>
                </div>
                <div class="space-y-1.5 text-xs text-slate-300">
                    <div class="flex items-center justify-between py-1 border-b border-white/10">
                        <span>Biaya Langganan:</span>
                        <span class="font-bold text-emerald-300">Rp 5.000 / {{ strtolower($labelMember) }} / bln</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-white/10">
                        <span>Model Kuota:</span>
                        <span class="font-medium text-white">Multi-Bucket (1 Bln – 3 Thn)</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span>Metode Pembayaran:</span>
                        <span class="font-bold text-white flex items-center gap-1.5">
                            <i class="fas fa-wallet text-sm text-emerald-400"></i> BCA & Google Pay
                        </span>
                    </div>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-2">Beli paket baru tidak menggantikan paket lama. Jika habis, kuota kembali ke default 1 (Admin) agar tetap bisa login.</p>
        </div>
    </div>

    <!-- Active Packages Section (Model Provider Internet / Bima Tri Multi-Bucket) -->
    <div class="bg-white rounded-2xl shadow-soft p-6 sm:p-7 border border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-5 border-b border-gray-100">
            <div>
                <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-layer-group text-emerald-600"></i>
                    Daftar Paket Lisensi & Kuota Berjalan
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    Sistem multi-paket berjalan aktif bersamaan (seperti paket provider internet/Bima Tri). Masing-masing paket memiliki masa aktif dan tanggal kedaluwarsa mandiri.
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 whitespace-nowrap self-start sm:self-auto">
                <i class="fas fa-cubes"></i> Total {{ $activePackages->count() }} Paket Aktif
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($allPackages as $pkg)
                @php
                    $pkgExpired = $pkg->isExpired();
                    $daysLeft = $pkg->getDaysRemaining();
                    $isExpiringSoon = !$pkgExpired && $daysLeft <= 30;
                @endphp
                <div class="p-4 rounded-2xl border-2 transition-all duration-200 {{ $pkgExpired ? 'border-gray-200 bg-gray-50/60 opacity-80' : ($isExpiringSoon ? 'border-amber-300 bg-amber-50/30' : 'border-emerald-100 bg-emerald-50/20') }} flex flex-col justify-between shadow-xs">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-mono text-xs font-bold {{ $pkgExpired ? 'text-gray-500' : 'text-gray-800' }}">
                                {{ $pkg->key }}
                            </span>
                            @if($pkgExpired)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                    Kedaluwarsa
                                </span>
                            @elseif($isExpiringSoon)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 animate-pulse">
                                    Sisa {{ $daysLeft }} Hari
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Aktif • {{ $daysLeft }} Hari
                                </span>
                            @endif
                        </div>

                        <div class="my-3">
                            <div class="text-2xl font-black text-gray-800">
                                {{ $pkg->kuota_pengguna }} <span class="text-xs font-semibold text-gray-500">{{ $labelMember }}</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                @if($pkgExpired)
                                    <span class="text-rose-600 font-semibold">Telah berakhir pada {{ $pkg->tanggal_berakhir?->format('d M Y') }}</span>
                                @else
                                    Berlaku hingga <span class="font-semibold text-gray-700">{{ $pkg->tanggal_berakhir?->format('d M Y') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 mt-2">
                        <button type="button" 
                                onclick="selectPackageForRenewal('{{ $pkg->id }}')"
                                class="w-full py-2 px-3 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer shadow-xs {{ $pkgExpired ? 'bg-rose-600 hover:bg-rose-700 text-white' : ($isExpiringSoon ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-emerald-600 hover:bg-emerald-700 text-white') }}">
                            <i class="fas fa-rotate"></i>
                            <span>{{ $pkgExpired ? 'Aktifkan Kembali Paket' : 'Perpanjang Paket Ini' }}</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-6 text-center text-gray-400">
                    <p>Belum ada paket kuota terdaftar.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Billing & Checkout Calculator Section -->
    <div id="billingFormSection" class="bg-white rounded-2xl shadow-soft p-6 sm:p-8 border border-gray-100">
        <div class="border-b border-gray-100 pb-4 mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-cart-shopping text-emerald-600"></i>
                Formulir Billing & Transaksi Lisensi {{ $tipeSebutan }}
            </h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Biaya langganan {{ strtolower($tipeSebutan) }} resmi <strong>Rp 5.000 / {{ strtolower($labelMember) }} / bulan</strong> (All-In). Akun {{ strtolower($labelMemberPlural) }} didaftarkan langsung oleh <strong>{{ $labelAdmin }}</strong>.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Left: Konfigurasi Pesanan & Paket (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

                {{-- Langkah 1: Switcher Mode Transaksi: Perpanjang Kuota Berjalan vs Tambah Slot Kuota --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">1</span>
                        <span>Pilih Jenis Kebutuhan Transaksi</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        {{-- Mode 1: Perpanjang Kuota Berjalan (Default) --}}
                        <button type="button" onclick="switchBillingMode('perpanjang')" id="tabBtnPerpanjang"
                            class="p-4 rounded-2xl border-2 text-left transition-all duration-200 border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-500/20 relative shadow-xs cursor-pointer">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    <i class="fas fa-rotate"></i> Perpanjangan Paket
                                </span>
                                <div class="w-5 h-5 rounded-full border-2 border-emerald-600 flex items-center justify-center bg-emerald-600 text-white text-[10px]" id="indicatorPerpanjang">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="font-bold text-gray-900 text-sm sm:text-base">Perpanjang Masa Aktif Lisensi</div>
                            <p class="text-xs text-gray-500 mt-1">
                                Pilih paket kuota berjalan untuk diperpanjang masa aktifnya (<strong>1, 3, 6 Bulan, atau 1–3 Tahun</strong>) tanpa merubah kapasitas {{ strtolower($labelMember) }} pada paket tersebut.
                            </p>
                        </button>

                        {{-- Mode 2: Tambah Slot Kuota Pengguna Baru --}}
                        <button type="button" onclick="switchBillingMode('tambah_kuota')" id="tabBtnTambahKuota"
                            class="p-4 rounded-2xl border-2 text-left transition-all duration-200 border-gray-200 bg-white hover:border-gray-300 relative cursor-pointer">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                                    <i class="fas fa-user-plus"></i> Tambah Slot Baru (Mandiri)
                                </span>
                                <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center bg-white text-transparent text-[10px]" id="indicatorTambahKuota">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <div class="font-bold text-gray-900 text-sm sm:text-base">Tambah Slot Kuota {{ $labelMember }} Baru</div>
                            <p class="text-xs text-gray-500 mt-1">
                                Beli paket kuota baru yang akan aktif mandiri sesuai durasi pilihan (<strong>1 Bulan s/d 3 Tahun</strong>) bersamaan dengan paket lama tanpa memotong masa aktif yang sudah ada.
                            </p>
                        </button>
                    </div>
                </div>

                {{-- Langkah 2: Pilihan Durasi Langganan / Perpanjangan (1, 3, 6 Bulan, 1, 2, 3 Tahun) --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 flex items-center gap-2">
                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">2</span>
                        <span>Pilih Durasi Langganan / Perpanjangan</span>
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                        @php
                            $durationOptions = [
                                ['months' => 1,  'label' => '1 Bulan',  'sub' => 'Rp 5.000 / ' . strtolower($labelMember)],
                                ['months' => 3,  'label' => '3 Bulan',  'sub' => 'Rp 15.000 / ' . strtolower($labelMember)],
                                ['months' => 6,  'label' => '6 Bulan',  'sub' => 'Rp 30.000 / ' . strtolower($labelMember)],
                                ['months' => 12, 'label' => '1 Tahun',  'sub' => 'Rp 60.000 / ' . strtolower($labelMember)],
                                ['months' => 24, 'label' => '2 Tahun',  'sub' => 'Rp 120.000 / ' . strtolower($labelMember)],
                                ['months' => 36, 'label' => '3 Tahun',  'sub' => 'Rp 180.000 / ' . strtolower($labelMember)],
                            ];
                        @endphp
                        @foreach($durationOptions as $opt)
                            <button type="button"
                                    onclick="setDuration({{ $opt['months'] }})"
                                    data-duration="{{ $opt['months'] }}"
                                    class="duration-preset-btn p-3 rounded-xl border-2 text-center transition-all duration-150 cursor-pointer flex flex-col items-center justify-center {{ $opt['months'] === 1 ? 'border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-500/20' : 'border-gray-200 bg-white hover:border-emerald-500' }}">
                                <span class="text-sm font-black text-gray-900">{{ $opt['label'] }}</span>
                                <span class="text-[11px] font-semibold text-emerald-700 mt-0.5">{{ $opt['sub'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- CONTAINER MODE 1: PERPANJANG KUOTA BERJALAN --}}
                <div id="panePerpanjang" class="space-y-4">
                    <div class="bg-gray-50/80 rounded-2xl p-5 border border-gray-200 space-y-4">
                        {{-- Dropdown Pemilihan Paket yang Akan Diperpanjang --}}
                        <div>
                            <label for="selectRenewalPackage" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5 flex items-center gap-1">
                                <i class="fas fa-ticket-simple text-emerald-600"></i>
                                <span>Pilih Paket yang Ingin Diperpanjang</span>
                            </label>
                            <select id="selectRenewalPackage" onchange="onRenewalPackageChange(this.value)"
                                class="w-full h-11 px-3.5 border border-gray-300 rounded-xl text-sm font-semibold bg-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                @foreach($allPackages as $pkg)
                                    <option value="{{ $pkg->id }}" {{ $primaryLicense && $primaryLicense->id === $pkg->id ? 'selected' : '' }}>
                                        Key: {{ $pkg->key }} — ({{ $pkg->kuota_pengguna }} {{ $labelMember }}) • Berlaku s/d {{ $pkg->tanggal_berakhir?->format('d M Y') }} {{ $pkg->isExpired() ? '[Kedaluwarsa]' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Kapasitas Paket Terpilih</span>
                                <div class="text-xl font-black text-gray-800 mt-0.5" id="renewalPackageQuotaText">
                                    {{ $kuotaPerpanjangDefault }} {{ $labelMember }}
                                </div>
                                <span class="text-[11px] text-gray-500 font-medium">Kapasitas paket tetap utuh</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Tarif Resmi</span>
                                <div class="text-sm font-bold text-emerald-700 mt-0.5">
                                    Rp 5.000 / {{ strtolower($labelMember) }} / bln
                                </div>
                                <span class="text-[11px] text-emerald-700 font-semibold">Paket Bulanan All-In</span>
                            </div>
                        </div>

                        {{-- Timeline Akumulasi Tanggal --}}
                        <div class="p-4 rounded-xl bg-white border border-emerald-200 space-y-3 shadow-xs">
                            <div class="flex items-center justify-between text-xs font-bold text-emerald-900">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-calendar-check text-emerald-600"></i>
                                    <span id="renewalDurationHeaderText">Perpanjangan Durasi +1 Bulan</span>
                                </span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Akumulasi Mandiri
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-center">
                                <div class="bg-gray-50 p-3 rounded-xl border border-gray-200">
                                    <span class="block text-[11px] text-gray-500">Masa Aktif Paket Saat Ini</span>
                                    <span class="block text-xs sm:text-sm font-bold text-gray-800 mt-0.5" id="renewalCurrentExpiry">
                                        {{ $primaryLicense && $primaryLicense->tanggal_berakhir ? $primaryLicense->tanggal_berakhir->format('d M Y') : 'Hari Ini' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-center text-emerald-700 font-bold text-xs py-1 sm:py-0">
                                    <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full flex items-center gap-1 text-[11px]">
                                        <i class="fas fa-arrow-right hidden sm:inline"></i>
                                        <i class="fas fa-arrow-down sm:hidden"></i>
                                        <span id="renewalDurationArrowBadge">+1 Bulan</span>
                                    </span>
                                </div>
                                <div class="bg-emerald-50/70 p-3 rounded-xl border border-emerald-200">
                                    <span class="block text-[11px] text-emerald-800 font-medium">Masa Aktif Baru Menjadi</span>
                                    <span class="block text-xs sm:text-sm font-black text-emerald-800 mt-0.5" id="renewalNewExpiry">
                                        {{ $tanggalBerakhirBaruPerpanjang->format('d M Y') }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-[11px] text-gray-600 leading-relaxed bg-emerald-50/40 p-2.5 rounded-lg border border-emerald-100">
                                <i class="fas fa-circle-check text-emerald-600 mr-1"></i>
                                Sisa waktu aktif paket ini tetap utuh tanpa terpotong. Perpanjangan langsung diakumulasikan ke masa berlaku paket tersebut.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- CONTAINER MODE 2: TAMBAH SLOT KUOTA PENGGUNA BARU --}}
                <div id="paneTambahKuota" class="space-y-6 hidden">
                    {{-- Notice Model Provider --}}
                    <div class="bg-blue-50/70 p-3.5 rounded-xl border border-blue-200 text-xs text-blue-900 flex items-start gap-2.5">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5 text-sm flex-shrink-0"></i>
                        <div>
                            <strong>Model Provider Kuota Mandiri:</strong> Kuota tambahan yang Anda beli akan menjadi paket baru yang aktif selama <strong id="topupDurationNoticeText">1 Bulan</strong> sejak hari ini dan berjalan bersamaan dengan paket kuota Anda yang lain.
                        </div>
                    </div>

                    {{-- Opsi: Paket Jumlah Pengguna --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">
                            Pilih Paket Kuota Tambahan <span class="text-xs font-normal text-emerald-700" id="topupPresetRateLabel">(Rp 5.000 / {{ strtolower($labelMember) }} / bulan)</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <button type="button" onclick="setQuota(50)" data-quota="50" class="quota-preset-btn border-2 border-emerald-600 bg-emerald-50/50 ring-2 ring-emerald-500/20 rounded-xl p-3.5 flex flex-col items-center justify-center text-center transition-all duration-150 hover:border-emerald-500">
                                <span class="text-xs font-bold text-gray-500 uppercase">+50 {{ $labelMember }}</span>
                                <span class="text-base font-black text-gray-800 mt-1" id="presetPrice50">Rp 250.000</span>
                                <span class="text-[10px] text-emerald-700 font-semibold mt-0.5">{{ $instansi->getPresetPaketName(50) }}</span>
                            </button>

                            <button type="button" onclick="setQuota(100)" data-quota="100" class="quota-preset-btn border-2 border-gray-200 bg-white rounded-xl p-3.5 flex flex-col items-center justify-center text-center transition-all duration-150 hover:border-emerald-500">
                                <span class="text-xs font-bold text-gray-500 uppercase">+100 {{ $labelMember }}</span>
                                <span class="text-base font-black text-gray-800 mt-1" id="presetPrice100">Rp 500.000</span>
                                <span class="text-[10px] text-gray-500 mt-0.5">{{ $instansi->getPresetPaketName(100) }}</span>
                            </button>

                            <button type="button" onclick="setQuota(250)" data-quota="250" class="quota-preset-btn border-2 border-gray-200 bg-white rounded-xl p-3.5 flex flex-col items-center justify-center text-center transition-all duration-150 hover:border-emerald-500">
                                <span class="text-xs font-bold text-gray-500 uppercase">+250 {{ $labelMember }}</span>
                                <span class="text-base font-black text-gray-800 mt-1" id="presetPrice250">Rp 1.250.000</span>
                                <span class="text-[10px] text-gray-500 mt-0.5">{{ $instansi->getPresetPaketName(250) }}</span>
                            </button>

                            <button type="button" onclick="setQuota(500)" data-quota="500" class="quota-preset-btn border-2 border-gray-200 bg-white rounded-xl p-3.5 flex flex-col items-center justify-center text-center transition-all duration-150 hover:border-emerald-500">
                                <span class="text-xs font-bold text-gray-500 uppercase">+500 {{ $labelMember }}</span>
                                <span class="text-base font-black text-gray-800 mt-1" id="presetPrice500">Rp 2.500.000</span>
                                <span class="text-[10px] text-emerald-700 font-semibold mt-0.5">{{ $instansi->getPresetPaketName(500) }}</span>
                            </button>

                            <button type="button" onclick="setQuota(1000)" data-quota="1000" class="quota-preset-btn border-2 border-gray-200 bg-white rounded-xl p-3.5 flex flex-col items-center justify-center text-center transition-all duration-150 hover:border-emerald-500 col-span-2 sm:col-span-2">
                                <span class="text-xs font-bold text-gray-500 uppercase">+1.000 {{ $labelMember }}</span>
                                <span class="text-base font-black text-gray-800 mt-1" id="presetPrice1000">Rp 5.000.000</span>
                                <span class="text-[10px] text-purple-700 font-semibold mt-0.5">{{ $instansi->getPresetPaketName(1000) }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- Opsi 2: Kustom Jumlah Pengguna --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">
                            Atau Masukkan Jumlah {{ $labelMemberPlural }} Tambahan Kustom
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <input type="number" id="customQuotaInput" min="1" max="10000" step="1" value="50"
                                    placeholder="Ketik jumlah {{ strtolower($labelMember) }}..."
                                    oninput="onCustomQuotaInput(this.value)"
                                    class="w-full h-11 px-3.5 border border-gray-300 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#308a34] focus:border-transparent">
                            </div>
                            <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-3 py-2.5 rounded-xl whitespace-nowrap" id="customQuotaDurationBadge">{{ $labelMember }} (1 Bulan)</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1.5">
                            * Kuota pengguna akan dibuatkan paket lisensi baru mandiri sesuai durasi yang dipilih di atas sejak hari pembelian.
                        </p>
                    </div>
                </div>



            </div>

            <!-- Right: Order Summary & Action Checkout (5 cols, sticky) -->
            <div class="lg:col-span-5 bg-gray-50/90 p-5 sm:p-6 rounded-2xl border border-gray-200 flex flex-col justify-between shadow-xs lg:sticky lg:top-6 self-start space-y-5">
                <div>
                    {{-- Langkah 3: Ringkasan Tagihan --}}
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Langkah 3</span>
                            <h3 class="text-base font-bold text-gray-800">Ringkasan Tagihan</h3>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                            <i class="fas fa-shield-halved text-emerald-600"></i> Checkout Aman
                        </span>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-start justify-between text-gray-600 gap-2">
                            <span id="summaryQuotaText" class="font-medium text-gray-700">Perpanjangan Lisensi ({{ $kuotaPerpanjangDefault }} {{ $labelMember }} / 1 Bln)</span>
                            <span id="summaryQuotaPrice" class="font-semibold text-gray-800 whitespace-nowrap">Rp {{ number_format($subtotalPerpanjang, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-gray-600">
                            <span id="summaryExpiryLabel">Perpanjangan Masa Aktif</span>
                            <span id="summaryExpiryBadge" class="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs">+1 Bulan</span>
                        </div>

                        <div class="flex items-center justify-between text-gray-600">
                            <span>Pajak PPN (11%)</span>
                            <span id="summaryTaxPrice" class="font-semibold text-gray-800">Rp {{ number_format($ppnPerpanjang, 0, ',', '.') }}</span>
                        </div>

                        <div class="pt-3 border-t border-gray-300 flex items-baseline justify-between">
                            <div>
                                <span class="text-base font-bold text-gray-900 block">Total Tagihan</span>
                                <span class="text-[11px] text-gray-500">Termasuk PPN 11% Resmi</span>
                            </div>
                            <span id="summaryTotalPrice" class="text-2xl font-black text-emerald-800">Rp {{ number_format($totalPerpanjang, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Langkah 4: Pilih Metode Pembayaran --}}
                <div class="pt-2 border-t border-gray-200 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Langkah 4</span>
                            <h4 class="text-xs font-bold text-gray-800">PILIH METODE PEMBAYARAN</h4>
                        </div>
                        @php
                            $activeCount = count($paymentMethods);
                        @endphp
                        <span class="text-[10px] font-bold text-gray-500 bg-gray-200/80 px-2 py-0.5 rounded-full">
                            TERSEDIA {{ $activeCount }} OPSI
                        </span>
                    </div>

                    @php
                        $isBcaActive = $paymentMethods->contains('method_key', 'manual_bca');
                        $isMidtransActive = $paymentMethods->contains('method_key', 'midtrans');
                        $isGpayActive = $paymentMethods->contains('method_key', 'google_pay');

                        // Metode default yang terpilih
                        $defaultMethod = $isBcaActive ? 'manual_bca' : ($isMidtransActive ? 'midtrans' : ($isGpayActive ? 'gpay' : ''));

                        // Responsive grid class
                        $gridColsClass = match($activeCount) {
                            1 => 'grid-cols-1',
                            2 => 'grid grid-cols-1 sm:grid-cols-2 gap-2.5',
                            default => 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5',
                        };
                    @endphp

                    @if($activeCount === 0)
                        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-2">
                            <i class="fas fa-exclamation-triangle text-amber-600"></i>
                            <span>Saat ini belum ada metode pembayaran yang diaktifkan oleh Administrator.</span>
                        </div>
                    @else
                        <div class="{{ $gridColsClass }}">
                            {{-- Opsi 1: Transfer BCA --}}
                            @if($isBcaActive)
                                <button type="button" id="btnMethodBca" onclick="selectPaymentMethod('manual_bca')"
                                    class="p-3 rounded-xl border-2 text-left transition-all duration-150 {{ $defaultMethod === 'manual_bca' ? 'border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20 shadow-xs' : 'border-gray-200 bg-white hover:border-gray-300' }} cursor-pointer flex flex-col justify-between">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-xs text-blue-950 flex items-center gap-1.5">
                                            <i class="fas fa-building-columns text-blue-600"></i>
                                            <span>Transfer BCA</span>
                                        </span>
                                        <div id="checkMethodBca" class="w-4 h-4 rounded-full {{ $defaultMethod === 'manual_bca' ? 'bg-blue-600 text-white' : 'border border-gray-300 bg-white text-transparent' }} flex items-center justify-center text-[9px]">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                    <span class="text-[10px] text-blue-700 font-medium">Rek. {{ $bankDetails['account_number'] ?? '842-171-3359' }}</span>
                                </button>
                            @endif

                            {{-- Opsi 2: Midtrans Snap --}}
                            @if($isMidtransActive)
                                <button type="button" id="btnMethodMidtrans" onclick="selectPaymentMethod('midtrans')"
                                    class="p-3 rounded-xl border-2 text-left transition-all duration-150 {{ $defaultMethod === 'midtrans' ? 'border-indigo-600 bg-indigo-50/70 ring-2 ring-indigo-500/20 shadow-xs' : 'border-gray-200 bg-white hover:border-gray-300' }} cursor-pointer flex flex-col justify-between">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-xs text-indigo-950 flex items-center gap-1.5">
                                            <i class="fas fa-bolt text-indigo-600"></i>
                                            <span>Midtrans Snap</span>
                                        </span>
                                        <div id="checkMethodMidtrans" class="w-4 h-4 rounded-full {{ $defaultMethod === 'midtrans' ? 'bg-indigo-600 text-white' : 'border border-gray-300 bg-white text-transparent' }} flex items-center justify-center text-[9px]">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                    <span class="text-[10px] text-indigo-700 font-medium">QRIS, VA Bank, E-Wallet</span>
                                </button>
                            @endif

                            {{-- Opsi 3: Google Pay --}}
                            @if($isGpayActive)
                                <button type="button" id="btnMethodGpay" onclick="selectPaymentMethod('gpay')"
                                    class="p-3 rounded-xl border-2 text-left transition-all duration-150 {{ $defaultMethod === 'gpay' ? 'border-purple-600 bg-purple-50/70 ring-2 ring-purple-500/20 shadow-xs' : 'border-gray-200 bg-white hover:border-gray-300' }} cursor-pointer flex flex-col justify-between">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="font-bold text-xs text-gray-800 flex items-center gap-1.5">
                                            <i class="fab fa-google-pay text-base text-gray-700"></i>
                                            <span>Google Pay</span>
                                        </span>
                                        <div id="checkMethodGpay" class="w-4 h-4 rounded-full {{ $defaultMethod === 'gpay' ? 'bg-purple-600 text-white' : 'border border-gray-300 bg-white text-transparent' }} flex items-center justify-center text-[9px]">
                                            <i class="fas fa-check"></i>
                                        </div>
                                    </div>
                                    <span class="text-[10px] text-gray-500">Instan Otomatis</span>
                                </button>
                            @endif
                        </div>

                        {{-- Detail Card: Bank BCA Manual --}}
                        <div id="paneMethodBca" class="rounded-xl p-4 bg-gradient-to-br from-[#0c2e68] to-[#123e87] text-white space-y-3 shadow-sm border border-blue-900/30 {{ $defaultMethod === 'manual_bca' ? '' : 'hidden' }}">
                            <div class="flex items-center justify-between pb-2 border-b border-white/10">
                                <div class="flex items-center gap-2">
                                    <span class="px-1.5 py-0.5 rounded bg-blue-500/30 font-black text-xs tracking-wider text-blue-100 border border-blue-400/30">{{ $bankDetails['bank'] ?? 'BCA' }}</span>
                                    <span class="text-xs font-bold tracking-wide text-white">BANK CENTRAL ASIA</span>
                                </div>
                                <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-white/15 text-blue-100">Manual</span>
                            </div>

                            <div class="space-y-1.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-blue-200 text-[11px]">Nomor Rekening:</span>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-sm text-white tracking-wider">{{ $bankDetails['account_number'] ?? '842-171-3359' }}</span>
                                        <button type="button" onclick="copyText('{{ preg_replace('/[^0-9]/', '', (string) ($bankDetails['account_number'] ?? '8421713359')) }}', 'Nomor Rekening BCA')"
                                            class="px-2 py-0.5 rounded-md bg-blue-600 hover:bg-blue-500 text-white font-semibold text-[10px] transition-colors cursor-pointer flex items-center gap-1">
                                            <i class="fas fa-copy"></i>
                                            <span>Salin</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-blue-200 text-[11px]">
                                    <span>Atas Nama (A.N):</span>
                                    <span class="font-bold text-white text-[12px]">{{ $bankDetails['account_holder'] ?? 'Kadavi Raditya Alvino' }}</span>
                                </div>
                                <div class="flex items-center justify-between pt-1 border-t border-white/10">
                                    <span class="text-blue-200 text-[11px]">Total Ditransfer:</span>
                                    <span id="bcaTransferTotalText" class="font-extrabold text-amber-300 text-sm">Rp {{ number_format($totalPerpanjang, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="text-[10px] leading-relaxed text-blue-100 bg-white/10 p-2 rounded-lg border border-white/10 flex items-start gap-1.5">
                                <i class="fab fa-whatsapp text-emerald-400 text-xs mt-0.5 flex-shrink-0"></i>
                                <span>Setelah bayar, konfirmasi ke WhatsApp <strong>{{ $bankDetails['wa_phone'] ?? '+62 895-3831-07479' }}</strong> dengan menyertakan Nomor Orderan dan Bukti Transaksi.</span>
                            </div>
                        </div>

                        {{-- Detail Card: Midtrans Snap Gateway --}}
                        <div id="paneMethodMidtrans" class="rounded-xl p-4 bg-gradient-to-br from-[#0f172a] via-[#1e1b4b] to-[#1e293b] text-white space-y-3 shadow-sm border border-indigo-900/40 {{ $defaultMethod === 'midtrans' ? '' : 'hidden' }}">
                            <div class="flex items-center justify-between pb-2 border-b border-white/10">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shadow-xs font-black">
                                        <i class="fas fa-bolt text-amber-300"></i>
                                    </span>
                                    <span class="text-xs font-bold tracking-wide text-white">MIDTRANS SNAP GATEWAY</span>
                                </div>
                                <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Instan Otomatis</span>
                            </div>

                            <div class="space-y-2 text-xs">
                                <div class="flex items-center justify-between text-slate-300 text-[11px]">
                                    <span>Metode Didukung:</span>
                                    <span class="font-semibold text-white">QRIS, VA Bank, E-Wallet, Kartu</span>
                                </div>
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    <span class="px-2 py-0.5 rounded bg-white/10 text-[10px] font-bold text-slate-200">QRIS (BCA, Gopay, OVO)</span>
                                    <span class="px-2 py-0.5 rounded bg-white/10 text-[10px] font-bold text-slate-200">Virtual Account (BCA/Mandiri/BRI)</span>
                                    <span class="px-2 py-0.5 rounded bg-white/10 text-[10px] font-bold text-slate-200">ShopeePay</span>
                                    <span class="px-2 py-0.5 rounded bg-white/10 text-[10px] font-bold text-slate-200">Kartu Kredit/Debit</span>
                                </div>
                                <div class="flex items-center justify-between pt-1 border-t border-white/10">
                                    <span class="text-slate-300 text-[11px]">Total Tagihan:</span>
                                    <span id="midtransTotalText" class="font-extrabold text-amber-300 text-sm">Rp {{ number_format($totalPerpanjang, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="text-[10px] leading-relaxed text-indigo-200 bg-white/10 p-2 rounded-lg border border-white/10 flex items-start gap-1.5">
                                <i class="fas fa-circle-check text-emerald-400 text-xs mt-0.5 flex-shrink-0"></i>
                                <span>Setelah pembayaran diverifikasi oleh Midtrans, lisensi dan kuota pengguna akan <strong>langsung aktif otomatis</strong> dalam hitungan detik.</span>
                            </div>
                        </div>

                        {{-- Detail Card: Google Pay --}}
                        <div id="paneMethodGpay" class="rounded-xl p-3 bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-1 {{ $defaultMethod === 'gpay' ? '' : 'hidden' }}">
                            <div class="flex items-center gap-1.5 font-bold text-emerald-800">
                                <i class="fas fa-bolt text-emerald-600"></i>
                                <span>Pembayaran Instan Google Pay</span>
                            </div>
                            <p class="text-[11px] text-emerald-700">
                                Masa aktif lisensi dan kuota pengguna akan langsung aktif secara otomatis setelah pembayaran kartu Google Pay terverifikasi.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Tombol Aksi Checkout -->
                <div class="space-y-3 pt-1">
                    <!-- Tombol Opsi 1: Transfer Bank BCA -->
                    <div id="actionBtnBcaContainer" class="{{ $defaultMethod === 'manual_bca' ? '' : 'hidden' }}">
                        <button type="button" id="btnPayWithBca" onclick="executeManualBcaCheckout()"
                            class="w-full py-3.5 px-5 rounded-xl font-bold text-white bg-[#25D366] hover:bg-[#1EBE5D] active:scale-[0.99] transition-all duration-200 shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2 cursor-pointer text-sm sm:text-base">
                            <i class="fab fa-whatsapp text-lg"></i>
                            <span>Buat Pesanan & Konfirmasi WhatsApp</span>
                        </button>
                    </div>

                    <!-- Tombol Opsi 2: Midtrans Snap -->
                    <div id="actionBtnMidtransContainer" class="{{ $defaultMethod === 'midtrans' ? '' : 'hidden' }}">
                        <button type="button" id="btnPayWithMidtrans" onclick="executeMidtransCheckout()"
                            class="w-full py-3.5 px-5 rounded-xl font-bold text-white bg-gradient-to-r from-blue-600 via-indigo-600 to-emerald-600 hover:opacity-95 active:scale-[0.99] transition-all duration-200 shadow-md shadow-indigo-600/20 flex items-center justify-center gap-2 cursor-pointer text-sm sm:text-base">
                            <i class="fas fa-bolt text-amber-300"></i>
                            <span>Bayar Instan Otomatis</span>
                        </button>
                    </div>

                    <!-- Tombol Opsi 3: Google Pay -->
                    <div id="actionBtnGpayContainer" class="{{ $defaultMethod === 'gpay' ? '' : 'hidden' }}">
                        <div id="gpay-button-container" class="w-full flex justify-center min-h-[48px]">
                            <button type="button" id="btnPayWithGoogle" onclick="openPaymentConfirmation()"
                                class="w-full py-3 px-5 rounded-xl font-bold text-white bg-black hover:bg-neutral-800 active:scale-[0.99] transition-all duration-200 shadow-md shadow-black/20 flex items-center justify-center cursor-pointer min-h-[48px]">
                                <svg class="h-6 w-auto flex-shrink-0" viewBox="9 11 52 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M26.544 24.1659C26.544 23.5831 26.4971 23.0003 26.4034 22.4294H18.4434V25.7239H23.0037C22.8161 26.7825 22.2065 27.734 21.3155 28.3286V30.4695H24.0353C25.6296 28.9828 26.544 26.7825 26.544 24.1659Z" fill="#4285F4"/>
                                    <path d="M18.4442 32.539C20.7185 32.539 22.6411 31.7778 24.0361 30.4695L21.3164 28.3287C20.5544 28.852 19.5814 29.1493 18.4442 29.1493C16.2403 29.1493 14.3763 27.6388 13.7081 25.5908H10.8794V27.7853C12.2625 30.5284 15.148 32.539 18.4442 32.539Z" fill="#34A853"/>
                                    <path d="M13.7083 25.5908C13.3524 24.5322 13.3524 23.3826 13.7083 22.324V19.3995H10.8795C9.6976 21.7554 9.6976 24.5824 10.8795 26.9383L13.7083 25.5908Z" fill="#FBBC04"/>
                                    <path d="M18.4442 15.9287C19.6496 15.9049 20.8219 16.3687 21.6777 17.2251L24.0954 14.8074C22.5601 13.2721 20.5376 12.4157 18.4442 12.4395C15.148 12.4395 12.2625 14.4501 10.8794 17.1932L13.7081 19.3877C14.3763 17.3397 16.2403 15.9287 18.4442 15.9287Z" fill="#EA4335"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-1.5 text-[11px] text-gray-500 text-center">
                        <i class="fas fa-shield-halved text-emerald-600"></i>
                        <span>Enkripsi Aman • Struk & Tagihan Pajak PDF Resmi Diterbitkan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History Table -->
    <div class="bg-white rounded-2xl shadow-soft p-6 border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-clock-rotate-left text-emerald-600"></i>
            Riwayat Pembayaran & Billing
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left bg-gray-50/50">
                        <th class="px-4 py-3">Order ID</th>
                        <th class="px-4 py-3">Tipe Transaksi</th>
                        <th class="px-4 py-3">Rincian Paket</th>
                        <th class="px-4 py-3">Total Nominal</th>
                        <th class="px-4 py-3">Metode</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3 text-center">Aksi / Struk</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs font-semibold text-gray-700">{{ $trx->order_id }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">
                            @php
                                $trxDurasiStr = match((int) $trx->durasi_bulan) {
                                    12 => '1 Thn',
                                    24 => '2 Thn',
                                    36 => '3 Thn',
                                    default => (($trx->durasi_bulan ?: 1) . ' Bln'),
                                };
                            @endphp
                            @if($trx->tipe_transaksi === 'perpanjang_lisensi')
                                <span class="inline-flex items-center gap-1 font-semibold text-emerald-800">
                                    <i class="fas fa-rotate text-xs"></i> Perpanjangan Lisensi (+{{ $trxDurasiStr }})
                                </span>
                            @elseif($trx->tipe_transaksi === 'tambah_kuota')
                                <span class="inline-flex items-center gap-1 font-semibold text-blue-800">
                                    <i class="fas fa-user-plus text-xs"></i> Tambah Paket Kuota Baru
                                </span>
                            @elseif($trx->tipe_transaksi === 'lisensi_bulanan')
                                Lisensi Pengguna Bulanan
                            @elseif($trx->tipe_transaksi === 'lisensi_tahunan')
                                Lisensi Pengguna Tahunan
                            @elseif($trx->tipe_transaksi === 'perpanjang_layanan')
                                Perpanjangan Layanan
                            @else
                                Perpanjangan & Kuota
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            @if($trx->tipe_transaksi === 'perpanjang_lisensi')
                                <span class="inline-block bg-emerald-50 text-emerald-800 px-2.5 py-0.5 rounded font-medium border border-emerald-100">
                                    {{ $trx->payload['kuota_paket'] ?? ($trx->payload['kuota_saat_perpanjang'] ?? $kuotaMaksimal) }} {{ $labelMember }} (Perpanjang +{{ $trxDurasiStr }})
                                </span>
                            @elseif($trx->tipe_transaksi === 'tambah_kuota')
                                <span class="inline-block bg-blue-50 text-blue-800 px-2.5 py-0.5 rounded font-medium border border-blue-100">
                                    +{{ $trx->tambah_kuota }} {{ $labelMember }} (Mandiri {{ $trxDurasiStr }})
                                </span>
                            @elseif($trx->tipe_transaksi === 'lisensi_bulanan')
                                <span class="inline-block bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded font-medium">+{{ $trx->tambah_kuota }} {{ $labelMember }} (1 Bln)</span>
                            @elseif($trx->tipe_transaksi === 'lisensi_tahunan')
                                <span class="inline-block bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded font-medium">+{{ $trx->tambah_kuota }} {{ $labelMember }} (1 Thn)</span>
                            @else
                                @if($trx->durasi_bulan > 0)
                                    <span class="inline-block bg-emerald-50 text-emerald-800 px-2 py-0.5 rounded font-medium">+{{ $trx->durasi_bulan }} Bulan</span>
                                @endif
                                @if($trx->tambah_kuota > 0)
                                    <span class="inline-block bg-blue-50 text-blue-800 px-2 py-0.5 rounded font-medium ml-1">+{{ $trx->tambah_kuota }} User</span>
                                @endif
                            @endif
                        </td>
                        <td class="px-4 py-3 font-bold text-gray-900">Rp {{ number_format($trx->nominal, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            @if($trx->payment_method === 'manual_transfer' || $trx->payment_method === 'manual_bca' || str_starts_with($trx->order_id, 'BCA-'))
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-900 text-white shadow-2xs">
                                    <i class="fas fa-building-columns text-xs text-blue-300"></i> Transfer BCA
                                </span>
                            @elseif($trx->payment_method === 'midtrans' || str_starts_with($trx->order_id, 'MID-'))
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gradient-to-r from-blue-700 to-indigo-800 text-white shadow-2xs">
                                    <i class="fas fa-bolt text-xs text-amber-300"></i> Midtrans Snap
                                </span>
                            @elseif($trx->payment_method === 'google_play' || $trx->payment_method === 'google_play_inapp')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-900 text-white shadow-2xs">
                                    <i class="fab fa-google-play text-xs text-emerald-300"></i> Google Play
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-neutral-900 text-white shadow-2xs">
                                    <i class="fab fa-google-pay text-sm text-neutral-300"></i> Google Pay
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($trx->status === 'success' || $trx->status === 'paid')
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                    Berhasil
                                </span>
                            @elseif($trx->status === 'pending')
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 animate-pulse">
                                    Menunggu Verifikasi
                                </span>
                            @elseif($trx->status === 'cancelled')
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 border border-gray-200">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-rose-100 text-rose-800">
                                    {{ ucfirst($trx->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                {{-- Tombol Bayar Sekarang untuk Midtrans yang masih Pending --}}
                                @if($trx->status === 'pending' && ($trx->payment_method === 'midtrans' || str_starts_with($trx->order_id, 'MID-')))
                                    @php
                                        $snapToken = $trx->payment_token ?? ($trx->payload['snap_token'] ?? null);
                                        $redirectUrl = $trx->payload['redirect_url'] ?? null;
                                    @endphp
                                    @if($snapToken)
                                        <button type="button" 
                                                onclick="resumeMidtransPay('{{ $snapToken }}', '{{ $trx->order_id }}', '{{ $redirectUrl }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-extrabold bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white transition-all shadow-xs cursor-pointer active:scale-95 animate-pulse"
                                                title="Buka Jendela Pembayaran Midtrans Snap">
                                            <i class="fas fa-bolt text-amber-300"></i>
                                            <span>Bayar Sekarang</span>
                                        </button>
                                    @elseif($redirectUrl)
                                        <a href="{{ $redirectUrl }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-extrabold bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white transition-all shadow-xs"
                                           title="Buka Halaman Pembayaran Midtrans">
                                            <i class="fas fa-bolt text-amber-300"></i>
                                            <span>Bayar Sekarang</span>
                                        </a>
                                    @endif

                                    {{-- Ganti Metode ke Transfer BCA --}}
                                    <button type="button" 
                                            onclick="confirmSwitchMethod('{{ $trx->id }}', 'manual_transfer', '{{ $trx->order_id }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition-colors shadow-2xs cursor-pointer active:scale-95"
                                            title="Ganti metode ke Transfer Bank BCA">
                                        <i class="fas fa-building-columns text-blue-600"></i>
                                        <span>Ganti BCA</span>
                                    </button>

                                    {{-- Batalkan Pesanan --}}
                                    <button type="button" 
                                            onclick="confirmCancelOrder('{{ $trx->id }}', '{{ $trx->order_id }}')"
                                            class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition-colors shadow-2xs cursor-pointer active:scale-95"
                                            title="Batalkan pesanan ini">
                                        <i class="fas fa-times text-rose-500"></i>
                                        <span>Batal</span>
                                    </button>
                                @endif

                                @if($trx->status === 'pending' && ($trx->payment_method === 'manual_transfer' || $trx->payment_method === 'manual_bca' || str_starts_with($trx->order_id, 'BCA-')))
                                    @php
                                        $waDirectUrl = $trx->payload['confirmation_wa_url'] ?? ('https://wa.me/62895383107479?text=' . rawurlencode("Halo Admin Aku Sehat, saya ingin konfirmasi pembayaran order {$trx->order_id}"));
                                    @endphp
                                    <a href="{{ $waDirectUrl }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-[#25D366] hover:bg-[#1EBE5D] text-white transition-colors shadow-xs"
                                       title="Kirim Bukti Pembayaran ke WhatsApp Admin">
                                        <i class="fab fa-whatsapp"></i>
                                        <span>Konfirmasi WA</span>
                                    </a>

                                    {{-- Ganti Metode ke Midtrans Snap --}}
                                    <button type="button" 
                                            onclick="confirmSwitchMethod('{{ $trx->id }}', 'midtrans', '{{ $trx->order_id }}')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 transition-colors shadow-2xs cursor-pointer active:scale-95"
                                            title="Ganti metode ke Midtrans Snap (QRIS, VA Bank, E-Wallet)">
                                        <i class="fas fa-bolt text-indigo-600"></i>
                                        <span>Ganti Midtrans</span>
                                    </button>

                                    {{-- Batalkan Pesanan --}}
                                    <button type="button" 
                                            onclick="confirmCancelOrder('{{ $trx->id }}', '{{ $trx->order_id }}')"
                                            class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition-colors shadow-2xs cursor-pointer active:scale-95"
                                            title="Batalkan pesanan ini">
                                        <i class="fas fa-times text-rose-500"></i>
                                        <span>Batal</span>
                                    </button>
                                @endif

                                @if($trx->status !== 'cancelled' && $trx->status !== 'failed')
                                    <a href="{{ route('admin.billing.invoice.pdf', $trx->id) }}?preview=1" 
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 transition-colors shadow-2xs"
                                       title="Lihat & Unduh Struk Belanja Resmi (PDF)">
                                        <i class="fas fa-file-invoice text-emerald-600"></i>
                                        <span>Struk PDF</span>
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 font-medium py-1 px-2">-</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-receipt text-3xl mb-2 text-gray-300"></i>
                            <p>Belum ada riwayat pembayaran.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="mt-4 pt-3 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL KONFIRMASI SEBELUM BAYAR (MENCEGAH SALAH PENCET) --}}
<div id="paymentConfirmModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 text-center animate-in fade-in zoom-in-95 duration-200">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-3 text-xl border border-emerald-100 shadow-xs">
            <i class="fas fa-shield-halved text-2xl text-emerald-600"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-1">Konfirmasi Pembayaran</h3>
        <p class="text-xs text-gray-500 mb-4">Pastikan rincian pesanan sudah sesuai sebelum melanjutkan ke Google Pay.</p>

        <div class="bg-gray-50 rounded-2xl p-4 text-left space-y-2.5 mb-5 border border-gray-100 text-xs">
            <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                <span class="text-gray-500 font-medium">Jenis Transaksi:</span>
                <span class="font-bold text-gray-800" id="confirmModeText">Perpanjangan Lisensi</span>
            </div>
            <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                <span class="text-gray-500 font-medium">Paket Terpilih / Tambahan:</span>
                <span class="font-bold text-gray-800" id="confirmQuotaText">{{ $kuotaPerpanjangDefault }} {{ $labelMember }}</span>
            </div>
            <div class="flex justify-between items-center pb-2 border-b border-gray-200">
                <span class="text-gray-500 font-medium">Masa Aktif:</span>
                <span class="font-semibold text-emerald-700" id="confirmExpiryText">+1 Bulan</span>
            </div>
            <div class="flex justify-between items-center pt-1">
                <span class="text-gray-700 font-bold text-sm">Total Bayar:</span>
                <span class="font-black text-emerald-700 text-base" id="confirmTotalText">Rp {{ number_format($totalPerpanjang, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <button type="button" onclick="closePaymentConfirmation()" class="py-3 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs transition-colors cursor-pointer">
                Periksa Kembali
            </button>
            <button type="button" onclick="executeGooglePayCheckout()" id="btnConfirmExecute" class="py-3 px-4 bg-black hover:bg-neutral-800 text-white rounded-xl font-bold text-xs transition-all shadow-md flex items-center justify-center gap-1.5 cursor-pointer">
                <span>Lanjutkan Bayar</span>
                <i class="fas fa-arrow-right text-[10px]"></i>
            </button>
        </div>
    </div>
</div>

{{-- MODAL SUKSES PEMBAYARAN GOOGLE PAY --}}
<div id="paymentSuccessModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 text-center animate-in fade-in zoom-in-95 duration-200">
        <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl shadow-md shadow-emerald-500/20">
            <i class="fas fa-check"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-1" id="paymentSuccessModalTitle">Pembayaran Berhasil!</h3>
        <p class="text-xs text-gray-500 mb-5" id="successOrderSubtext">Transaksi berhasil diverifikasi.</p>

        <div class="bg-gray-50 rounded-2xl p-4 text-left space-y-2 mb-6 border border-gray-100 text-xs">
            <div class="flex justify-between">
                <span class="text-gray-500">Order ID:</span>
                <span class="font-mono font-bold text-gray-800" id="successOrderId">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Total Dibayar:</span>
                <span class="font-bold text-emerald-800" id="successAmount">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Paket Terkait:</span>
                <span class="font-mono font-bold text-gray-800" id="successPackageKey">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Total Kuota Aktif Institusi:</span>
                <span class="font-bold text-emerald-800" id="successQuota">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Masa Aktif Paket:</span>
                <span class="font-bold text-gray-800" id="successExpiry">-</span>
            </div>
        </div>

        <div class="space-y-2">
            <a href="#" id="modalDownloadInvoiceBtn" target="_blank" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-md shadow-emerald-600/30 flex items-center justify-center gap-2 transition-all">
                <i class="fas fa-file-invoice"></i>
                <span>Unduh Struk Resmi (PDF)</span>
            </a>
            <button type="button" onclick="window.location.reload()" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold text-xs transition-colors">
                Tutup & Refresh Halaman
            </button>
        </div>
    </div>
</div>

{{-- MODAL SUKSES PESANAN TRANSFER MANUAL BCA --}}
<div id="bcaSuccessModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 text-center animate-in fade-in zoom-in-95 duration-200">
        <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl shadow-md shadow-blue-500/20">
            <i class="fas fa-building-columns"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-800 mb-1">Pesanan Transfer BCA Dibuat!</h3>
        <p class="text-xs text-gray-500 mb-4">Silakan selesaikan pembayaran dan kirimkan bukti transfer ke WhatsApp admin.</p>

        <div class="bg-blue-50/70 rounded-2xl p-4 text-left space-y-2.5 mb-5 border border-blue-100 text-xs">
            <div class="flex justify-between items-center pb-2 border-b border-blue-200/60">
                <span class="text-gray-500 font-medium">No. Order:</span>
                <span class="font-mono font-bold text-blue-900 select-all" id="bcaModalOrderId">-</span>
            </div>
            <div class="flex justify-between items-center pb-2 border-b border-blue-200/60">
                <span class="text-gray-500 font-medium">Bank Tujuan:</span>
                <span class="font-bold text-gray-800">BCA (Bank Central Asia)</span>
            </div>
            <div class="flex justify-between items-center pb-2 border-b border-blue-200/60">
                <span class="text-gray-500 font-medium">No. Rekening:</span>
                <div class="flex items-center gap-1.5">
                    <span class="font-mono font-black text-gray-900">842-171-3359</span>
                    <button type="button" onclick="copyText('842-171-3359', 'Nomor rekening BCA')" class="text-[10px] text-blue-700 bg-blue-100 px-1.5 py-0.5 rounded font-bold hover:bg-blue-200 cursor-pointer">Salin</button>
                </div>
            </div>
            <div class="flex justify-between items-center pb-2 border-b border-blue-200/60">
                <span class="text-gray-500 font-medium">Atas Nama:</span>
                <span class="font-bold text-gray-800">Kadavi Raditya Alvino</span>
            </div>
            <div class="flex justify-between items-center pt-0.5">
                <span class="text-gray-700 font-bold">Total Ditransfer:</span>
                <span class="font-black text-emerald-700 text-base" id="bcaModalTotalText">-</span>
            </div>
        </div>

        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-900 mb-5 text-left flex items-start gap-2">
            <i class="fas fa-triangle-exclamation text-amber-600 mt-0.5 flex-shrink-0"></i>
            <div>
                <strong>Penting:</strong> Kirimkan screenshot/foto bukti transfer ke WhatsApp <strong>+62 895-3831-07479</strong> dengan menyertakan Nomor Order untuk mempercepat proses aktivasi lisensi.
            </div>
        </div>

        <div class="space-y-2">
            <a href="#" id="bcaModalWaBtn" target="_blank" class="w-full py-3 px-4 bg-[#25D366] hover:bg-[#1EBE5D] text-white rounded-xl font-bold text-sm shadow-md shadow-emerald-600/30 flex items-center justify-center gap-2 transition-all">
                <i class="fab fa-whatsapp text-lg"></i>
                <span>Konfirmasi via WhatsApp Sekarang</span>
            </a>
            <a href="#" id="bcaModalDownloadInvoiceBtn" target="_blank" class="w-full py-2.5 px-4 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                <i class="fas fa-file-invoice"></i>
                <span>Unduh Tagihan / Invoice (PDF)</span>
            </a>
            <button type="button" onclick="window.location.reload()" class="w-full py-2 bg-transparent text-gray-500 hover:text-gray-700 font-semibold text-xs cursor-pointer">
                Tutup & Refresh Halaman
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(!empty($midtransConfig['is_active']))
{{-- Midtrans Snap Client SDK --}}
<script src="{{ $midtransConfig['snap_js'] }}" data-client-key="{{ $midtransConfig['client_key'] }}"></script>
@endif

@if(!empty($googlePayConfig['is_active']))
{{-- Google Payment API Client SDK --}}
<script src="https://pay.google.com/gp/p/js/pay.js" async onload="onGooglePayLoaded()"></script>
@endif

<script>
    const HARGA_USER = {{ $hargaPerUser }}; // 5000 per bulan
    const LABEL_MEMBER = '{{ $labelMember }}';
    const ALL_PACKAGES = @json($packagesPayload ?? []);

    let currentMode = 'perpanjang'; // 'perpanjang' or 'tambah_kuota'
    let selectedRenewalPackageId = '{{ $primaryLicense ? $primaryLicense->id : '' }}';
    let selectedQuota = 50;
    let selectedDuration = 1; // 1, 3, 6, 12, 24, 36 bulan
    let selectedPaymentMethod = '{{ $defaultMethod ?? 'manual_bca' }}';
    let paymentsClient = null;

    function formatRupiah(num) {
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function getDurationLabel(months) {
        switch (parseInt(months)) {
            case 1: return '1 Bulan';
            case 3: return '3 Bulan';
            case 6: return '6 Bulan';
            case 12: return '1 Tahun';
            case 24: return '2 Tahun';
            case 36: return '3 Tahun';
            default: return months + ' Bulan';
        }
    }

    function getPackageById(id) {
        return ALL_PACKAGES.find(p => p.id === id) || ALL_PACKAGES[0] || null;
    }

    function setDuration(months) {
        selectedDuration = parseInt(months) || 1;
        const durLabel = getDurationLabel(selectedDuration);

        document.querySelectorAll('.duration-preset-btn').forEach(btn => {
            const btnVal = parseInt(btn.getAttribute('data-duration'));
            if (btnVal === selectedDuration) {
                btn.classList.add('border-emerald-600', 'bg-emerald-50/60', 'ring-2', 'ring-emerald-500/20');
                btn.classList.remove('border-gray-200', 'bg-white');
            } else {
                btn.classList.remove('border-emerald-600', 'bg-emerald-50/60', 'ring-2', 'ring-emerald-500/20');
                btn.classList.add('border-gray-200', 'bg-white');
            }
        });

        // Update preset quota button prices dynamically based on selectedDuration
        [50, 100, 250, 500, 1000].forEach(q => {
            const priceEl = document.getElementById('presetPrice' + q);
            if (priceEl) {
                priceEl.textContent = formatRupiah(q * HARGA_USER * selectedDuration);
            }
        });

        const topupNoticeEl = document.getElementById('topupDurationNoticeText');
        if (topupNoticeEl) topupNoticeEl.textContent = durLabel;

        const customBadgeEl = document.getElementById('customQuotaDurationBadge');
        if (customBadgeEl) customBadgeEl.textContent = `${LABEL_MEMBER} (${durLabel})`;

        const renewalHeaderEl = document.getElementById('renewalDurationHeaderText');
        if (renewalHeaderEl) renewalHeaderEl.textContent = `Perpanjangan Durasi +${durLabel}`;

        const renewalArrowEl = document.getElementById('renewalDurationArrowBadge');
        if (renewalArrowEl) renewalArrowEl.textContent = `+${durLabel}`;

        updateRenewalExpiryPreview();
        calculateTotal();
    }

    function updateRenewalExpiryPreview() {
        const pkg = getPackageById(selectedRenewalPackageId);
        const newExpEl = document.getElementById('renewalNewExpiry');
        if (!newExpEl) return;

        const baseDate = (pkg && pkg.raw_expiry && !pkg.is_expired)
            ? new Date(pkg.raw_expiry)
            : new Date();
        baseDate.setMonth(baseDate.getMonth() + selectedDuration);
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        newExpEl.textContent = `${baseDate.getDate()} ${months[baseDate.getMonth()]} ${baseDate.getFullYear()}`;
    }

    function switchBillingMode(mode) {
        currentMode = mode;
        const tabPerpanjang = document.getElementById('tabBtnPerpanjang');
        const tabTambah = document.getElementById('tabBtnTambahKuota');
        const indPerpanjang = document.getElementById('indicatorPerpanjang');
        const indTambah = document.getElementById('indicatorTambahKuota');
        const panePerpanjang = document.getElementById('panePerpanjang');
        const paneTambah = document.getElementById('paneTambahKuota');

        if (mode === 'perpanjang') {
            tabPerpanjang.className = "p-4 rounded-2xl border-2 text-left transition-all duration-200 border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-500/20 relative shadow-xs";
            tabTambah.className = "p-4 rounded-2xl border-2 text-left transition-all duration-200 border-gray-200 bg-white hover:border-gray-300 relative";
            indPerpanjang.className = "w-5 h-5 rounded-full border-2 border-emerald-600 flex items-center justify-center bg-emerald-600 text-white text-[10px]";
            indTambah.className = "w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center bg-white text-transparent text-[10px]";
            panePerpanjang.classList.remove('hidden');
            paneTambah.classList.add('hidden');
        } else {
            tabTambah.className = "p-4 rounded-2xl border-2 text-left transition-all duration-200 border-emerald-600 bg-emerald-50/60 ring-2 ring-emerald-500/20 relative shadow-xs";
            tabPerpanjang.className = "p-4 rounded-2xl border-2 text-left transition-all duration-200 border-gray-200 bg-white hover:border-gray-300 relative";
            indTambah.className = "w-5 h-5 rounded-full border-2 border-emerald-600 flex items-center justify-center bg-emerald-600 text-white text-[10px]";
            indPerpanjang.className = "w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center bg-white text-transparent text-[10px]";
            paneTambah.classList.remove('hidden');
            panePerpanjang.classList.add('hidden');
        }

        calculateTotal();
    }

    function selectPackageForRenewal(pkgId) {
        switchBillingMode('perpanjang');
        selectedRenewalPackageId = pkgId;
        const selectEl = document.getElementById('selectRenewalPackage');
        if (selectEl) {
            selectEl.value = pkgId;
        }
        onRenewalPackageChange(pkgId);

        // Smooth scroll to billing form section
        const formSection = document.getElementById('billingFormSection');
        if (formSection) {
            formSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function onRenewalPackageChange(pkgId) {
        selectedRenewalPackageId = pkgId;
        const pkg = getPackageById(pkgId);
        if (!pkg) return;

        const quotaTextEl = document.getElementById('renewalPackageQuotaText');
        const currentExpEl = document.getElementById('renewalCurrentExpiry');

        if (quotaTextEl) {
            quotaTextEl.textContent = `${pkg.quota} ${LABEL_MEMBER}`;
        }
        if (currentExpEl) {
            currentExpEl.textContent = pkg.expiry_formatted;
        }
        updateRenewalExpiryPreview();

        calculateTotal();
    }

    function calculateTotal() {
        const summaryQuotaEl = document.getElementById('summaryQuotaText');
        const summaryPriceEl = document.getElementById('summaryQuotaPrice');
        const summaryTaxEl = document.getElementById('summaryTaxPrice');
        const summaryTotalEl = document.getElementById('summaryTotalPrice');
        const summaryExpiryLabel = document.getElementById('summaryExpiryLabel');
        const summaryExpiryBadge = document.getElementById('summaryExpiryBadge');
        const durLabel = getDurationLabel(selectedDuration);

        let total = 0;
        if (currentMode === 'perpanjang') {
            const pkg = getPackageById(selectedRenewalPackageId);
            const quota = pkg ? pkg.quota : 50;
            const subtotal = quota * HARGA_USER * selectedDuration;
            const tax = Math.round(subtotal * 0.11);
            total = subtotal + tax;

            if (summaryQuotaEl) {
                summaryQuotaEl.textContent = `Perpanjangan Paket (${pkg ? pkg.key : 'Lisensi'} - ${quota} ${LABEL_MEMBER} × ${durLabel})`;
            }
            if (summaryPriceEl) summaryPriceEl.textContent = formatRupiah(subtotal);
            if (summaryTaxEl) summaryTaxEl.textContent = formatRupiah(tax);
            if (summaryTotalEl) summaryTotalEl.textContent = formatRupiah(total);
            if (summaryExpiryLabel) summaryExpiryLabel.textContent = 'Masa Perpanjangan';
            if (summaryExpiryBadge) {
                summaryExpiryBadge.textContent = `+${durLabel} (Mandiri)`;
                summaryExpiryBadge.className = 'font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs';
            }
        } else {
            const subtotal = selectedQuota * HARGA_USER * selectedDuration;
            const tax = Math.round(subtotal * 0.11);
            total = subtotal + tax;

            if (summaryQuotaEl) summaryQuotaEl.textContent = `Paket Kuota Baru (+${selectedQuota} ${LABEL_MEMBER} × ${durLabel})`;
            if (summaryPriceEl) summaryPriceEl.textContent = formatRupiah(subtotal);
            if (summaryTaxEl) summaryTaxEl.textContent = formatRupiah(tax);
            if (summaryTotalEl) summaryTotalEl.textContent = formatRupiah(total);
            if (summaryExpiryLabel) summaryExpiryLabel.textContent = 'Masa Aktif Paket Baru';
            if (summaryExpiryBadge) {
                summaryExpiryBadge.textContent = `${durLabel} Mandiri (Multi-Paket)`;
                summaryExpiryBadge.className = 'font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded text-xs';
            }
        }

        // Total calculation complete
        const bcaTransferTotalEl = document.getElementById('bcaTransferTotalText');
        if (bcaTransferTotalEl) bcaTransferTotalEl.textContent = formatRupiah(total);

        const midtransTotalEl = document.getElementById('midtransTotalText');
        if (midtransTotalEl) midtransTotalEl.textContent = formatRupiah(total);

        return total;
    }

    function setQuota(qty) {
        selectedQuota = parseInt(qty) || 50;
        if (selectedQuota < 1) selectedQuota = 1;
        
        const input = document.getElementById('customQuotaInput');
        if (input) input.value = selectedQuota;
        
        document.querySelectorAll('.quota-preset-btn').forEach(btn => {
            const btnVal = parseInt(btn.getAttribute('data-quota'));
            if (btnVal === selectedQuota) {
                btn.classList.add('border-emerald-600', 'bg-emerald-50/50', 'ring-2', 'ring-emerald-500/20');
                btn.classList.remove('border-gray-200', 'bg-white');
            } else {
                btn.classList.remove('border-emerald-600', 'bg-emerald-50/50', 'ring-2', 'ring-emerald-500/20');
                btn.classList.add('border-gray-200', 'bg-white');
            }
        });

        calculateTotal();
    }

    function onCustomQuotaInput(val) {
        let qty = parseInt(val) || 0;
        if (qty < 1) qty = 1;
        selectedQuota = qty;
        
        document.querySelectorAll('.quota-preset-btn').forEach(btn => {
            const btnVal = parseInt(btn.getAttribute('data-quota'));
            if (btnVal === selectedQuota) {
                btn.classList.add('border-emerald-600', 'bg-emerald-50/50', 'ring-2', 'ring-emerald-500/20');
                btn.classList.remove('border-gray-200', 'bg-white');
            } else {
                btn.classList.remove('border-emerald-600', 'bg-emerald-50/50', 'ring-2', 'ring-emerald-500/20');
                btn.classList.add('border-gray-200', 'bg-white');
            }
        });

        calculateTotal();
    }

    // Pemilihan Metode Pembayaran Dinamis (BCA vs Midtrans vs Google Pay)
    function selectPaymentMethod(method) {
        selectedPaymentMethod = method;
        const btnBca = document.getElementById('btnMethodBca');
        const btnMidtrans = document.getElementById('btnMethodMidtrans');
        const btnGpay = document.getElementById('btnMethodGpay');

        const checkBca = document.getElementById('checkMethodBca');
        const checkMidtrans = document.getElementById('checkMethodMidtrans');
        const checkGpay = document.getElementById('checkMethodGpay');

        const paneBca = document.getElementById('paneMethodBca');
        const paneMidtrans = document.getElementById('paneMethodMidtrans');
        const paneGpay = document.getElementById('paneMethodGpay');

        const actionBca = document.getElementById('actionBtnBcaContainer');
        const actionMidtrans = document.getElementById('actionBtnMidtransContainer');
        const actionGpay = document.getElementById('actionBtnGpayContainer');
        const badgeEl = document.getElementById('selectedMethodBadge');

        // Reset semua tombol ke status default
        if (btnBca) btnBca.className = "p-3 rounded-xl border-2 text-left transition-all duration-150 border-gray-200 bg-white hover:border-gray-300 cursor-pointer flex flex-col justify-between";
        if (btnMidtrans) btnMidtrans.className = "p-3 rounded-xl border-2 text-left transition-all duration-150 border-gray-200 bg-white hover:border-gray-300 cursor-pointer flex flex-col justify-between";
        if (btnGpay) btnGpay.className = "p-3 rounded-xl border-2 text-left transition-all duration-150 border-gray-200 bg-white hover:border-gray-300 cursor-pointer flex flex-col justify-between";

        if (checkBca) checkBca.className = "w-4 h-4 rounded-full border border-gray-300 bg-white text-transparent flex items-center justify-center text-[9px]";
        if (checkMidtrans) checkMidtrans.className = "w-4 h-4 rounded-full border border-gray-300 bg-white text-transparent flex items-center justify-center text-[9px]";
        if (checkGpay) checkGpay.className = "w-4 h-4 rounded-full border border-gray-300 bg-white text-transparent flex items-center justify-center text-[9px]";

        if (paneBca) paneBca.classList.add('hidden');
        if (paneMidtrans) paneMidtrans.classList.add('hidden');
        if (paneGpay) paneGpay.classList.add('hidden');

        if (actionBca) actionBca.classList.add('hidden');
        if (actionMidtrans) actionMidtrans.classList.add('hidden');
        if (actionGpay) actionGpay.classList.add('hidden');

        if (method === 'manual_bca') {
            if (btnBca) btnBca.className = "p-3 rounded-xl border-2 text-left transition-all duration-150 border-blue-600 bg-blue-50/70 ring-2 ring-blue-500/20 cursor-pointer flex flex-col justify-between shadow-xs";
            if (checkBca) checkBca.className = "w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center text-[9px]";
            if (paneBca) paneBca.classList.remove('hidden');
            if (actionBca) actionBca.classList.remove('hidden');
            if (badgeEl) badgeEl.innerHTML = '<i class="fas fa-building-columns text-blue-600"></i> Transfer BCA';
        } else if (method === 'midtrans') {
            if (btnMidtrans) btnMidtrans.className = "p-3 rounded-xl border-2 text-left transition-all duration-150 border-indigo-600 bg-indigo-50/70 ring-2 ring-indigo-500/20 cursor-pointer flex flex-col justify-between shadow-xs";
            if (checkMidtrans) checkMidtrans.className = "w-4 h-4 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[9px]";
            if (paneMidtrans) paneMidtrans.classList.remove('hidden');
            if (actionMidtrans) actionMidtrans.classList.remove('hidden');
            if (badgeEl) badgeEl.innerHTML = '<i class="fas fa-bolt text-indigo-600"></i> Midtrans Snap';
        } else if (method === 'gpay') {
            if (btnGpay) btnGpay.className = "p-3 rounded-xl border-2 text-left transition-all duration-150 border-purple-600 bg-purple-50/70 ring-2 ring-purple-500/20 cursor-pointer flex flex-col justify-between shadow-xs";
            if (checkGpay) checkGpay.className = "w-4 h-4 rounded-full bg-purple-600 text-white flex items-center justify-center text-[9px]";
            if (paneGpay) paneGpay.classList.remove('hidden');
            if (actionGpay) actionGpay.classList.remove('hidden');
            if (badgeEl) badgeEl.innerHTML = '<i class="fab fa-google-pay text-lg text-purple-600"></i> Google Pay';
        }
    }

    function copyText(text, label) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function() {
                alert(label + ' berhasil disalin: ' + text);
            }).catch(function() {
                fallbackCopy(text, label);
            });
        } else {
            fallbackCopy(text, label);
        }
    }

    function fallbackCopy(text, label) {
        const temp = document.createElement('input');
        temp.value = text;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        alert(label + ' berhasil disalin: ' + text);
    }

    function executeManualBcaCheckout() {
        const total = calculateTotal();
        if (currentMode === 'tambah_kuota' && selectedQuota <= 0) {
            alert('Silakan tentukan jumlah kuota tambahan terlebih dahulu (minimal 1 user).');
            return;
        }

        const btn = document.getElementById('btnPayWithBca');
        const origContent = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin text-lg mr-2"></i><span>Membuat Pesanan BCA...</span>';

        fetch("{{ route('admin.billing.manual-transfer') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                mode_transaksi: currentMode,
                license_id: currentMode === 'perpanjang' ? selectedRenewalPackageId : null,
                tambah_kuota: currentMode === 'perpanjang' ? 0 : selectedQuota,
                durasi_bulan: selectedDuration
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = origContent;

            if (data.success) {
                const modal = document.getElementById('bcaSuccessModal');
                document.getElementById('bcaModalOrderId').textContent = data.data.order_id;
                document.getElementById('bcaModalTotalText').textContent = 'Rp ' + data.data.total_dibayar;
                
                const waBtn = document.getElementById('bcaModalWaBtn');
                if (waBtn) waBtn.href = data.data.wa_url;

                const invoiceBtn = document.getElementById('bcaModalDownloadInvoiceBtn');
                if (invoiceBtn) {
                    invoiceBtn.href = "{{ url('admin/billing/invoice') }}/" + data.data.transaction_id + "/pdf";
                }

                // Buka WhatsApp di tab baru
                window.open(data.data.wa_url, '_blank');

                modal.classList.remove('hidden');
            } else {
                alert(data.message || 'Gagal memproses pesanan transfer.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = origContent;
            console.error(err);
            alert('Terjadi kesalahan saat menghubungi server.');
        });
    }

    // Eksekusi Pembayaran Instan Midtrans Snap
    function executeMidtransCheckout() {
        const total = calculateTotal();
        if (currentMode === 'tambah_kuota' && selectedQuota <= 0) {
            alert('Silakan tentukan jumlah kuota tambahan terlebih dahulu (minimal 1 user).');
            return;
        }

        const btn = document.getElementById('btnPayWithMidtrans');
        const origContent = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-lg mr-2"></i><span>Mempersiapkan Midtrans Snap...</span>';
        }

        fetch("{{ route('admin.billing.midtrans.snap-token') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                mode_transaksi: currentMode,
                license_id: currentMode === 'perpanjang' ? selectedRenewalPackageId : null,
                tambah_kuota: currentMode === 'perpanjang' ? 0 : selectedQuota,
                durasi_bulan: selectedDuration
            })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origContent;
            }

            if (data.success && data.snap_token) {
                if (typeof window.snap === 'undefined') {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                        return;
                    }
                    alert('SDK Midtrans Snap belum termuat. Silakan refresh halaman dan coba kembali.');
                    return;
                }

                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        console.log('Midtrans Snap Success:', result);
                        verifyMidtransPayment(data.order_id);
                    },
                    onPending: function(result) {
                        console.log('Midtrans Snap Pending:', result);
                        alert('Pesanan ' + data.order_id + ' sedang menunggu pembayaran. Silakan selesaikan pembayaran sesuai petunjuk yang diberikan.');
                        window.location.reload();
                    },
                    onError: function(result) {
                        console.error('Midtrans Snap Error:', result);
                        alert('Pembayaran Midtrans gagal atau dibatalkan.');
                    },
                    onClose: function() {
                        verifyMidtransPayment(data.order_id, false);
                    }
                });
            } else {
                alert(data.message || 'Gagal memproses sesi pembayaran Midtrans.');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origContent;
            }
            console.error('Midtrans Checkout error:', err);
            alert('Terjadi kesalahan saat menghubungi gateway Midtrans.');
        });
    }

    function resumeMidtransPay(snapToken, orderId, redirectUrl) {
        if (typeof window.snap !== 'undefined' && snapToken) {
            window.snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Midtrans Snap Success:', result);
                    verifyMidtransPayment(orderId);
                },
                onPending: function(result) {
                    console.log('Midtrans Snap Pending:', result);
                    alert('Pesanan ' + orderId + ' sedang menunggu pembayaran.');
                    window.location.reload();
                },
                onError: function(result) {
                    console.error('Midtrans Snap Error:', result);
                    alert('Pembayaran Midtrans gagal atau dibatalkan.');
                },
                onClose: function() {
                    verifyMidtransPayment(orderId, false);
                }
            });
        } else if (redirectUrl && redirectUrl !== '') {
            window.location.href = redirectUrl;
        } else if (snapToken) {
            const isProd = {{ !empty($midtransConfig['is_production']) ? 'true' : 'false' }};
            const baseUrl = isProd ? 'https://app.midtrans.com/snap/v2/vtweb/' : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/';
            window.location.href = baseUrl + snapToken;
        } else {
            alert('Token pembayaran Midtrans tidak ditemukan. Silakan hubungi CS.');
        }
    }

    function confirmSwitchMethod(trxId, targetMethod, orderId) {
        const methodLabel = targetMethod === 'midtrans' ? 'Midtrans Snap (QRIS, VA Bank, E-Wallet)' : 'Transfer Bank BCA Manual';
        if (!confirm(`Apakah Anda yakin ingin mengalihkan metode pembayaran pesanan #${orderId} ke ${methodLabel}?`)) {
            return;
        }

        const btn = event.currentTarget;
        const origHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';
        }

        fetch(`{{ url('admin/billing/transactions') }}/${trxId}/switch-method`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ target_method: targetMethod })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }

            if (data.success) {
                if (targetMethod === 'midtrans' && data.snap_token) {
                    alert(data.message || 'Metode berhasil dialihkan ke Midtrans Snap. Jendela pembayaran akan terbuka otomatis.');
                    resumeMidtransPay(data.snap_token, data.order_id, data.redirect_url);
                } else if (targetMethod === 'manual_transfer') {
                    alert(data.message || 'Metode berhasil dialihkan ke Transfer BCA.');
                    if (data.wa_url) {
                        window.open(data.wa_url, '_blank');
                    }
                    window.location.reload();
                } else {
                    alert(data.message || 'Berhasil mengalihkan metode pembayaran.');
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Gagal mengalihkan metode pembayaran.');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
            console.error('Error switching payment method:', err);
            alert('Terjadi kesalahan saat mengalihkan metode pembayaran.');
        });
    }

    function confirmCancelOrder(trxId, orderId) {
        if (!confirm(`Apakah Anda yakin ingin membatalkan pesanan #${orderId}? Transaksi yang dibatalkan tidak dapat diproses lagi.`)) {
            return;
        }

        const reason = prompt('Masukkan alasan pembatalan (opsional):', 'Ingin mengubah pesanan/metode');
        if (reason === null) return;

        const btn = event.currentTarget;
        const origHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Membatalkan...';
        }

        fetch(`{{ url('admin/billing/transactions') }}/${trxId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }

            if (data.success) {
                alert(data.message || 'Pesanan berhasil dibatalkan.');
                window.location.reload();
            } else {
                alert(data.message || 'Gagal membatalkan pesanan.');
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origHtml;
            }
            console.error('Error cancelling order:', err);
            alert('Terjadi kesalahan saat membatalkan pesanan.');
        });
    }

    function verifyMidtransPayment(orderId, showAlertOnPending = true) {
        fetch("{{ url('admin/billing/midtrans/status') }}/" + orderId, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success && res.status === 'success') {
                const modal = document.getElementById('paymentSuccessModal');
                if (modal) {
                    const titleEl = document.getElementById('paymentSuccessModalTitle');
                    if (titleEl) titleEl.textContent = 'Pembayaran Midtrans Snap Berhasil!';

                    const subtextEl = document.getElementById('successOrderSubtext');
                    if (subtextEl) subtextEl.textContent = 'Pembayaran instan online telah terverifikasi lunas otomatis.';

                    const d = res.data || {};
                    const orderIdEl = document.getElementById('successOrderId');
                    if (orderIdEl) orderIdEl.textContent = d.order_id || orderId;

                    const amountEl = document.getElementById('successAmount');
                    if (amountEl) amountEl.textContent = d.total_dibayar ? ('Rp ' + d.total_dibayar) : '-';

                    const pkgEl = document.getElementById('successPackageKey');
                    if (pkgEl) pkgEl.textContent = d.license_key || 'Multi-Paket';

                    const quotaEl = document.getElementById('successQuota');
                    if (quotaEl) quotaEl.textContent = d.kuota_sekarang ? (d.kuota_sekarang + ' ' + LABEL_MEMBER + ' (Total Aktif)') : '-';

                    const expiryEl = document.getElementById('successExpiry');
                    if (expiryEl) expiryEl.textContent = d.tanggal_berakhir ? (d.tanggal_berakhir + ' (Masa Aktif Paket)') : '-';

                    const modalBtn = document.getElementById('modalDownloadInvoiceBtn');
                    if (modalBtn && d.transaction_id) {
                        modalBtn.href = "{{ url('admin/billing/invoice') }}/" + d.transaction_id + "/pdf";
                    }

                    modal.classList.remove('hidden');
                } else {
                    alert('Pembayaran Berhasil! Lisensi Anda telah aktif.');
                    window.location.reload();
                }
            } else if (showAlertOnPending) {
                alert('Status pembayaran: ' + (res.message || 'Menunggu konfirmasi.'));
                window.location.reload();
            }
        })
        .catch(err => {
            console.error(err);
        });
    }

    // Google Pay API Client Configuration
    const GOOGLE_PAY_ENV = '{{ config('services.google_pay.environment', env('GOOGLE_PAY_ENVIRONMENT', 'TEST')) }}';
    const GOOGLE_PAY_MERCHANT_ID = '{{ config('services.google_pay.merchant_id', env('GOOGLE_PAY_MERCHANT_ID', '')) }}';
    const GOOGLE_PAY_MERCHANT_NAME = '{{ config('services.google_pay.merchant_name', env('GOOGLE_PAY_MERCHANT_NAME', 'AkuSehat - Neura Cakrawira Solusi')) }}';

    const baseGooglePayRequest = {
        apiVersion: 2,
        apiVersionMinor: 0
    };

    const allowedCardNetworks = ["AMEX", "DISCOVER", "JCB", "MASTERCARD", "VISA"];
    const allowedCardAuthMethods = ["PAN_ONLY", "CRYPTOGRAM_3DS"];

    const baseCardPaymentMethod = {
        type: 'CARD',
        parameters: {
            allowedAuthMethods: allowedCardAuthMethods,
            allowedCardNetworks: allowedCardNetworks
        }
    };

    const tokenizationSpecification = {
        type: 'PAYMENT_GATEWAY',
        parameters: {
            'gateway': 'example',
            'gatewayMerchantId': 'exampleGatewayMerchantId'
        }
    };

    const cardPaymentMethod = Object.assign({}, baseCardPaymentMethod, {
        tokenizationSpecification: tokenizationSpecification
    });

    function getGooglePaymentDataRequest(totalAmount) {
        const paymentDataRequest = Object.assign({}, baseGooglePayRequest);
        paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
        paymentDataRequest.transactionInfo = {
            totalPriceStatus: 'FINAL',
            totalPrice: totalAmount.toString(),
            currencyCode: 'IDR',
            countryCode: 'ID'
        };
        paymentDataRequest.merchantInfo = {
            merchantName: GOOGLE_PAY_MERCHANT_NAME
        };
        if (GOOGLE_PAY_ENV === 'PRODUCTION' && GOOGLE_PAY_MERCHANT_ID) {
            paymentDataRequest.merchantInfo.merchantId = GOOGLE_PAY_MERCHANT_ID;
        }
        return paymentDataRequest;
    }

    // Google Pay API Client Initialization
    function onGooglePayLoaded() {
        try {
            paymentsClient = new google.payments.api.PaymentsClient({
                environment: GOOGLE_PAY_ENV
            });

            const isReadyToPayRequest = Object.assign({}, baseGooglePayRequest);
            isReadyToPayRequest.allowedPaymentMethods = [baseCardPaymentMethod];

            paymentsClient.isReadyToPay(isReadyToPayRequest)
                .then(function(response) {
                    if (response.result) {
                        console.log('Google Pay is ready to pay.');
                        renderGooglePayButton();
                    }
                })
                .catch(function(err) {
                    console.warn('Google Pay isReadyToPay notice: ', err);
                });
        } catch(e) {
            console.log('Google Pay client init error: ', e);
        }
    }

    function renderGooglePayButton() {
        const container = document.getElementById('gpay-button-container');
        if (container && paymentsClient) {
            try {
                const officialBtn = paymentsClient.createButton({
                    buttonColor: 'black',
                    buttonType: 'pay',
                    buttonSizeMode: 'fill',
                    buttonRadius: 12,
                    onClick: openPaymentConfirmation
                });
                container.innerHTML = '';
                container.appendChild(officialBtn);
            } catch(e) {
                console.warn('Google Pay createButton notice: ', e);
            }
        }
    }

    function setPayBtnLoading(text) {
        const container = document.getElementById('gpay-button-container');
        if (container) {
            container.innerHTML = `
                <button type="button" disabled class="w-full py-3.5 px-5 rounded-xl font-bold text-white bg-black opacity-80 flex items-center justify-center gap-2 cursor-wait text-sm sm:text-base">
                    <i class="fas fa-spinner fa-spin text-lg"></i><span>${text}</span>
                </button>
            `;
        }
    }

    function resetPayBtn() {
        renderGooglePayButton();
    }

    // Workflow Konfirmasi Transaksi
    function openPaymentConfirmation() {
        const total = calculateTotal();
        if (currentMode === 'tambah_kuota' && selectedQuota <= 0) {
            alert('Silakan tentukan jumlah kuota tambahan terlebih dahulu (minimal 1 user).');
            return;
        }

        const confirmModeEl = document.getElementById('confirmModeText');
        const confirmQuotaEl = document.getElementById('confirmQuotaText');
        const confirmExpiryEl = document.getElementById('confirmExpiryText');
        const confirmTotalEl = document.getElementById('confirmTotalText');

        const durLabel = getDurationLabel(selectedDuration);
        if (confirmTotalEl) confirmTotalEl.textContent = formatRupiah(total);

        if (currentMode === 'perpanjang') {
            const pkg = getPackageById(selectedRenewalPackageId);
            if (confirmModeEl) confirmModeEl.textContent = `Perpanjangan Paket Lisensi (+${durLabel})`;
            if (confirmQuotaEl) confirmQuotaEl.textContent = `${pkg ? pkg.key : 'Paket'} (${pkg ? pkg.quota : 50} ${LABEL_MEMBER})`;
            if (confirmExpiryEl) {
                confirmExpiryEl.textContent = `+${durLabel} (Akumulasi Mandiri)`;
                confirmExpiryEl.className = 'font-semibold text-emerald-700';
            }
        } else {
            if (confirmModeEl) confirmModeEl.textContent = 'Tambah Paket Kuota Baru';
            if (confirmQuotaEl) confirmQuotaEl.textContent = `+${selectedQuota} ${LABEL_MEMBER} Baru`;
            if (confirmExpiryEl) {
                confirmExpiryEl.textContent = `${durLabel} Mandiri (Aktif Bersamaan)`;
                confirmExpiryEl.className = 'font-semibold text-blue-700';
            }
        }

        const modal = document.getElementById('paymentConfirmModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closePaymentConfirmation() {
        const modal = document.getElementById('paymentConfirmModal');
        if (modal) modal.classList.add('hidden');
    }

    function executeGooglePayCheckout() {
        closePaymentConfirmation();

        const total = calculateTotal();
        if (currentMode === 'tambah_kuota' && selectedQuota <= 0) {
            alert('Silakan tentukan jumlah kuota tambahan terlebih dahulu (minimal 1 user).');
            return;
        }

        setPayBtnLoading('Membuka Google Pay...');

        if (!paymentsClient) {
            try {
                paymentsClient = new google.payments.api.PaymentsClient({
                    environment: GOOGLE_PAY_ENV
                });
            } catch(e) {
                alert('Gagal memuat Google Pay API SDK. Pastikan koneksi internet stabil.');
                resetPayBtn();
                return;
            }
        }

        const paymentDataRequest = getGooglePaymentDataRequest(total);

        // BUKA JENDELA POPUP RESMI GOOGLE PAY
        paymentsClient.loadPaymentData(paymentDataRequest)
            .then(function(paymentData) {
                setPayBtnLoading('Memverifikasi Transaksi...');
                handleGooglePaySubmit(paymentData);
            })
            .catch(function(err) {
                resetPayBtn();
                if (err.statusCode === 'CANCELED') {
                    // Pengguna membatalkan / menutup modal Google Pay
                    return;
                }
                console.error('Google Pay loadPaymentData error: ', err);
                const msg = err.statusMessage || (err.statusCode === 'BUYER_ACCOUNT_ERROR' 
                    ? 'Akun Google Anda belum memiliki metode pembayaran aktif.' 
                    : 'Jendela Google Pay ditutup atau pembayaran tidak dapat diproses.');
                alert('Google Pay (' + (err.statusCode || 'ERROR') + '): ' + msg);
            });
    }

    function handleGooglePaySubmit(paymentData) {
        const total = calculateTotal();
        const token = paymentData?.paymentMethodData?.tokenizationData?.token || '';
        const cardDescription = paymentData?.paymentMethodData?.description || 'Google Pay Card';
        const cardNetwork = paymentData?.paymentMethodData?.info?.cardNetwork || 'CARD';
        const orderId = 'GPAY-AKU-' + Date.now();

        fetch("{{ route('admin.billing.google-pay') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                mode_transaksi: currentMode,
                license_id: currentMode === 'perpanjang' ? selectedRenewalPackageId : null,
                tambah_kuota: currentMode === 'perpanjang' ? 0 : selectedQuota,
                durasi_bulan: selectedDuration,
                order_id: orderId,
                payment_token: token,
                card_details: cardDescription,
                card_network: cardNetwork
            })
        })
        .then(res => res.json())
        .then(data => {
            resetPayBtn();
            if (data.success) {
                const titleEl = document.getElementById('paymentSuccessModalTitle');
                if (titleEl) titleEl.textContent = 'Pembayaran Google Pay Berhasil!';
                const subtextEl = document.getElementById('successOrderSubtext');
                if (subtextEl) subtextEl.textContent = 'Transaksi berhasil diverifikasi.';

                document.getElementById('successOrderId').textContent = data.data.order_id;
                document.getElementById('successAmount').textContent = 'Rp ' + data.data.total_dibayar;
                document.getElementById('successPackageKey').textContent = data.data.license_key || '-';
                document.getElementById('successQuota').textContent = data.data.kuota_sekarang + ' ' + LABEL_MEMBER + ' (Total Aktif)';
                document.getElementById('successExpiry').textContent = data.data.tanggal_berakhir + ' (Masa Aktif Paket)';
                
                const invoiceUrl = "{{ url('admin/billing/invoice') }}/" + data.data.transaction_id + "/pdf";
                const modalBtn = document.getElementById('modalDownloadInvoiceBtn');
                if (modalBtn) {
                    modalBtn.href = invoiceUrl;
                }

                document.getElementById('paymentSuccessModal').classList.remove('hidden');
            } else {
                alert(data.message || 'Gagal memproses pembayaran Google Pay.');
            }
        })
        .catch(err => {
            resetPayBtn();
            console.error(err);
            alert('Terjadi kesalahan saat memproses transaksi ke server.');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        switchBillingMode('perpanjang');
        selectPaymentMethod('manual_bca');
    });
</script>
@endpush
