<div class="space-y-4">
    <!-- Group: OVERVIEW -->
    @php
        $isOverviewActive = request()->routeIs('dashboard') || request()->routeIs('superadmin.logs.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="sa-overview" data-has-active="{{ $isOverviewActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('sa-overview')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-compass text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Overview</span>
            </span>
            <i id="sa-overview-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="sa-overview-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('superadmin.logs.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('superadmin.logs.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('superadmin.logs.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-teal-400 group-hover:bg-white/10 group-hover:text-teal-300' }}">
                    <i class="fas fa-clock-rotate-left text-sm"></i>
                </div>
                <span>Log & Audit Sistem</span>
            </a>
        </div>
    </div>

    <!-- Group: MANAJEMEN SISTEM -->
    @php
        $isInstansiActive = request()->routeIs('superadmin.instansi.*') || request()->routeIs('superadmin.users.*') || request()->routeIs('superadmin.license.*') || request()->routeIs('superadmin.modules.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="sa-instansi" data-has-active="{{ $isInstansiActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('sa-instansi')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-building-columns text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Instansi & Akses</span>
            </span>
            <i id="sa-instansi-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="sa-instansi-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('superadmin.instansi.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('superadmin.instansi.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('superadmin.instansi.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-school-flag text-sm"></i>
                </div>
                <span>Data Sekolah/Instansi</span>
            </a>

            <a href="{{ route('superadmin.users.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('superadmin.users.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('superadmin.users.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-user-shield text-sm"></i>
                </div>
                <span>Manajemen User</span>
            </a>

            <a href="{{ route('superadmin.license.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('superadmin.license.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('superadmin.license.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-amber-400 group-hover:bg-white/10 group-hover:text-amber-300' }}">
                    <i class="fas fa-key text-sm"></i>
                </div>
                <span>Lisensi Aplikasi</span>
            </a>

            <a href="{{ route('superadmin.modules.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('superadmin.modules.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('superadmin.modules.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-cyan-400 group-hover:bg-white/10 group-hover:text-cyan-300' }}">
                    <i class="fas fa-sliders text-sm"></i>
                </div>
                <span>Master Modul & Fitur</span>
            </a>
        </div>
    </div>

    <!-- Group: PEMELIHARAAN -->
    @php
        $isMaintenanceActive = request()->routeIs('superadmin.update.*') || request()->routeIs('superadmin.maintenance.*') || request()->routeIs('superadmin.terminal.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="sa-pemeliharaan" data-has-active="{{ $isMaintenanceActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('sa-pemeliharaan')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-screwdriver-wrench text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Pemeliharaan</span>
            </span>
            <i id="sa-pemeliharaan-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="sa-pemeliharaan-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('superadmin.terminal.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('superadmin.terminal.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('superadmin.terminal.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-terminal text-sm"></i>
                </div>
                <span>Terminal Konsol</span>
            </a>

            <a href="{{ route('superadmin.update.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('superadmin.update.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('superadmin.update.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-sky-400 group-hover:bg-white/10 group-hover:text-sky-300' }}">
                    <i class="fas fa-cloud-arrow-down text-sm"></i>
                </div>
                <span>Update Aplikasi</span>
            </a>

            <a href="{{ route('superadmin.maintenance.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('superadmin.maintenance.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('superadmin.maintenance.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-rose-400 group-hover:bg-white/10 group-hover:text-rose-300' }}">
                    <i class="fas fa-screwdriver-wrench text-sm"></i>
                </div>
                <span>Mode Maintenance</span>
            </a>

            <a href="{{ route('patch_notes.index') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('patch_notes.*') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('patch_notes.*') ? 'bg-white/20 text-white' : 'bg-white/5 text-amber-400 group-hover:bg-white/10 group-hover:text-amber-300' }}">
                    <i class="fas fa-code-branch text-sm"></i>
                </div>
                <span>Catatan Patch (Publik)</span>
            </a>
        </div>
    </div>
</div>