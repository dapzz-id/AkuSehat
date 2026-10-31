<!-- Welcome Hero Banner -->
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-950 via-emerald-900 to-teal-950 p-6 sm:p-8 lg:p-10 text-white shadow-xl shadow-emerald-950/20 border border-emerald-800/40">
    <!-- Background Pattern & Ambient Glow -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#34d399_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>
    <div class="absolute -top-24 -right-24 w-80 h-80 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-2xl">
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                Selamat Datang, {{ auth()->user()->nama }}!
            </h1>
            <p class="text-sm sm:text-base text-emerald-100/80 leading-relaxed">
                @if(auth()->user()->instansi)
                    Pemantauan kesehatan berkala & analitik terpadu untuk 
                    <strong class="text-white font-semibold">{{ auth()->user()->instansi->nama_instansi }}</strong>.
                @else
                    Pantau status kesehatan dan catatan hemoglobin seluruh member secara real-time.
                @endif
            </p>
        </div>

        <div class="flex flex-wrap sm:flex-nowrap items-center gap-3">
            <a href="{{ route('admin.kesehatan.create') }}" 
               class="btn-press flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-white text-emerald-900 hover:bg-emerald-50 shadow-md shadow-black/20 transition-all duration-200">
                <i class="fas fa-plus text-emerald-700"></i>
                <span>Catat Kesehatan</span>
            </a>
            <a href="{{ route('admin.hb.create') }}" 
               class="btn-press flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-semibold bg-emerald-500/20 hover:bg-emerald-500/30 text-white border border-emerald-400/30 backdrop-blur-md transition-all duration-200">
                <i class="fas fa-droplet text-rose-400"></i>
                <span>Input HB</span>
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    <!-- Card 1: Total Member -->
    <div class="group bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-soft hover:shadow-card hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Member</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($data['total_member']) }}</p>
            </div>
            <div class="w-13 h-13 w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <i class="fas fa-users text-xl"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">Anggota terdaftar</span>
            <span class="font-semibold text-emerald-700 flex items-center gap-1">
                <i class="fas fa-circle-check text-[10px]"></i> Aktif
            </span>
        </div>
    </div>

    <!-- Card 2: Total Divisi -->
    <div class="group bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-soft hover:shadow-card hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Divisi / Kelas</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($data['total_divisi']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-teal-50 border border-teal-100 flex items-center justify-center text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <i class="fas fa-sitemap text-xl"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">Kelompok divisi</span>
            <span class="font-semibold text-teal-700">Tersinkron</span>
        </div>
    </div>

    <!-- Card 3: Rekam Kesehatan Tahun Ini -->
    <div class="group bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-soft hover:shadow-card hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Rekam Kesehatan</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($data['data_kesehatan_tahun_ini']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-center text-sky-600 group-hover:bg-sky-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <i class="fas fa-notes-medical text-xl"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">Pemeriksaan fisik</span>
            <span class="font-semibold text-sky-700">Tahun {{ now()->year }}</span>
        </div>
    </div>

    <!-- Card 4: Pemeriksaan HB Tahun Ini -->
    <div class="group bg-white rounded-2xl p-5 sm:p-6 border border-slate-200/80 shadow-soft hover:shadow-card hover:-translate-y-1 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pemeriksaan HB</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($data['data_hb_tahun_ini']) }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-all duration-300 shadow-sm">
                <i class="fas fa-droplet text-xl"></i>
            </div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500">Skrining anemia</span>
            <span class="font-semibold text-rose-700">Tahun {{ now()->year }}</span>
        </div>
    </div>
</div>

