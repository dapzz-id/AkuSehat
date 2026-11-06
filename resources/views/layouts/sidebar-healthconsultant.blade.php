<div class="space-y-4">
    @php
        $instansi = auth()->user()->instansi;
        $userInstansiId = auth()->user()->instansi_id;
        $userId = auth()->id();

        // Count pending SafeSpace sessions addressed to this consultant or unassigned
        $pendingCounselingCount = \Illuminate\Support\Facades\Cache::remember('hc_pending_csl_' . $userId, 30, function() use ($userInstansiId, $userId) {
            return \App\Models\CounselingSession::where('instansi_id', $userInstansiId)
                ->where('status', 'menunggu')
                ->where(function ($q) use ($userId) {
                    $q->where('consultant_id', $userId)
                      ->orWhereNull('consultant_id');
                })
                ->count();
        });

        // Count late ribbon loans
        $latePitaCount = \Illuminate\Support\Facades\Cache::remember('late_pita_cnt_' . $userInstansiId, 60, function() use ($userInstansiId) {
            return \App\Models\PeminjamanPita::where('instansi_id', $userInstansiId)
                ->whereIn('status', ['terlambat', 'dipinjam'])
                ->whereDate('estimasi_selesai_haid', '<', now()->toDateString())
                ->count();
        });
    @endphp

    <!-- Group: MENU UTAMA -->
    @php
        $isMainActive = request()->routeIs('dashboard');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="hc-menu-utama" data-has-active="{{ $isMainActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hc-menu-utama')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-compass text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Menu Utama</span>
            </span>
            <i id="hc-menu-utama-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hc-menu-utama-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-chart-pie text-sm"></i>
                </div>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Group: LAYANAN BIMBINGAN & KONSELING (SAFESPACE) -->
    @if((auth()->user()->isModuleActive('konsultasi', 'duty') && auth()->user()->canRead('konsultasi', 'duty')) || (auth()->user()->isModuleActive('burnout', 'duty') && auth()->user()->canRead('burnout', 'duty')))
    @php
        $isMentalActive = request()->routeIs('admin.counseling.*') || request()->routeIs('admin.burnout.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="hc-kesehatan-mental" data-has-active="{{ $isMentalActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hc-kesehatan-mental')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-comments text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Bimbingan & SafeSpace</span>
            </span>
            <i id="hc-kesehatan-mental-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hc-kesehatan-mental-content" class="space-y-1 transition-all duration-200">
            @if(auth()->user()->isModuleActive('konsultasi', 'duty') && auth()->user()->canRead('konsultasi', 'duty'))
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
            @endif

            @if(auth()->user()->isModuleActive('burnout', 'duty') && auth()->user()->canRead('burnout', 'duty'))
            <a href="{{ route('admin.burnout.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.burnout.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.burnout.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-purple-400 group-hover:bg-white/10 group-hover:text-purple-300' }}">
                    <i class="fas fa-brain text-sm"></i>
                </div>
                <span>Skrining Burnout & Stres</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Group: KESEHATAN REPRODUKSI SISWI (FEMHEALTH & PITA HAID) -->
    @if(auth()->user()->isModuleActive('haid_pita', 'duty') && auth()->user()->canRead('haid_pita', 'duty'))
    @php
        $isFemActive = request()->routeIs('admin.femhealth.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="hc-femhealth" data-has-active="{{ $isFemActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hc-femhealth')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-calendar-alt text-[11px] text-pink-400/80 group-hover/toggle:text-pink-300"></i>
                <span>Siklus & Pita Haid</span>
            </span>
            <i id="hc-femhealth-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hc-femhealth-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('admin.femhealth.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.femhealth.*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-md shadow-pink-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.femhealth.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-pink-400 group-hover:bg-white/10 group-hover:text-pink-300' }}">
                    <i class="fas fa-clipboard-list text-sm"></i>
                </div>
                <div class="flex-1 flex items-center justify-between">
                    <span>Siklus & Pita Siswi</span>
                    @if($latePitaCount > 0)
                        <span class="text-[9px] bg-rose-500 text-white font-bold px-1.5 py-0.5 rounded-full animate-pulse shadow-sm">
                            {{ $latePitaCount }} Warning
                        </span>
                    @else
                        <span class="text-[10px] bg-pink-500/20 text-pink-300 px-1.5 py-0.5 rounded border border-pink-500/30">Fem</span>
                    @endif
                </div>
            </a>
        </div>
    </div>
    @endif

    <!-- Group: PEMANTAUAN MEDIS & KLINIS -->
    @if((auth()->user()->isModuleActive('kesehatan', 'duty') && auth()->user()->canRead('kesehatan', 'duty')) || (auth()->user()->isModuleActive('hemoglobin', 'duty') && auth()->user()->canRead('hemoglobin', 'duty')) || (auth()->user()->isModuleActive('vaksinasi', 'duty') && auth()->user()->canRead('vaksinasi', 'duty')))
    @php
        $isMedisActive = request()->routeIs('admin.kesehatan.*') || request()->routeIs('admin.hb.*') || request()->routeIs('admin.vaccine.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="hc-data-medis" data-has-active="{{ $isMedisActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hc-data-medis')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-heart-pulse text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Data Medis & Fisik</span>
            </span>
            <i id="hc-data-medis-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hc-data-medis-content" class="space-y-1 transition-all duration-200">
            @if(auth()->user()->isModuleActive('kesehatan', 'duty') && auth()->user()->canRead('kesehatan', 'duty'))
            <a href="{{ route('admin.kesehatan.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.kesehatan.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.kesehatan.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-teal-400 group-hover:bg-white/10 group-hover:text-teal-300' }}">
                    <i class="fas fa-notes-medical text-sm"></i>
                </div>
                <span>Data Kesehatan Fisik</span>
            </a>
            @endif

            @if(auth()->user()->isModuleActive('hemoglobin', 'duty') && auth()->user()->canRead('hemoglobin', 'duty'))
            <a href="{{ route('admin.hb.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.hb.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.hb.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-rose-400 group-hover:bg-white/10 group-hover:text-rose-300' }}">
                    <i class="fas fa-droplet text-sm"></i>
                </div>
                <span>Data Hemoglobin</span>
            </a>
            @endif

            @if(auth()->user()->isModuleActive('vaksinasi', 'duty') && auth()->user()->canRead('vaksinasi', 'duty'))
            <a href="{{ route('admin.vaccine.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.vaccine.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.vaccine.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-blue-400 group-hover:bg-white/10 group-hover:text-blue-300' }}">
                    <i class="fas fa-syringe text-sm"></i>
                </div>
                <span>Paspor Vaksinasi</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Group: DATA ANGGOTA & KELAS -->
    @if(auth()->user()->canRead())
    @php
        $isUsersActive = request()->routeIs('admin.users.*') || request()->routeIs('admin.divisi.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="hc-data-pengguna" data-has-active="{{ $isUsersActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hc-data-pengguna')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-users text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Data Siswa & Kelas</span>
            </span>
            <i id="hc-data-pengguna-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hc-data-pengguna-content" class="space-y-1 transition-all duration-200">
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
    <div class="sidebar-group space-y-1" data-group-id="hc-sistem" data-has-active="{{ request()->routeIs('patch_notes.*') ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hc-sistem')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-gear text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Sistem</span>
            </span>
            <i id="hc-sistem-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="hc-sistem-content" class="space-y-1 transition-all duration-200">
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

