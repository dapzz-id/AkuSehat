@extends('layouts.app')

@section('title', 'Data HB')

@section('content')
    <div class="space-y-4 md:space-y-6">
        <!-- Header -->
        <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 md:gap-4">
                <div class="flex items-center gap-2 md:gap-3">
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-tint text-[#1b5e20] text-lg md:text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-[#1a472a]">Data Hemoglobin (HB)</h2>
                        <p class="text-xs md:text-sm text-[#388e3c]">Manajemen data hemoglobin member</p>
                    </div>
                </div>
                <div class="flex flex-col gap-2 w-full lg:w-auto">
                    <div class="flex flex-wrap gap-2">
                        <button onclick="openExportModal('excel')"
                            class="flex-1 sm:flex-none bg-[#4caf50] hover:bg-[#388e3c] text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-file-excel text-xs md:text-sm mr-1.5 md:mr-2"></i>
                            <span>Export Excel</span>
                        </button>
                        <button onclick="openExportModal('pdf')"
                            class="flex-1 sm:flex-none bg-[#f44336] hover:bg-[#d32f2f] text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-file-pdf text-xs md:text-sm mr-1.5 md:mr-2"></i>
                            <span>Export PDF</span>
                        </button>
                        @if(auth()->user()->isAdminInstansi())
                            <div class="px-3 md:px-4 py-1.5 md:py-2 bg-emerald-50 border border-emerald-200 text-[#1b5e20] rounded-md text-xs md:text-sm font-semibold inline-flex items-center gap-1.5">
                                <i class="fas fa-chart-pie text-emerald-600"></i>
                                <span>Mode Laporan & Rekapitulasi (Read-Only)</span>
                            </div>
                        @else
                            <button onclick="window.location.href='{{ route('admin.hb.create') }}'"
                                class="w-32 flex-1 sm:flex-none bg-[#2e7d32] hover:bg-[#1b5e20] text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-plus text-xs md:text-sm mr-1.5 md:mr-2"></i>
                                <span>Tambah</span>
                            </button>
                            <button type="submit" form="bulkDeleteForm" id="bulkDeleteBtn"
                                class="w-40 flex-1 sm:flex-none bg-[#d32f2f] hover:bg-[#b71c1c] text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled>
                                <i class="fas fa-trash text-xs md:text-sm mr-1.5 md:mr-2"></i>
                                <span>Hapus Terpilih</span>
                            </button>
                        @endif
                    </div>
                    <form id="searchForm" action="{{ route('admin.hb.index') }}" method="GET"
                        class="w-full flex-1 mt-1 flex flex-col sm:flex-row gap-2">
                        <div class="relative w-full sm:flex-1">
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                placeholder="Cari data HB..."
                                class="w-full text-sm md:text-base pl-9 md:pl-10 pr-9 md:pr-10 py-1.5 md:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#2e7d32] focus:border-[#2e7d32] transition-colors">
                            <div class="absolute inset-y-0 left-0 pl-2.5 md:pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400 text-xs md:text-sm"></i>
                            </div>
                            <div id="clearSearch"
                                class="absolute inset-y-0 right-0 pr-2.5 md:pr-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600"
                                style="display: none;">
                                <i class="fas fa-times text-xs md:text-sm"></i>
                            </div>
                        </div>
                        <select name="year"
                            class="w-full sm:w-auto text-sm md:text-base px-3 py-1.5 md:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#2e7d32] focus:border-[#2e7d32] transition-colors">
                            <option value="all">Semua Tahun</option>
                            @foreach ($years as $y)
                                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>
                                    {{ $y }}</option>
                            @endforeach
                        </select>
                        <button type="submit"
                            class="w-full sm:w-auto bg-[#2e7d32] hover:bg-[#1b5e20] text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                            Cari
                        </button>
                        <a href="{{ route('admin.hb.index') }}"
                            class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                            Reset
                        </a>
                    </form>
                </div>
            </div>
        </div>

        <!-- Grafik & Analisis Pemeriksaan Hemoglobin -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-5">
            <!-- Grafik 1: Proporsi Status Hemoglobin -->
            <div class="lg:col-span-5 bg-white p-5 rounded-lg border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-red-100 text-[#b71c1c] flex items-center justify-center text-xs font-bold shadow-sm">
                            <i class="fas fa-tint"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-gray-800">Distribusi Status Hemoglobin</h3>
                            <p class="text-[11px] text-gray-400">Proporsi normal vs anemia member</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold text-red-800 bg-red-50 px-2.5 py-1 rounded-md border border-red-200">
                        {{ $totalHb }} Sampel
                    </span>
                </div>

                <div class="relative flex items-center justify-center my-auto py-3">
                    <div class="w-44 h-44 sm:w-48 sm:h-48 relative">
                        <canvas id="hbStatusChart"></canvas>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 pt-3 border-t border-gray-100 text-[11px]">
                    <div class="flex flex-col items-center justify-center p-2 rounded-lg bg-[#c8e6c9]/40 border border-[#c8e6c9] text-center">
                        <span class="text-[10px] text-[#1b5e20] font-medium flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-[#1b5e20]"></span> Normal
                        </span>
                        <span class="font-bold text-[#1b5e20] text-sm mt-0.5">{{ $countNormal }}</span>
                    </div>
                    <div class="flex flex-col items-center justify-center p-2 rounded-lg bg-[#ffcdd2]/50 border border-[#ffcdd2] text-center">
                        <span class="text-[10px] text-[#c62828] font-medium flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-[#c62828]"></span> Anemia
                        </span>
                        <span class="font-bold text-[#c62828] text-sm mt-0.5">{{ $countAnemia }}</span>
                    </div>
                    <div class="flex flex-col items-center justify-center p-2 rounded-lg bg-[#ffe0b2]/50 border border-[#ffe0b2] text-center">
                        <span class="text-[10px] text-[#ef6c00] font-medium flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-[#ef6c00]"></span> Tinggi
                        </span>
                        <span class="font-bold text-[#ef6c00] text-sm mt-0.5">{{ $countTinggi }}</span>
                    </div>
                </div>
            </div>

            <!-- Grafik 2: Rata-rata Hb per Divisi -->
            <div class="lg:col-span-7 bg-white p-5 rounded-lg border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-[#1b5e20] flex items-center justify-center text-xs font-bold shadow-sm">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-gray-800">Rata-rata Kadar Hb per Divisi / Kelas</h3>
                            <p class="text-[11px] text-gray-400">Deteksi kelompok yang rentan mengalami anemia (g/dL)</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                        Tren Divisi
                    </span>
                </div>

                <div class="relative w-full h-52 sm:h-56 my-auto">
                    <canvas id="divisionHbChart"></canvas>
                </div>

                <p class="text-[11px] text-gray-400 pt-3 border-t border-gray-100 flex items-center gap-1.5">
                    <i class="fas fa-circle-info text-red-500"></i>
                    Batas acuan normal Hb: &ge; 12.0 g/dL untuk perempuan, dan &ge; 13.0 g/dL untuk laki-laki.
                </p>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100">
            <form id="bulkDeleteForm" action="{{ route('admin.hb.mass_destroy') }}" method="POST"
                onsubmit="return confirm('Yakin ingin menghapus data HB yang dipilih?')">
                @csrf
                @method('DELETE')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#e8f5e9]">
                            <tr>
                                @if(!auth()->user()->isAdminInstansi())
                                <th class="w-10 px-2 sm:px-3 md:px-6 py-2 md:py-3 text-center">
                                    <input type="checkbox" id="selectAll"
                                        class="rounded border-gray-300 text-[#2e7d32] focus:ring-[#2e7d32]">
                                </th>
                                @endif
                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                                    Member
                                </th>
                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden md:table-cell">
                                    Divisi
                                </th>
                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                                    Tanggal
                                </th>
                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                                    HB (g/dL)
                                </th>
                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                                    Status
                                </th>
                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-center text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($hb as $item)
                                <tr class="hover:bg-[#f1f8e9] transition-colors duration-150">
                                    @if(!auth()->user()->isAdminInstansi())
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $item->id_hb }}"
                                            class="rowCheckbox rounded border-gray-300 text-[#2e7d32] focus:ring-[#2e7d32]">
                                    </td>
                                    @endif
                                    <!-- Member -->
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4">
                                        <div class="text-xs md:text-sm font-medium text-gray-900">{{ $item->user->nama }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $item->user->nomor_induk }}
                                            ({{ $item->user->jk === 'L' ? 'L' : 'P' }})
                                        </div>
                                        <div class="text-xs text-gray-500 md:hidden mt-0.5">
                                            Divisi: {{ $item->user->divisi->divisi_name ?? '-' }}
                                        </div>
                                    </td>
                                    <!-- Divisi -->
                                    <td
                                        class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-xs md:text-sm text-gray-900 hidden md:table-cell">
                                        {{ $item->user->divisi->divisi_name ?? '-' }}
                                    </td>
                                    <!-- Tanggal -->
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-xs md:text-sm text-gray-900">
                                        {{ $item->tgl->format('d/m/Y') }}
                                    </td>
                                    <!-- HB -->
                                    <td
                                        class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-xs md:text-sm font-semibold text-gray-900">
                                        {{ $item->hb }}
                                    </td>
                                    <!-- Status -->
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4">
                                        @php
                                            $statusClass = match ($item->status) {
                                                'Normal' => 'bg-[#c8e6c9] text-[#1b5e20]',
                                                'Anemia' => 'bg-[#ffcdd2] text-[#c62828]',
                                                'Tinggi' => 'bg-[#ffe0b2] text-[#ef6c00]',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="px-1 py-0.5 sm:px-2 sm:py-1 inline-flex text-xs leading-4 md:leading-5 font-semibold rounded-full {{ $statusClass }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <!-- Aksi -->
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 whitespace-nowrap text-center">
                                        @if(auth()->user()->isAdminInstansi())
                                            <button type="button" onclick="openHbDetail(this); return false;" class="view-detail inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#e8f5e9] text-[#2e7d32] hover:bg-[#c8e6c9] font-medium text-xs transition-colors shadow-sm cursor-pointer"
                                                data-nama="{{ $item->user->nama }}"
                                                data-nomor-induk="{{ $item->user->nomor_induk }} ({{ $item->user->jk === 'L' ? 'L' : 'P' }})"
                                                data-divisi="{{ $item->user->divisi->divisi_name ?? '-' }}"
                                                data-tanggal="{{ $item->tgl->format('d/m/Y') }}"
                                                data-hb="{{ $item->hb }}"
                                                data-status="{{ $item->status }}"
                                                data-pesan="{{ $item->pesan ?? '-' }}"
                                                data-created-at="{{ $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : '-' }}"
                                                data-updated-at="{{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i:s') : '-' }}"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye text-xs"></i>
                                                <span>Lihat Detail</span>
                                            </button>
                                        @else
                                            <div class="flex items-center justify-center gap-1 md:gap-2">
                                                <button type="button" onclick="openHbDetail(this); return false;" class="view-detail text-[#388e3c] hover:text-[#2e7d32] p-1 transition-colors duration-200 cursor-pointer"
                                                    data-nama="{{ $item->user->nama }}"
                                                    data-nomor-induk="{{ $item->user->nomor_induk }} ({{ $item->user->jk === 'L' ? 'L' : 'P' }})"
                                                    data-divisi="{{ $item->user->divisi->divisi_name ?? '-' }}"
                                                    data-tanggal="{{ $item->tgl->format('d/m/Y') }}"
                                                    data-hb="{{ $item->hb }}"
                                                    data-status="{{ $item->status }}"
                                                    data-pesan="{{ $item->pesan ?? '-' }}"
                                                    data-created-at="{{ $item->created_at ? $item->created_at->format('d/m/Y H:i:s') : '-' }}"
                                                    data-updated-at="{{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i:s') : '-' }}"
                                                    title="Lihat Detail">
                                                    <i class="fas fa-eye text-sm md:text-base"></i>
                                                </button>
                                                <a href="{{ route('admin.hb.edit', $item->id_hb) }}"
                                                    class="text-[#2e7d32] hover:text-[#1b5e20] p-1 transition-colors duration-200"
                                                    title="Edit">
                                                    <i class="fas fa-edit text-sm md:text-base"></i>
                                                </a>
                                                <form action="{{ route('admin.hb.destroy', $item->id_hb) }}" method="POST"
                                                    class="inline"
                                                    onsubmit="return confirm('Yakin ingin menghapus data HB ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-[#d32f2f] hover:text-[#b71c1c] p-1 transition-colors duration-200"
                                                        title="Hapus">
                                                        <i class="fas fa-trash text-sm md:text-base"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->isAdminInstansi() ? 6 : 7 }}" class="px-4 md:px-6 py-6 md:py-8 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <div
                                                class="w-12 h-12 md:w-16 md:h-16 bg-red-100 rounded-full flex items-center justify-center mb-2 md:mb-3">
                                                <i class="fas fa-tint text-red-300 text-xl md:text-2xl"></i>
                                            </div>
                                            <p class="text-sm md:text-lg font-medium text-gray-600">Tidak ada data HB</p>
                                            @if(!auth()->user()->isAdminInstansi())
                                                <p class="text-xs md:text-sm mt-1 text-gray-500">Klik "Tambah Data HB" untuk
                                                    menambahkan data baru</p>
                                                <a href="{{ route('admin.hb.create') }}"
                                                    class="mt-3 inline-flex items-center bg-[#2e7d32] hover:bg-[#1b5e20] text-white px-4 py-2 rounded-md text-xs md:text-sm font-medium transition-colors">
                                                    <i class="fas fa-plus mr-1.5 md:mr-2 text-xs"></i>
                                                    Tambah Data HB
                                                </a>
                                            @else
                                                <p class="text-xs md:text-sm mt-1 text-gray-500">Belum ada riwayat hasil pemeriksaan hemoglobin yang tercatat.</p>
                                            @endif
                                        </div>
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
                        Showing <span class="font-medium">{{ $hb->firstItem() }}</span>
                        to <span class="font-medium">{{ $hb->lastItem() }}</span>
                        of <span class="font-medium">{{ $hb->total() }}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        {{-- Tombol Previous --}}
                        @if ($hb->onFirstPage())
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
                            <a href="{{ $hb->previousPageUrl() }}"
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
                            $currentPage = $hb->currentPage();
                            $lastPage = $hb->lastPage();
                            $start = max($currentPage - 1, 1);
                            $end = min($currentPage + 1, $lastPage);
                        @endphp

                        {{-- Halaman pertama --}}
                        @if ($start > 1)
                            <a href="{{ $hb->url(1) }}"
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
                                <a href="{{ $hb->url($page) }}"
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
                            <a href="{{ $hb->url($lastPage) }}"
                                class="relative inline-flex items-center px-2 sm:px-4 py-1 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium {{ $currentPage == $lastPage ? 'bg-[#2e7d32] text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                                {{ $lastPage }}
                            </a>
                        @endif

                        {{-- Tombol Next --}}
                        @if ($hb->hasMorePages())
                            <a href="{{ $hb->nextPageUrl() }}"
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
                            Export Data HB
                        </h3>
                        <form id="exportForm" method="GET">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">Filter
                                        Export</label>
                                    <select name="filter_type" id="filterType"
                                        class="w-full text-xs md:text-sm px-3 py-1.5 md:py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#2e7d32]">
                                        <option value="all">Export Semua Data</option>
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
                                            <option value="{{ $d->id }}">{{ $d->divisi_name }}</option>
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

    <!-- Detail Modal -->
    <div id="detailModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm hidden z-[999999] flex items-center justify-center p-4"
        onclick="closeDetailModal()">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl" onclick="event.stopPropagation()">
            <div class="p-4 md:p-6 max-h-[70vh] overflow-y-auto">
                <div class="flex items-start gap-3 md:gap-4 mb-4 md:mb-6">
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle text-[#388e3c] text-sm md:text-base"></i>
                    </div>
                    <h3 class="text-base md:text-lg font-semibold text-gray-900">Detail Data HB</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Member</label>
                        <p id="detailNama" class="text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk</label>
                        <p id="detailNomorInduk" class="text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                        <p id="detailDivisi" class="text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <p id="detailTanggal" class="text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">HB (g/dL)</label>
                        <p id="detailHb" class="text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <p id="detailStatus" class="text-sm text-gray-900"></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                        <p id="detailPesan" class="text-sm text-gray-900 whitespace-pre-wrap"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Created At</label>
                        <p id="detailCreatedAt" class="text-sm text-gray-900"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Updated At</label>
                        <p id="detailUpdatedAt" class="text-sm text-gray-900"></p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 md:px-6 flex justify-end">
                <button type="button" onclick="closeDetailModal()"
                    class="px-4 py-2 text-xs md:text-sm font-medium rounded-md text-white bg-[#2e7d32] hover:bg-[#1b5e20]">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        let exportType = '';

        function openExportModal(type) {
            exportType = type;
            document.getElementById('exportModalTitle').textContent = `Export Data HB (${type.toUpperCase()})`;
            document.getElementById('exportForm').action = type === 'excel' ? '{{ route('admin.hb.export.excel') }}' :
                '{{ route('admin.hb.export.pdf') }}';
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

        window.closeDetailModal = function() {
            const m = document.getElementById('detailModal');
            if (m) {
                m.classList.add('hidden');
                m.style.display = 'none';
                m.style.setProperty('display', 'none', 'important');
            }
        };

        window.openHbDetail = function(btn) {
            try {
                if (!btn) {
                    btn = (window.event && window.event.target) ? window.event.target.closest('button, .view-detail') : null;
                } else if (btn.closest) {
                    btn = btn.closest('button, .view-detail') || btn;
                }
                if (!btn) return;

                const getVal = function(attr, fallback) {
                    return btn.getAttribute(attr) || fallback || '-';
                };

                const setTxt = function(id, val) {
                    const el = document.getElementById(id);
                    if (el) el.textContent = val;
                };

                setTxt('detailNama', getVal('data-nama', '-'));
                setTxt('detailNomorInduk', getVal('data-nomor-induk', '-'));
                setTxt('detailDivisi', getVal('data-divisi', '-'));
                setTxt('detailTanggal', getVal('data-tanggal', '-'));
                setTxt('detailHb', getVal('data-hb', '-'));
                setTxt('detailStatus', getVal('data-status', '-'));
                setTxt('detailPesan', getVal('data-pesan', '-'));
                setTxt('detailCreatedAt', getVal('data-created-at', '-'));
                setTxt('detailUpdatedAt', getVal('data-updated-at', '-'));

                const m = document.getElementById('detailModal');
                if (m) {
                    m.classList.remove('hidden');
                    m.style.removeProperty('display');
                    m.style.display = 'flex';
                    m.style.setProperty('display', 'flex', 'important');
                    m.style.zIndex = '999999';
                    m.style.opacity = '1';
                    m.style.pointerEvents = 'auto';
                }
            } catch (err) {
                console.error('Error opening Hb detail modal:', err);
                const m = document.getElementById('detailModal');
                if (m) {
                    m.classList.remove('hidden');
                    m.style.display = 'flex';
                    m.style.setProperty('display', 'flex', 'important');
                    m.style.zIndex = '999999';
                }
            }
        };

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.view-detail');
            if (btn) {
                e.preventDefault();
                openHbDetail(btn);
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeExportModal();
                closeDetailModal();
            }
        });

        const selectAll = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

        if (selectAll && bulkDeleteBtn) {
            selectAll.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                toggleBulkDeleteBtn();
            });

            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', toggleBulkDeleteBtn);
            });

            function toggleBulkDeleteBtn() {
                const checkedCount = document.querySelectorAll('.rowCheckbox:checked').length;
                bulkDeleteBtn.disabled = checkedCount === 0;
            }
        }

        // Search clear functionality
        const searchInput = document.getElementById('searchInput');
        const clearSearch = document.getElementById('clearSearch');
        const searchForm = document.getElementById('searchForm');

        if (searchInput && clearSearch) {
            function toggleClearIcon() {
                clearSearch.style.display = searchInput.value.trim() !== '' ? 'flex' : 'none';
            }

            toggleClearIcon();

            searchInput.addEventListener('input', toggleClearIcon);

            clearSearch.addEventListener('click', function() {
                searchInput.value = '';
                toggleClearIcon();
            });
        }

        // Inisialisasi Grafik Analisis Hb
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Chart === 'undefined') return;

            // 1. Donut Chart - Distribusi Status Hb
            const hbStatusCtx = document.getElementById('hbStatusChart');
            if (hbStatusCtx) {
                const hbStatusData = @json($hbChartData ?? ['labels' => [], 'data' => [], 'colors' => []]);
                new Chart(hbStatusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: hbStatusData.labels,
                        datasets: [{
                            data: hbStatusData.data,
                            backgroundColor: hbStatusData.colors || ['#2e7d32', '#d32f2f', '#ed6c02'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return ` ${label}: ${value} orang (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 2. Bar Chart - Rata-rata Hb per Divisi
            const divHbCtx = document.getElementById('divisionHbChart');
            if (divHbCtx) {
                const divData = @json($divisionHbChartData ?? ['labels' => [], 'data' => []]);
                new Chart(divHbCtx, {
                    type: 'bar',
                    data: {
                        labels: divData.labels,
                        datasets: [{
                            label: 'Rata-rata Hb (g/dL)',
                            data: divData.data,
                            backgroundColor: 'rgba(46, 125, 50, 0.75)',
                            borderColor: '#2e7d32',
                            borderWidth: 1.5,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return ` Rata-rata: ${context.parsed.y} g/dL`;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                min: 8,
                                max: 18,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    font: { size: 11 },
                                    callback: function(val) { return val + ' g/dL'; }
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: {
                                    font: { size: 10 },
                                    maxRotation: 45,
                                    minRotation: 0
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection