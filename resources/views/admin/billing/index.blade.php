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
                <i class="fab fa-google-pay text-base text-gray-900"></i> Google Payment Certified
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
                        <span class="font-bold text-emerald-300">Rp 12.000 / {{ strtolower($labelMember) }} / thn</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-white/10">
                        <span>Model Kuota:</span>
                        <span class="font-medium text-white">Multi-Bucket (Mandiri 1 Thn)</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span>Metode Pembayaran:</span>
                        <span class="font-bold text-white flex items-center gap-1">
                            <i class="fab fa-google-pay text-base text-white"></i> Google Pay
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
                            <span>{{ $pkgExpired ? 'Aktifkan Kembali (+1 Thn)' : 'Perpanjang Paket (+1 Thn)' }}</span>
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
                Biaya langganan {{ strtolower($tipeSebutan) }} resmi <strong>Rp 12.000 / {{ strtolower($labelMember) }} / tahun</strong> (All-In). Akun {{ strtolower($labelMemberPlural) }} didaftarkan langsung oleh <strong>{{ $labelAdmin }}</strong>.
            </p>
        </div>

        {{-- Switcher Mode Transaksi: Perpanjang Kuota Berjalan vs Tambah Slot Kuota --}}
        <div class="mb-6">
            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-3 flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">1</span>
                <span>Pilih Jenis Kebutuhan Transaksi</span>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                        Pilih paket kuota berjalan untuk diperpanjang masa aktifnya <strong>+1 Tahun</strong> tanpa merubah kapasitas siswa pada paket tersebut.
                    </p>
                </button>

                {{-- Mode 2: Tambah Slot Kuota Siswa Baru --}}
                <button type="button" onclick="switchBillingMode('tambah_kuota')" id="tabBtnTambahKuota"
                    class="p-4 rounded-2xl border-2 text-left transition-all duration-200 border-gray-200 bg-white hover:border-gray-300 relative cursor-pointer">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                            <i class="fas fa-user-plus"></i> Tambah Slot Baru (1 Thn Mandiri)
                        </span>
                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center bg-white text-transparent text-[10px]" id="indicatorTambahKuota">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                    <div class="font-bold text-gray-900 text-sm sm:text-base">Tambah Slot Kuota {{ $labelMember }} Baru</div>
                    <p class="text-xs text-gray-500 mt-1">
                        Beli paket kuota baru yang akan aktif mandiri selama <strong>1 Tahun</strong> bersamaan dengan paket lama tanpa memotong masa aktif yang sudah ada.
                    </p>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Options Selector (7 cols) -->
            <div class="lg:col-span-7 space-y-6">

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
                                    Rp 12.000 / {{ strtolower($labelMember) }} / thn
                                </div>
                                <span class="text-[11px] text-emerald-700 font-semibold">Paket Tahunan All-In</span>
                            </div>
                        </div>

                        {{-- Timeline Akumulasi Tanggal --}}
                        <div class="p-4 rounded-xl bg-white border border-emerald-200 space-y-3 shadow-xs">
                            <div class="flex items-center justify-between text-xs font-bold text-emerald-900">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-calendar-check text-emerald-600"></i>
                                    Perpanjangan Durasi +1 Tahun (12 Bulan)
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
                                        +1 Tahun
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
                                Sisa waktu aktif paket ini tetap utuh tanpa terpotong. Perpanjangan 1 tahun langsung diakumulasikan ke masa berlaku paket tersebut.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- CONTAINER MODE 2: TAMBAH SLOT KUOTA SISWA BARU --}}
                <div id="paneTambahKuota" class="space-y-6 hidden">
                    {{-- Notice Model Provider --}}
                    <div class="bg-blue-50/70 p-3.5 rounded-xl border border-blue-200 text-xs text-blue-900 flex items-start gap-2.5">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5 text-sm flex-shrink-0"></i>
                        <div>
                            <strong>Model Provider Kuota Mandiri:</strong> Kuota tambahan yang Anda beli akan menjadi paket baru yang aktif selama <strong>1 Tahun Penuh (12 Bulan)</strong> sejak hari ini dan berjalan bersamaan dengan paket kuota Anda yang lain.
                        </div>
                    </div>

                    {{-- Opsi: Paket Jumlah Pengguna/Siswa --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">
                            1. Pilih Paket Kuota Tambahan <span class="text-xs font-normal text-emerald-700">(Rp 12.000 / {{ strtolower($labelMember) }} / tahun)</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <button type="button" onclick="setQuota(50)" data-quota="50" class="quota-preset-btn border-2 border-emerald-600 bg-emerald-50/50 ring-2 ring-emerald-500/20 rounded-xl p-3.5 flex flex-col items-center justify-center text-center transition-all duration-150 hover:border-emerald-500">
                                <span class="text-xs font-bold text-gray-500 uppercase">+50 {{ $labelMember }}</span>
                                <span class="text-base font-black text-gray-800 mt-1">Rp 600.000</span>
                                <span class="text-[10px] text-emerald-700 font-semibold mt-0.5">{{ $instansi->getPresetPaketName(50) }}</span>
                            </button>

                            <button type="button" onclick="setQuota(100)" data-quota="100" class="quota-preset-btn border-2 border-gray-200 bg-white rounded-xl p-3.5 flex flex-col items-center justify-center text-center transition-all duration-150 hover:border-emerald-500">
                                <span class="text-xs font-bold text-gray-500 uppercase">+100 {{ $labelMember }}</span>
                                <span class="text-base font-black text-gray-800 mt-1">Rp 1.200.000</span>
                                <span class="text-[10px] text-gray-500 mt-0.5">{{ $instansi->getPresetPaketName(100) }}</span>
                            </button>

                            <button type="button" onclick="setQuota(250)" data-quota="250" class="quota-preset-btn border-2 border-gray-200 bg-white rounded-xl p-3.5 flex flex-col items-center justify-center text-center transition-all duration-150 hover:border-emerald-500">
                                <span class="text-xs font-bold text-gray-500 uppercase">+250 {{ $labelMember }}</span>
                                <span class="text-base font-black text-gray-800 mt-1">Rp 3.000.000</span>
                                <span class="text-[10px] text-gray-500 mt-0.5">{{ $instansi->getPresetPaketName(250) }}</span>
                            </button>

                            <button type="button" onclick="setQuota(500)" data-quota="500" class="quota-preset-btn border-2 border-gray-200 bg-white rounded-xl p-3.5 flex flex-col items-center justify-center text-center transition-all duration-150 hover:border-emerald-500">
                                <span class="text-xs font-bold text-gray-500 uppercase">+500 {{ $labelMember }}</span>
                                <span class="text-base font-black text-gray-800 mt-1">Rp 6.000.000</span>
                                <span class="text-[10px] text-emerald-700 font-semibold mt-0.5">{{ $instansi->getPresetPaketName(500) }}</span>
                            </button>

                            <button type="button" onclick="setQuota(1000)" data-quota="1000" class="quota-preset-btn border-2 border-gray-200 bg-white rounded-xl p-3.5 flex flex-col items-center justify-center text-center transition-all duration-150 hover:border-emerald-500 col-span-2 sm:col-span-2">
                                <span class="text-xs font-bold text-gray-500 uppercase">+1.000 {{ $labelMember }}</span>
                                <span class="text-base font-black text-gray-800 mt-1">Rp 12.000.000</span>
                                <span class="text-[10px] text-purple-700 font-semibold mt-0.5">{{ $instansi->getPresetPaketName(1000) }}</span>
                            </button>
                        </div>
                    </div>

                    {{-- Opsi 2: Kustom Jumlah Pengguna/Siswa --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-2">
                            2. Atau Masukkan Jumlah {{ $labelMemberPlural }} Tambahan Kustom
                        </label>
                        <div class="flex items-center gap-3">
                            <div class="flex-1">
                                <input type="number" id="customQuotaInput" min="1" max="10000" step="1" value="50"
                                    placeholder="Ketik jumlah {{ strtolower($labelMember) }}..."
                                    oninput="onCustomQuotaInput(this.value)"
                                    class="w-full h-11 px-3.5 border border-gray-300 rounded-xl text-sm font-semibold focus:ring-2 focus:ring-[#308a34] focus:border-transparent">
                            </div>
                            <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-3 py-2.5 rounded-xl whitespace-nowrap">{{ $labelMember }} (1 Tahun)</span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1.5">
                            * Kuota pengguna akan dibuatkan paket lisensi baru mandiri dengan masa aktif 1 tahun (365 hari) sejak hari pembelian.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right: Order Summary & Google Pay Button (5 cols) -->
            <div class="lg:col-span-5 bg-gray-50/90 p-6 rounded-2xl border border-gray-200 flex flex-col justify-between shadow-xs">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-200">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Langkah 2</span>
                            <h3 class="text-base font-bold text-gray-800">Ringkasan Tagihan</h3>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <i class="fab fa-google-pay text-base text-gray-900"></i> Resmi
                        </span>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex items-start justify-between text-gray-600 gap-2">
                            <span id="summaryQuotaText" class="font-medium text-gray-700">Perpanjangan Lisensi ({{ $kuotaPerpanjangDefault }} {{ $labelMember }} / 1 Thn)</span>
                            <span id="summaryQuotaPrice" class="font-semibold text-gray-800 whitespace-nowrap">Rp {{ number_format($subtotalPerpanjang, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-gray-600">
                            <span id="summaryExpiryLabel">Perpanjangan Masa Aktif</span>
                            <span id="summaryExpiryBadge" class="font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs">+12 Bulan (1 Thn)</span>
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

                <!-- Single Unified Google Pay Action Button Container -->
                <div class="mt-6 pt-4 border-t border-gray-200 space-y-3">
                    <button type="button" id="btnPayWithGoogle" onclick="openPaymentConfirmation()"
                        class="w-full py-3.5 px-5 rounded-xl font-bold text-white bg-black hover:bg-neutral-800 active:scale-[0.99] transition-all duration-200 shadow-md shadow-black/20 flex items-center justify-center gap-2.5 cursor-pointer text-sm sm:text-base">
                        <svg class="h-5 w-auto flex-shrink-0" viewBox="9 11 52 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M26.544 24.1659C26.544 23.5831 26.4971 23.0003 26.4034 22.4294H18.4434V25.7239H23.0037C22.8161 26.7825 22.2065 27.734 21.3155 28.3286V30.4695H24.0353C25.6296 28.9828 26.544 26.7825 26.544 24.1659Z" fill="#4285F4"/>
                            <path d="M18.4442 32.539C20.7185 32.539 22.6411 31.7778 24.0361 30.4695L21.3164 28.3287C20.5544 28.852 19.5814 29.1493 18.4442 29.1493C16.2403 29.1493 14.3763 27.6388 13.7081 25.5908H10.8794V27.7853C12.2625 30.5284 15.148 32.539 18.4442 32.539Z" fill="#34A853"/>
                            <path d="M13.7083 25.5908C13.3524 24.5322 13.3524 23.3826 13.7083 22.324V19.3995H10.8795C9.6976 21.7554 9.6976 24.5824 10.8795 26.9383L13.7083 25.5908Z" fill="#FBBC04"/>
                            <path d="M18.4442 15.9287C19.6496 15.9049 20.8219 16.3687 21.6777 17.2251L24.0954 14.8074C22.5601 13.2721 20.5376 12.4157 18.4442 12.4395C15.148 12.4395 12.2625 14.4501 10.8794 17.1932L13.7081 19.3877C14.3763 17.3397 16.2403 15.9287 18.4442 15.9287Z" fill="#EA4335"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M33.0603 31.5161V25.4741H36.1786C37.4564 25.4741 38.535 25.046 39.4142 24.2015L39.6252 23.9875C41.2313 22.2391 41.1258 19.5155 39.4142 17.898C38.5584 17.0416 37.3861 16.5778 36.1786 16.6016H31.1729V31.5161H33.0603ZM33.0605 23.6425V18.4332H36.2262C36.9063 18.4332 37.5512 18.6948 38.0319 19.1706C39.052 20.1696 39.0754 21.8347 38.0905 22.8694C37.6098 23.3809 36.9297 23.6663 36.2262 23.6425H33.0605ZM48.4293 22.1083C47.6204 21.359 46.5185 20.9784 45.1234 20.9784C43.3298 20.9784 41.9816 21.6444 41.0906 22.9646L42.7553 24.0231C43.3649 23.1192 44.1973 22.6673 45.2524 22.6673C45.9206 22.6673 46.5653 22.9171 47.0694 23.369C47.5618 23.7972 47.8432 24.4156 47.8432 25.0698V25.5098C47.1163 25.1055 46.2019 24.8914 45.0765 24.8914C43.7635 24.8914 42.7084 25.2006 41.923 25.831C41.1375 26.4613 40.739 27.2939 40.739 28.3524C40.7155 29.3158 41.1258 30.2316 41.8527 30.85C42.5912 31.5161 43.5291 31.8491 44.631 31.8491C45.9323 31.8491 46.9639 31.2663 47.7494 30.1007H47.8314V31.5161H49.6368V25.2244C49.6368 23.9042 49.2382 22.8576 48.4293 22.1083ZM43.3066 29.6369C42.9197 29.3514 42.6852 28.8876 42.6852 28.3881C42.6852 27.8291 42.9432 27.3652 43.4473 26.9965C43.9632 26.6278 44.6081 26.4375 45.3702 26.4375C46.4255 26.4256 47.2462 26.6635 47.8325 27.1392C47.8325 27.948 47.5159 28.6497 46.8945 29.2444C46.3317 29.8153 45.5696 30.1364 44.7723 30.1364C44.2446 30.1483 43.7287 29.9699 43.3066 29.6369ZM53.693 35.9999L60.0001 21.3114H57.9485L55.0295 28.6378H54.9943L52.0049 21.3114H49.9534L54.0916 30.8619L51.747 35.9999H53.693Z" fill="#FFFFFF"/>
                        </svg>
                        <span id="btnPayLabel" class="whitespace-nowrap">Bayar dengan Google Pay</span>
                    </button>

                    <div class="flex items-center justify-center gap-2 text-[11px] text-gray-500 text-center">
                        <i class="fas fa-shield-halved text-emerald-600"></i>
                        <span>Enkripsi Resmi Google Payment API • Struk Pajak PDF Langsung Terbit</span>
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
                            @if($trx->tipe_transaksi === 'perpanjang_lisensi')
                                <span class="inline-flex items-center gap-1 font-semibold text-emerald-800">
                                    <i class="fas fa-rotate text-xs"></i> Perpanjangan Lisensi (+1 Thn)
                                </span>
                            @elseif($trx->tipe_transaksi === 'tambah_kuota')
                                <span class="inline-flex items-center gap-1 font-semibold text-blue-800">
                                    <i class="fas fa-user-plus text-xs"></i> Tambah Paket Kuota Baru
                                </span>
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
                                    {{ $trx->payload['kuota_paket'] ?? ($trx->payload['kuota_saat_perpanjang'] ?? $kuotaMaksimal) }} {{ $labelMember }} (Perpanjang +1 Thn)
                                </span>
                            @elseif($trx->tipe_transaksi === 'tambah_kuota')
                                <span class="inline-block bg-blue-50 text-blue-800 px-2.5 py-0.5 rounded font-medium border border-blue-100">
                                    +{{ $trx->tambah_kuota }} {{ $labelMember }} (Mandiri 1 Thn)
                                </span>
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
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-black text-white">
                                <i class="fab fa-google-pay text-sm"></i> Google Pay
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                Berhasil
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.billing.invoice.pdf', $trx->id) }}?preview=1" 
                               target="_blank"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-300 transition-colors shadow-xs"
                               title="Lihat & Unduh Struk Belanja Resmi (PDF)">
                                <i class="fas fa-file-invoice text-emerald-600"></i>
                                <span>Struk PDF</span>
                            </a>
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
            <i class="fab fa-google-pay text-3xl text-gray-900"></i>
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
                <span class="font-semibold text-emerald-700" id="confirmExpiryText">+12 Bulan (1 Tahun)</span>
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
        <h3 class="text-xl font-bold text-gray-800 mb-1">Pembayaran Google Pay Berhasil!</h3>
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
@endsection

@push('scripts')
{{-- Google Payment API Client SDK --}}
<script src="https://pay.google.com/gp/p/js/pay.js" async onload="onGooglePayLoaded()"></script>

<script>
    const HARGA_USER = {{ $hargaPerUser }}; // 12000
    const LABEL_MEMBER = '{{ $labelMember }}';
    const ALL_PACKAGES = @json($packagesPayload ?? []);

    let currentMode = 'perpanjang'; // 'perpanjang' or 'tambah_kuota'
    let selectedRenewalPackageId = '{{ $primaryLicense ? $primaryLicense->id : '' }}';
    let selectedQuota = 50;
    let paymentsClient = null;

    function formatRupiah(num) {
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function getPackageById(id) {
        return ALL_PACKAGES.find(p => p.id === id) || ALL_PACKAGES[0] || null;
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
        const newExpEl = document.getElementById('renewalNewExpiry');

        if (quotaTextEl) {
            quotaTextEl.textContent = `${pkg.quota} ${LABEL_MEMBER}`;
        }
        if (currentExpEl) {
            currentExpEl.textContent = pkg.expiry_formatted;
        }
        if (newExpEl && pkg.raw_expiry) {
            const expDate = new Date(pkg.raw_expiry);
            expDate.setFullYear(expDate.getFullYear() + 1);
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            newExpEl.textContent = `${expDate.getDate()} ${months[expDate.getMonth()]} ${expDate.getFullYear()}`;
        }

        calculateTotal();
    }

    function calculateTotal() {
        const summaryQuotaEl = document.getElementById('summaryQuotaText');
        const summaryPriceEl = document.getElementById('summaryQuotaPrice');
        const summaryTaxEl = document.getElementById('summaryTaxPrice');
        const summaryTotalEl = document.getElementById('summaryTotalPrice');
        const summaryExpiryLabel = document.getElementById('summaryExpiryLabel');
        const summaryExpiryBadge = document.getElementById('summaryExpiryBadge');
        const btnPayLabel = document.getElementById('btnPayLabel');

        let total = 0;
        if (currentMode === 'perpanjang') {
            const pkg = getPackageById(selectedRenewalPackageId);
            const quota = pkg ? pkg.quota : 50;
            const subtotal = quota * HARGA_USER;
            const tax = Math.round(subtotal * 0.11);
            total = subtotal + tax;

            if (summaryQuotaEl) {
                summaryQuotaEl.textContent = `Perpanjangan Paket (${pkg ? pkg.key : 'Lisensi'} - ${quota} ${LABEL_MEMBER})`;
            }
            if (summaryPriceEl) summaryPriceEl.textContent = formatRupiah(subtotal);
            if (summaryTaxEl) summaryTaxEl.textContent = formatRupiah(tax);
            if (summaryTotalEl) summaryTotalEl.textContent = formatRupiah(total);
            if (summaryExpiryLabel) summaryExpiryLabel.textContent = 'Masa Perpanjangan';
            if (summaryExpiryBadge) {
                summaryExpiryBadge.textContent = '+12 Bulan (1 Thn Mandiri)';
                summaryExpiryBadge.className = 'font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded text-xs';
            }
        } else {
            const subtotal = selectedQuota * HARGA_USER;
            const tax = Math.round(subtotal * 0.11);
            total = subtotal + tax;

            if (summaryQuotaEl) summaryQuotaEl.textContent = `Paket Kuota Baru (+${selectedQuota} ${LABEL_MEMBER})`;
            if (summaryPriceEl) summaryPriceEl.textContent = formatRupiah(subtotal);
            if (summaryTaxEl) summaryTaxEl.textContent = formatRupiah(tax);
            if (summaryTotalEl) summaryTotalEl.textContent = formatRupiah(total);
            if (summaryExpiryLabel) summaryExpiryLabel.textContent = 'Masa Aktif Paket Baru';
            if (summaryExpiryBadge) {
                summaryExpiryBadge.textContent = '1 Tahun Mandiri (Multi-Paket)';
                summaryExpiryBadge.className = 'font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded text-xs';
            }
        }

        if (btnPayLabel) {
            btnPayLabel.textContent = 'Bayar dengan Google Pay';
        }

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

    // Google Pay API Client Initialization
    function onGooglePayLoaded() {
        try {
            paymentsClient = new google.payments.api.PaymentsClient({
                environment: '{{ env('GOOGLE_PAY_ENVIRONMENT', 'TEST') }}'
            });
        } catch(e) {
            console.log('Google Pay client init error: ', e);
        }
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

        if (confirmTotalEl) confirmTotalEl.textContent = formatRupiah(total);

        if (currentMode === 'perpanjang') {
            const pkg = getPackageById(selectedRenewalPackageId);
            if (confirmModeEl) confirmModeEl.textContent = 'Perpanjangan Paket Lisensi (+1 Thn)';
            if (confirmQuotaEl) confirmQuotaEl.textContent = `${pkg ? pkg.key : 'Paket'} (${pkg ? pkg.quota : 50} ${LABEL_MEMBER})`;
            if (confirmExpiryEl) {
                confirmExpiryEl.textContent = '+12 Bulan (Akumulasi Mandiri)';
                confirmExpiryEl.className = 'font-semibold text-emerald-700';
            }
        } else {
            if (confirmModeEl) confirmModeEl.textContent = 'Tambah Paket Kuota Baru';
            if (confirmQuotaEl) confirmQuotaEl.textContent = `+${selectedQuota} ${LABEL_MEMBER} Baru`;
            if (confirmExpiryEl) {
                confirmExpiryEl.textContent = '1 Tahun Mandiri (Aktif Bersamaan)';
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
        handleGooglePaySubmit();
    }

    function handleGooglePaySubmit() {
        const total = calculateTotal();
        if (currentMode === 'tambah_kuota' && selectedQuota <= 0) {
            alert('Silakan tentukan jumlah kuota tambahan terlebih dahulu (minimal 1 user).');
            return;
        }

        const btn = document.getElementById('btnPayWithGoogle');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin text-lg"></i><span>Memproses Google Pay...</span>`;
        }

        const simulatedOrderId = 'GPAY-AKU-' + Date.now();
        const simulatedToken = 'GPay_Token_' + Math.random().toString(36).substring(2, 12);

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
                order_id: simulatedOrderId,
                payment_token: simulatedToken,
            })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg class="h-6 w-auto flex-shrink-0" viewBox="9 11 52 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M26.544 24.1659C26.544 23.5831 26.4971 23.0003 26.4034 22.4294H18.4434V25.7239H23.0037C22.8161 26.7825 22.2065 27.734 21.3155 28.3286V30.4695H24.0353C25.6296 28.9828 26.544 26.7825 26.544 24.1659Z" fill="#4285F4"/>
                        <path d="M18.4442 32.539C20.7185 32.539 22.6411 31.7778 24.0361 30.4695L21.3164 28.3287C20.5544 28.852 19.5814 29.1493 18.4442 29.1493C16.2403 29.1493 14.3763 27.6388 13.7081 25.5908H10.8794V27.7853C12.2625 30.5284 15.148 32.539 18.4442 32.539Z" fill="#34A853"/>
                        <path d="M13.7083 25.5908C13.3524 24.5322 13.3524 23.3826 13.7083 22.324V19.3995H10.8795C9.6976 21.7554 9.6976 24.5824 10.8795 26.9383L13.7083 25.5908Z" fill="#FBBC04"/>
                        <path d="M18.4442 15.9287C19.6496 15.9049 20.8219 16.3687 21.6777 17.2251L24.0954 14.8074C22.5601 13.2721 20.5376 12.4157 18.4442 12.4395C15.148 12.4395 12.2625 14.4501 10.8794 17.1932L13.7081 19.3877C14.3763 17.3397 16.2403 15.9287 18.4442 15.9287Z" fill="#EA4335"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M33.0603 31.5161V25.4741H36.1786C37.4564 25.4741 38.535 25.046 39.4142 24.2015L39.6252 23.9875C41.2313 22.2391 41.1258 19.5155 39.4142 17.898C38.5584 17.0416 37.3861 16.5778 36.1786 16.6016H31.1729V31.5161H33.0603ZM33.0605 23.6425V18.4332H36.2262C36.9063 18.4332 37.5512 18.6948 38.0319 19.1706C39.052 20.1696 39.0754 21.8347 38.0905 22.8694C37.6098 23.3809 36.9297 23.6663 36.2262 23.6425H33.0605ZM48.4293 22.1083C47.6204 21.359 46.5185 20.9784 45.1234 20.9784C43.3298 20.9784 41.9816 21.6444 41.0906 22.9646L42.7553 24.0231C43.3649 23.1192 44.1973 22.6673 45.2524 22.6673C45.9206 22.6673 46.5653 22.9171 47.0694 23.369C47.5618 23.7972 47.8432 24.4156 47.8432 25.0698V25.5098C47.1163 25.1055 46.2019 24.8914 45.0765 24.8914C43.7635 24.8914 42.7084 25.2006 41.923 25.831C41.1375 26.4613 40.739 27.2939 40.739 28.3524C40.7155 29.3158 41.1258 30.2316 41.8527 30.85C42.5912 31.5161 43.5291 31.8491 44.631 31.8491C45.9323 31.8491 46.9639 31.2663 47.7494 30.1007H47.8314V31.5161H49.6368V25.2244C49.6368 23.9042 49.2382 22.8576 48.4293 22.1083ZM43.3066 29.6369C42.9197 29.3514 42.6852 28.8876 42.6852 28.3881C42.6852 27.8291 42.9432 27.3652 43.4473 26.9965C43.9632 26.6278 44.6081 26.4375 45.3702 26.4375C46.4255 26.4256 47.2462 26.6635 47.8325 27.1392C47.8325 27.948 47.5159 28.6497 46.8945 29.2444C46.3317 29.8153 45.5696 30.1364 44.7723 30.1364C44.2446 30.1483 43.7287 29.9699 43.3066 29.6369ZM53.693 35.9999L60.0001 21.3114H57.9485L55.0295 28.6378H54.9943L52.0049 21.3114H49.9534L54.0916 30.8619L51.747 35.9999H53.693Z" fill="#FFFFFF"/>
                    </svg>
                    <span id="btnPayLabel" class="whitespace-nowrap">Bayar dengan Google Pay</span>
                `;
            }

            if (data.success) {
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
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg class="h-6 w-auto flex-shrink-0" viewBox="9 11 52 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M26.544 24.1659C26.544 23.5831 26.4971 23.0003 26.4034 22.4294H18.4434V25.7239H23.0037C22.8161 26.7825 22.2065 27.734 21.3155 28.3286V30.4695H24.0353C25.6296 28.9828 26.544 26.7825 26.544 24.1659Z" fill="#4285F4"/>
                        <path d="M18.4442 32.539C20.7185 32.539 22.6411 31.7778 24.0361 30.4695L21.3164 28.3287C20.5544 28.852 19.5814 29.1493 18.4442 29.1493C16.2403 29.1493 14.3763 27.6388 13.7081 25.5908H10.8794V27.7853C12.2625 30.5284 15.148 32.539 18.4442 32.539Z" fill="#34A853"/>
                        <path d="M13.7083 25.5908C13.3524 24.5322 13.3524 23.3826 13.7083 22.324V19.3995H10.8795C9.6976 21.7554 9.6976 24.5824 10.8795 26.9383L13.7083 25.5908Z" fill="#FBBC04"/>
                        <path d="M18.4442 15.9287C19.6496 15.9049 20.8219 16.3687 21.6777 17.2251L24.0954 14.8074C22.5601 13.2721 20.5376 12.4157 18.4442 12.4395C15.148 12.4395 12.2625 14.4501 10.8794 17.1932L13.7081 19.3877C14.3763 17.3397 16.2403 15.9287 18.4442 15.9287Z" fill="#EA4335"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M33.0603 31.5161V25.4741H36.1786C37.4564 25.4741 38.535 25.046 39.4142 24.2015L39.6252 23.9875C41.2313 22.2391 41.1258 19.5155 39.4142 17.898C38.5584 17.0416 37.3861 16.5778 36.1786 16.6016H31.1729V31.5161H33.0603ZM33.0605 23.6425V18.4332H36.2262C36.9063 18.4332 37.5512 18.6948 38.0319 19.1706C39.052 20.1696 39.0754 21.8347 38.0905 22.8694C37.6098 23.3809 36.9297 23.6663 36.2262 23.6425H33.0605ZM48.4293 22.1083C47.6204 21.359 46.5185 20.9784 45.1234 20.9784C43.3298 20.9784 41.9816 21.6444 41.0906 22.9646L42.7553 24.0231C43.3649 23.1192 44.1973 22.6673 45.2524 22.6673C45.9206 22.6673 46.5653 22.9171 47.0694 23.369C47.5618 23.7972 47.8432 24.4156 47.8432 25.0698V25.5098C47.1163 25.1055 46.2019 24.8914 45.0765 24.8914C43.7635 24.8914 42.7084 25.2006 41.923 25.831C41.1375 26.4613 40.739 27.2939 40.739 28.3524C40.7155 29.3158 41.1258 30.2316 41.8527 30.85C42.5912 31.5161 43.5291 31.8491 44.631 31.8491C45.9323 31.8491 46.9639 31.2663 47.7494 30.1007H47.8314V31.5161H49.6368V25.2244C49.6368 23.9042 49.2382 22.8576 48.4293 22.1083ZM43.3066 29.6369C42.9197 29.3514 42.6852 28.8876 42.6852 28.3881C42.6852 27.8291 42.9432 27.3652 43.4473 26.9965C43.9632 26.6278 44.6081 26.4375 45.3702 26.4375C46.4255 26.4256 47.2462 26.6635 47.8325 27.1392C47.8325 27.948 47.5159 28.6497 46.8945 29.2444C46.3317 29.8153 45.5696 30.1364 44.7723 30.1364C44.2446 30.1483 43.7287 29.9699 43.3066 29.6369ZM53.693 35.9999L60.0001 21.3114H57.9485L55.0295 28.6378H54.9943L52.0049 21.3114H49.9534L54.0916 30.8619L51.747 35.9999H53.693Z" fill="#FFFFFF"/>
                    </svg>
                    <span id="btnPayLabel" class="whitespace-nowrap">Bayar dengan Google Pay</span>
                `;
            }
            console.error(err);
            alert('Terjadi kesalahan saat memproses transaksi ke server.');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        switchBillingMode('perpanjang');
    });
</script>
@endpush
