@php
    $currentInstansi = $currentInstansi ?? (auth()->check() ? auth()->user()->instansi : null);
@endphp
@extends('layouts.app')
@section('title', $currentInstansi ? $currentInstansi->label_data_users_menu : 'Data Users')
@section('content')
    @php
        $isPendidikan = $currentInstansi ? $currentInstansi->isInstitusiPendidikan() : true;
        $tabActionInfo = $currentInstansi ? $currentInstansi->getContextualActionInfo($activeTab) : [
            'btn_label' => 'Keluarkan dari Divisi',
            'btn_title' => 'Keluarkan dari Divisi',
            'icon' => 'fa-user-minus',
            'status_label' => 'Tanpa Divisi',
            'badge_label' => 'Tanpa Divisi / Arsip',
            'modal_title' => 'Keluarkan dari Divisi',
            'modal_desc' => 'Pengguna akan dikeluarkan dari divisi aktif.',
            'is_alumni' => false,
        ];
        $labelAlumni = $currentInstansi ? $currentInstansi->label_alumni : 'Alumni';
        $labelBadgeAlumni = $currentInstansi ? $currentInstansi->label_badge_alumni : 'Alumni / Arsip';
        $labelTombolKeluarkan = $tabActionInfo['btn_label'];
        $iconAlumni = $tabActionInfo['icon'];
    @endphp
    <div class="space-y-4 md:space-y-6">
        <!-- Header -->
        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-100">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-users text-[#1b5e20] text-lg md:text-xl"></i>
                    </div>

                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-[#1a472a]">{{ $currentInstansi ? $currentInstansi->label_data_users_menu : 'Data Users' }}</h2>
                        <p class="text-xs md:text-sm text-[#388e3c]">{{ $currentInstansi ? $currentInstansi->subtitle_data_users : 'Manajemen data pengguna, divisi, dan riwayat kesehatan' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto flex-wrap sm:flex-nowrap">
                    @if(auth()->user()->canAddUser())
                        @if ($divisi->isEmpty())
                            <button type="button" onclick="alert('Instansi Anda belum memiliki divisi. Silakan buat divisi terlebih dahulu sebelum mengimpor akun.'); window.location.href='{{ route('admin.divisi.index') }}';"
                                class="flex-1 sm:flex-none bg-blue-500 hover:bg-blue-600 text-white px-3.5 py-2 rounded-lg text-xs md:text-sm font-semibold transition inline-flex items-center justify-center gap-1.5 shadow-sm" title="Wajib buat divisi terlebih dahulu">
                                <i class="fas fa-file-import text-xs md:text-sm"></i>
                                <span>Import</span>
                            </button>
                        @else
                            <button onclick="openImportModal()"
                                class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-2 rounded-lg text-xs md:text-sm font-semibold transition inline-flex items-center justify-center gap-1.5 shadow-sm">
                                <i class="fas fa-file-import text-xs md:text-sm"></i>
                                <span>Import</span>
                            </button>
                        @endif
                    @endif

                    <button onclick="openExportModal('excel')"
                        class="flex-1 sm:flex-none bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 px-3.5 py-2 rounded-lg text-xs md:text-sm font-semibold transition inline-flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fas fa-file-excel text-emerald-600 text-xs md:text-sm"></i>
                        <span>Export Excel</span>
                    </button>

                    @if(auth()->user()->canAddUser())
                        @if ($divisi->isEmpty())
                            <a href="{{ route('admin.divisi.index') }}" onclick="return confirm('Instansi Anda belum memiliki divisi. Anda wajib membuat divisi terlebih dahulu sebelum membuat akun baru. Buka halaman Data Divisi?');"
                                class="flex-1 sm:flex-none bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition inline-flex items-center justify-center gap-1.5 shadow-sm" title="Wajib buat divisi terlebih dahulu">
                                <i class="fas fa-plus text-xs md:text-sm"></i>
                                <span>+ Tambah Divisi Dulu</span>
                            </a>
                        @else
                            <a href="{{ route('admin.users.create') }}"
                                class="flex-1 sm:flex-none bg-[#2e7d32] hover:bg-[#1b5e20] text-white px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition inline-flex items-center justify-center gap-1.5 shadow-sm shadow-emerald-700/20">
                                <i class="fas fa-plus text-xs md:text-sm"></i>
                                <span>Tambah User</span>
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab Navigasi Kategori Pengguna -->
        <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-100 flex flex-wrap items-center gap-2">
            @php
                $katOpts = $kategoriOptions ?? \App\Models\Divisi::getKategoriOptions($currentInstansi?->jenis_instansi);
            @endphp

            @if ($isPendidikan)
                <!-- Tab 1: Siswa -->
                <a href="{{ route('admin.users.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'siswa'])) }}"
                   class="flex items-center gap-2 px-3.5 py-2 md:px-4 md:py-2.5 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 {{ $activeTab === 'siswa' ? 'bg-[#2e7d32] text-white shadow-sm shadow-emerald-700/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <i class="fas {{ $katOpts['member']['icon'] ?? 'fa-user-graduate' }} {{ $activeTab === 'siswa' ? 'text-white' : 'text-emerald-600' }}"></i>
                    <span>{{ $katOpts['member']['badge'] ?? 'Siswa / Siswi' }}</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $activeTab === 'siswa' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                        {{ $counts['siswa'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab 2: Guru & Tendik -->
                <a href="{{ route('admin.users.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'guru'])) }}"
                   class="flex items-center gap-2 px-3.5 py-2 md:px-4 md:py-2.5 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 {{ $activeTab === 'guru' ? 'bg-[#2e7d32] text-white shadow-sm shadow-emerald-700/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <i class="fas {{ $katOpts['staf']['icon'] ?? 'fa-chalkboard-teacher' }} {{ $activeTab === 'guru' ? 'text-white' : 'text-blue-600' }}"></i>
                    <span>{{ $katOpts['staf']['badge'] ?? 'Dewan Guru & Tendik' }}</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $activeTab === 'guru' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                        {{ $counts['guru'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab 3: Pimpinan / Yayasan -->
                <a href="{{ route('admin.users.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'pimpinan'])) }}"
                   class="flex items-center gap-2 px-3.5 py-2 md:px-4 md:py-2.5 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 {{ $activeTab === 'pimpinan' ? 'bg-[#2e7d32] text-white shadow-sm shadow-emerald-700/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <i class="fas {{ $katOpts['pimpinan']['icon'] ?? 'fa-school' }} {{ $activeTab === 'pimpinan' ? 'text-white' : 'text-purple-600' }}"></i>
                    <span>{{ $katOpts['pimpinan']['badge'] ?? 'Pimpinan / Yayasan' }}</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $activeTab === 'pimpinan' ? 'bg-white/20 text-white' : 'bg-purple-50 text-purple-800 border border-purple-200' }}">
                        {{ $counts['pimpinan'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab 4: Petugas UKS / Tim Medis -->
                <a href="{{ route('admin.users.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'petugas'])) }}"
                   class="flex items-center gap-2 px-3.5 py-2 md:px-4 md:py-2.5 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 {{ $activeTab === 'petugas' ? 'bg-[#2e7d32] text-white shadow-sm shadow-emerald-700/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <i class="fas fa-shield-halved {{ $activeTab === 'petugas' ? 'text-white' : 'text-amber-600' }}"></i>
                    <span>{{ $currentInstansi ? $currentInstansi->label_petugas_tab : 'Petugas & Pengelola UKS' }}</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $activeTab === 'petugas' ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                        {{ $counts['petugas'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab 5: Semua Warga / Sivitas -->
                <a href="{{ route('admin.users.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'all'])) }}"
                   class="flex items-center gap-2 px-3.5 py-2 md:px-4 md:py-2.5 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 {{ $activeTab === 'all' ? 'bg-[#2e7d32] text-white shadow-sm shadow-emerald-700/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <i class="fas fa-users {{ $activeTab === 'all' ? 'text-white' : 'text-slate-600' }}"></i>
                    <span>{{ $currentInstansi ? $currentInstansi->label_all_users_tab : 'Semua Warga Sekolah' }}</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                        {{ $counts['all'] ?? 0 }}
                    </span>
                </a>
            @else
                <!-- Tab 1: Karyawan / Mahasiswa / Anggota Reguler -->
                <a href="{{ route('admin.users.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'member'])) }}"
                   class="flex items-center gap-2 px-3.5 py-2 md:px-4 md:py-2.5 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 {{ $activeTab === 'member' ? 'bg-[#2e7d32] text-white shadow-sm shadow-emerald-700/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <i class="fas {{ $katOpts['member']['icon'] ?? 'fa-user-tie' }} {{ $activeTab === 'member' ? 'text-white' : 'text-emerald-600' }}"></i>
                    <span>{{ $katOpts['member']['badge'] ?? 'Karyawan / Anggota' }}</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $activeTab === 'member' ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                        {{ $counts['member'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab 2: Manajerial / Staf Khusus / Dosen -->
                <a href="{{ route('admin.users.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'staf'])) }}"
                   class="flex items-center gap-2 px-3.5 py-2 md:px-4 md:py-2.5 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 {{ $activeTab === 'staf' ? 'bg-[#2e7d32] text-white shadow-sm shadow-emerald-700/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <i class="fas {{ $katOpts['staf']['icon'] ?? 'fa-briefcase' }} {{ $activeTab === 'staf' ? 'text-white' : 'text-blue-600' }}"></i>
                    <span>{{ $katOpts['staf']['badge'] ?? 'Manajerial & Staf' }}</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $activeTab === 'staf' ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-800 border border-blue-200' }}">
                        {{ $counts['staf'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab 3: Pimpinan & Direksi / Bos / Rektorat -->
                <a href="{{ route('admin.users.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'pimpinan'])) }}"
                   class="flex items-center gap-2 px-3.5 py-2 md:px-4 md:py-2.5 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 {{ $activeTab === 'pimpinan' ? 'bg-[#2e7d32] text-white shadow-sm shadow-emerald-700/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <i class="fas {{ $katOpts['pimpinan']['icon'] ?? 'fa-crown' }} {{ $activeTab === 'pimpinan' ? 'text-white' : 'text-purple-600' }}"></i>
                    <span>{{ $katOpts['pimpinan']['badge'] ?? 'Pimpinan & Direksi' }}</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $activeTab === 'pimpinan' ? 'bg-white/20 text-white' : 'bg-purple-50 text-purple-800 border border-purple-200' }}">
                        {{ $counts['pimpinan'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab 4: Tim Medis / Pengelola HSE -->
                <a href="{{ route('admin.users.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'petugas'])) }}"
                   class="flex items-center gap-2 px-3.5 py-2 md:px-4 md:py-2.5 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 {{ $activeTab === 'petugas' ? 'bg-[#2e7d32] text-white shadow-sm shadow-emerald-700/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <i class="fas fa-shield-halved {{ $activeTab === 'petugas' ? 'text-white' : 'text-amber-600' }}"></i>
                    <span>{{ $currentInstansi ? $currentInstansi->label_petugas_tab : 'Tim Medis & HSE' }}</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $activeTab === 'petugas' ? 'bg-white/20 text-white' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                        {{ $counts['petugas'] ?? 0 }}
                    </span>
                </a>

                <!-- Tab 5: Semua Pengguna -->
                <a href="{{ route('admin.users.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'all'])) }}"
                   class="flex items-center gap-2 px-3.5 py-2 md:px-4 md:py-2.5 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 {{ $activeTab === 'all' ? 'bg-[#2e7d32] text-white shadow-sm shadow-emerald-700/20' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                    <i class="fas fa-users {{ $activeTab === 'all' ? 'text-white' : 'text-slate-600' }}"></i>
                    <span>{{ $currentInstansi ? $currentInstansi->label_all_users_tab : 'Semua Pengguna' }}</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[11px] font-bold {{ $activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                        {{ $counts['all'] ?? 0 }}
                    </span>
                </a>
            @endif
        </div>

        <!-- Filter & Aksi Massal Section -->
        <div class="bg-white p-4 md:p-5 rounded-xl shadow-sm border border-gray-100 space-y-3.5">
            <!-- Row 1: Search and Filter Form -->
            <form id="searchForm" action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full">
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                <div class="relative flex-1 min-w-[200px]">
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Cari nama pengguna, username, nomor induk..."
                        class="w-full text-xs md:text-sm pl-9 pr-9 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2e7d32] focus:border-[#2e7d32] transition bg-gray-50/50 hover:bg-white focus:bg-white">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search text-xs md:text-sm"></i>
                    </div>
                    <div id="clearSearch"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600"
                        style="display: none;">
                        <i class="fas fa-times text-xs"></i>
                    </div>
                </div>

                @php
                    $dropdownPlaceholder = match($activeTab) {
                        'siswa', 'member' => $isPendidikan ? 'Semua Divisi / ' . $labelAlumni : 'Semua Divisi / Nonaktif',
                        'guru', 'staf', 'pimpinan', 'petugas' => 'Semua Divisi / Tanpa Divisi',
                        default => 'Semua Divisi / Tanpa Divisi',
                    };
                    $alumniOptionLabel = match($activeTab) {
                        'siswa', 'member' => $isPendidikan ? ($currentInstansi?->isUniversitas() ? '🎓 Alumni Kampus' : '🎓 Alumni / Lulusan') : '⏸️ Non-Aktif / Arsip',
                        'pimpinan' => '🏛️ Tanpa Penugasan Divisi',
                        'guru', 'staf' => '🧑‍🏫 Tanpa Penugasan Divisi',
                        'petugas' => '🛡️ Tanpa Penugasan Divisi',
                        default => ($isPendidikan ? '🎓' : '⏸️') . ' ' . $tabActionInfo['badge_label'],
                    };
                @endphp
                <div class="w-full sm:w-64 flex-shrink-0">
                    <select name="divisi" onchange="this.form.submit()"
                        class="w-full text-xs md:text-sm py-2 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2e7d32] bg-gray-50/50 hover:bg-white focus:bg-white text-gray-700 font-medium">
                        <option value="">{{ $dropdownPlaceholder }}</option>
                        <option value="alumni" {{ request('divisi') === 'alumni' || request('divisi') === 'none' ? 'selected' : '' }}>
                            {{ $alumniOptionLabel }}
                        </option>
                        @foreach ($divisi as $d)
                            <option value="{{ $d->id }}" {{ request('divisi') == $d->id ? 'selected' : '' }}>
                                [{{ $d->kategori_meta['badge'] ?? 'Divisi' }}] {{ $d->divisi_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <button type="submit"
                        class="flex-1 sm:flex-none bg-[#2e7d32] hover:bg-[#1b5e20] text-white px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition inline-flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fas fa-search text-xs"></i>
                        <span>Cari</span>
                    </button>
                    <a href="{{ route('admin.users.index', ['tab' => $activeTab]) }}"
                        class="flex-1 sm:flex-none bg-gray-100 hover:bg-gray-200 text-gray-700 px-3.5 py-2 rounded-lg text-xs md:text-sm font-medium transition inline-flex items-center justify-center gap-1" title="Reset pencarian di tab ini">
                        <i class="fas fa-rotate-left text-xs"></i>
                        <span>Reset</span>
                    </a>
                </div>
            </form>

            <!-- Row 2: Contextual Bulk Actions Bar -->
            <div id="bulkActionBar" class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2.5">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 flex items-center gap-1.5">
                        <i class="fas fa-check-double text-emerald-600"></i>
                        <span>Aksi Massal:</span>
                    </span>
                    <span id="selectedCountText" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200 transition-colors">
                        0 pengguna dipilih
                    </span>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto flex-wrap sm:flex-nowrap">
                    <button id="bulkChangeDivisiBtn" type="button" onclick="openChangeDivisiModal()"
                        class="flex-1 sm:flex-none bg-[#0288d1] hover:bg-[#01579b] text-white px-3.5 py-2 rounded-lg text-xs md:text-sm font-semibold transition-colors inline-flex items-center justify-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm"
                        disabled>
                        <i class="fas fa-edit text-xs"></i>
                        <span>Ubah Divisi</span>
                    </button>

                    <button id="bulkRemoveDivisiBtn" type="button" onclick="openRemoveDivisiModal()"
                        class="flex-1 sm:flex-none bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-2 rounded-lg text-xs md:text-sm font-semibold transition-colors inline-flex items-center justify-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm"
                        disabled title="{{ $tabActionInfo['btn_title'] }}">
                        <i id="bulkRemoveDivisiBtnIcon" class="fas {{ $tabActionInfo['icon'] }} text-xs"></i>
                        <span id="bulkRemoveDivisiBtnText">{{ $tabActionInfo['btn_label'] }}</span>
                    </button>

                    <button type="submit" form="bulkDeleteForm" id="bulkDeleteBtn"
                        class="flex-1 sm:flex-none bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-2 rounded-lg text-xs md:text-sm font-semibold transition-colors inline-flex items-center justify-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed shadow-sm"
                        disabled>
                        <i class="fas fa-trash text-xs"></i>
                        <span>Hapus Terpilih</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Alert Belum Ada Divisi -->
        @if ($divisi->isEmpty())
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-sm">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div class="flex items-start sm:items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 text-amber-600">
                            <i class="fas fa-exclamation-triangle text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-sm md:text-base font-bold text-amber-900">Perhatian: Instansi Belum Memiliki Divisi</h4>
                            <p class="text-xs md:text-sm text-amber-700">Sebelum membuat akun, pastikan Anda harus membuat divisi terlebih dahulu. Jika belum membuat divisi atau divisi kosong, maka tidak dapat membuat akun.</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.divisi.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs md:text-sm font-medium rounded-md shadow-sm transition whitespace-nowrap">
                        <i class="fas fa-sitemap"></i>
                        <span>+ Buat Divisi Baru</span>
                    </a>
                </div>
            </div>
        @endif

        <!-- Import Errors -->
        @if (session('import_errors'))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 md:p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400 text-sm md:text-base"></i>
                    </div>

                    <div class="ml-2 md:ml-3 flex-1">
                        <h3 class="text-xs md:text-sm font-medium text-yellow-800">Error saat import:</h3>
                        <div class="mt-2 text-xs md:text-sm text-yellow-700 max-h-40 overflow-y-auto">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach (session('import_errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6">
            <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex flex-col md:flex-row md:items-center gap-2">
                    <div
                        class="w-8 h-8 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-users text-blue-600 text-sm md:text-xl"></i>
                    </div>

                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-lg md:text-2xl font-semibold text-gray-900">{{ is_array($userStats) ? ($userStats['total'] ?? 0) : ($userStats->total ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex flex-col md:flex-row md:items-center gap-2">
                    <div
                        class="w-8 h-8 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-male text-blue-600 text-sm md:text-xl"></i>
                    </div>

                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Laki-laki</p>
                        <p class="text-lg md:text-2xl font-semibold text-gray-900">
                            {{ is_array($userStats) ? ($userStats['total_laki'] ?? 0) : ($userStats->total_laki ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex flex-col md:flex-row md:items-center gap-2">
                    <div
                        class="w-8 h-8 md:w-12 md:h-12 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-female text-pink-600 text-sm md:text-xl"></i>
                    </div>

                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Perempuan</p>
                        <p class="text-lg md:text-2xl font-semibold text-gray-900">
                            {{ is_array($userStats) ? ($userStats['total_perempuan'] ?? 0) : ($userStats->total_perempuan ?? 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex flex-col md:flex-row md:items-center gap-2">
                    <div
                        class="w-8 h-8 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-heartbeat text-green-600 text-sm md:text-xl"></i>
                    </div>

                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Data Kesehatan</p>
                        <p class="text-lg md:text-2xl font-semibold text-gray-900">
                            {{ is_array($userStats) ? ($userStats['total_kesehatan'] ?? 0) : ($userStats->total_kesehatan ?? 0) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100">
            <form id="bulkDeleteForm" action="{{ route('admin.users.mass_destroy') }}" method="POST"
                onsubmit="return confirm('Yakin ingin menghapus users yang dipilih?')">
                @csrf
                @method('DELETE')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#e8f5e9]">
                            <tr>
                                <th class="w-10 px-2 sm:px-3 md:px-6 py-2 md:py-3 text-center">
                                    <input type="checkbox" id="selectAll"
                                        class="rounded border-gray-300 text-[#2e7d32] focus:ring-[#2e7d32]">
                                </th>

                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                                    Users
                                </th>

                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden sm:table-cell">
                                    Nomor Induk
                                </th>

                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden md:table-cell">
                                    Divisi
                                </th>

                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                                    JK
                                </th>

                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden lg:table-cell">
                                    Data
                                </th>

                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden sm:table-cell">
                                    Status BMI
                                </th>

                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-center text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200" id="usersTable">
                            @forelse($member as $item)
                                @php
                                    $memberTerakhir = $item->kesehatan->sortByDesc('tgl')->first();
                                    $statusClass = $memberTerakhir
                                        ? match ($memberTerakhir->status) {
                                            'Underweight' => 'bg-[#fff9c4] text-[#f57f17]',
                                            'Normal' => 'bg-[#c8e6c9] text-[#1b5e20]',
                                            'Overweight' => 'bg-[#ffe0b2] text-[#ef6c00]',
                                            'Obesitas Level 1' => 'bg-[#ffcdd2] text-[#c62828]',
                                            'Obesitas Level 2' => 'bg-[#f44336] text-white',
                                            'Obesitas Level 3' => 'bg-[#b71c1c] text-white',
                                            default => 'bg-gray-100 text-gray-800',
                                        }
                                        : 'bg-gray-100 text-gray-800';
                                    $userActionInfo = $currentInstansi ? $currentInstansi->getContextualActionInfo($activeTab, $item) : $tabActionInfo;
                                @endphp

                                <tr class="hover:bg-[#f1f8e9] transition-colors duration-150" data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama }}"
                                    data-divisi="{{ $item->divisi ? $item->divisi->divisi_name : $userActionInfo['badge_label'] }}">
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                                            class="rowCheckbox rounded border-gray-300 text-[#2e7d32] focus:ring-[#2e7d32]">
                                    </td>

                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4">
                                        <div class="flex items-center gap-1 sm:gap-2 md:gap-3">
                                            <div
                                                class="w-6 h-6 sm:w-8 md:w-10 sm:h-8 md:h-10 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-user text-gray-600 text-xs sm:text-sm"></i>
                                            </div>

                                            <div class="min-w-0">
                                                <div class="text-xs md:text-sm font-medium text-gray-900 truncate">
                                                    {{ $item->nama }}</div>

                                                <div class="text-xs text-gray-500 truncate">{{ $item->username }}</div>

                                                <div class="text-xs text-gray-500 sm:hidden mt-1.5">
                                                    Nomor Induk: {{ $item->nomor_induk ?? '-' }}
                                                </div>

                                                <div class="text-xs text-gray-500 md:hidden mt-1">
                                                    @if ($item->level !== 'Member')
                                                        <span class="inline-flex items-center gap-1 text-indigo-700 font-semibold bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">
                                                            <i class="fas fa-shield-halved text-[10px]"></i> {{ $item->role_label }}
                                                        </span>
                                                        @if ($item->divisi)
                                                            <span class="text-gray-500 ml-1">({{ $item->divisi->divisi_name }})</span>
                                                        @endif
                                                    @elseif ($item->divisi)
                                                        <span>Divisi: <strong class="text-gray-700">{{ $item->divisi->divisi_name }}</strong></span>
                                                    @else
                                                        <span class="text-amber-700 font-semibold">({{ $labelBadgeAlumni }})</span>
                                                    @endif
                                                </div>

                                                <div class="text-xs text-gray-500 sm:hidden mt-1">
                                                    Status BMI: {{ $memberTerakhir->status ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td
                                        class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-xs md:text-sm text-gray-900 hidden sm:table-cell">
                                        {{ $item->nomor_induk ?? '-' }}
                                    </td>

                                    <td
                                        class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-xs md:text-sm hidden md:table-cell">
                                        @if ($item->level !== 'Member')
                                            <div class="flex flex-col gap-1 items-start">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200" title="{{ $item->role_label }}">
                                                    <i class="fas fa-shield-halved text-indigo-500"></i> {{ $item->role_label }}
                                                </span>
                                                @if ($item->divisi)
                                                    <span class="text-[11px] text-gray-500 ml-1">Divisi: {{ $item->divisi->divisi_name }}</span>
                                                @endif
                                            </div>
                                        @elseif ($item->divisi)
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-medium text-gray-900">{{ $item->divisi->divisi_name }}</span>
                                                @if ($item->divisi->kategori && $item->divisi->kategori !== 'member')
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-{{ $item->divisi->kategori_meta['color'] }}-50 text-{{ $item->divisi->kategori_meta['color'] }}-700 border border-{{ $item->divisi->kategori_meta['color'] }}-200">
                                                        <i class="fas {{ $item->divisi->kategori_meta['icon'] }} text-[9px]"></i>
                                                        <span>{{ $item->divisi->kategori_meta['badge'] }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200" title="{{ $userActionInfo['badge_label'] }}">
                                                <i class="fas {{ $userActionInfo['icon'] }} text-amber-600"></i> {{ $userActionInfo['badge_label'] }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4">
                                        <span
                                            class="px-1 py-0.5 sm:px-1.5 md:px-2 sm:py-0.5 md:py-1 inline-flex text-xs leading-4 md:leading-5 font-semibold rounded-full {{ $item->jk === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                            {{ $item->jk }}
                                        </span>
                                    </td>

                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 hidden lg:table-cell">
                                        <div class="space-y-1">
                                            <span
                                                class="px-1 py-0.5 sm:px-1.5 md:px-2 sm:py-0.5 md:py-1 inline-flex text-xs leading-4 md:leading-5 font-semibold rounded-full {{ $item->kesehatan->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                K: {{ $item->kesehatan->count() }}
                                            </span>

                                            <span
                                                class="px-1 py-0.5 sm:px-1.5 md:px-2 sm:py-0.5 md:py-1 inline-flex text-xs leading-4 md:leading-5 font-semibold rounded-full {{ $item->hb->count() > 0 ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                                HB: {{ $item->hb->count() }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 hidden sm:table-cell">
                                        <span
                                            class="px-1 py-0.5 sm:px-1.5 md:px-2 sm:py-0.5 md:py-1 inline-flex text-xs leading-4 md:leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $memberTerakhir->status ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1 md:gap-2">
                                            @if ($item->id_divisi)
                                                <button type="button" onclick="singleRemoveDivisi('{{ $item->id }}', '{{ addslashes($item->nama) }}', '{{ addslashes($item->divisi ? $item->divisi->divisi_name : '') }}', '{{ addslashes($userActionInfo['modal_title']) }}', '{{ addslashes($userActionInfo['modal_desc']) }}', '{{ $userActionInfo['icon'] }}', '{{ addslashes($userActionInfo['btn_label']) }}')"
                                                    class="text-amber-600 hover:text-amber-800 p-1 transition-colors duration-200"
                                                    title="{{ $userActionInfo['btn_title'] }}">
                                                    <i class="fas {{ $userActionInfo['icon'] }} text-xs sm:text-sm md:text-base"></i>
                                                </button>
                                            @endif

                                            <button type="button" onclick="openUserPermModal({{ json_encode($item) }})"
                                                class="text-indigo-600 hover:text-indigo-800 p-1 transition-colors duration-200"
                                                title="Atur Hak Akses Modul Khusus Pengguna">
                                                <i class="fas fa-sliders-h text-xs sm:text-sm md:text-base"></i>
                                            </button>

                                            <a href="{{ route('admin.users.edit', $item->id) }}"
                                                class="text-[#2e7d32] hover:text-[#1b5e20] p-1 transition-colors duration-200"
                                                title="Edit">
                                                <i class="fas fa-edit text-xs sm:text-sm md:text-base"></i>
                                            </a>

                                            <form action="{{ route('admin.users.destroy', $item->id) }}" method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus users ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-[#d32f2f] hover:text-[#b71c1c] p-1 transition-colors duration-200"
                                                    title="Hapus">
                                                    <i class="fas fa-trash text-xs sm:text-sm md:text-base"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 md:px-6 py-8 text-center">
                                        @if ($activeTab === 'pimpinan')
                                            <div class="flex flex-col items-center justify-center text-gray-500 max-w-md mx-auto py-4">
                                                <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-3 text-2xl shadow-sm">
                                                    <i class="fas {{ $katOpts['pimpinan']['icon'] ?? 'fa-crown' }}"></i>
                                                </div>
                                                <h3 class="text-base font-bold text-gray-900">Belum Ada Data {{ $katOpts['pimpinan']['badge'] ?? 'Pimpinan & Yayasan' }}</h3>
                                                <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                                                    Ingin memantau kesehatan pimpinan, pengurus yayasan, direksi, atau jajaran eksekutif? Anda cukup membuat divisi baru dengan kategori <span class="font-semibold text-purple-700">"{{ $katOpts['pimpinan']['badge'] ?? 'Pimpinan' }}"</span> di menu Data Divisi, lalu tambahkan akun pimpinan ke divisi tersebut.
                                                </p>
                                                @if(auth()->user()->canAddUser())
                                                    <div class="mt-4 flex items-center gap-2">
                                                        <a href="{{ route('admin.divisi.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold shadow-sm transition">
                                                            <i class="fas fa-sitemap"></i>
                                                            <span>+ Buat Divisi {{ $katOpts['pimpinan']['badge'] ?? 'Pimpinan' }} Baru</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif ($activeTab === 'guru' || $activeTab === 'staf')
                                            <div class="flex flex-col items-center justify-center text-gray-500 max-w-md mx-auto py-4">
                                                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-3 text-2xl shadow-sm">
                                                    <i class="fas {{ $katOpts['staf']['icon'] ?? 'fa-chalkboard-teacher' }}"></i>
                                                </div>
                                                <h3 class="text-base font-bold text-gray-900">Belum Ada Data {{ $katOpts['staf']['badge'] ?? 'Pendidik & Staf' }}</h3>
                                                <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                                                    Ingin memantau kesehatan bapak/ibu guru, dosen, atau staf karyawan? Anda cukup membuat divisi baru dengan kategori <span class="font-semibold text-blue-700">"{{ $katOpts['staf']['badge'] ?? 'Pendidik & Staf' }}"</span> di menu Data Divisi, lalu tambahkan akun ke divisi tersebut.
                                                </p>
                                                @if(auth()->user()->canAddUser())
                                                    <div class="mt-4 flex items-center gap-2">
                                                        <a href="{{ route('admin.divisi.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold shadow-sm transition">
                                                            <i class="fas fa-sitemap"></i>
                                                            <span>+ Buat Divisi {{ $katOpts['staf']['badge'] ?? 'Staf' }} Baru</span>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif ($activeTab === 'siswa' || $activeTab === 'member')
                                            <div class="flex flex-col items-center justify-center text-gray-400 py-4">
                                                <div class="w-12 h-12 md:w-16 md:h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mb-2 md:mb-3">
                                                    <i class="fas {{ $katOpts['member']['icon'] ?? 'fa-user-graduate' }} text-xl md:text-2xl"></i>
                                                </div>
                                                <p class="text-sm md:text-base font-medium text-gray-700">Tidak ada data {{ $katOpts['member']['badge'] ?? ($currentInstansi ? $currentInstansi->getRoleLabel('Member') : 'Siswa / Siswi') }}</p>
                                                <p class="text-xs text-gray-500 mt-1">Gunakan tombol Tambah User atau Import untuk memasukkan data {{ strtolower($katOpts['member']['badge'] ?? 'pengguna') }}</p>
                                            </div>
                                        @elseif ($activeTab === 'petugas')
                                            <div class="flex flex-col items-center justify-center text-gray-400 py-4">
                                                <div class="w-12 h-12 md:w-16 md:h-16 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mb-2 md:mb-3">
                                                    <i class="fas fa-shield-halved text-xl md:text-2xl"></i>
                                                </div>
                                                <p class="text-sm md:text-base font-medium text-gray-700">Tidak ada {{ $isPendidikan ? 'Petugas UKS / Pengelola' : 'Pengelola & Tim Medis / HSE' }}</p>
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center justify-center text-gray-400 py-4">
                                                <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-100 rounded-full flex items-center justify-center mb-2 md:mb-3">
                                                    <i class="fas fa-users text-gray-300 text-xl md:text-2xl"></i>
                                                </div>
                                                <p class="text-sm md:text-base font-medium text-gray-700">Tidak ada data pengguna</p>
                                                <p class="text-xs text-gray-500 mt-1">Belum ada pengguna yang terdaftar pada kriteria ini</p>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Custom Pagination -->
            <div
                class="px-4 py-3 sm:px-6 sm:py-4 bg-[#f9fafb] border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-2 sm:gap-0">
                <div>
                    <p class="text-xs sm:text-sm text-gray-700 text-center sm:text-left">
                        Showing <span class="font-medium">{{ $member->firstItem() }}</span>
                        to <span class="font-medium">{{ $member->lastItem() }}</span>
                        of <span class="font-medium">{{ $member->total() }}</span> results
                    </p>
                </div>

                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        {{-- Tombol Previous --}}
                        @if ($member->onFirstPage())
                            <span
                                class="relative inline-flex items-center px-2 py-1 sm:py-2 rounded-l-md border border-gray-300 bg-gray-100 text-xs sm:text-sm font-medium text-gray-500">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @else
                            <a href="{{ $member->previousPageUrl() }}"
                                class="relative inline-flex items-center px-2 py-1 sm:py-2 rounded-l-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif

                        {{-- Nomor Halaman dengan Ellipses --}}
                        @php
                            $currentPage = $member->currentPage();
                            $lastPage = $member->lastPage();
                            $start = max($currentPage - 1, 1); // Kurangi range untuk mobile
                            $end = min($currentPage + 1, $lastPage); // Kurangi untuk mobile
                        @endphp

                        {{-- Halaman pertama --}}
                        @if ($start > 1)
                            <a href="{{ $member->url(1) }}"
                                class="relative inline-flex items-center px-2 sm:px-4 py-1 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium {{ $currentPage == 1 ? 'bg-[#2e7d32] text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                                1
                            </a>
                            @if ($start > 2)
                                <span
                                    class="relative inline-flex items-center px-2 sm:px-4 py-1 sm:py-2 border border-gray-300 bg-gray-100 text-xs sm:text-sm font-medium text-gray-500">…</span>
                            @endif
                        @endif

                        {{-- Halaman di sekitar current --}}
                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $currentPage)
                                <span
                                    class="z-10 bg-[#2e7d32] text-white relative inline-flex items-center px-2 sm:px-4 py-1 sm:py-2 border border-[#2e7d32] text-xs sm:text-sm font-medium">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $member->url($page) }}"
                                    class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-2 sm:px-4 py-1 sm:py-2 border text-xs sm:text-sm font-medium">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        {{-- Halaman terakhir --}}
                        @if ($end < $lastPage)
                            @if ($end < $lastPage - 1)
                                <span
                                    class="relative inline-flex items-center px-2 sm:px-4 py-1 sm:py-2 border border-gray-300 bg-gray-100 text-xs sm:text-sm font-medium text-gray-500">…</span>
                            @endif
                            <a href="{{ $member->url($lastPage) }}"
                                class="relative inline-flex items-center px-2 sm:px-4 py-1 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium {{ $currentPage == $lastPage ? 'bg-[#2e7d32] text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                                {{ $lastPage }}
                            </a>
                        @endif

                        {{-- Tombol Next --}}
                        @if ($member->hasMorePages())
                            <a href="{{ $member->nextPageUrl() }}"
                                class="relative inline-flex items-center px-2 py-1 sm:py-2 rounded-r-md border border-gray-300 bg-white text-xs sm:text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        @else
                            <span
                                class="relative inline-flex items-center px-2 py-1 sm:py-2 rounded-r-md border border-gray-300 bg-gray-100 text-xs sm:text-sm font-medium text-gray-500">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="importModal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4"
        onclick="closeImportModal()">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md sm:max-w-lg" onclick="event.stopPropagation()">
            <div class="p-4 md:p-6">
                <div class="flex items-start gap-3 md:gap-4">
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-import text-blue-600 text-sm md:text-base"></i>
                    </div>

                    <div class="flex-1">
                        <h3 class="text-base md:text-lg font-medium text-gray-900 mb-3 md:mb-4">
                            Import Data Users
                        </h3>

                        <!-- Download Template -->
                        <div class="bg-blue-50 p-3 md:p-4 rounded-md mb-3 md:mb-4">
                            <p class="text-xs md:text-sm text-blue-800 mb-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Download template Excel terlebih dahulu
                            </p>
                            <a href="{{ route('admin.users.template') }}"
                                class="inline-flex items-center px-3 py-1.5 md:py-2 text-xs md:text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-download mr-1.5 md:mr-2 text-xs"></i>
                                Download Template
                            </a>
                        </div>

                        <!-- Upload Form -->
                        <form id="importForm" action="{{ route('admin.users.import') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3 md:mb-4">
                                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">
                                    Upload File Excel
                                </label>
                                <input type="file" name="file" accept=".xlsx,.xls" required
                                    class="block w-full text-xs md:text-sm text-gray-500
                                       file:mr-3 md:file:mr-4 file:py-1.5 md:file:py-2 file:px-3 md:file:px-4
                                       file:rounded-md file:border-0
                                       file:text-xs md:file:text-sm file:font-semibold
                                       file:bg-blue-50 file:text-blue-700
                                       hover:file:bg-blue-100"
                                    onchange="displayFileName(this)">
                                <p class="mt-1 text-xs text-gray-500">Format: .xlsx atau .xls (Max: 2MB)</p>
                                <p id="fileName" class="mt-2 text-xs md:text-sm text-gray-600"></p>
                            </div>

                            <!-- Instructions -->
                            <div class="text-xs text-gray-600">
                                <p class="font-medium mb-1">Panduan:</p>
                                <ul class="list-disc list-inside space-y-0.5">
                                    <li>Pastikan format sesuai template</li>
                                    <li>Nomor Induk dan Username harus unik</li>
                                    <li>Jenis Kelamin: L atau P</li>
                                    <li>Password minimal 6 karakter</li>
                                </ul>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 md:px-6 flex flex-col-reverse sm:flex-row justify-end gap-2">
                <button type="button" onclick="closeImportModal()"
                    class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </button>

                <button type="submit" form="importForm"
                    class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-upload mr-1.5 text-xs"></i>
                    Import
                </button>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div id="exportModal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4"
        onclick="closeExportModal()">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md sm:max-w-lg" onclick="event.stopPropagation()">
            <div class="p-4 md:p-6">
                <div class="flex items-start gap-3 md:gap-4">
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-file-export text-blue-600 text-sm md:text-base"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base md:text-lg font-medium text-gray-900 mb-3 md:mb-4" id="exportModalTitle">
                            Export Data Users
                        </h3>
                        <form id="exportForm" method="GET">
                            <input type="hidden" name="tab" id="exportTabInput" value="{{ $activeTab }}">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Filter
                                        Export</label>
                                    <select name="filter_type" id="filterType"
                                        class="w-full text-xs md:text-sm px-3 py-1.5 md:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#2e7d32]">
                                        <option value="tab">Export Tab Aktif Saja ({{ $activeTab === 'siswa' ? 'Siswa / Siswi' : ($activeTab === 'guru' ? 'Dewan Guru & Tendik' : ($activeTab === 'petugas' ? 'Petugas UKS' : 'Semua Data')) }})</option>
                                        <option value="all">Export Semua Pengguna (Seluruh Tab)</option>
                                        <option value="divisi">Per Divisi</option>
                                        <option value="tanggal">Per Tanggal</option>
                                        <option value="divisi_tanggal">Per Divisi dan Tanggal</option>
                                    </select>
                                </div>
                                <div id="divisiSection" class="hidden">
                                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Pilih
                                        Divisi</label>
                                    <select name="divisi"
                                        class="w-full text-xs md:text-sm px-3 py-1.5 md:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#2e7d32]">
                                        @foreach ($divisi as $d)
                                            <option value="{{ $d->id }}">[{{ $d->kategori_meta['badge'] ?? 'Divisi' }}] {{ $d->divisi_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="tanggalSection" class="hidden space-y-2">
                                    <div>
                                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Dari
                                            Tanggal</label>
                                        <input type="date" name="from"
                                            class="w-full text-xs md:text-sm px-3 py-1.5 md:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#2e7d32]">
                                    </div>
                                    <div>
                                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1">Sampai
                                            Tanggal</label>
                                        <input type="date" name="to"
                                            class="w-full text-xs md:text-sm px-3 py-1.5 md:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#2e7d32]">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 md:px-6 flex flex-col-reverse sm:flex-row justify-end gap-2">
                <button type="button" onclick="closeExportModal()"
                    class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" form="exportForm"
                    class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-download mr-1.5 text-xs"></i>
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Change Divisi Modal -->
    <div id="changeDivisiModal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4"
        onclick="closeChangeDivisiModal()">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md md:max-w-lg lg:max-w-xl"
            onclick="event.stopPropagation()">
            <div class="p-4 md:p-6">
                <div class="flex items-start gap-3 md:gap-4">
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-edit text-blue-600 text-sm md:text-base"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base md:text-lg font-medium text-gray-900 mb-3 md:mb-4">
                            Ubah Divisi Terpilih
                        </h3>
                        <form id="changeDivisiForm" action="{{ route('admin.users.mass_update_divisi') }}"
                            method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3 text-xs text-blue-800 bg-blue-50 p-2.5 rounded-lg border border-blue-200">
                                <i class="fas fa-info-circle mr-1 text-blue-600"></i>
                                Perpindahan divisi antar divisi tidak memengaruhi sisa kuota lisensi instansi.
                            </div>
                            <div class="mb-4 max-h-40 md:max-h-60 overflow-y-auto pr-2">
                                <ul id="selectedUsersList" class="space-y-2"></ul>
                            </div>
                            <div>
                                <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Pilih Divisi
                                    Baru</label>
                                <select name="new_divisi" required
                                    class="w-full text-xs md:text-sm px-3 py-1.5 md:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#2e7d32]">
                                    <option value="">Pilih Divisi</option>
                                    @foreach ($divisi as $d)
                                        <option value="{{ $d->id }}">[{{ $d->kategori_meta['badge'] ?? 'Divisi' }}] {{ $d->divisi_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 md:px-6 flex flex-col-reverse sm:flex-row justify-end gap-2">
                <button type="button" onclick="closeChangeDivisiModal()"
                    class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" form="changeDivisiForm"
                    class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-save mr-1.5 text-xs"></i>
                    Ubah Divisi
                </button>
            </div>
        </div>
    </div>

    <!-- Remove Divisi (Alumni) Modal -->
    <div id="removeDivisiModal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4"
        onclick="closeRemoveDivisiModal()">
        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg md:max-w-xl overflow-hidden"
            onclick="event.stopPropagation()">
            <!-- Header with Amber gradient -->
            <div class="p-4 md:p-6 bg-gradient-to-r from-amber-600 to-amber-700 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                        <i id="removeDivisiModalIcon" class="fas {{ $tabActionInfo['icon'] }} text-lg"></i>
                    </div>
                    <div>
                        <h3 id="removeDivisiModalTitle" class="text-base md:text-lg font-bold">{{ $tabActionInfo['modal_title'] }}</h3>
                        <p id="removeDivisiModalSubtitle" class="text-xs text-amber-100">{{ $tabActionInfo['modal_desc'] }}</p>
                    </div>
                </div>
                <button type="button" onclick="closeRemoveDivisiModal()" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>

            <div class="p-4 md:p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                <!-- Info Points Card -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3.5 space-y-2.5 text-xs text-amber-900">
                    <div class="flex items-start gap-2.5">
                        <i class="fas fa-coins text-amber-600 mt-0.5 text-sm flex-shrink-0"></i>
                        <div>
                            <strong>Hemat Biaya Kuota Lisensi:</strong> Slot lisensi {{ strtolower($currentInstansi ? $currentInstansi->tipe_sebutan : 'instansi') }} Anda akan langsung dibebaskan sejumlah pengguna yang dikeluarkan, sehingga kuota dapat dialokasikan untuk penugasan pengguna aktif lainnya.
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <i class="fas fa-shield-heart text-emerald-600 mt-0.5 text-sm flex-shrink-0"></i>
                        <div>
                            <strong>Riwayat Medis & Kesehatan 100% Aman:</strong> Seluruh data pemeriksaan Hb, fisik, rekam konseling, dan riwayat vital <u>TIDAK AKAN DIHAPUS</u> dan tetap tersimpan sebagai arsip {{ strtolower($currentInstansi ? $currentInstansi->tipe_sebutan : 'instansi') }}.
                        </div>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <i class="fas fa-lock text-rose-600 mt-0.5 text-sm flex-shrink-0"></i>
                        <div>
                            <strong>Akses Penugasan Dinonaktifkan:</strong> <span id="removeDivisiModalAlertDesc">Pengguna yang dikeluarkan otomatis tidak memiliki penugasan divisi aktif sampai dialokasikan kembali ke divisi/kelas baru.</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2">
                        Daftar Pengguna Yang Dipilih (<span id="removeUsersCount">0</span> pengguna):
                    </h4>
                    <div class="border border-gray-200 rounded-lg max-h-48 overflow-y-auto p-2 divide-y divide-gray-100 bg-gray-50">
                        <ul id="removeSelectedUsersList" class="space-y-1"></ul>
                    </div>
                </div>

                <form id="removeDivisiForm" action="{{ route('admin.users.mass_remove_divisi') }}" method="POST">
                    @csrf
                    @method('PATCH')
                </form>
            </div>

            <div class="bg-gray-50 px-4 py-3 md:px-6 flex flex-col-reverse sm:flex-row justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="closeRemoveDivisiModal()"
                    class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" form="removeDivisiForm"
                    class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm font-semibold rounded-md text-white bg-amber-600 hover:bg-amber-700 transition inline-flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fas fa-user-slash text-xs"></i>
                    <span id="removeDivisiSubmitBtnText">Ya, {{ $tabActionInfo['btn_label'] }}</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Atur Hak Akses Modul Per Pengguna -->
    <div id="userPermModal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4" onclick="closeUserPermModal()">
        <div class="bg-white rounded-2xl max-w-xl w-full mx-auto shadow-2xl overflow-hidden transform transition-all flex flex-col max-h-[90vh]" onclick="event.stopPropagation()">
            <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-700 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fas fa-user-gear text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold">Hak Akses Modul Personal</h3>
                        <p class="text-xs text-indigo-100" id="userPermTitle">-</p>
                    </div>
                </div>
                <button type="button" onclick="closeUserPermModal()" class="text-white/80 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto space-y-5 flex-1">
                <form id="userPermForm" method="POST" action="" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Skema Modul Pengguna
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex items-start p-3 rounded-xl border border-gray-200 cursor-pointer hover:border-indigo-400 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/50 transition-colors">
                                <input type="radio" name="use_custom_modules" value="0" id="userInheritRadio" onchange="toggleUserCustomView()" class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                                <div class="ml-2 text-xs">
                                    <div class="font-bold text-gray-800">Ikuti Divisi / Instansi</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">Otomatis mewarisi hak akses divisi</div>
                                </div>
                            </label>

                            <label class="relative flex items-start p-3 rounded-xl border border-gray-200 cursor-pointer hover:border-indigo-400 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/50 transition-colors">
                                <input type="radio" name="use_custom_modules" value="1" id="userCustomRadio" onchange="toggleUserCustomView()" class="mt-0.5 text-indigo-600 focus:ring-indigo-500">
                                <div class="ml-2 text-xs">
                                    <div class="font-bold text-gray-800">Kustomisasi Khusus User</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">Override modul khusus akun ini</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div id="userCustomModulesContainer" class="space-y-3 pt-2 border-t border-gray-100 hidden">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-700">Modul Diizinkan:</span>
                            <div class="space-x-2 text-[11px]">
                                <button type="button" onclick="checkAllUserModules(true)" class="text-indigo-600 hover:underline font-semibold">Pilih Semua</button>
                                <span class="text-gray-300">|</span>
                                <button type="button" onclick="checkAllUserModules(false)" class="text-gray-500 hover:underline">Hapus Semua</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @php
                                $all10 = [
                                    'haid_pita' => ['Siklus & Cuti Haid (FemHealth)', 'fas fa-venus text-pink-500'],
                                    'olahraga' => ['Aktivitas Olahraga & Tracker', 'fas fa-running text-emerald-500'],
                                    'vitalitas' => ['Vitalitas, Tidur & Hidrasi', 'fas fa-heart-pulse text-sky-500'],
                                    'kesehatan' => ['Rekam Medis Fisik Klinis', 'fas fa-notes-medical text-teal-500'],
                                    'hemoglobin' => ['Skrining HB & Anemia', 'fas fa-droplet text-rose-500'],
                                    'konsultasi' => ['Konseling SafeSpace', 'fas fa-comments text-indigo-500'],
                                    'nutrisi' => ['Nutrisi & Pola Makan Sehat', 'fas fa-apple-whole text-amber-500'],
                                    'burnout' => ['Skrining Burnout & Stres', 'fas fa-brain text-purple-500'],
                                    'ergonomi' => ['Ergonomi & Peringatan Duduk', 'fas fa-chair text-cyan-500'],
                                    'vaksinasi' => ['Paspor Vaksinasi & Imunisasi', 'fas fa-syringe text-blue-500'],
                                ];
                            @endphp

                            @foreach($all10 as $mKey => $mInfo)
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 hover:bg-slate-50 cursor-pointer text-xs">
                                <input type="checkbox" name="custom_modules[]" value="{{ $mKey }}" class="userModuleCheckbox rounded text-indigo-600 focus:ring-indigo-500">
                                <i class="{{ $mInfo[1] }}"></i>
                                <span class="text-gray-800 text-[11px] font-medium">{{ $mInfo[0] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Wewenang Add User Khusus Pengguna -->
                    <div class="pt-3 border-t border-gray-100 mt-3">
                        <label class="flex items-center justify-between p-2.5 rounded-xl border border-gray-200 hover:bg-slate-50 cursor-pointer">
                            <div>
                                <span class="text-xs font-semibold text-gray-800 block">Izinkan Akun Ini Menambah / Import User</span>
                                <span class="text-[11px] text-gray-500">Memberikan wewenang pendaftaran akun baru khusus akun ini.</span>
                            </div>
                            <input type="checkbox" name="custom_permissions[can_add_user]" value="1" id="userPermCanAddUser" class="rounded text-indigo-600 focus:ring-indigo-500">
                        </label>
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2 border-t border-gray-100">
                <button type="button" onclick="closeUserPermModal()" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="submit" form="userPermForm" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-md shadow-indigo-700/20 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-save"></i>
                    <span>Simpan Izin Personal</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        let exportType = '';

        function openExportModal(type) {
            exportType = type;
            document.getElementById('exportModalTitle').textContent = `Export Data Users (${type.toUpperCase()})`;
            document.getElementById('exportForm').action = '{{ route('admin.users.export.excel') }}';
            document.getElementById('exportModal').classList.remove('hidden');
            toggleFilterSections();
        }

        function closeExportModal() {
            document.getElementById('exportModal').classList.add('hidden');
        }

        document.getElementById('filterType').addEventListener('change', toggleFilterSections);

        function toggleFilterSections() {
            const filterType = document.getElementById('filterType').value;
            document.getElementById('divisiSection').classList.toggle('hidden', !['divisi', 'divisi_tanggal'].includes(
                filterType));
            document.getElementById('tanggalSection').classList.toggle('hidden', !['tanggal', 'divisi_tanggal'].includes(
                filterType));
        }

        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        function displayFileName(input) {
            const fileName = input.files[0]?.name;
            const fileNameDisplay = document.getElementById('fileName');
            if (fileName) {
                fileNameDisplay.textContent = `File dipilih: ${fileName}`;
                fileNameDisplay.classList.add('text-green-600', 'font-medium');
            }
        }

        function openChangeDivisiModal() {
            const checkedBoxes = document.querySelectorAll('.rowCheckbox:checked');
            const list = document.getElementById('selectedUsersList');
            const form = document.getElementById('changeDivisiForm');

            // Clear the user list
            list.innerHTML = '';

            // Remove any existing hidden ids[] inputs (but keep CSRF and method)
            const prevInputs = form.querySelectorAll('input[name="ids[]"]');
            prevInputs.forEach(input => input.remove());

            if (checkedBoxes.length === 0) return;

            checkedBoxes.forEach(box => {
                const tr = box.closest('tr');
                const id = tr.dataset.id;
                const nama = tr.dataset.nama;
                const divisi = tr.dataset.divisi;

                // Add to list
                const li = document.createElement('li');
                li.className = 'text-xs md:text-sm border-b py-2 last:border-0';
                li.innerHTML =
                    `<strong>${nama}</strong> <span class="text-gray-500">(Divisi saat ini: ${divisi})</span>`;
                list.appendChild(li);

                // Add hidden input
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });

            document.getElementById('changeDivisiModal').classList.remove('hidden');
        }

        function closeChangeDivisiModal() {
            document.getElementById('changeDivisiModal').classList.add('hidden');
        }

        function openRemoveDivisiModal() {
            const checkedBoxes = document.querySelectorAll('.rowCheckbox:checked');
            const list = document.getElementById('removeSelectedUsersList');
            const countEl = document.getElementById('removeUsersCount');
            const form = document.getElementById('removeDivisiForm');

            // Reset modal header ke konteks tab saat ini
            document.getElementById('removeDivisiModalTitle').textContent = @json($tabActionInfo['modal_title']);
            document.getElementById('removeDivisiModalSubtitle').textContent = @json($tabActionInfo['modal_desc']);
            document.getElementById('removeDivisiModalIcon').className = 'fas ' + @json($tabActionInfo['icon']) + ' text-lg';
            document.getElementById('removeDivisiSubmitBtnText').textContent = 'Ya, ' + @json($tabActionInfo['btn_label']);

            list.innerHTML = '';
            const prevInputs = form.querySelectorAll('input[name="ids[]"]');
            prevInputs.forEach(input => input.remove());

            if (checkedBoxes.length === 0) return;

            let validCount = 0;
            checkedBoxes.forEach(box => {
                const tr = box.closest('tr');
                const id = tr.dataset.id;
                const nama = tr.dataset.nama;
                const divisi = tr.dataset.divisi;

                const li = document.createElement('li');
                li.className = 'text-xs py-1.5 flex items-center justify-between';
                li.innerHTML = `<span class="font-medium text-gray-900">${nama}</span> <span class="text-xs text-gray-500 bg-white px-2 py-0.5 rounded border border-gray-200">${divisi}</span>`;
                list.appendChild(li);

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
                validCount++;
            });

            countEl.textContent = validCount;
            document.getElementById('removeDivisiModal').classList.remove('hidden');
        }

        function closeRemoveDivisiModal() {
            document.getElementById('removeDivisiModal').classList.add('hidden');
        }

        function singleRemoveDivisi(id, nama, divisi, modalTitle, modalDesc, iconClass, btnLabel) {
            const list = document.getElementById('removeSelectedUsersList');
            const countEl = document.getElementById('removeUsersCount');
            const form = document.getElementById('removeDivisiForm');

            if (modalTitle) {
                document.getElementById('removeDivisiModalTitle').textContent = modalTitle;
            }
            if (modalDesc) {
                document.getElementById('removeDivisiModalSubtitle').textContent = modalDesc;
            }
            if (iconClass) {
                document.getElementById('removeDivisiModalIcon').className = 'fas ' + iconClass + ' text-lg';
            }
            if (btnLabel) {
                document.getElementById('removeDivisiSubmitBtnText').textContent = 'Ya, ' + btnLabel;
            }

            list.innerHTML = '';
            const prevInputs = form.querySelectorAll('input[name="ids[]"]');
            prevInputs.forEach(input => input.remove());

            const li = document.createElement('li');
            li.className = 'text-xs py-1.5 flex items-center justify-between';
            li.innerHTML = `<span class="font-medium text-gray-900">${nama}</span> <span class="text-xs text-gray-500 bg-white px-2 py-0.5 rounded border border-gray-200">${divisi || 'Tanpa Divisi'}</span>`;
            list.appendChild(li);

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);

            countEl.textContent = '1';
            document.getElementById('removeDivisiModal').classList.remove('hidden');
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImportModal();
                closeExportModal();
                closeChangeDivisiModal();
                closeRemoveDivisiModal();
                closeUserPermModal();
            }
        });

        const selectAll = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkChangeDivisiBtn = document.getElementById('bulkChangeDivisiBtn');
        const bulkRemoveDivisiBtn = document.getElementById('bulkRemoveDivisiBtn');

        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            toggleBulkButtons();
        });

        rowCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkButtons);
        });

        function toggleBulkButtons() {
            const checkedCount = document.querySelectorAll('.rowCheckbox:checked').length;
            const countText = document.getElementById('selectedCountText');
            if (countText) {
                countText.textContent = `${checkedCount} pengguna dipilih`;
                if (checkedCount > 0) {
                    countText.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 transition-colors shadow-sm';
                } else {
                    countText.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200 transition-colors';
                }
            }
            bulkDeleteBtn.disabled = checkedCount === 0;
            bulkChangeDivisiBtn.disabled = checkedCount === 0;
            if (bulkRemoveDivisiBtn) {
                bulkRemoveDivisiBtn.disabled = checkedCount === 0;
            }
        }

        const searchInput = document.getElementById('searchInput');
        const clearSearch = document.getElementById('clearSearch');
        const searchForm = document.getElementById('searchForm');

        function toggleClearIcon() {
            clearSearch.style.display = searchInput.value.trim() !== '' ? 'flex' : 'none';
        }

        toggleClearIcon();

        searchInput.addEventListener('input', toggleClearIcon);

        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            toggleClearIcon();
        });

        // User Custom Permissions Logic
        function openUserPermModal(user) {
            document.getElementById('userPermModal').classList.remove('hidden');
            document.getElementById('userPermTitle').textContent = user.nama + ' (' + (user.nomor_induk || user.username) + ')';
            document.getElementById('userPermForm').action = '/admin/users/' + user.id + '/permissions';

            const isCustom = user.custom_modules !== null && Array.isArray(user.custom_modules);
            if (isCustom) {
                document.getElementById('userCustomRadio').checked = true;
                document.getElementById('userCustomModulesContainer').classList.remove('hidden');

                const checkboxes = document.querySelectorAll('.userModuleCheckbox');
                checkboxes.forEach(cb => {
                    cb.checked = user.custom_modules.includes(cb.value);
                });
            } else {
                document.getElementById('userInheritRadio').checked = true;
                document.getElementById('userCustomModulesContainer').classList.add('hidden');

                const checkboxes = document.querySelectorAll('.userModuleCheckbox');
                checkboxes.forEach(cb => cb.checked = true);
            }

            const customPerms = user.custom_permissions || {};
            document.getElementById('userPermCanAddUser').checked = !!customPerms.can_add_user;
        }

        function closeUserPermModal() {
            document.getElementById('userPermModal').classList.add('hidden');
        }

        function toggleUserCustomView() {
            const isCustom = document.getElementById('userCustomRadio').checked;
            const container = document.getElementById('userCustomModulesContainer');
            if (isCustom) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        function checkAllUserModules(check) {
            const checkboxes = document.querySelectorAll('.userModuleCheckbox');
            checkboxes.forEach(cb => cb.checked = check);
        }
    </script>
@endsection