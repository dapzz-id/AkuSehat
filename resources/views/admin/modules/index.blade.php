@extends('layouts.app')

@section('title', 'Kelola Modul & Izin Instansi')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/20">
                    <i class="fas fa-sliders text-base"></i>
                </div>
                <span>Kelola Modul & Hak Akses</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kustomisasi fitur aktif, penamaan sebutan peran, dan perizinan hak akses untuk <strong class="text-slate-700">{{ $instansi->nama_instansi }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <i class="fas {{ $instansi->jenis_instansi === 'Sekolah' ? 'fa-school' : ($instansi->jenis_instansi === 'Universitas' ? 'fa-graduation-cap' : ($instansi->jenis_instansi === 'Perusahaan' ? 'fa-briefcase' : ($instansi->jenis_instansi === 'Pemerintah' ? 'fa-building-shield' : ($instansi->jenis_instansi === 'Klinik' ? 'fa-hospital' : 'fa-building')))) }} text-emerald-600"></i>
                <span>Tipe: {{ $instansi->jenis_instansi }}</span>
            </span>
        </div>
    </div>

    <!-- Main Configuration Form -->
    <form action="{{ route('admin.modules.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- SECTION 1: PROFIL & KLASIFIKASI RESMI INSTITUSI (DIKELOLA SUPERADMIN) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 flex-shrink-0">
                        <i class="fas {{ $instansi->jenis_instansi === 'Sekolah' ? 'fa-school' : ($instansi->jenis_instansi === 'Universitas' ? 'fa-graduation-cap' : ($instansi->jenis_instansi === 'Perusahaan' ? 'fa-briefcase' : ($instansi->jenis_instansi === 'Pemerintah' ? 'fa-building-shield' : ($instansi->jenis_instansi === 'Klinik' ? 'fa-hospital' : 'fa-landmark')))) }} text-lg"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-slate-800">Profil & Klasifikasi Resmi Institusi</h2>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                <i class="fas fa-shield-check text-[10px]"></i> Terverifikasi SuperAdmin
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Klasifikasi dan identitas resmi entitas dikonfigurasi & diverifikasi langsung oleh SuperAdmin.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        <i class="fas fa-lock text-slate-400"></i>
                        <span>Otoritas SuperAdmin</span>
                    </span>
                </div>
            </div>

            <!-- Profile Info Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-5">
                <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/70">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Nama Resmi Institusi</span>
                    <span class="text-sm font-bold text-slate-800 block truncate" title="{{ $instansi->nama_instansi }}">
                        {{ $instansi->nama_instansi }}
                    </span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Entitas Terdaftar</span>
                </div>

                <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/70">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Klasifikasi / Jenis</span>
                    <div class="flex items-center gap-1.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold 
                            @if($instansi->jenis_instansi === 'Sekolah') bg-blue-100 text-blue-800
                            @elseif($instansi->jenis_instansi === 'Universitas') bg-purple-100 text-purple-800
                            @elseif($instansi->jenis_instansi === 'Perusahaan') bg-emerald-100 text-emerald-800
                            @elseif($instansi->jenis_instansi === 'Pemerintah') bg-amber-100 text-amber-800
                            @elseif($instansi->jenis_instansi === 'Klinik') bg-rose-100 text-rose-800
                            @else bg-slate-200 text-slate-800 @endif">
                            <i class="fas {{ $instansi->jenis_instansi === 'Sekolah' ? 'fa-school' : ($instansi->jenis_instansi === 'Universitas' ? 'fa-graduation-cap' : ($instansi->jenis_instansi === 'Perusahaan' ? 'fa-briefcase' : ($instansi->jenis_instansi === 'Pemerintah' ? 'fa-building-shield' : ($instansi->jenis_instansi === 'Klinik' ? 'fa-hospital' : 'fa-users')))) }} text-[11px]"></i>
                            {{ $instansi->jenis_instansi }}
                        </span>
                        @if($instansi->jenjang)
                            <span class="text-xs text-slate-600 font-medium">({{ $instansi->jenjang }})</span>
                        @endif
                    </div>
                    <span class="text-[11px] text-slate-500 mt-1 block">Tipe Basis Fitur</span>
                </div>

                <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/70">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">NPSN / Kode Registrasi</span>
                    <span class="text-sm font-mono font-bold text-slate-800 block">
                        {{ $instansi->npsn ?? '-' }}
                    </span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Identitas Legalitas</span>
                </div>

                <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/70">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Domisili & Wilayah</span>
                    <span class="text-sm font-bold text-slate-800 block truncate" title="{{ $instansi->kota }}, {{ $instansi->provinsi }}">
                        {{ $instansi->kota ?? '-' }}, {{ $instansi->provinsi ?? '-' }}
                    </span>
                    <span class="text-[11px] text-slate-500 mt-0.5 block">Lokasi Operasional</span>
                </div>
            </div>

            <!-- Notice / Hint -->
            <div class="mt-4 p-3.5 bg-blue-50/70 rounded-xl border border-blue-100/80 flex items-start gap-2.5">
                <i class="fas fa-circle-info text-blue-600 text-sm mt-0.5 flex-shrink-0"></i>
                <p class="text-xs text-blue-800 leading-relaxed">
                    <strong>Catatan Kebijakan:</strong> Klasifikasi dan profil resmi institusi ditetapkan serta dikelola langsung oleh <strong>SuperAdmin</strong> saat proses registrasi institusi. Pengaturan di halaman ini khusus untuk aktivasi modul fitur, penyesuaian sebutan peran, dan hak akses pengguna di lingkungan {{ $instansi->nama_instansi }}. Jika terdapat perubahan klasifikasi atau profil legalitas entitas, silakan hubungi tim SuperAdmin.
                </p>
            </div>
        </div>

        <!-- SECTION 2: MODUL AKTIF (SWITCHES) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-cubes text-emerald-600"></i>
                        <span>Aktivasi Modul Fitur</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Aktifkan atau nonaktifkan modul sesuai kebutuhan operasional institusi Anda.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($availableModules as $key => $mod)
                    @php
                        $globalCfg = $globalConfigs[$key] ?? null;
                        $globalStatus = $globalCfg ? $globalCfg->status : 'active';
                        $isLockedBySuperAdmin = in_array($globalStatus, ['invisible', 'tester_only']);
                        $isMaintenance = ($globalStatus === 'maintenance');
                        $isActive = in_array($key, $activeModules) && !$isLockedBySuperAdmin;
                    @endphp
                    <div id="module_card_{{ $key }}" class="p-4 rounded-xl border transition-all flex flex-col justify-between
                        @if($globalStatus === 'invisible')
                            border-slate-300 bg-slate-100/70 opacity-75
                        @elseif($globalStatus === 'maintenance')
                            border-amber-300 bg-amber-50/40
                        @elseif($globalStatus === 'tester_only')
                            border-purple-300 bg-purple-50/40
                        @elseif($isActive)
                            border-emerald-200 bg-emerald-50/30
                        @else
                            border-slate-200 bg-slate-50/50
                        @endif">
                        
                        <div class="flex items-start gap-3.5">
                            <div id="module_icon_{{ $key }}" class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 
                                @if($globalStatus === 'invisible') bg-slate-300 text-slate-500
                                @elseif($globalStatus === 'maintenance') bg-amber-500 text-white shadow-sm
                                @elseif($globalStatus === 'tester_only') bg-purple-600 text-white shadow-sm
                                @elseif($isActive) bg-emerald-600 text-white shadow-sm
                                @else bg-slate-200 text-slate-400 @endif">
                                <i class="{{ $mod['icon'] }} text-lg"></i>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-800 truncate">{{ $mod['name'] }}</h3>
                                    <span id="module_badge_{{ $key }}" class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded-full 
                                        @if($isActive) bg-emerald-100 text-emerald-800 @else bg-slate-200 text-slate-600 @endif">
                                        {{ $mod['badge'] }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                    {{ $mod['description'] }}
                                </p>
                            </div>

                            <!-- Toggle Switch -->
                            <div class="flex items-center pt-1">
                                @if($isLockedBySuperAdmin)
                                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-200/90 text-slate-600 text-[11px] font-bold" title="Dikunci oleh SuperAdmin">
                                        <i class="fas fa-lock text-slate-400"></i>
                                        <span>Terkunci</span>
                                    </div>
                                @else
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="active_modules[]" value="{{ $key }}" class="sr-only peer module-checkbox" id="mod_check_{{ $key }}" {{ $isActive ? 'checked' : '' }} onchange="updateModuleVisual('{{ $key }}', this.checked)">
                                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <!-- SuperAdmin Override Warning Banner if applicable -->
                        @if($globalStatus === 'invisible')
                            <div class="mt-3 pt-2.5 border-t border-slate-200 flex items-center gap-2 text-slate-600 text-[11px]">
                                <i class="fas fa-eye-slash text-slate-400"></i>
                                <span><strong>Nonaktif Global oleh SuperAdmin:</strong> Modul ini disembunyikan/dinonaktifkan oleh administrator utama sistem sehingga tidak dapat diaktifkan oleh institusi.</span>
                            </div>
                        @elseif($globalStatus === 'maintenance')
                            <div class="mt-3 pt-2.5 border-t border-amber-200 flex items-center gap-2 text-amber-800 text-[11px]">
                                <i class="fas fa-wrench text-amber-600"></i>
                                <span><strong>Dalam Perbaikan/Maintenance SuperAdmin:</strong> {{ $globalCfg->maintenance_message ?: 'Modul ini sedang dalam masa perbaikan bug/pemeliharaan berkala sistem.' }}</span>
                            </div>
                        @elseif($globalStatus === 'tester_only')
                            <div class="mt-3 pt-2.5 border-t border-purple-200 flex items-center gap-2 text-purple-800 text-[11px]">
                                <i class="fas fa-flask text-purple-600"></i>
                                <span><strong>Akses Khusus Akun Tester:</strong> Fitur ini sedang dalam fase uji coba (Beta) dan hanya dapat diakses oleh akun yang ditandai sebagai Tester oleh SuperAdmin.</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- SECTION 3: KUSTOMISASI LABEL PERAN (ROLES) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                <div>
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-id-card-clip text-emerald-600"></i>
                        <span>Penamaan Label Peran (Role Customization)</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Sesuaikan istilah sebutan peran agar selaras dengan kultur institusi Anda (contoh: Siswa vs Karyawan).
                    </p>
                </div>
                <button type="button" onclick="loadDefaultRolePresets('{{ $instansi->jenis_instansi }}')" class="btn-press inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition-colors">
                    <i class="fas fa-rotate-left text-xs"></i>
                    <span>Reset ke Rekomendasi ({{ $instansi->jenis_instansi }})</span>
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Label 'Admin' (PMR / Unit)
                    </label>
                    <input type="text" 
                           id="role_admin_input"
                           name="role_labels[Admin]" 
                           value="{{ $roleLabels['Admin'] ?? 'Admin PMR' }}" 
                           placeholder="Contoh: Admin PMR / Koordinator HSE"
                           class="block w-full px-3.5 py-2.5 text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600">
                    <p class="text-[11px] text-slate-400 mt-1">Pengelola harian (PMR, UKS, HSE, KSR)</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Label untuk 'Member'
                    </label>
                    <input type="text" 
                           id="role_member_input"
                           name="role_labels[Member]" 
                           value="{{ $roleLabels['Member'] ?? 'Member' }}" 
                           placeholder="Contoh: Siswa / Karyawan"
                           class="block w-full px-3.5 py-2.5 text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600">
                    <p class="text-[11px] text-slate-400 mt-1">Pengguna biasa/anggota institusi</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Label untuk 'Health Monitor'
                    </label>
                    <input type="text" 
                           id="role_monitor_input"
                           name="role_labels[Health Monitor]" 
                           value="{{ $roleLabels['Health Monitor'] ?? 'Health Monitor' }}" 
                           placeholder="Contoh: Guru Penjaskes / Tim Medis HSE"
                           class="block w-full px-3.5 py-2.5 text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600">
                    <p class="text-[11px] text-slate-400 mt-1">Petugas pemeriksa fisik & skrining</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Label untuk 'Health Consultant'
                    </label>
                    <input type="text" 
                           id="role_consultant_input"
                           name="role_labels[Health Consultant]" 
                           value="{{ $roleLabels['Health Consultant'] ?? 'Health Consultant' }}" 
                           placeholder="Contoh: Guru BK / Psikolog HRD"
                           class="block w-full px-3.5 py-2.5 text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600">
                    <p class="text-[11px] text-slate-400 mt-1">Konselor kesehatan mental & pembimbing</p>
                </div>
            </div>
        </div>

        <!-- SECTION 4: HAK AKSES & PERMISSIONS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-2">
                <i class="fas fa-shield-halved text-emerald-600"></i>
                <span>Matriks Hak Akses & Kebijakan Data</span>
            </h2>
            <p class="text-xs text-slate-500 mb-5">
                Konfigurasi perizinan mandiri bagi para pengguna instansi.
            </p>

            <div class="space-y-3.5">
                <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50/50 cursor-pointer">
                    <div>
                        <span class="text-sm font-semibold text-slate-800 block">Izinkan Member Menginput Tanda Vital Mandiri</span>
                        <span class="text-xs text-slate-500">Member/karyawan dapat mencatat tensi, jam tidur, dan mood harian dari aplikasi mobile.</span>
                    </div>
                    <input type="checkbox" name="permissions[member_can_input_vitals]" value="1" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500" {{ !empty($permissions['member_can_input_vitals']) ? 'checked' : '' }}>
                </label>

                <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50/50 cursor-pointer">
                    <div>
                        <span class="text-sm font-semibold text-slate-800 block">Tampilkan Papan Peringkat (Leaderboard) Lari ke Semua Member</span>
                        <span class="text-xs text-slate-500">Mendorong kompetisi sehat antaranggota dan antardivisi di institusi.</span>
                    </div>
                    <input type="checkbox" name="permissions[member_can_view_leaderboard]" value="1" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500" {{ !empty($permissions['member_can_view_leaderboard']) ? 'checked' : '' }}>
                </label>

                <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50/50 cursor-pointer">
                    <div>
                        <span class="text-sm font-semibold text-slate-800 block">Izinkan Health Monitor Mengekspor Laporan Excel/PDF</span>
                        <span class="text-xs text-slate-500">Memberikan wewenang kepada guru penjaskes/tim medis untuk mengunduh rekap.</span>
                    </div>
                    <input type="checkbox" name="permissions[health_monitor_can_export]" value="1" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500" {{ !empty($permissions['health_monitor_can_export']) ? 'checked' : '' }}>
                </label>

                <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50/50 cursor-pointer">
                    <div>
                        <span class="text-sm font-semibold text-slate-800 block">Peringatan Otomatis Risiko Anemia (HB Rendah)</span>
                        <span class="text-xs text-slate-500">Sistem otomatis memicu notifikasi jika kadar HB siswa/karyawan di bawah ambang batas normal.</span>
                    </div>
                    <input type="checkbox" name="permissions[auto_warning_anemia]" value="1" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500" {{ !empty($permissions['auto_warning_anemia']) ? 'checked' : '' }}>
                </label>

                <!-- SECTION KHUSUS: PERMISSION ADD USER -->
                <div class="mt-5 pt-5 border-t border-slate-200">
                    <div class="mb-3">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider block flex items-center gap-1.5">
                            <i class="fas fa-user-plus text-emerald-600"></i>
                            <span>Wewenang Penambahan Pengguna (Add User Permission)</span>
                        </span>
                        <span class="text-xs text-slate-500">Tentukan siapa saja selain Admin Instansi yang memiliki hak akses untuk menambahkan dan mengimpor akun pengguna.</span>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50/50 cursor-pointer">
                            <div>
                                <span class="text-sm font-semibold text-slate-800 block">Izinkan {{ $roleLabels['Admin'] ?? 'Admin PMR' }} Menambah User</span>
                                <span class="text-xs text-slate-500">Pengguna dengan level Admin dapat membuka form tambah user dan mengimpor data via Excel.</span>
                            </div>
                            <input type="checkbox" name="permissions[admin_can_add_user]" value="1" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500" {{ !empty($permissions['admin_can_add_user']) ? 'checked' : '' }}>
                        </label>

                        <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50/50 cursor-pointer">
                            <div>
                                <span class="text-sm font-semibold text-slate-800 block">Izinkan {{ $roleLabels['Health Monitor'] ?? 'Health Monitor' }} Menambah User</span>
                                <span class="text-xs text-slate-500">Petugas pemeriksa fisik/UKS dapat mendaftarkan akun anggota baru langsung ke divisi.</span>
                            </div>
                            <input type="checkbox" name="permissions[health_monitor_can_add_user]" value="1" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500" {{ !empty($permissions['health_monitor_can_add_user']) ? 'checked' : '' }}>
                        </label>

                        <label class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200/80 hover:bg-slate-50/50 cursor-pointer">
                            <div>
                                <span class="text-sm font-semibold text-slate-800 block">Izinkan {{ $roleLabels['Health Consultant'] ?? 'Health Consultant' }} Menambah User</span>
                                <span class="text-xs text-slate-500">Konselor atau Guru BK dapat mendaftarkan akun siswa/karyawan baru untuk sesi bimbingan.</span>
                            </div>
                            <input type="checkbox" name="permissions[health_consultant_can_add_user]" value="1" class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500" {{ !empty($permissions['health_consultant_can_add_user']) ? 'checked' : '' }}>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Submit Bar -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="btn-press px-6 py-3 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 transition-all flex items-center gap-2">
                <i class="fas fa-check"></i>
                <span>Simpan Konfigurasi Modul & Izin</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const ROLE_PRESETS = {
        'Sekolah': {
            admin: 'Admin PMR / Pengurus UKS',
            member: 'Siswa / Siswi',
            monitor: 'Guru Penjaskes / Pembina UKS',
            consultant: 'Guru BK / Konselor',
            modules: ['haid_pita', 'olahraga', 'vitalitas', 'kesehatan', 'hemoglobin', 'konsultasi', 'nutrisi', 'vaksinasi'],
        },
        'Universitas': {
            admin: 'Admin KSR PMI / Satgas Kampus',
            member: 'Mahasiswa / Mahasiswi',
            monitor: 'Instruktur Olahraga / Tim Klinik Kampus',
            consultant: 'Dosen Pembimbing / Konselor Kampus',
            modules: ['haid_pita', 'olahraga', 'vitalitas', 'kesehatan', 'hemoglobin', 'konsultasi', 'nutrisi', 'burnout', 'ergonomi'],
        },
        'Perusahaan': {
            admin: 'Admin K3 / Koordinator HSE',
            member: 'Karyawan / Staf',
            monitor: 'Petugas HSE / Tim Medis Perusahaan',
            consultant: 'HR People Care / Psikolog EAP',
            modules: ['haid_pita', 'olahraga', 'vitalitas', 'kesehatan', 'hemoglobin', 'konsultasi', 'burnout', 'ergonomi'],
        },
        'Pemerintah': {
            admin: 'Admin Poliklinik / PIC Kesehatan ASN',
            member: 'ASN / Pegawai',
            monitor: 'Tim Medis / K3 Pegawai',
            consultant: 'Tim Kepegawaian / Konselor ASN',
            modules: ['haid_pita', 'olahraga', 'vitalitas', 'kesehatan', 'hemoglobin', 'konsultasi', 'burnout', 'ergonomi'],
        },
        'Klinik': {
            admin: 'Admin Operasional / Resepsionis Klinis',
            member: 'Pasien / Klien',
            monitor: 'Perawat / Asisten Medis',
            consultant: 'Dokter / Konselor Medis',
            modules: ['haid_pita', 'vitalitas', 'kesehatan', 'hemoglobin', 'konsultasi', 'nutrisi', 'vaksinasi'],
        },
        'Lainnya': {
            admin: 'Admin Unit / Fasilitator Kesehatan',
            member: 'Anggota / Member',
            monitor: 'Instruktur Jasmani',
            consultant: 'Konselor Institusi',
            modules: ['haid_pita', 'olahraga', 'vitalitas', 'kesehatan', 'hemoglobin', 'konsultasi', 'nutrisi'],
        }
    };

    function updateModuleVisual(key, isChecked) {
        const card = document.getElementById('module_card_' + key);
        const icon = document.getElementById('module_icon_' + key);
        const badge = document.getElementById('module_badge_' + key);

        if (card) {
            if (isChecked) {
                card.classList.remove('border-slate-200', 'bg-slate-50/50');
                card.classList.add('border-emerald-200', 'bg-emerald-50/30');
            } else {
                card.classList.remove('border-emerald-200', 'bg-emerald-50/30');
                card.classList.add('border-slate-200', 'bg-slate-50/50');
            }
        }

        if (icon) {
            if (isChecked) {
                icon.classList.remove('bg-slate-200', 'text-slate-400');
                icon.classList.add('bg-emerald-600', 'text-white', 'shadow-sm');
            } else {
                icon.classList.remove('bg-emerald-600', 'text-white', 'shadow-sm');
                icon.classList.add('bg-slate-200', 'text-slate-400');
            }
        }

        if (badge) {
            if (isChecked) {
                badge.classList.remove('bg-slate-200', 'text-slate-600');
                badge.classList.add('bg-emerald-100', 'text-emerald-800');
            } else {
                badge.classList.remove('bg-emerald-100', 'text-emerald-800');
                badge.classList.add('bg-slate-200', 'text-slate-600');
            }
        }
    }

    function loadDefaultRolePresets(type) {
        const preset = ROLE_PRESETS[type] || ROLE_PRESETS['Sekolah'];
        if (!preset) return;

        // Auto populate role names for this instansi type
        const adminInput = document.getElementById('role_admin_input');
        const memberInput = document.getElementById('role_member_input');
        const monitorInput = document.getElementById('role_monitor_input');
        const consultantInput = document.getElementById('role_consultant_input');

        if (adminInput) adminInput.value = preset.admin;
        if (memberInput) memberInput.value = preset.member;
        if (monitorInput) monitorInput.value = preset.monitor;
        if (consultantInput) consultantInput.value = preset.consultant;

        showPresetToast(`Label peran disesuaikan dengan standar rekomendasi untuk ${type}.`);
    }

    function showPresetToast(msg) {
        let toast = document.getElementById('presetToast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'presetToast';
            toast.className = 'fixed bottom-6 right-6 z-50 max-w-md bg-slate-900 text-white text-xs px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3 border border-slate-700 transition-all duration-300';
            document.body.appendChild(toast);
        }
        toast.innerHTML = `<i class="fas fa-magic text-emerald-400 text-sm"></i><span>${msg}</span>`;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
        }, 4000);
    }
</script>
@endpush
