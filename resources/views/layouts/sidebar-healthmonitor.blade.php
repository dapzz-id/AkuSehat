<div class="space-y-4">
    @php
        $instansi = auth()->user()->instansi;
        $userInstansiId = auth()->user()->instansi_id;
        $userId = auth()->id();

        // Count pending SafeSpace sessions addressed to this health monitor or unassigned
        $pendingCounselingCount = \Illuminate\Support\Facades\Cache::remember('hm_pending_csl_' . $userId, 30, function() use ($userInstansiId, $userId) {
            return \App\Models\CounselingSession::where('instansi_id', $userInstansiId)
                ->where('status', 'menunggu')
                ->where(function ($q) use ($userId) {
                    $q->where('consultant_id', $userId)
                      ->orWhereNull('consultant_id');
                })
                ->count();
        });
    @endphp

    <!-- Group: MENU UTAMA -->
    @php
        $isMainActive = request()->routeIs('dashboard');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="hm-menu-utama" data-has-active="{{ $isMainActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hm-menu-utama')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-compass text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Menu Utama</span>
            </span>
            <i id="hm-menu-utama-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hm-menu-utama-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-chart-pie text-sm"></i>
                </div>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Group: KEBUGARAN & RUN TRACKER -->
    @if(!$instansi || $instansi->isModuleActive('olahraga') || $instansi->isModuleActive('vitalitas'))
    @php
        $isKebugaranActive = request()->routeIs('admin.activities.*') || request()->routeIs('admin.vital-logs.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="hm-kebugaran" data-has-active="{{ $isKebugaranActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hm-kebugaran')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-person-running text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Kebugaran & Run Tracker</span>
            </span>
            <i id="hm-kebugaran-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hm-kebugaran-content" class="space-y-1 transition-all duration-200">
            @if(!$instansi || $instansi->isModuleActive('olahraga'))
            <a href="{{ route('admin.activities.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.activities.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.activities.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-running text-sm"></i>
                </div>
                <span>Hasil Run Tracker & Olahraga</span>
            </a>
            @endif

            @if(!$instansi || $instansi->isModuleActive('vitalitas'))
            <a href="{{ route('admin.vital-logs.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vital-logs.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.vital-logs.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-sky-400 group-hover:bg-white/10 group-hover:text-sky-300' }}">
                    <i class="fas fa-heart-pulse text-sm"></i>
                </div>
                <span>Vitalitas & Tidur</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Group: KONSELING & SAFESPACE KEBUGARAN -->
    @if(!$instansi || $instansi->isModuleActive('konsultasi'))
    @php
        $isKonselingActive = request()->routeIs('admin.counseling.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="hm-konseling" data-has-active="{{ $isKonselingActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hm-konseling')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-comments text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>SafeSpace & Konseling</span>
            </span>
            <i id="hm-konseling-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hm-konseling-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('admin.counseling.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.counseling.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.counseling.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-indigo-400 group-hover:bg-white/10 group-hover:text-indigo-300' }}">
                    <i class="fas fa-comments text-sm"></i>
                </div>
                <div class="flex-1 flex items-center justify-between">
                    <span>Konseling SafeSpace</span>
                    @if($pendingCounselingCount > 0)
                        <span class="text-[9px] bg-indigo-500 text-white font-bold px-1.5 py-0.5 rounded-full animate-pulse shadow-sm">
                            {{ $pendingCounselingCount }} Request
                        </span>
                    @endif
                </div>
            </a>
        </div>
    </div>
    @endif

    <!-- Group: PEMANTAUAN KESEHATAN FISIK -->
    @php
        $isHealthActive = request()->routeIs('admin.kesehatan.*') || request()->routeIs('admin.hb.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="hm-pemantauan" data-has-active="{{ $isHealthActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hm-pemantauan')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-heart-pulse text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Pemantauan Kesehatan Fisik</span>
            </span>
            <i id="hm-pemantauan-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hm-pemantauan-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('admin.kesehatan.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.kesehatan.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.kesehatan.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-notes-medical text-sm"></i>
                </div>
                <span>Data Kesehatan</span>
            </a>

            <a href="{{ route('admin.hb.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.hb.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.hb.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-rose-400 group-hover:bg-white/10 group-hover:text-rose-300' }}">
                    <i class="fas fa-droplet text-sm"></i>
                </div>
                <span>Data Hemoglobin</span>
            </a>
        </div>
    </div>

    <!-- Group: DATA ANGGOTA & KELAS -->
    @if(auth()->user()->canRead())
    @php
        $isUsersActive = request()->routeIs('admin.users.*') || request()->routeIs('admin.divisi.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="hm-data-pengguna" data-has-active="{{ $isUsersActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hm-data-pengguna')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-users text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Data Siswa & Kelas</span>
            </span>
            <i id="hm-data-pengguna-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hm-data-pengguna-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('admin.users.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-sky-400 group-hover:bg-white/10 group-hover:text-sky-300' }}">
                    <i class="fas fa-user-group text-sm"></i>
                </div>
                <span>Daftar Siswa / Member</span>
            </a>

            <a href="{{ route('admin.divisi.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.divisi.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.divisi.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-teal-400 group-hover:bg-white/10 group-hover:text-teal-300' }}">
                    <i class="fas fa-layer-group text-sm"></i>
                </div>
                <span>Daftar Divisi / Kelas</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Group: SISTEM & PEMBARUAN -->
    <div class="sidebar-group space-y-1" data-group-id="hm-sistem" data-has-active="{{ request()->routeIs('patch_notes.*') ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hm-sistem')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-gear text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Sistem</span>
            </span>
            <i id="hm-sistem-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hm-sistem-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('patch_notes.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('patch_notes.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('patch_notes.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-amber-400 group-hover:bg-white/10 group-hover:text-amber-300' }}">
                    <i class="fas fa-code-branch text-sm"></i>
                </div>
                <span>Riwayat Pembaruan</span>
            </a>
        </div>
    </div>
</div>
