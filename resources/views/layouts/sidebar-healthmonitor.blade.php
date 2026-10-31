<div class="space-y-4">
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

    <!-- Group: PEMANTAUAN -->
    @php
        $isHealthActive = request()->routeIs('admin.kesehatan.*') || request()->routeIs('admin.hb.*');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="hm-pemantauan" data-has-active="{{ $isHealthActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('hm-pemantauan')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-heart-pulse text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Pemantauan Kesehatan</span>
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
