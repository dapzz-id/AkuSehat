@extends('layouts.app')

@section('title', 'Master Modul & Hak Akses Global')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-600 to-blue-700 text-white flex items-center justify-center shadow-md shadow-cyan-700/20">
                    <i class="fas fa-sliders text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight">Master Modul & Hak Akses Global</h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Kontrol otoritas tertinggi SuperAdmin untuk aktivasi fitur, mode maintenance perbaikan bug, dan akses akun tester.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-cyan-50 text-cyan-800 border border-cyan-200">
                <i class="fas fa-shield-halved text-cyan-600"></i>
                <span>Otoritas SuperAdmin (Master Override)</span>
            </span>
        </div>
    </div>

    <!-- Alert / Banner Kebijakan Global -->
    <div class="p-4 bg-gradient-to-r from-blue-50/80 via-cyan-50/50 to-slate-50 border border-cyan-200/80 rounded-2xl flex items-start gap-3.5 shadow-sm">
        <div class="w-9 h-9 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fas fa-circle-exclamation text-base"></i>
        </div>
        <div class="text-xs text-slate-700 leading-relaxed">
            <strong class="text-cyan-900 block font-bold mb-0.5">Aturan Hirarki Hak Akses:</strong>
            Pengaturan yang ditetapkan di sini akan mengesampingkan (override) kontrol pada tingkat Admin Institusi/Sekolah.
            Jika suatu fitur dinonaktifkan secara global (<strong>Invisible</strong>), Admin Institusi <u>tidak dapat</u> mengaktifkannya.
            Jika disetel ke <strong>Maintenance</strong>, fitur tetap tampil (divisible) dengan banner perbaikan bug, dan jika disetel ke <strong>Tester Only</strong>, fitur hanya terbuka untuk akun yang ditandai sebagai Penguji/Tester.
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200 gap-4" id="moduleTabNav">
        <button type="button" 
                onclick="switchTab('tab-modules')" 
                id="btn-tab-modules" 
                class="pb-3 px-1 text-sm font-bold border-b-2 border-emerald-600 text-emerald-600 transition-colors flex items-center gap-2">
            <i class="fas fa-cubes-stacked"></i>
            <span>Daftar Master Modul ({{ count($catalog) }})</span>
        </button>

        <button type="button" 
                onclick="switchTab('tab-testers')" 
                id="btn-tab-testers" 
                class="pb-3 px-1 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2">
            <i class="fas fa-flask"></i>
            <span>Manajemen Akun Tester ({{ $testers->count() }})</span>
        </button>
    </div>

    <!-- TAB 1: DAFTAR MASTER MODUL -->
    <div id="tab-modules" class="tab-pane space-y-6">
        <form id="modulesForm" action="{{ route('superadmin.modules.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Sticky Action Bar -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-700">Ringkasan Status:</span>
                    <div class="flex flex-wrap items-center gap-2 text-[11px]">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-semibold">
                            <i class="fas fa-circle-check text-[9px]"></i> Aktif Normal
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 font-semibold">
                            <i class="fas fa-wrench text-[9px]"></i> Maintenance / Bug
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-800 font-semibold">
                            <i class="fas fa-eye-slash text-[9px]"></i> Invisible / Nonaktif
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 font-semibold">
                            <i class="fas fa-flask text-[9px]"></i> Tester Only
                        </span>
                    </div>
                </div>

                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-md shadow-emerald-700/20 transition-all">
                    <i class="fas fa-floppy-disk text-sm"></i>
                    <span>Simpan Perubahan Master Modul</span>
                </button>
            </div>

            <!-- Toolbar: Search, Filter Kategori, & Opsi Grid / Pagination -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="flex items-center gap-2 flex-1 max-w-md">
                    <div class="relative w-full">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="moduleSearchInput" onkeyup="applyModuleFilters()" placeholder="Cari nama modul atau kode fitur..."
                               class="w-full pl-9 pr-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600 transition-all">
                    </div>
                </div>

                <div class="flex items-center flex-wrap gap-2 text-xs">
                    <span class="text-slate-400 text-[11px] font-bold uppercase mr-1 hidden sm:inline">Kategori:</span>
                    <button type="button" onclick="setCategoryFilter('all')" id="btn-cat-all" class="cat-filter-btn px-3 py-1.5 rounded-xl font-bold bg-slate-800 text-white shadow-sm transition-all text-xs">Semua ({{ count($catalog) }})</button>
                    <button type="button" onclick="setCategoryFilter('UNIVERSAL')" id="btn-cat-UNIVERSAL" class="cat-filter-btn px-3 py-1.5 rounded-xl font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all text-xs">Universal</button>
                    <button type="button" onclick="setCategoryFilter('SPECIAL')" id="btn-cat-SPECIAL" class="cat-filter-btn px-3 py-1.5 rounded-xl font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all text-xs">Siswa / Khusus</button>
                    
                    <div class="h-5 w-px bg-slate-200 mx-1"></div>
                    
                    <!-- Pagination Toggle -->
                    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-[11px]">
                        <button type="button" onclick="setPageSize(6)" id="btn-page-6" class="page-size-btn px-2.5 py-1 rounded-lg font-bold bg-white text-emerald-700 shadow-sm transition-all">6 / Hlm</button>
                        <button type="button" onclick="setPageSize(999)" id="btn-page-all" class="page-size-btn px-2.5 py-1 rounded-lg font-semibold text-slate-500 hover:text-slate-800 transition-all">Semua</button>
                    </div>
                </div>
            </div>

            <!-- Grid 2 Kolom Master Modules (Responsive md:grid-cols-2) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-5" id="modulesGridContainer">
                @foreach($catalog as $key => $mod)
                    @php
                        $cfg = $configs[$key] ?? null;
                        $status = $cfg ? $cfg->status : 'active';
                        $note = $cfg ? $cfg->maintenance_message : '';
                    @endphp
                    @php
                        $isUniversal = in_array(strtoupper($mod['badge']), ['UNIVERSAL', 'PUBLIC', 'ALL']);
                    @endphp
                    <div class="module-card bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between"
                         id="card_{{ $key }}"
                         data-key="{{ $key }}"
                         data-badge="{{ $isUniversal ? 'UNIVERSAL' : 'SPECIAL' }}"
                         data-name="{{ strtolower($mod['name']) }}"
                         data-desc="{{ strtolower($mod['description']) }}"
                         data-status="{{ $status }}">
                        <div>
                            <!-- Header Modul -->
                            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0" id="icon_box_{{ $key }}">
                                        <i class="{{ $mod['icon'] }} text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-bold text-slate-800">{{ $mod['name'] }}</h3>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600 uppercase">
                                                {{ $mod['badge'] }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] font-mono text-slate-400">Kode: {{ $key }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Deskripsi -->
                            <p class="text-xs text-slate-500 mt-2.5 leading-relaxed min-h-[38px]">
                                {{ $mod['description'] }}
                            </p>

                            <!-- Pilihan 4 Status Global dengan Tombol Interaktif -->
                            <div class="mt-4 pt-3 border-t border-slate-100">
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-2">
                                    STATUS OTORITAS SUPERADMIN:
                                </label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs" id="status_group_{{ $key }}">
                                    <!-- 1. Normal (Active) -->
                                    <button type="button" 
                                            id="btn_status_{{ $key }}_active"
                                            onclick="selectModuleStatus('{{ $key }}', 'active')"
                                            style="cursor: pointer;"
                                            class="status-btn p-2.5 rounded-xl border text-slate-700 font-semibold flex flex-col items-center justify-center text-center gap-1 transition-all select-none hover:border-emerald-300">
                                        <i class="fas fa-circle-check text-sm text-emerald-600"></i>
                                        <span class="text-[11px]">Normal</span>
                                        <span class="text-[9px] text-slate-400 font-normal">Tergantung Instansi</span>
                                    </button>

                                    <!-- 2. Maintenance -->
                                    <button type="button" 
                                            id="btn_status_{{ $key }}_maintenance"
                                            onclick="selectModuleStatus('{{ $key }}', 'maintenance')"
                                            style="cursor: pointer;"
                                            class="status-btn p-2.5 rounded-xl border text-slate-700 font-semibold flex flex-col items-center justify-center text-center gap-1 transition-all select-none hover:border-amber-300">
                                        <i class="fas fa-wrench text-sm text-amber-600"></i>
                                        <span class="text-[11px]">Maintenance</span>
                                        <span class="text-[9px] text-slate-400 font-normal">Tampil banner bug</span>
                                    </button>

                                    <!-- 3. Invisible -->
                                    <button type="button" 
                                            id="btn_status_{{ $key }}_invisible"
                                            onclick="selectModuleStatus('{{ $key }}', 'invisible')"
                                            style="cursor: pointer;"
                                            class="status-btn p-2.5 rounded-xl border text-slate-700 font-semibold flex flex-col items-center justify-center text-center gap-1 transition-all select-none hover:border-slate-400">
                                        <i class="fas fa-eye-slash text-sm text-slate-600"></i>
                                        <span class="text-[11px]">Invisible</span>
                                        <span class="text-[9px] text-slate-400 font-normal">Terkunci & Sembunyi</span>
                                    </button>

                                    <!-- 4. Tester Only -->
                                    <button type="button" 
                                            id="btn_status_{{ $key }}_tester_only"
                                            onclick="selectModuleStatus('{{ $key }}', 'tester_only')"
                                            style="cursor: pointer;"
                                            class="status-btn p-2.5 rounded-xl border text-slate-700 font-semibold flex flex-col items-center justify-center text-center gap-1 transition-all select-none hover:border-purple-300">
                                        <i class="fas fa-flask text-sm text-purple-600"></i>
                                        <span class="text-[11px]">Tester Only</span>
                                        <span class="text-[9px] text-slate-400 font-normal">Khusus Akun Tester</span>
                                    </button>

                                    <!-- Hidden Input for Form Submission -->
                                    <input type="hidden" name="modules[{{ $key }}][status]" id="input_status_{{ $key }}" value="{{ $status }}">
                                </div>
                            </div>

                            <!-- Input Catatan Maintenance (hanya jika mode maintenance/perbaikan) -->
                            <div id="maintenance_note_container_{{ $key }}" class="mt-3 {{ $status === 'maintenance' ? '' : 'hidden' }}">
                                <label for="maintenance_note_{{ $key }}" class="block text-[11px] font-bold text-amber-800 uppercase tracking-wider mb-1">
                                    <i class="fas fa-triangle-exclamation text-amber-600 mr-1"></i>
                                    Pesan Pemeliharaan / Perbaikan Teknis:
                                </label>
                                <textarea id="maintenance_note_{{ $key }}" 
                                          name="modules[{{ $key }}][maintenance_message]" 
                                          rows="2" 
                                          placeholder="Contoh: Modul ini sedang dalam masa pengujian patch perbaikan bug v2.4 dan akan segera aktif kembali."
                                          class="w-full px-3 py-2 text-xs bg-amber-50/50 border border-amber-200 rounded-xl text-slate-800 focus:bg-white focus:outline-none focus:border-amber-500">{{ $note }}</textarea>
                            </div>
                        </div>

                        <!-- Footer Status Note -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                            <span>Status Terakhir: <strong id="badge_status_{{ $key }}" class="uppercase font-bold text-slate-700">{{ $status }}</strong></span>
                            @if($cfg && $cfg->updated_at)
                                <span>Diperbarui: {{ $cfg->updated_at->diffForHumans() }}</span>
                            @else
                                <span>Nilai Default</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Client Pagination Navigation -->
            <div id="paginationNav" class="bg-white rounded-2xl border border-slate-200/80 p-3.5 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <span id="paginationInfo" class="text-slate-500 font-medium">Menampilkan 1-6 dari 10 modul</span>
                <div class="flex items-center gap-1.5" id="paginationButtons">
                    <!-- dynamically rendered buttons -->
                </div>
            </div>

            <!-- Bottom Save Button -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm flex justify-end">
                <button type="button" onclick="confirmSaveModules()" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 shadow-md shadow-emerald-700/20 transition-all">
                    <i class="fas fa-floppy-disk text-sm"></i>
                    <span>Simpan Seluruh Pengaturan Modul</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 2: MANAJEMEN AKUN TESTER -->
    <div id="tab-testers" class="tab-pane hidden space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
            <!-- Header Tab Tester -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-user-check text-purple-600"></i>
                        <span>Daftar Akun Penguji Resmi (Tester)</span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800" id="activeTesterBadgeCount">{{ $testers->count() }}</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Akun yang memiliki hak akses eksklusif untuk mencoba modul yang berada dalam status <strong>Tester Only</strong>.
                    </p>
                </div>
                <div class="relative w-full sm:w-72">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="activeTesterSearchInput" onkeyup="filterActiveTesterTable()"
                           placeholder="Filter di tabel tester aktif..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-purple-600 transition-all">
                </div>
            </div>

            <!-- Tester Users Table -->
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-4">Nama Pengguna</th>
                            <th class="py-3 px-4">Identitas & Akun</th>
                            <th class="py-3 px-4">Peran (Role)</th>
                            <th class="py-3 px-4">Asal Institusi</th>
                            <th class="py-3 px-4 text-center">Status Tester</th>
                            <th class="py-3 px-4 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs" id="activeTesterTableBody">
                        @forelse($testers as $tester)
                            <tr class="active-tester-row hover:bg-slate-50/60 transition-colors">
                                <td class="py-3 px-4 font-bold text-slate-800">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            {{ strtoupper(substr($tester->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $tester->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-600">
                                    <div>{{ '@' . $tester->username }}</div>
                                    @if($tester->email)
                                        <div class="text-[11px] text-slate-400">{{ $tester->email }}</div>
                                    @endif
                                    @if($tester->nomor_induk)
                                        <div class="text-[10px] text-blue-600 font-sans mt-0.5">NIS/NIP: {{ $tester->nomor_induk }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $tester->level }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ $tester->instansi->nama_instansi ?? 'SuperAdmin Ekosistem' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                                        <i class="fas fa-flask text-[9px]"></i> Aktif Tester
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('superadmin.modules.tester.toggle', $tester->id) }}" method="POST" id="form-revoke-tester-{{ $tester->id }}">
                                        @csrf
                                        <button type="button" onclick="confirmRevokeTester('{{ addslashes($tester->nama ?? $tester->name) }}', 'form-revoke-tester-{{ $tester->id }}')" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition-colors">
                                            <i class="fas fa-user-xmark text-xs"></i>
                                            <span>Cabut Tester</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyActiveTestersRow">
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    <p class="font-semibold text-slate-600">Belum ada akun pengguna yang ditandai sebagai Tester.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Tandai pengguna dari daftar di bawah untuk memberikan akses prarilis.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Section Add New Tester with Search -->
            <div class="mt-8 pt-6 border-t border-slate-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                            <i class="fas fa-user-plus text-emerald-600"></i>
                            <span>Tetapkan Akun Tester Baru</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Cari dan pilih akun pengguna untuk dijadikan penguji (tester). Pengguna yang dijadikan tester dapat mengakses modul berstatus <em>Tester Only</em> pada website maupun mobile app.
                        </p>
                    </div>
                </div>

                <!-- Search Input Bar -->
                <div class="bg-slate-50/80 border border-slate-200 rounded-2xl p-3.5 mb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="relative flex-1">
                        <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="candidateTesterSearchInput" onkeyup="filterTesterCandidates()"
                               placeholder="Cari berdasarkan nama, username (@...), email, nomor induk (NIS/NIP), atau asal institusi..."
                               class="w-full pl-9 pr-3.5 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-purple-600 focus:ring-2 focus:ring-purple-500/10 transition-all">
                    </div>
                    <span id="candidateCountBadge" class="text-xs font-semibold text-slate-500 whitespace-nowrap">
                        Menampilkan {{ $recentUsers->count() }} akun pengguna
                    </span>
                </div>

                <!-- User Candidate Scrollable List -->
                <div class="max-h-80 overflow-y-auto border border-slate-200 rounded-xl divide-y divide-slate-100" id="candidateListContainer">
                    @forelse($recentUsers as $usr)
                        @php
                            $instansiName = $usr->instansi->nama_instansi ?? 'Ekosistem Pusat';
                            $searchKeywords = strtolower(implode(' ', array_filter([
                                $usr->name,
                                $usr->username,
                                $usr->email,
                                $usr->nomor_induk,
                                $usr->level,
                                $instansiName
                            ])));
                        @endphp
                        <div class="candidate-user-row p-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 hover:bg-slate-50/80 transition-colors text-xs"
                             data-search="{{ $searchKeywords }}">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($usr->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-bold text-slate-800 text-xs">{{ $usr->name }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-700">
                                            {{ $usr->level }}
                                        </span>
                                        @if($usr->nomor_induk)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-mono bg-blue-50 text-blue-700 border border-blue-200">
                                                ID/NIS: {{ $usr->nomor_induk }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-2 flex-wrap font-mono">
                                        <span>{{ '@' . $usr->username }}</span>
                                        @if($usr->email)
                                            <span>&bull; {{ $usr->email }}</span>
                                        @endif
                                        <span class="text-slate-400 font-sans">&bull; <i class="fas fa-building text-[10px] mr-0.5"></i>{{ $instansiName }}</span>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('superadmin.modules.tester.toggle', $usr->id) }}" method="POST" id="form-toggle-tester-{{ $usr->id }}" class="flex-shrink-0">
                                @csrf
                                @if($usr->is_tester)
                                    <button type="button" onclick="confirmRevokeTester('{{ addslashes($usr->nama ?? $usr->name) }}', 'form-toggle-tester-{{ $usr->id }}')" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-purple-100 text-purple-800 hover:bg-rose-100 hover:text-rose-800 transition-colors flex items-center gap-1.5">
                                        <i class="fas fa-check"></i>
                                        <span>Tester Aktif</span>
                                    </button>
                                @else
                                    <button type="button" onclick="confirmAssignTester('{{ addslashes($usr->nama ?? $usr->name) }}', 'form-toggle-tester-{{ $usr->id }}')" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-emerald-600 hover:text-white transition-colors flex items-center gap-1.5">
                                        <i class="fas fa-plus"></i>
                                        <span>Jadikan Tester</span>
                                    </button>
                                @endif
                            </form>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-400 text-xs">
                            Tidak ada akun pengguna yang ditemukan.
                        </div>
                    @endforelse
                    <div id="noCandidateFoundNotice" class="hidden p-6 text-center text-slate-400 text-xs">
                        <i class="fas fa-search text-slate-300 text-lg mb-1 block"></i>
                        Tidak ada akun pengguna yang cocok dengan kata kunci pencarian Anda.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab switching
    function switchTab(tabId) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        const activeTab = document.getElementById(tabId);
        if (activeTab) {
            activeTab.classList.remove('hidden');
        }

        const btnModules = document.getElementById('btn-tab-modules');
        const btnTesters = document.getElementById('btn-tab-testers');

        if (tabId === 'tab-modules') {
            btnModules.className = 'pb-3 px-1 text-sm font-bold border-b-2 border-emerald-600 text-emerald-600 transition-colors flex items-center gap-2';
            btnTesters.className = 'pb-3 px-1 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2';
        } else {
            btnTesters.className = 'pb-3 px-1 text-sm font-bold border-b-2 border-purple-600 text-purple-600 transition-colors flex items-center gap-2';
            btnModules.className = 'pb-3 px-1 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-2';
        }
    }

    // Interaktif: Pemilihan Status Modul
    const STATUS_STYLES = {
        active: {
            bg: '#ecfdf5',
            border: '2px solid #10b981',
            color: '#065f46',
            shadow: '0 4px 6px -1px rgba(16, 185, 129, 0.2)',
            label: 'Normal (Aktif)',
            badgeClass: 'text-emerald-700 font-extrabold'
        },
        maintenance: {
            bg: '#fffbeb',
            border: '2px solid #f59e0b',
            color: '#92400e',
            shadow: '0 4px 6px -1px rgba(245, 158, 11, 0.2)',
            label: 'Maintenance (Bug)',
            badgeClass: 'text-amber-700 font-extrabold'
        },
        invisible: {
            bg: '#f1f5f9',
            border: '2px solid #64748b',
            color: '#0f172a',
            shadow: '0 4px 6px -1px rgba(100, 116, 139, 0.2)',
            label: 'Invisible (Nonaktif)',
            badgeClass: 'text-slate-800 font-extrabold'
        },
        tester_only: {
            bg: '#faf5ff',
            border: '2px solid #a855f7',
            color: '#581c87',
            shadow: '0 4px 6px -1px rgba(168, 85, 247, 0.2)',
            label: 'Tester Only',
            badgeClass: 'text-purple-700 font-extrabold'
        }
    };

    const DEFAULT_BUTTON_STYLE = {
        bg: '#f8fafc',
        border: '1px solid #e2e8f0',
        color: '#64748b',
        shadow: 'none'
    };

    function selectModuleStatus(key, status) {
        // 1. Update Hidden Form Input
        const input = document.getElementById('input_status_' + key);
        if (input) {
            input.value = status;
        }

        // 2. Update visual state on all 4 buttons for this module
        const allStatuses = ['active', 'maintenance', 'invisible', 'tester_only'];
        allStatuses.forEach(s => {
            const btn = document.getElementById(`btn_status_${key}_${s}`);
            if (btn) {
                if (s === status) {
                    const cfg = STATUS_STYLES[s];
                    btn.style.backgroundColor = cfg.bg;
                    btn.style.border = cfg.border;
                    btn.style.color = cfg.color;
                    btn.style.boxShadow = cfg.shadow;
                    btn.style.transform = 'translateY(-1px)';
                } else {
                    btn.style.backgroundColor = DEFAULT_BUTTON_STYLE.bg;
                    btn.style.border = DEFAULT_BUTTON_STYLE.border;
                    btn.style.color = DEFAULT_BUTTON_STYLE.color;
                    btn.style.boxShadow = DEFAULT_BUTTON_STYLE.shadow;
                    btn.style.transform = 'none';
                }
            }
        });

        // 3. Update footer status badge
        const badge = document.getElementById('badge_status_' + key);
        if (badge && STATUS_STYLES[status]) {
            badge.innerText = STATUS_STYLES[status].label;
            badge.className = 'uppercase font-bold ' + STATUS_STYLES[status].badgeClass;
        }

        // 4. Update data-status on card for filtering
        const card = document.getElementById('card_' + key);
        if (card) {
            card.setAttribute('data-status', status);
        }

        // 5. Toggle maintenance note container
        const noteContainer = document.getElementById('maintenance_note_container_' + key);
        if (noteContainer) {
            if (status === 'maintenance') {
                noteContainer.classList.remove('hidden');
                const textarea = document.getElementById('maintenance_note_' + key);
                if (textarea) textarea.focus();
            } else {
                noteContainer.classList.add('hidden');
            }
        }
    }

    // Inisialisasi awal warna tombol saat dokumen selesai dimuat
    function initAllModuleButtons() {
        document.querySelectorAll('input[id^="input_status_"]').forEach(input => {
            const key = input.id.replace('input_status_', '');
            const currentStatus = input.value || 'active';
            selectModuleStatus(key, currentStatus);
        });
    }

    // ==========================================
    // FILTER & PAGINATION ENGINE
    // ==========================================
    let currentCategoryFilter = 'all';
    let currentPageSize = 6;
    let currentPage = 1;
    let filteredCards = [];

    function setCategoryFilter(cat) {
        currentCategoryFilter = cat;
        document.querySelectorAll('.cat-filter-btn').forEach(b => {
            b.className = 'cat-filter-btn px-3 py-1.5 rounded-xl font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all text-xs';
        });
        const activeBtn = document.getElementById('btn-cat-' + cat);
        if (activeBtn) {
            activeBtn.className = 'cat-filter-btn px-3 py-1.5 rounded-xl font-bold bg-slate-800 text-white shadow-sm transition-all text-xs';
        }
        currentPage = 1;
        applyModuleFilters();
    }

    function setPageSize(size) {
        currentPageSize = size;
        const btn6 = document.getElementById('btn-page-6');
        const btnAll = document.getElementById('btn-page-all');
        if (size === 6) {
            btn6.className = 'page-size-btn px-2.5 py-1 rounded-lg font-bold bg-white text-emerald-700 shadow-sm transition-all';
            btnAll.className = 'page-size-btn px-2.5 py-1 rounded-lg font-semibold text-slate-500 hover:text-slate-800 transition-all';
        } else {
            btnAll.className = 'page-size-btn px-2.5 py-1 rounded-lg font-bold bg-white text-emerald-700 shadow-sm transition-all';
            btn6.className = 'page-size-btn px-2.5 py-1 rounded-lg font-semibold text-slate-500 hover:text-slate-800 transition-all';
        }
        currentPage = 1;
        applyModuleFilters();
    }

    function applyModuleFilters() {
        const query = (document.getElementById('moduleSearchInput')?.value || '').toLowerCase().trim();
        const allCards = Array.from(document.querySelectorAll('.module-card'));

        filteredCards = allCards.filter(card => {
            const name = card.getAttribute('data-name') || '';
            const desc = card.getAttribute('data-desc') || '';
            const key = card.getAttribute('data-key') || '';
            const badge = card.getAttribute('data-badge') || '';

            const matchQuery = !query || name.includes(query) || desc.includes(query) || key.includes(query);
            const matchCat = currentCategoryFilter === 'all' || badge === currentCategoryFilter;

            return matchQuery && matchCat;
        });

        currentPage = 1;
        renderPagination();
    }

    function renderPagination() {
        // Sembunyikan SEMUA modul terlebih dahulu agar kartu halaman sebelumnya tidak tertumpuk
        document.querySelectorAll('.module-card').forEach(c => c.classList.add('hidden'));

        const total = filteredCards.length;
        const totalPages = Math.ceil(total / currentPageSize) || 1;
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIdx = (currentPage - 1) * currentPageSize;
        const endIdx = Math.min(startIdx + currentPageSize, total);

        // Tampilkan hanya modul pada rentang halaman aktif
        filteredCards.slice(startIdx, endIdx).forEach(c => c.classList.remove('hidden'));

        // Update info text
        const infoEl = document.getElementById('paginationInfo');
        if (infoEl) {
            if (total === 0) {
                infoEl.innerText = 'Tidak ada modul yang cocok dengan pencarian.';
            } else {
                infoEl.innerText = `Menampilkan ${startIdx + 1}–${endIdx} dari total ${total} modul (Halaman ${currentPage} dari ${totalPages})`;
            }
        }

        // Render buttons
        const btnContainer = document.getElementById('paginationButtons');
        if (btnContainer) {
            btnContainer.innerHTML = '';
            if (totalPages > 1 && currentPageSize < 999) {
                for (let p = 1; p <= totalPages; p++) {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.innerText = `Halaman ${p}`;
                    btn.className = (p === currentPage)
                        ? 'px-3 py-1.5 rounded-xl font-bold bg-emerald-600 text-white shadow-sm text-xs'
                        : 'px-3 py-1.5 rounded-xl font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs transition-colors';
                    btn.onclick = () => {
                        currentPage = p;
                        renderPagination();
                        document.getElementById('modulesGridContainer')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    };
                    btnContainer.appendChild(btn);
                }
            }
        }
    }

    // ==========================================
    // TESTER SEARCH & FILTER ENGINE
    // ==========================================
    function filterTesterCandidates() {
        const query = (document.getElementById('candidateTesterSearchInput')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.candidate-user-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const searchData = (row.getAttribute('data-search') || '').toLowerCase();
            if (!query || searchData.includes(query)) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });

        const badge = document.getElementById('candidateCountBadge');
        if (badge) {
            badge.innerText = `Menampilkan ${visibleCount} akun pengguna`;
        }

        const notice = document.getElementById('noCandidateFoundNotice');
        if (notice) {
            notice.classList.toggle('hidden', visibleCount > 0);
        }
    }

    function filterActiveTesterTable() {
        const query = (document.getElementById('activeTesterSearchInput')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('#activeTesterTableBody tr.active-tester-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (!query || text.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const badge = document.getElementById('activeTesterBadgeCount');
        if (badge) {
            badge.innerText = visibleCount;
        }
    }

    // ==========================================
    // SWEETALERT CONFIRMATION POPUPS (IDENTICAL TO LOGOUT)
    // ==========================================
    function confirmSaveModules() {
        if (typeof Swal === 'undefined') {
            if (confirm('Apakah Anda yakin ingin menyimpan dan menerapkan seluruh status master modul ke sistem?')) {
                document.getElementById('modulesForm')?.submit();
            }
            return;
        }

        Swal.fire({
            title: 'Simpan Pengaturan Modul?',
            html: 'Apakah Anda yakin ingin menyimpan seluruh konfigurasi status master modul dan tester ke sistem?<br><span class="text-xs text-slate-500 mt-1 block">Modul yang berstatus <em>Tester Only</em> hanya dapat diakses oleh akun penguji resmi.</span>',
            icon: 'question',
            iconColor: '#059669',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-floppy-disk mr-1.5"></i> Ya, Simpan Pengaturan',
            cancelButtonText: '<i class="fas fa-times mr-1.5"></i> Batal',
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                popup: 'swal2-akusehat-popup',
                title: 'swal2-akusehat-title',
                htmlContainer: 'swal2-akusehat-html',
                confirmButton: 'swal2-akusehat-btn-emerald',
                cancelButton: 'swal2-akusehat-btn-cancel',
                actions: 'swal2-akusehat-actions'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('modulesForm')?.submit();
            }
        });
    }

    function confirmRevokeTester(userName, formId) {
        if (typeof Swal === 'undefined') {
            if (confirm(`Cabut hak akses akun tester dari ${userName}?`)) {
                document.getElementById(formId)?.submit();
            }
            return;
        }

        Swal.fire({
            title: 'Cabut Hak Akses Tester?',
            html: `Apakah Anda yakin ingin mencabut hak akses Tester dari akun <strong>${userName}</strong>?<br><span class="text-xs text-rose-600 font-semibold mt-1 block"><i class="fas fa-triangle-exclamation mr-1"></i> Pengguna tidak akan dapat lagi mencoba modul yang berstatus <em>Tester Only</em>.</span>`,
            icon: 'warning',
            iconColor: '#e11d48',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-user-xmark mr-1.5"></i> Ya, Cabut Tester',
            cancelButtonText: '<i class="fas fa-times mr-1.5"></i> Batal',
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                popup: 'swal2-akusehat-popup',
                title: 'swal2-akusehat-title',
                htmlContainer: 'swal2-akusehat-html',
                confirmButton: 'swal2-akusehat-btn-danger',
                cancelButton: 'swal2-akusehat-btn-cancel',
                actions: 'swal2-akusehat-actions'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById(formId);
                if (form) form.submit();
            }
        });
    }

    function confirmAssignTester(userName, formId) {
        if (typeof Swal === 'undefined') {
            if (confirm(`Tetapkan ${userName} sebagai Akun Tester?`)) {
                document.getElementById(formId)?.submit();
            }
            return;
        }

        Swal.fire({
            title: 'Tetapkan Sebagai Akun Tester?',
            html: `Jadikan akun <strong>${userName}</strong> sebagai penguji resmi (Tester)?<br><span class="text-xs text-purple-700 font-semibold mt-1 block"><i class="fas fa-flask mr-1"></i> Pengguna akan mendapatkan hak akses eksklusif untuk mencoba modul berstatus <em>Tester Only</em> pada website maupun mobile app.</span>`,
            icon: 'question',
            iconColor: '#9333ea',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-flask mr-1.5"></i> Ya, Jadikan Tester',
            cancelButtonText: '<i class="fas fa-times mr-1.5"></i> Batal',
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                popup: 'swal2-akusehat-popup',
                title: 'swal2-akusehat-title',
                htmlContainer: 'swal2-akusehat-html',
                confirmButton: 'swal2-akusehat-btn-purple',
                cancelButton: 'swal2-akusehat-btn-cancel',
                actions: 'swal2-akusehat-actions'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById(formId);
                if (form) form.submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initAllModuleButtons();
        applyModuleFilters();

        // Cek URL Hash untuk navigasi otomatis ke tab tester
        if (window.location.hash === '#tab-testers') {
            switchTab('tab-testers');
        }
    });
</script>
@endpush
