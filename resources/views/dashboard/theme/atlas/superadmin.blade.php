<div class="space-y-6">
    <!-- Main Two-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Tool Categories Launcher (8 Cols) -->
        <div class="lg:col-span-8 xl:col-span-8 flex flex-col gap-6">
            <!-- No Results Found Box -->
            <div id="atlas-no-results" hidden class="hidden p-8 text-center bg-white rounded-2xl border border-dashed border-slate-300">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-magnifying-glass text-xl"></i>
                </div>
                <p class="text-base font-bold text-slate-800">Modul Tidak Ditemukan</p>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">Tidak ada fitur yang cocok dengan kata kunci pencarian Anda.</p>
                <button type="button" onclick="clearAtlasSearch()" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100">
                    <i class="fas fa-rotate-left text-[10px]"></i> Reset Pencarian
                </button>
            </div>

            <!-- Group 1: Manajemen Instansi & Lisensi Nasional -->
            <div class="atlas-section cpanel-section bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition-all duration-200">
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 cursor-pointer select-none"
                     onclick="toggleAtlasCategory('sa-instansi')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-700 flex items-center justify-center text-sm">
                            <i class="fas fa-building-columns"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Manajemen Instansi & Lisensi Nasional</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200/70 text-slate-700 cpanel-count-badge">Instansi</span>
                        <i id="sa-instansi-chevron" class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"></i>
                    </div>
                </div>

                <div id="sa-instansi-content" class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5">
                    <a href="{{ route('superadmin.instansi.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-500/50 hover:bg-blue-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="instansi sekolah data npsn profil institusi pendidikan">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-school-flag text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-blue-700 transition-colors truncate">Data Sekolah & Instansi</h3>
                        </div>
                    </a>

                    <a href="{{ route('superadmin.license.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-amber-500/50 hover:bg-amber-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="lisensi kuota aplikasi generate license key masa aktif">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-key text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-amber-700 transition-colors truncate">Lisensi & Kuota Aplikasi</h3>
                        </div>
                    </a>

                    <a href="{{ route('superadmin.users.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-emerald-500/50 hover:bg-emerald-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="users data pengguna anggota admin monitor member global">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-user-shield text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition-colors truncate">Manajemen Pengguna Global</h3>
                        </div>
                    </a>

                    <a href="{{ route('superadmin.modules.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-cyan-500/50 hover:bg-cyan-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="master modul fitur kesehatan visibilitas konfigurasi global">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-600 to-blue-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-sliders text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-cyan-700 transition-colors truncate">Master Modul & Fitur</h3>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Group 2: Pemeliharaan Server & Software -->
            <div class="atlas-section cpanel-section bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition-all duration-200">
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 cursor-pointer select-none"
                     onclick="toggleAtlasCategory('sa-maintenance')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-rose-500/10 text-rose-700 flex items-center justify-center text-sm">
                            <i class="fas fa-screwdriver-wrench"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Pemeliharaan Sistem, Server & Pembaruan</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200/70 text-slate-700 cpanel-count-badge">System</span>
                        <i id="sa-maintenance-chevron" class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"></i>
                    </div>
                </div>

                <div id="sa-maintenance-content" class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5">
                    <a href="{{ route('superadmin.update.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-sky-500/50 hover:bg-sky-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="update software rilis aplikasi apk bundle versi web">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-cloud-arrow-down text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-sky-700 transition-colors truncate">Update Software Aplikasi</h3>
                        </div>
                    </a>

                    <a href="{{ route('superadmin.maintenance.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-rose-500/50 hover:bg-rose-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="mode maintenance bypass token pemeliharaan web mobile">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-screwdriver-wrench text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-rose-700 transition-colors truncate">Mode Maintenance</h3>
                        </div>
                    </a>

                    <a href="{{ route('superadmin.logs.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-teal-500/50 hover:bg-teal-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="log audit sistem aktivitas login modifikasi data keamanan server">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-clock-rotate-left text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-teal-700 transition-colors truncate">Log & Audit Sistem</h3>
                        </div>
                    </a>

                    <a href="{{ route('patch_notes.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-amber-500/50 hover:bg-amber-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="catatan patch publik rilis fitur perbaikan instansi">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-code-branch text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-amber-700 transition-colors truncate">Catatan Patch Publik</h3>
                        </div>
                    </a>

                    <a href="{{ route('superadmin.terminal.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-emerald-500/50 hover:bg-emerald-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="terminal console cli artisan composer bash shell eksekusi perintah">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-900 to-emerald-950 text-emerald-400 border border-emerald-500/30 flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-terminal text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition-colors truncate">Web Terminal &amp; Konsol</h3>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Group 3: Akun SuperAdmin & Switch Theme -->
            <div class="atlas-section cpanel-section bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition-all duration-200">
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 cursor-pointer select-none"
                     onclick="toggleAtlasCategory('sa-utilitas')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-500/10 text-slate-700 flex items-center justify-center text-sm">
                            <i class="fas fa-gear"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Akun SuperAdmin & Tampilan</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200/70 text-slate-700 cpanel-count-badge">Akun</span>
                        <i id="sa-utilitas-chevron" class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"></i>
                    </div>
                </div>

                <div id="sa-utilitas-content" class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5">
                    <a href="{{ route('profile.edit') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-slate-400 hover:bg-slate-50 hover:shadow-sm transition-all duration-200"
                       data-keywords="profil keamanan akun kredensial password login super admin">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-user-gear text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-slate-900 transition-colors truncate">Profil & Keamanan Akun</h3>
                        </div>
                    </a>

                    <a href="{{ route('privacy.policy') }}" target="_blank"
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-500/50 hover:bg-blue-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="kebijakan privasi rekam medis data publik privacy policy">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-shield-halved text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-blue-700 transition-colors truncate">Kebijakan Privasi Sistem</h3>
                        </div>
                    </a>

                    <button type="button" 
                            onclick="confirmLogout(event)"
                            class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-rose-500/50 hover:bg-rose-50/40 hover:shadow-sm transition-all duration-200 text-left w-full"
                            data-keywords="logout keluar sistem sign out ganti akun keluar akun">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-arrow-right-from-bracket text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-rose-700 transition-colors truncate">Keluar Sistem (Logout)</h3>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Right: Server Status & Quick Information Sidebar (4 Cols) -->
        <div class="lg:col-span-4 xl:col-span-4 flex flex-col gap-6">
            <!-- Box 1: Status Server Nasional -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 sm:p-5">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-2">
                        <i class="fas fa-server text-emerald-600 text-sm"></i>
                        <span>Status Sistem Server</span>
                    </h3>
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Online
                    </span>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between gap-3 py-0.5">
                        <span class="text-slate-500 font-medium">Total Instansi:</span>
                        <span class="font-semibold text-slate-800 shrink-0">{{ number_format($totalSekolah) }} Sekolah</span>
                    </div>

                    <div class="flex items-center justify-between gap-3 py-0.5">
                        <span class="text-slate-500 font-medium">Lisensi Aktif:</span>
                        <span class="font-semibold text-emerald-700 shrink-0">{{ number_format($licenseAktif) }} Lisensi</span>
                    </div>

                    <div class="flex items-center justify-between gap-3 py-0.5">
                        <span class="text-slate-500 font-medium">Lisensi Expired:</span>
                        <span class="font-semibold text-rose-600 shrink-0">{{ number_format($licenseExpired) }} Lisensi</span>
                    </div>

                    <div class="flex items-center justify-between gap-3 py-0.5">
                        <span class="text-slate-500 font-medium">Total Pengguna:</span>
                        <span class="font-semibold text-slate-800 shrink-0">{{ number_format($totalUsers) }} Akun</span>
                    </div>

                    <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-3 py-0.5">
                        <span class="text-slate-500 font-medium">Mode Pemeliharaan:</span>
                        <span class="font-semibold text-xs shrink-0 {{ $maintenanceWeb ? 'text-rose-600' : 'text-emerald-700' }}">
                            {{ $maintenanceWeb ? 'Maintenance Aktif' : 'Normal (Off)' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between gap-3 py-0.5">
                        <span class="text-slate-500 font-medium">Versi PHP / Engine:</span>
                        <span class="font-mono text-[11px] text-slate-700 font-semibold shrink-0">PHP {{ PHP_VERSION }} / L{{ explode('.', app()->version())[0] }}</span>
                    </div>
                </div>
            </div>

            <!-- Box 2: Tindakan Cepat SuperAdmin -->
            <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 rounded-2xl p-4 sm:p-5 text-white shadow-md border border-slate-800">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-300 flex items-center gap-2 mb-3">
                    <i class="fas fa-bolt-lightning text-amber-400 text-sm"></i>
                    <span>Tindakan SuperAdmin</span>
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('superadmin.instansi.create') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-medium text-white transition-colors">
                        <i class="fas fa-plus text-emerald-400 text-xs w-4 text-center"></i>
                        <span>Daftarkan Instansi Baru</span>
                    </a>
                    <a href="{{ route('superadmin.license.index') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-medium text-white transition-colors">
                        <i class="fas fa-key text-amber-400 text-xs w-4 text-center"></i>
                        <span>Kelola Lisensi & Kuota</span>
                    </a>
                    <a href="{{ route('superadmin.update.index') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-medium text-white transition-colors">
                        <i class="fas fa-cloud-arrow-up text-sky-400 text-xs w-4 text-center"></i>
                        <span>Rilis Update Software</span>
                    </a>
                    <a href="{{ route('superadmin.terminal.index') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-medium text-white transition-colors">
                        <i class="fas fa-terminal text-emerald-400 text-xs w-4 text-center"></i>
                        <span>Buka Web Terminal</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

