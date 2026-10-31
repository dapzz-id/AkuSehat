<!-- Header Hero Banner -->
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-950 via-emerald-950 to-slate-900 p-6 sm:p-8 lg:p-10 text-white shadow-xl shadow-slate-950/20 border border-emerald-900/40">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>
    <div class="absolute -top-24 -right-24 w-80 h-80 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300 text-xs font-semibold backdrop-blur-md">
                <i class="fas fa-shield-halved text-emerald-400"></i>
                <span>Super Administrator Portal</span>
                <span class="text-emerald-400/60">&bull;</span>
                <span>Monitoring Seluruh Instansi</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                Pusat Kontrol & Manajemen Sistem
            </h1>
            <p class="text-sm sm:text-base text-emerald-100/80 leading-relaxed">
                Pengawasan lisensi instansi, distribusi pembaruan software, dan kontrol server nasional.
            </p>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 self-start md:self-auto">
            <div class="flex items-center gap-2 text-xs sm:text-sm text-emerald-200/90 bg-white/5 border border-white/10 px-4 py-2.5 rounded-2xl backdrop-blur-md">
                <i class="far fa-calendar-alt text-emerald-400 text-base"></i>
                <span class="font-medium">{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6">
    <!-- Card 1: Total Sekolah -->
    <div class="group bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-soft hover:shadow-card hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Instansi</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalSekolah) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <i class="fas fa-school-flag text-xl"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">Sekolah & Institusi</span>
            <a href="{{ route('superadmin.instansi.index') }}" class="font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                Kelola <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

    <!-- Card 2: License Aktif -->
    <div class="group bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-soft hover:shadow-card hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Lisensi Aktif</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($licenseAktif) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <i class="fas fa-key text-xl"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">Berjalan normal</span>
            <span class="font-semibold text-emerald-700 flex items-center gap-1">
                <i class="fas fa-check-circle text-[10px]"></i> Aktif
            </span>
        </div>
    </div>

    <!-- Card 3: License Expired -->
    <div class="group bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-soft hover:shadow-card hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Lisensi Kadaluarsa</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($licenseExpired) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <i class="fas fa-triangle-exclamation text-xl"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">Perlu perpanjangan</span>
            <span class="font-semibold text-rose-600">{{ $licenseExpired > 0 ? 'Perlu tindakan' : 'Bersih' }}</span>
        </div>
    </div>

    <!-- Card 4: Total Users -->
    <div class="group bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-soft hover:shadow-card hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Seluruh User</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalUsers) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <i class="fas fa-users-gear text-xl"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">Akun terdaftar</span>
            <a href="{{ route('superadmin.users.index') }}" class="font-semibold text-purple-600 hover:text-purple-700 flex items-center gap-1">
                Kelola <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>
</div>

<!-- License Akan Expired Warning -->
@if($licenseAkanExpired->count() > 0)
    <div class="p-5 sm:p-6 rounded-3xl bg-amber-50/80 border border-amber-200/90 shadow-soft">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-amber-500/20">
                <i class="fas fa-bell text-base"></i>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-2">
                    <h3 class="text-base font-bold text-amber-900">Peringatan Masa Berlaku Lisensi</h3>
                    <span class="text-xs font-semibold text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded-full border border-amber-200 self-start sm:self-auto">
                        {{ $licenseAkanExpired->count() }} Lisensi Segera Berakhir (&lt; 30 Hari)
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                    @foreach($licenseAkanExpired as $license)
                        <div class="bg-white p-3.5 rounded-2xl border border-amber-200/80 shadow-xs flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $license->instansi->nama_instansi ?? ($license->sekolah->nama_sekolah ?? "Instansi") }}</p>
                                <p class="text-xs text-slate-500 font-mono mt-0.5 truncate">{{ $license->key }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="px-2 py-0.5 rounded-md text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200">
                                    {{ round($license->getDaysRemaining()) }} hari lagi
                                </span>
                                <p class="text-[11px] text-slate-400 mt-1">{{ $license->tanggal_berakhir->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Split Grid: Instansi Terbaru & Status Maintenance -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Instansi / Sekolah Terbaru -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Instansi Terbaru</h2>
                    <p class="text-xs text-slate-500">Sekolah/institusi yang baru didaftarkan ke sistem</p>
                </div>
                <a href="{{ route('superadmin.instansi.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                    Lihat Semua <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="space-y-3">
                @forelse($sekolahTerbaru as $sekolah)
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-50 hover:bg-emerald-50/50 border border-slate-100 transition-colors gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $sekolah->nama_instansi ?? $sekolah->nama_sekolah }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ $sekolah->kota ?? 'Indonesia' }} &bull; {{ $sekolah->provinsi ?? '' }}</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-medium text-slate-400 flex-shrink-0">
                            {{ $sekolah->created_at->diffForHumans() }}
                        </span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada data instansi baru</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Status Server & Pembaruan Sistem -->
    <div class="space-y-6">
        <!-- Status Maintenance Controls -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Status Operasional</h2>
                    <p class="text-xs text-slate-500">Mode pemeliharaan sistem web & mobile</p>
                </div>
                <a href="{{ route('superadmin.maintenance.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                    Atur <i class="fas fa-cog"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Web Status -->
                <div class="p-4 rounded-2xl border {{ $maintenanceWeb ? 'bg-rose-50/60 border-rose-200' : 'bg-emerald-50/60 border-emerald-200' }} flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $maintenanceWeb ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white' }} flex items-center justify-center shadow-sm">
                            <i class="fas fa-globe text-base"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Portal Website</p>
                            <p class="text-sm font-bold {{ $maintenanceWeb ? 'text-rose-700' : 'text-emerald-800' }}">
                                {{ $maintenanceWeb ? 'Maintenance' : 'Normal / Online' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Mobile Status -->
                <div class="p-4 rounded-2xl border {{ $maintenanceMobile ? 'bg-rose-50/60 border-rose-200' : 'bg-emerald-50/60 border-emerald-200' }} flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $maintenanceMobile ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white' }} flex items-center justify-center shadow-sm">
                            <i class="fas fa-mobile-screen text-base"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Aplikasi Mobile</p>
                            <p class="text-sm font-bold {{ $maintenanceMobile ? 'text-rose-700' : 'text-emerald-800' }}">
                                {{ $maintenanceMobile ? 'Maintenance' : 'Normal / Online' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pembaruan Aplikasi Terbaru -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-slate-900 tracking-tight">Rilis Software</h2>
                <a href="{{ route('superadmin.update.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">
                    Detail <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            @if($latestUpdate)
                <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-600/20">
                        <i class="fas fa-code-branch text-base"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm font-bold text-emerald-950 truncate">{{ $latestUpdate->name }}</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white">v{{ $latestUpdate->version }}</span>
                        </div>
                        <p class="text-xs text-emerald-800 mt-1 line-clamp-2">{{ $latestUpdate->description }}</p>
                        <p class="text-[11px] text-emerald-600 mt-2 font-medium">
                            Dirilis: {{ $latestUpdate->release_date ? $latestUpdate->release_date->format('d M Y') : '-' }}
                        </p>
                    </div>
                </div>
            @else
                <p class="text-xs text-slate-400 text-center py-4">Belum ada catatan update software</p>
            @endif
        </div>
    </div>
</div>
