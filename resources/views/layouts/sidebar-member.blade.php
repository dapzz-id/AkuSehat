<div class="space-y-4">
    <!-- Group: MENU PENGGUNA -->
    @php
        $isMemberActive = request()->routeIs('dashboard');
    @endphp
    <div class="sidebar-group space-y-1" data-group-id="member-menu" data-has-active="{{ $isMemberActive ? '1' : '0' }}">
        <button type="button" 
                onclick="toggleSidebarGroup('member-menu')" 
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase tracking-wider text-emerald-400/80 hover:text-white hover:bg-white/5 transition-all group/toggle cursor-pointer select-none">
            <span class="flex items-center gap-2">
                <i class="fas fa-compass text-[11px] text-emerald-400/70 group-hover/toggle:text-emerald-300"></i>
                <span>Menu Pengguna</span>
            </span>
            <i id="member-menu-chevron" class="fas fa-chevron-down text-[9px] text-emerald-400/60 group-hover/toggle:text-emerald-300 transition-transform duration-200"></i>
        </button>
        <div id="member-menu-content" class="space-y-1 transition-all duration-200">
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white shadow-md shadow-emerald-900/30 font-semibold' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors {{ request()->routeIs('dashboard') ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400 group-hover:bg-white/10 group-hover:text-emerald-300' }}">
                    <i class="fas fa-chart-pie text-sm"></i>
                </div>
                <span>Dashboard Pribadi</span>
            </a>

            <a href="{{ route('privacy.policy') }}" target="_blank"
               class="group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 text-slate-300 hover:bg-white/10 hover:text-white">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center transition-colors bg-white/5 text-sky-400 group-hover:bg-white/10 group-hover:text-sky-300">
                    <i class="fas fa-shield-halved text-sm"></i>
                </div>
                <span>Kebijakan Privasi</span>
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
