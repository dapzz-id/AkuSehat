@extends('layouts.app')

@section('title', 'Kontrol Mode Maintenance')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <i class="fas fa-screwdriver-wrench mr-1"></i> Maintenance Control
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">Kontrol Mode Pemeliharaan (Maintenance)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Atur mode pemeliharaan terpisah untuk platform <strong>Web</strong> dan <strong>Mobile</strong>. Saat aktif, hanya <strong>Super Administrator</strong> yang dapat masuk dan mengakses sistem.
            </p>
        </div>
    </div>

    <!-- Current Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Web Application Status Card -->
        <div class="bg-white rounded-2xl shadow-soft p-5 border-2 {{ $mode->is_web_maintenance ? 'border-rose-300 bg-rose-50/20' : 'border-emerald-200' }} transition-all">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl {{ $mode->is_web_maintenance ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }} flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-desktop"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Web Application</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Website, Dashboard Institusi & Halaman Publik</p>
                    </div>
                </div>
                <span class="px-3.5 py-1 rounded-full text-xs font-bold {{ $mode->is_web_maintenance ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                    @if($mode->is_web_maintenance)
                        <i class="fas fa-triangle-exclamation mr-1"></i> Maintenance
                    @else
                        <i class="fas fa-circle-check mr-1"></i> Online
                    @endif
                </span>
            </div>
        </div>

        <!-- Mobile Application Status Card -->
        <div class="bg-white rounded-2xl shadow-soft p-5 border-2 {{ $mode->is_mobile_maintenance ? 'border-rose-300 bg-rose-50/20' : 'border-emerald-200' }} transition-all">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-2xl {{ $mode->is_mobile_maintenance ? 'bg-rose-100 text-rose-700' : 'bg-green-100 text-green-700' }} flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-mobile-screen-button"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Mobile Application</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Aplikasi Siswa/Member (Android & iOS) & REST API</p>
                    </div>
                </div>
                <span class="px-3.5 py-1 rounded-full text-xs font-bold {{ $mode->is_mobile_maintenance ? 'bg-rose-100 text-rose-700 border border-rose-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                    @if($mode->is_mobile_maintenance)
                        <i class="fas fa-triangle-exclamation mr-1"></i> Maintenance
                    @else
                        <i class="fas fa-circle-check mr-1"></i> Online
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- SuperAdmin Privilege Alert -->
    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/80 border border-amber-200/90 text-amber-900 flex items-start gap-3.5">
        <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 text-base mt-0.5">
            <i class="fas fa-shield-halved"></i>
        </div>
        <div class="space-y-1">
            <p class="text-xs font-bold uppercase tracking-wider text-amber-800">Kebijakan Akses Pemeliharaan (Superadmin Bypass)</p>
            <p class="text-xs text-amber-700 leading-relaxed">
                Saat mode pemeliharaan diaktifkan pada suatu platform, seluruh pengguna reguler (Siswa/Member, Admin Sekolah/Instansi, Health Monitor) akan dicegah mengakses layanan dan diarahkan ke layar informasi pemeliharaan (HTTP 503). 
                <strong>Super Administrator memiliki akses penuh tanpa hambatan</strong> untuk login, mengakses dashboard, dan melakukan perbaikan sistem.
            </p>
        </div>
    </div>

    <!-- Platform Controls Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-bold text-slate-900">Pengaturan Status Platform</h3>
                <p class="text-xs text-slate-500 mt-0.5">Aktifkan atau nonaktifkan pemeliharaan platform menggunakan tombol saklar di bawah ini.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/70">
                        <th class="px-6 py-3.5">Platform Target</th>
                        <th class="px-6 py-3.5">Cakupan Akses</th>
                        <th class="px-6 py-3.5">Status Saat Ini</th>
                        <th class="px-6 py-3.5 text-center">Aksi Saklar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    <!-- Row 1: Web Application -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4.5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-teal-50 border border-teal-200 text-teal-700 flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-desktop"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 text-sm block">Web Application</span>
                                    <span class="text-[11px] text-slate-400 font-mono">Platform Web & Portal Dashboard</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4.5 text-slate-600 max-w-xs">
                            Seluruh halaman browser, login web, dashboard instansi/sekolah, modul kesehatan, dan portal administrasi.
                        </td>
                        <td class="px-6 py-4.5 whitespace-nowrap">
                            @if($mode->is_web_maintenance)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                    Sedang Maintenance
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Online & Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4.5 whitespace-nowrap text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       id="switch-web"
                                       onchange="openToggleModal('web', {{ $mode->is_web_maintenance ? 'true' : 'false' }})"
                                       class="sr-only peer"
                                       {{ $mode->is_web_maintenance ? 'checked' : '' }}>
                                <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-6 peer-checked:bg-rose-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all shadow-inner border border-slate-300 peer-checked:border-rose-600"></div>
                            </label>
                        </td>
                    </tr>

                    <!-- Row 2: Mobile Application -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4.5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center justify-center text-lg shrink-0">
                                    <i class="fas fa-mobile-screen-button"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 text-sm block">Mobile Application</span>
                                    <span class="text-[11px] text-slate-400 font-mono">Android & iOS App</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4.5 text-slate-600 max-w-xs">
                            Aplikasi seluler member/siswa (Play Store & App Store) serta seluruh endpoint REST API mobile.
                        </td>
                        <td class="px-6 py-4.5 whitespace-nowrap">
                            @if($mode->is_mobile_maintenance)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                                    Sedang Maintenance
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    Online & Aktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4.5 whitespace-nowrap text-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       id="switch-mobile"
                                       onchange="openToggleModal('mobile', {{ $mode->is_mobile_maintenance ? 'true' : 'false' }})"
                                       class="sr-only peer"
                                       {{ $mode->is_mobile_maintenance ? 'checked' : '' }}>
                                <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-6 peer-checked:bg-rose-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all shadow-inner border border-slate-300 peer-checked:border-rose-600"></div>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Toggle Maintenance -->