<!-- Quick Action Shortcut Hub -->
<div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-soft">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Aksi Cepat Sistem</h2>
            <p class="text-xs text-slate-500">Akses pintas untuk entri data dan manajemen harian</p>
        </div>
        <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
            Pintasan
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Action 1 -->
        <a href="{{ route('admin.kesehatan.create') }}" 
           class="btn-press group flex items-center gap-4 p-4 rounded-2xl bg-slate-50/80 hover:bg-emerald-50/80 border border-slate-200/70 hover:border-emerald-300 transition-all duration-200">
            <div class="w-11 h-11 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/20 group-hover:scale-105 transition-transform">
                <i class="fas fa-plus"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-bold text-slate-800 group-hover:text-emerald-900 truncate">Tambah Kesehatan</h3>
                <p class="text-xs text-slate-500 truncate">Rekam data fisik member</p>
            </div>
            <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all"></i>
        </a>

        <!-- Action 2 -->
        <a href="{{ route('admin.hb.create') }}" 
           class="btn-press group flex items-center gap-4 p-4 rounded-2xl bg-slate-50/80 hover:bg-rose-50/80 border border-slate-200/70 hover:border-rose-300 transition-all duration-200">
            <div class="w-11 h-11 rounded-xl bg-rose-600 text-white flex items-center justify-center shadow-md shadow-rose-600/20 group-hover:scale-105 transition-transform">
                <i class="fas fa-droplet"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-bold text-slate-800 group-hover:text-rose-900 truncate">Input Data HB</h3>
                <p class="text-xs text-slate-500 truncate">Catat level hemoglobin</p>
            </div>
            <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-rose-600 group-hover:translate-x-1 transition-all"></i>
        </a>

        <!-- Action 3 -->
        <a href="{{ route('admin.users.index') }}" 
           class="btn-press group flex items-center gap-4 p-4 rounded-2xl bg-slate-50/80 hover:bg-sky-50/80 border border-slate-200/70 hover:border-sky-300 transition-all duration-200">
            <div class="w-11 h-11 rounded-xl bg-sky-600 text-white flex items-center justify-center shadow-md shadow-sky-600/20 group-hover:scale-105 transition-transform">
                <i class="fas fa-users-gear"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-bold text-slate-800 group-hover:text-sky-900 truncate">Daftar Member</h3>
                <p class="text-xs text-slate-500 truncate">Kelola data seluruh siswa/anggota</p>
            </div>
            <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-sky-600 group-hover:translate-x-1 transition-all"></i>
        </a>

        <!-- Action 4 -->
        <a href="{{ route('admin.divisi.index') }}" 
           class="btn-press group flex items-center gap-4 p-4 rounded-2xl bg-slate-50/80 hover:bg-teal-50/80 border border-slate-200/70 hover:border-teal-300 transition-all duration-200">
            <div class="w-11 h-11 rounded-xl bg-teal-600 text-white flex items-center justify-center shadow-md shadow-teal-600/20 group-hover:scale-105 transition-transform">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-bold text-slate-800 group-hover:text-teal-900 truncate">Kelola Divisi</h3>
                <p class="text-xs text-slate-500 truncate">Atur struktur kelas / bagian</p>
            </div>
            <i class="fas fa-chevron-right text-xs text-slate-400 group-hover:text-teal-600 group-hover:translate-x-1 transition-all"></i>
        </a>
    </div>
</div>

<!-- Health Summary by Divisi / Class Section -->
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Ringkasan Kesehatan Berdasarkan Divisi</h2>
            <p class="text-xs text-slate-500">Status kelengkapan pemeriksaan kesehatan per kelas/divisi</p>
        </div>
        <a href="{{ route('admin.divisi.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1.5 self-start sm:self-auto">
            <span>Semua Divisi</span>
            <i class="fas fa-arrow-right text-[10px]"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        @forelse ($dataByClass as $item)
            @php
                $kesehatanCount = $item->total_kesehatan_tahun_ini ?? 0;
                $hbCount = $item->total_hb_tahun_ini ?? 0;
                $accentColor = !empty($item->color_cover) ? $item->color_cover : '#059669';
            @endphp

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft hover:shadow-card hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                <!-- Top Colored Accent Bar -->
                <div class="h-2 w-full" style="background-color: {{ $accentColor }};"></div>

                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 leading-snug">{{ $item->divisi_name }}</h3>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $item->member ?? 'Divisi' }}</p>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 flex-shrink-0">
                                {{ $item->total_member }} Member
                            </span>
                        </div>

                        <!-- Metrics Chips -->
                        <div class="grid grid-cols-2 gap-2 mt-4">
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <div class="text-[11px] text-slate-500 font-medium">Kesehatan</div>
                                <div class="text-base font-extrabold text-slate-800 mt-0.5">{{ $kesehatanCount }}</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <div class="text-[11px] text-slate-500 font-medium">Tes HB</div>
                                <div class="text-base font-extrabold text-slate-800 mt-0.5">{{ $hbCount }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <a href="{{ route('admin.users.index') }}?divisi={{ $item->id }}" 
                           class="btn-press flex items-center justify-between w-full px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-emerald-50 hover:text-emerald-800 border border-slate-200/60 transition-all duration-200">
                            <span>Lihat Member Divisi</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full p-8 text-center bg-white rounded-2xl border border-dashed border-slate-300">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-inbox text-xl"></i>
                </div>
                <p class="text-sm font-semibold text-slate-700">Belum Ada Data Divisi</p>
                <p class="text-xs text-slate-400 mt-1">Silakan tambahkan data divisi terlebih dahulu melalui menu Data Divisi.</p>
            </div>
        @endforelse
    </div>
</div>
