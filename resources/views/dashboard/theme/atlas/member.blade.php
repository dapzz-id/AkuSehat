<div class="space-y-6">
    <!-- Top Portal Bar: Search & Quick Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-emerald-950 rounded-2xl p-6 sm:p-8 text-white shadow-lg border border-slate-700/60 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 min-w-0">
            <div class="flex flex-wrap items-center gap-x-2.5 gap-y-1">
                <span class="shrink-0 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 whitespace-nowrap">
                    <i class="fas fa-th-large text-emerald-400 mr-1.5 text-[10px]"></i> Atlas Theme
                </span>
                <span class="text-xs sm:text-sm text-slate-300 truncate max-w-full font-medium">&bull; {{ $instansi ? $instansi->nama_instansi : 'Akun Pribadi' }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-white leading-tight">
                Pusat Layanan Kesehatan Member
            </h1>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                Pilih menu di bawah ini untuk mengakses fitur catatan kesehatan dan profil Anda.
            </p>
        </div>

        <!-- Search Bar -->
        <div class="flex items-center gap-3 w-full md:w-auto shrink-0">
            <div class="relative w-full md:w-80">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fas fa-search text-sm"></i>
                </div>
                <input type="text" 
                       id="atlas-search-input"
                       onkeyup="filterAtlasFeatures(this.value)"
                       placeholder="Cari menu layanan..." 
                       class="w-full pl-10 pr-9 py-2.5 text-sm bg-slate-950/60 border border-slate-600/80 rounded-xl text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                <button type="button" 
                        id="atlas-search-clear"
                        onclick="clearAtlasSearch()"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-white hidden">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Tool Categories Launcher (8 Cols) -->
        <div class="lg:col-span-8 xl:col-span-8 flex flex-col gap-6">
            <!-- No Results Found Box -->
            <div id="atlas-no-results" hidden class="hidden p-8 text-center bg-white rounded-2xl border border-dashed border-slate-300">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-magnifying-glass text-xl"></i>
                </div>
                <p class="text-base font-bold text-slate-800">Fitur Tidak Ditemukan</p>
                <p class="text-xs sm:text-sm text-slate-400 mt-1">Tidak ada fitur yang cocok dengan kata kunci pencarian Anda.</p>
                <button type="button" onclick="clearAtlasSearch()" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100">
                    <i class="fas fa-rotate-left text-[10px]"></i> Reset Pencarian
                </button>
            </div>

            <!-- Group 1: Layanan Kesehatan Mandiri -->
            <div class="atlas-section cpanel-section bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition-all duration-200">
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 cursor-pointer select-none"
                     onclick="toggleAtlasCategory('mbr-kesehatan')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-700 flex items-center justify-center text-sm">
                            <i class="fas fa-notes-medical"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Catatan Kesehatan Mandiri</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200/70 text-slate-700 cpanel-count-badge">Kesehatan</span>
                        <i id="mbr-kesehatan-chevron" class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"></i>
                    </div>
                </div>

                <div id="mbr-kesehatan-content" class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5">
                    <a href="{{ route('profile.edit') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-emerald-500/50 hover:bg-emerald-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="profil rekam identitas biodata member siswa data diri kontak darurat">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-id-card text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition-colors truncate">Profil & Rekam Identitas</h3>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Group 2: Akun & Bantuan -->
            <div class="atlas-section cpanel-section bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition-all duration-200">
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 cursor-pointer select-none"
                     onclick="toggleAtlasCategory('mbr-bantuan')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-500/10 text-slate-700 flex items-center justify-center text-sm">
                            <i class="fas fa-circle-question"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Akun, Bantuan & Informasi</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200/70 text-slate-700 cpanel-count-badge">Info</span>
                        <i id="mbr-bantuan-chevron" class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"></i>
                    </div>
                </div>

                <div id="mbr-bantuan-content" class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5">
                    <a href="{{ route('patch_notes.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-amber-500/50 hover:bg-amber-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="riwayat pembaruan patch notes fitur baru rilis update">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-code-branch text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-amber-700 transition-colors truncate">Riwayat Pembaruan</h3>
                        </div>
                    </a>

                    <a href="{{ route('privacy.policy') }}" target="_blank"
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-sky-500/50 hover:bg-sky-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="kebijakan privasi perlindungan data medis kerahasiaan rekam kesehatan">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-shield-halved text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-sky-700 transition-colors truncate">Kebijakan Privasi</h3>
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

        <!-- Right: Member Profile Sidebar (4 Cols) -->
        <div class="lg:col-span-4 xl:col-span-4 flex flex-col gap-6">
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 sm:p-5">
                <div class="flex items-center justify-between pb-3.5 mb-3.5 border-b border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        <i class="fas fa-circle-user text-emerald-600 text-sm"></i>
                        <span>Status Anggota</span>
                    </h3>
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Aktif
                    </span>
                </div>

                <div class="space-y-3.5 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium block text-[11px]">Nama Member:</span>
                        <span class="font-bold text-slate-800 text-xs sm:text-sm block mt-0.5">{{ $user->nama }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-medium block text-[11px]">Instansi Terhubung:</span>
                        <span class="font-bold text-slate-800 text-xs sm:text-sm block mt-0.5">
                            {{ $instansi ? $instansi->nama_instansi : 'Akun Pribadi (Belum Terhubung)' }}
                        </span>
                    </div>

                    @if($user->nomor_induk)
                    <div>
                        <span class="text-slate-400 font-medium block text-[11px]">Nomor Induk / NISN:</span>
                        <span class="font-mono text-slate-700 font-bold text-xs sm:text-sm block mt-0.5">{{ $user->nomor_induk }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
