@php
    $instansi = auth()->user()->instansi;
    $memberLabel = $instansi ? $instansi->getRoleLabel('Member') : 'Member';
    $usersMenuLabel = $instansi ? $instansi->label_data_users_menu : 'Data Pengguna';
    $kuotaPengguna = $instansi ? $instansi->getTotalActiveQuota() : 1;
    $totalMember = $data['total_member'] ?? 0;
    $persenKuota = min(100, round(($totalMember / max(1, $kuotaPengguna)) * 100));
    $kuotaBarColor = $persenKuota > 90 ? 'bg-rose-500' : ($persenKuota > 75 ? 'bg-amber-500' : 'bg-emerald-500');
    $latestAppRelease = \App\Models\SoftwareApp::where('status', 'active')
        ->whereIn('platform', ['web'])
        ->orderBy('release_date', 'desc')
        ->first() ?? \App\Models\SoftwareApp::where('status', 'active')->orderBy('release_date', 'desc')->first();
    $portalVersion = $latestAppRelease ? 'v' . $latestAppRelease->version : 'v1.0.0';
@endphp

<div class="space-y-6">
    <!-- Main Two-Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Tool Categories Launcher (8 Cols) -->
        <div class="lg:col-span-8 xl:col-span-8 flex flex-col gap-6">
            <!-- No Results Found Box (Hidden by default) -->
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

            <!-- Group 1: Data Medis & Rekam Klinis -->
            @if(!$instansi || $instansi->isModuleActive('kesehatan') || $instansi->isModuleActive('hemoglobin') || $instansi->isModuleActive('haid_pita') || $instansi->isModuleActive('vaksinasi'))
            <div class="atlas-section cpanel-section bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition-all duration-200">
                <!-- Section Header -->
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 cursor-pointer select-none"
                     onclick="toggleAtlasCategory('cat-medis')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/10 text-teal-700 flex items-center justify-center text-sm">
                            <i class="fas fa-notes-medical"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Data Medis & Rekam Klinis</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200/70 text-slate-700 cpanel-count-badge">Medis</span>
                        <i id="cat-medis-chevron" class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"></i>
                    </div>
                </div>

                <!-- Section Content Grid -->
                <div id="cat-medis-content" class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5">
                    @if(!$instansi || $instansi->isModuleActive('kesehatan'))
                    <a href="{{ route('admin.kesehatan.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-emerald-500/50 hover:bg-emerald-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="antropometri keluhan fisik berkala rekam medis kesehatan">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-notes-medical text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition-colors truncate">Data Kesehatan Fisik</h3>
                        </div>
                    </a>

                    <a href="{{ route('admin.kesehatan.create') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-emerald-500/50 hover:bg-emerald-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="input formulir pemeriksaan fisik baru keluhan member">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-file-circle-plus text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition-colors truncate">Input Pemeriksaan Fisik</h3>
                        </div>
                    </a>
                    @endif

                    @if(!$instansi || $instansi->isModuleActive('hemoglobin'))
                    <a href="{{ route('admin.hb.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-rose-500/50 hover:bg-rose-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="hemoglobin hb darah anemia skrining">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-droplet text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-rose-700 transition-colors truncate">Data Hemoglobin (Hb)</h3>
                        </div>
                    </a>

                    <a href="{{ route('admin.hb.create') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-rose-500/50 hover:bg-rose-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="input skrining hb baru tes darah kadar hemoglobin">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-rose-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-plus text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-rose-700 transition-colors truncate">Input Skrining Hb Baru</h3>
                        </div>
                    </a>
                    @endif

                    @if(!$instansi || $instansi->isModuleActive('haid_pita'))
                    <a href="{{ route('admin.femhealth.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-pink-500/50 hover:bg-pink-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="siklus menstruasi kalender pita haid femhealth wanita">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-calendar-alt text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-pink-700 transition-colors truncate">Siklus & Pita Haid</h3>
                        </div>
                    </a>
                    @endif

                    @if(!$instansi || $instansi->isModuleActive('vaksinasi'))
                    <a href="{{ route('admin.vaccine.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-blue-500/50 hover:bg-blue-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="paspor vaksinasi imunisasi sertifikat dosis booster vaccine">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-syringe text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-blue-700 transition-colors truncate">Paspor Vaksinasi</h3>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Group 2: Kebugaran & Gaya Hidup -->
            @if(!$instansi || $instansi->isModuleActive('olahraga') || $instansi->isModuleActive('vitalitas') || $instansi->isModuleActive('nutrisi') || $instansi->isModuleActive('ergonomi'))
            <div class="atlas-section cpanel-section bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition-all duration-200">
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 cursor-pointer select-none"
                     onclick="toggleAtlasCategory('cat-kebugaran')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-700 flex items-center justify-center text-sm">
                            <i class="fas fa-heart-pulse"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Kebugaran & Gaya Hidup</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200/70 text-slate-700 cpanel-count-badge">Lifestyle</span>
                        <i id="cat-kebugaran-chevron" class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"></i>
                    </div>
                </div>

                <div id="cat-kebugaran-content" class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5">
                    @if(!$instansi || $instansi->isModuleActive('olahraga'))
                    <a href="{{ route('admin.activities.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-emerald-500/50 hover:bg-emerald-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="olahraga aktivitas fisik latihan kalori lari step track">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-running text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition-colors truncate">Aktivitas Olahraga</h3>
                        </div>
                    </a>
                    @endif

                    @if(!$instansi || $instansi->isModuleActive('vitalitas'))
                    <a href="{{ route('admin.vital-logs.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-sky-500/50 hover:bg-sky-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="vitalitas denyut nadi tensi darah tidur hidrasi sleep vitals">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-sky-500 to-teal-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-heart-pulse text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-sky-700 transition-colors truncate">Vitalitas & Tidur</h3>
                        </div>
                    </a>
                    @endif

                    @if(!$instansi || $instansi->isModuleActive('nutrisi'))
                    <a href="{{ route('admin.nutrition.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-amber-500/50 hover:bg-amber-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="nutrisi gizi makanan asupan kalori diet makan sehat">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-apple-whole text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-amber-700 transition-colors truncate">Nutrisi & Gizi</h3>
                        </div>
                    </a>
                    @endif

                    @if(!$instansi || $instansi->isModuleActive('ergonomi'))
                    <a href="{{ route('admin.ergonomics.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-cyan-500/50 hover:bg-cyan-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="ergonomi postur tubuh duduk belajar keluhan fisik muskuloskeletal rula reba">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-chair text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-cyan-700 transition-colors truncate">Ergonomi & Postur</h3>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Group 3: Kesehatan Mental & Konseling -->
            @if(!$instansi || $instansi->isModuleActive('konsultasi') || $instansi->isModuleActive('burnout'))
            <div class="atlas-section cpanel-section bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition-all duration-200">
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 cursor-pointer select-none"
                     onclick="toggleAtlasCategory('cat-mental')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-700 flex items-center justify-center text-sm">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Kesehatan Mental & Konseling</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200/70 text-slate-700 cpanel-count-badge">SafeSpace</span>
                        <i id="cat-mental-chevron" class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"></i>
                    </div>
                </div>

                <div id="cat-mental-content" class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5">
                    @if(!$instansi || $instansi->isModuleActive('konsultasi'))
                    <a href="{{ route('admin.counseling.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-indigo-500/50 hover:bg-indigo-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="konseling safespace temu konselor curhat bimbingan konseling bk psikolog">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-comments text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-indigo-700 transition-colors truncate">Konseling SafeSpace</h3>
                        </div>
                    </a>
                    @endif

                    @if(!$instansi || $instansi->isModuleActive('burnout'))
                    <a href="{{ route('admin.burnout.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-purple-500/50 hover:bg-purple-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="burnout stres skrining beban mental kejenuhan skor stres">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-fuchsia-600 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-brain text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-purple-700 transition-colors truncate">Skrining Burnout</h3>
                        </div>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Group 4: Manajemen Instansi & Administrasi -->
            @if(in_array(auth()->user()->level, ['Admin', 'Admin Instansi']))
            <div class="atlas-section cpanel-section bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition-all duration-200">
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 cursor-pointer select-none"
                     onclick="toggleAtlasCategory('cat-instansi')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-700 flex items-center justify-center text-sm">
                            <i class="fas fa-building-columns"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Manajemen {{ $instansi ? $instansi->tipe_sebutan : 'Institusi' }} & Pengguna</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200/70 text-slate-700 cpanel-count-badge">Admin</span>
                        <i id="cat-instansi-chevron" class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"></i>
                    </div>
                </div>

                <div id="cat-instansi-content" class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5">
                    <a href="{{ route('admin.users.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-emerald-500/50 hover:bg-emerald-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="users data pengguna anggota siswa member kelola akun import excel">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-users text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition-colors truncate">{{ $usersMenuLabel }}</h3>
                        </div>
                    </a>

                    <a href="{{ route('admin.divisi.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-emerald-500/50 hover:bg-emerald-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="divisi kelas rombel rombongan belajar departemen kelompok">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-600 to-cyan-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-sitemap text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition-colors truncate">Data Divisi / Kelas</h3>
                        </div>
                    </a>

                    <a href="{{ route('admin.modules.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-amber-500/50 hover:bg-amber-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="modul izin kelola aktivasi fitur kesehatan visibilitas">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-sliders text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-amber-700 transition-colors truncate">Kelola Modul & Izin</h3>
                        </div>
                    </a>

                    <a href="{{ route('admin.billing.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-emerald-500/50 hover:bg-emerald-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="langganan kuota billing pembayaran invoice perpanjang lisensi">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-600 to-green-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-credit-card text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition-colors truncate">Langganan & Kuota Member</h3>
                        </div>
                    </a>
                </div>
            </div>
            @endif

            <!-- Group 5: Akun, Keamanan & Utilitas -->
            <div class="atlas-section cpanel-section bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden transition-all duration-200">
                <div class="flex items-center justify-between px-5 py-4 bg-slate-50/80 border-b border-slate-200/80 cursor-pointer select-none"
                     onclick="toggleAtlasCategory('cat-utilitas')">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-500/10 text-slate-700 flex items-center justify-center text-sm">
                            <i class="fas fa-gear"></i>
                        </div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Preferensi Akun & Bantuan Sistem</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-200/70 text-slate-700 cpanel-count-badge">Sistem</span>
                        <i id="cat-utilitas-chevron" class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-200"></i>
                    </div>
                </div>

                <div id="cat-utilitas-content" class="p-4 sm:p-5 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3.5">
                    <a href="{{ route('profile.edit') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-emerald-500/50 hover:bg-emerald-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="profil sandi akun ganti password ubah nama email avatar">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-600 to-slate-800 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-user-gear text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-emerald-700 transition-colors truncate">Pengaturan Profil & Sandi</h3>
                        </div>
                    </a>

                    <a href="{{ route('patch_notes.index') }}" 
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-amber-500/50 hover:bg-amber-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="patch notes catatan rilis pembaruan riwayat versi update">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-amber-700 text-white flex items-center justify-center shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                            <i class="fas fa-code-branch text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-bold text-slate-800 group-hover:text-amber-700 transition-colors truncate">Catatan Rilis (Patch Notes)</h3>
                        </div>
                    </a>

                    <a href="{{ route('privacy.policy') }}" target="_blank"
                       class="cpanel-tool-card atlas-tool-card group flex items-center gap-3.5 p-3.5 rounded-xl border border-slate-200/70 hover:border-sky-500/50 hover:bg-sky-50/40 hover:shadow-sm transition-all duration-200"
                       data-keywords="kebijakan privasi perlindungan data medis privacy policy">
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

        <!-- Right: General Information & Server Health Sidebar (4 Cols) -->
        <div class="lg:col-span-4 xl:col-span-4 flex flex-col gap-6">
            <!-- Box 1: Informasi Umum Akun & Instansi -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 sm:p-5">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        <i class="fas fa-circle-info text-emerald-600"></i>
                        <span>Informasi Instansi</span>
                    </h3>
                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Aktif
                    </span>
                </div>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium block text-[11px]">Nama Instansi:</span>
                        <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ $instansi ? $instansi->nama_instansi : 'Aku Sehat Portal' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 font-medium block text-[11px]">Akun Login / Role:</span>
                        <div class="mt-0.5">
                            <span class="text-xs font-bold text-slate-800 block break-words">{{ auth()->user()->nama }}</span>
                            <span class="inline-flex items-center mt-1 text-[11px] font-semibold px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200">
                                {{ auth()->user()->role_label }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 font-medium block text-[11px]">Tipe Organisasi:</span>
                        <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ $instansi ? $instansi->sebutan_spesifik : 'Institusi Pendidikan' }}</span>
                    </div>
                </div>
            </div>

            <!-- Box 2: Kuota & Lisensi Meter -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-4 sm:p-5">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        <i class="fas fa-server text-emerald-600"></i>
                        <span>Status Kuota & Lisensi</span>
                    </h3>
                    <a href="{{ route('admin.billing.index') }}" class="text-xs font-bold text-emerald-700 hover:underline">
                        Kelola
                    </a>
                </div>

                <div class="space-y-3.5">
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-600 mb-1.5">
                            <span>Kuota Pengguna:</span>
                            <span class="font-extrabold text-slate-900">{{ number_format($totalMember) }} / {{ number_format($kuotaPengguna) }}</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full {{ $kuotaBarColor }} transition-all duration-500 rounded-full" style="width: {{ $persenKuota }}%;"></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1">
                            <span>Terpakai {{ $persenKuota }}%</span>
                            <span>Sisa {{ max(0, $kuotaPengguna - $totalMember) }} kuota</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 space-y-2.5">
                        <div class="flex items-center justify-between gap-2.5 py-0.5 text-[11px] sm:text-xs">
                            <span class="text-slate-500 font-medium">Koneksi Database:</span>
                            <span class="font-semibold text-emerald-700 flex items-center gap-1.5 shrink-0">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Online (Optimal)</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-2.5 py-0.5 text-[11px] sm:text-xs">
                            <span class="text-slate-500 font-medium">Keamanan Data:</span>
                            <span class="font-semibold text-slate-800 shrink-0">AES-256 TLS 1.3</span>
                        </div>
                        <div class="flex items-center justify-between gap-2.5 py-0.5 text-[11px] sm:text-xs">
                            <span class="text-slate-500 font-medium">Versi Portal:</span>
                            <span class="font-mono text-[10.5px] sm:text-[11px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded border border-slate-200/60 shrink-0">{{ $portalVersion }}</span>
                        </div>
                    </div>

                    <a href="{{ route('admin.billing.index') }}" 
                       class="btn-press flex items-center justify-center gap-2 w-full px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-all">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah Kuota / Perpanjang</span>
                    </a>
                </div>
            </div>

            <!-- Box 3: Quick Action Launchpad -->
            <div class="bg-gradient-to-br from-emerald-950 to-slate-900 rounded-2xl p-4 sm:p-5 text-white shadow-md border border-emerald-900/40">
                <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-300 flex items-center gap-1.5 mb-3">
                    <i class="fas fa-bolt-lightning text-amber-400"></i>
                    <span>Tindakan Cepat</span>
                </h3>
                <div class="space-y-2">
                    <a href="{{ route('admin.kesehatan.create') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-medium text-white transition-colors">
                        <i class="fas fa-plus text-emerald-400 text-xs w-4 text-center"></i>
                        <span>Pemeriksaan Kesehatan Baru</span>
                    </a>
                    <a href="{{ route('admin.hb.create') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-medium text-white transition-colors">
                        <i class="fas fa-droplet text-rose-400 text-xs w-4 text-center"></i>
                        <span>Catat Hasil Tes Hemoglobin</span>
                    </a>
                    <a href="{{ route('admin.users.create') }}" 
                       class="flex items-center gap-2.5 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-medium text-white transition-colors">
                        <i class="fas fa-user-plus text-teal-400 text-xs w-4 text-center"></i>
                        <span>Tambah Anggota / Member Baru</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>