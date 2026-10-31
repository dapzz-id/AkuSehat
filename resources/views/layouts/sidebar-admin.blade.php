<div class="space-y-4">
    @php
        $instansi = auth()->user()->instansi;
        $memberLabel = $instansi ? $instansi->getRoleLabel('Member') : 'Member';
        $usersMenuLabel = $instansi ? $instansi->label_data_users_menu : 'Data Pengguna';
    @endphp

    <!-- Group: MENU UTAMA -->
    @php
        $isMainActive = request()->routeIs('dashboard');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="menu-utama" data-has-active="{{ $isMainActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('menu-utama')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-compass text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Menu Utama</span>
            </span>
            <i id="menu-utama-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="menu-utama-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-chart-pie text-sm"></i>
                </div>
                <span>Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Group: DATA MEDIS & KLINIS -->
    @if(!$instansi || $instansi->isModuleActive('kesehatan') || $instansi->isModuleActive('hemoglobin') || $instansi->isModuleActive('haid_pita') || $instansi->isModuleActive('vaksinasi'))
    @php
        $isMedisActive = request()->routeIs('admin.kesehatan.*') || request()->routeIs('admin.hb.*') || request()->routeIs('admin.femhealth.*') || request()->routeIs('admin.vaccine.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="data-medis" data-has-active="{{ $isMedisActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('data-medis')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-notes-medical text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Data Medis & Klinis</span>
            </span>
            <i id="data-medis-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="data-medis-content" class="space-y-1 transition-all duration-200">
            @if(!$instansi || $instansi->isModuleActive('kesehatan'))
            <a href="{{ route('admin.kesehatan.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.kesehatan.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.kesehatan.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-teal-400 group-hover:bg-white/10 group-hover:text-teal-300' }}">
                    <i class="fas fa-notes-medical text-sm"></i>
                </div>
                <span>Data Kesehatan Fisik</span>
            </a>
            @endif

            @if(!$instansi || $instansi->isModuleActive('hemoglobin'))
            <a href="{{ route('admin.hb.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.hb.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.hb.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-rose-400 group-hover:bg-white/10 group-hover:text-rose-300' }}">
                    <i class="fas fa-droplet text-sm"></i>
                </div>
                <span>Data Hemoglobin</span>
            </a>
            @endif

            @if(!$instansi || $instansi->isModuleActive('haid_pita'))
            @php
                $userInstansiId = auth()->user()->instansi_id;
                $latePitaCount = \Illuminate\Support\Facades\Cache::remember('late_pita_cnt_' . $userInstansiId, 60, function() use ($userInstansiId) {
                    return \App\Models\PeminjamanPita::where('instansi_id', $userInstansiId)
                        ->whereIn('status', ['terlambat', 'dipinjam'])
                        ->whereDate('estimasi_selesai_haid', '<', now()->toDateString())
                        ->count();
                });
            @endphp
            <a href="{{ route('admin.femhealth.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.femhealth.*') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-md shadow-pink-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.femhealth.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-pink-400 group-hover:bg-white/10 group-hover:text-pink-300' }}">
                    <i class="fas fa-calendar-alt text-sm"></i>
                </div>
                <div class="flex-1 flex items-center justify-between">
                    <span>Siklus & Pita Haid</span>
                    @if($latePitaCount > 0)
                        <span class="text-[9px] bg-rose-500 text-white font-bold px-1.5 py-0.5 rounded-full animate-pulse shadow-sm">
                            {{ $latePitaCount }} Warning
                        </span>
                    @else
                        <span class="text-[10px] bg-pink-500/20 text-pink-300 px-1.5 py-0.5 rounded border border-pink-500/30">Fem</span>
                    @endif
                </div>
            </a>
            @endif

            @if(!$instansi || $instansi->isModuleActive('vaksinasi'))
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

    <!-- Group: KEBUGARAN & GAYA HIDUP -->
    @if(!$instansi || $instansi->isModuleActive('olahraga') || $instansi->isModuleActive('vitalitas') || $instansi->isModuleActive('nutrisi') || $instansi->isModuleActive('ergonomi'))
    @php
        $isKebugaranActive = request()->routeIs('admin.activities.*') || request()->routeIs('admin.vital-logs.*') || request()->routeIs('admin.nutrition.*') || request()->routeIs('admin.ergonomics.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="kebugaran" data-has-active="{{ $isKebugaranActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('kebugaran')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-heart-pulse text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Kebugaran & Gaya Hidup</span>
            </span>
            <i id="kebugaran-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="kebugaran-content" class="space-y-1 transition-all duration-200">
            @if(!$instansi || $instansi->isModuleActive('olahraga'))
            <a href="{{ route('admin.activities.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.activities.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.activities.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-running text-sm"></i>
                </div>
                <span>Aktivitas Olahraga</span>
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

            @if(!$instansi || $instansi->isModuleActive('nutrisi'))
            <a href="{{ route('admin.nutrition.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.nutrition.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.nutrition.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-amber-400 group-hover:bg-white/10 group-hover:text-amber-300' }}">
                    <i class="fas fa-apple-whole text-sm"></i>
                </div>
                <span>Nutrisi & Gizi</span>
            </a>
            @endif

            @if(!$instansi || $instansi->isModuleActive('ergonomi'))
            <a href="{{ route('admin.ergonomics.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.ergonomics.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.ergonomics.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-cyan-400 group-hover:bg-white/10 group-hover:text-cyan-300' }}">
                    <i class="fas fa-chair text-sm"></i>
                </div>
                <span>Ergonomi & Postur</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Group: KESEHATAN MENTAL & KONSELING -->
    @if(!$instansi || $instansi->isModuleActive('konsultasi') || $instansi->isModuleActive('burnout'))
    @php
        $isMentalActive = request()->routeIs('admin.counseling.*') || request()->routeIs('admin.burnout.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="kesehatan-mental" data-has-active="{{ $isMentalActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('kesehatan-mental')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-comments text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Kesehatan Mental & Konseling</span>
            </span>
            <i id="kesehatan-mental-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="kesehatan-mental-content" class="space-y-1 transition-all duration-200">
            @if(!$instansi || $instansi->isModuleActive('konsultasi'))
            <a href="{{ route('admin.counseling.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.counseling.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.counseling.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-indigo-400 group-hover:bg-white/10 group-hover:text-indigo-300' }}">
                    <i class="fas fa-comments text-sm"></i>
                </div>
                <span>Konseling SafeSpace</span>
            </a>
            @endif

            @if(!$instansi || $instansi->isModuleActive('burnout'))
            <a href="{{ route('admin.burnout.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.burnout.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.burnout.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-purple-400 group-hover:bg-white/10 group-hover:text-purple-300' }}">
                    <i class="fas fa-brain text-sm"></i>
                </div>
                <span>Skrining Burnout</span>
            </a>
            @endif
        </div>
    </div>
    @endif

    <!-- Group: MANAJEMEN INSTANSI -->
    @if(in_array(auth()->user()->level, ['Admin', 'Admin Instansi']))
    @php
        $isInstansiActive = request()->routeIs('admin.users.*') || request()->routeIs('admin.divisi.*') || request()->routeIs('admin.modules.*') || request()->routeIs('admin.billing.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="manajemen-instansi" data-has-active="{{ $isInstansiActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('manajemen-instansi')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-building-columns text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Manajemen {{ $instansi ? $instansi->tipe_sebutan : 'Institusi' }}</span>
            </span>
            <i id="manajemen-instansi-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="manajemen-instansi-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('admin.users.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-users text-sm"></i>
                </div>
                <span>{{ $usersMenuLabel }}</span>
            </a>

            <a href="{{ route('admin.divisi.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.divisi.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.divisi.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-sitemap text-sm"></i>
                </div>
                <span>Data Divisi</span>
            </a>

            <a href="{{ route('admin.modules.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.modules.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.modules.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-amber-400 group-hover:bg-white/10 group-hover:text-amber-300' }}">
                    <i class="fas fa-sliders text-sm"></i>
                </div>
                <span>Kelola Modul & Izin</span>
            </a>

            <a href="{{ route('admin.billing.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('admin.billing.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('admin.billing.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-credit-card text-sm"></i>
                </div>
                <span>Langganan & Kuota</span>
            </a>

            <a href="{{ route('patch_notes.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('patch_notes.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('patch_notes.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-amber-400 group-hover:bg-white/10 group-hover:text-amber-300' }}">
                    <i class="fas fa-code-branch text-sm"></i>
                </div>
                <span>Riwayat Pembaruan</span>
            </a>
        </div>
    </div>
    @endif
</div>