<div id="toggleModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all">
    <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="p-6">
            <div class="flex items-center gap-3.5 mb-4">
                <div id="modalIconWrap" class="w-11 h-11 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl shrink-0">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900" id="toggleModalTitle">Konfirmasi Mode Maintenance</h3>
                    <p class="text-xs text-slate-500" id="toggleModalSubtitle">Perubahan status akses platform</p>
                </div>
            </div>

            <form id="toggleForm" action="{{ route('superadmin.maintenance.toggle', $mode->id) }}" method="POST" data-no-confirm="true">
                @csrf
                <input type="hidden" name="platform" id="togglePlatform">
                
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 mb-5">
                    <p class="text-xs text-slate-700 leading-relaxed" id="toggleMessage"></p>
                </div>

                <div class="flex items-center justify-end gap-2.5">
                    <button type="button" onclick="cancelToggleModal()" 
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="confirmButton"
                            class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 border border-emerald-800 shadow-md transition-all flex items-center gap-2">
                        <i class="fas fa-check"></i>
                        <span id="confirmButtonText">Ya, Terapkan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let activePlatform = null;
let originalChecked = false;

function openToggleModal(platform, isCurrentlyActive) {
    activePlatform = platform;
    originalChecked = isCurrentlyActive;

    const modal = document.getElementById('toggleModal');
    const title = document.getElementById('toggleModalTitle');
    const subtitle = document.getElementById('toggleModalSubtitle');
    const message = document.getElementById('toggleMessage');
    const platformInput = document.getElementById('togglePlatform');
    const iconWrap = document.getElementById('modalIconWrap');
    const confirmBtn = document.getElementById('confirmButton');
    const confirmText = document.getElementById('confirmButtonText');

    platformInput.value = platform;

    const platformDisplayName = platform === 'web' ? 'Web Application' : 'Mobile Application';
    const willActivate = !isCurrentlyActive;

    if (willActivate) {
        title.textContent = `Aktifkan Maintenance ${platformDisplayName}`;
        subtitle.textContent = `Membatasi akses pengguna publik & instansi`;
        iconWrap.className = 'w-11 h-11 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl shrink-0';
        confirmBtn.className = 'px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-rose-700 hover:bg-rose-800 border border-rose-800 shadow-md transition-all flex items-center gap-2';
        confirmText.textContent = 'Ya, Aktifkan Maintenance';

        if (platform === 'web') {
            message.innerHTML = '<strong>Perhatian:</strong> Website akan dialihkan ke layar pemeliharaan 503 untuk semua pengguna reguler. Hanya akun <strong>Super Administrator</strong> yang dapat masuk dan mengakses seluruh fitur website.';
        } else {
            message.innerHTML = '<strong>Perhatian:</strong> Layanan aplikasi mobile (Android/iOS) dan REST API akan diblokir dengan status 503. Hanya akun <strong>Super Administrator</strong> yang tetap diizinkan mengakses.';
        }
    } else {
        title.textContent = `Nonaktifkan Maintenance ${platformDisplayName}`;
        subtitle.textContent = `Memulihkan akses normal untuk semua pengguna`;
        iconWrap.className = 'w-11 h-11 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl shrink-0';
        confirmBtn.className = 'px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 border border-emerald-800 shadow-md transition-all flex items-center gap-2';
        confirmText.textContent = 'Ya, Kembalikan Online';

        message.innerHTML = `Apakah Anda yakin ingin menonaktifkan mode pemeliharaan untuk <strong>${platformDisplayName}</strong>? Layanan akan langsung kembali online dan dapat diakses oleh semua pengguna.`;
    }

    modal.classList.remove('hidden');
}

function cancelToggleModal() {
    const modal = document.getElementById('toggleModal');
    modal.classList.add('hidden');

    // Kembalikan posisi saklar switch ke status aslinya
    if (activePlatform) {
        const switchEl = document.getElementById(`switch-${activePlatform}`);
        if (switchEl) {
            switchEl.checked = originalChecked;
        }
    }
}

// Tutup modal bila klik luar area kartu
document.addEventListener('click', function(e) {
    const modal = document.getElementById('toggleModal');
    if (e.target.id === 'toggleModal') {
        cancelToggleModal();
    }
});
</script>
@endpush
