@extends('layouts.app')
@section('title', 'Data Divisi')
@section('content')
    <div class="space-y-4 md:space-y-6">
        <!-- Header -->
        <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 md:gap-4">
                <div class="flex items-center gap-2 md:gap-3">
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-school text-[#1b5e20] text-lg md:text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-[#1a472a]">Data Divisi</h2>
                        <p class="text-xs md:text-sm text-[#388e3c]">Manajemen data divisi</p>
                    </div>
                </div>
                <div class="flex flex-col gap-2 w-full lg:w-auto">
                    <div class="flex flex-wrap gap-2">
                        <button onclick="openModal()"
                            class="w-52 flex-1 sm:flex-none bg-[#2e7d32] hover:bg-[#1b5e20] text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-plus text-xs md:text-sm mr-1.5 md:mr-2"></i>
                            <span>Tambah</span>
                        </button>
                        <button type="submit" form="bulkDeleteForm" id="bulkDeleteBtn"
                            class="w-52 flex-1 sm:flex-none bg-[#d32f2f] hover:bg-[#b71c1c] text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            <i class="fas fa-trash text-xs md:text-sm mr-1.5 md:mr-2"></i>
                            <span>Hapus Terpilih</span>
                        </button>
                    </div>
                    <form id="searchForm" action="{{ route('admin.divisi.index') }}" method="GET" class="w-full flex-1 mt-1 flex flex-col sm:flex-row gap-2">
                        <div class="relative w-full sm:flex-1">
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                placeholder="Cari divisi..."
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
                        <button type="submit"
                            class="w-full sm:w-auto bg-[#2e7d32] hover:bg-[#1b5e20] text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                            Cari
                        </button>
                        <a href="{{ route('admin.divisi.index') }}"
                            class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                            Reset
                        </a>
                    </form>
                </div>
            </div>
        </div>
        <!-- Statistics Overview -->
        <div class="grid grid-cols-2 sm:grid-cols-2 gap-3">
            <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex flex-col md:flex-row md:items-center gap-2">
                    <div
                        class="w-8 h-8 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-school text-green-600 text-sm md:text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Total Divisi</p>
                        <p class="text-lg md:text-2xl font-semibold text-gray-900">{{ $divisi->total() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                <div class="flex flex-col md:flex-row md:items-center gap-2">
                    <div
                        class="w-8 h-8 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-users text-blue-600 text-sm md:text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs md:text-sm font-medium text-gray-600">Total Members</p>
                        <p class="text-lg md:text-2xl font-semibold text-gray-900">{{ $divisi->sum('jumlah_member') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Table Section -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100">
            <form id="bulkDeleteForm" action="{{ route('admin.divisi.mass_destroy') }}" method="POST"
                onsubmit="return confirm('Yakin ingin menghapus divisi yang dipilih?')">
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
                                    Nama Divisi
                                </th>
                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                                    Warna Cover
                                </th>
                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden md:table-cell">
                                    Jumlah Members
                                </th>
                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden lg:table-cell">
                                    Data Kesehatan
                                </th>
                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden xl:table-cell">
                                    Tanggal Dibuat
                                </th>
                                <th
                                    class="px-2 sm:px-3 md:px-6 py-2 md:py-3 text-center text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($divisi as $item)
                                <tr class="hover:bg-[#f1f8e9] transition-colors duration-150">
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-center">
                                        <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                                            class="rowCheckbox rounded border-gray-300 text-[#2e7d32] focus:ring-[#2e7d32]">
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4">
                                        <div class="text-xs md:text-sm font-medium text-gray-900">{{ $item->divisi_name }}
                                        </div>
                                        <div class="text-xs text-gray-500 md:hidden mt-0.5">
                                            Members: {{ $item->jumlah_member }}
                                        </div>
                                        <div class="text-xs text-gray-500 lg:hidden mt-0.5">
                                            Kesehatan:
                                            {{ $item->kesehatan->filter(fn($k) => \Carbon\Carbon::parse($k->tgl)->year == now()->year)->count() }}
                                            data
                                        </div>
                                        <div class="text-xs text-gray-500 xl:hidden mt-0.5">
                                            Dibuat: {{ $item->created_at->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-xs md:text-sm text-gray-900">
                                        <div class="flex items-center gap-1 sm:gap-2">
                                            <div class="w-4 h-4 sm:w-6 sm:h-6 rounded-md"
                                                style="background-color: {{ $item->color_cover }};"></div>
                                            <span class="uppercase">{{ $item->color_cover }}</span>
                                        </div>
                                    </td>
                                    <td
                                        class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-xs md:text-sm text-gray-900 hidden md:table-cell">
                                        <div class="flex items-center">
                                            <i class="fas fa-users text-gray-400 mr-1 sm:mr-2"></i>
                                            {{ $item->jumlah_member }} members
                                        </div>
                                    </td>
                                    <td
                                        class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-xs md:text-sm text-gray-900 hidden lg:table-cell">
                                        <div class="flex items-center">
                                            <i class="fas fa-heartbeat text-gray-400 mr-1 sm:mr-2"></i>
                                            {{ $item->kesehatan->filter(fn($k) => \Carbon\Carbon::parse($k->tgl)->year == now()->year)->count() }}
                                            data
                                        </div>
                                    </td>
                                    <td
                                        class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-xs md:text-sm text-gray-500 hidden xl:table-cell">
                                        {{ $item->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4">
                                        <div class="flex items-center justify-center text-xs sm:text-sm gap-1 sm:gap-2">
                                            <button type="button" onclick="editDivisi({{ json_encode($item) }})"
                                                class="text-[#2e7d32] hover:text-[#1b5e20] p-1 sm:p-2 rounded-full hover:bg-green-50 transition-colors"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="{{ route('admin.users.index') }}?divisi={{ $item->id }}"
                                                class="text-[#0288d1] hover:text-[#01579b] p-1 sm:p-2 rounded-full hover:bg-blue-50 transition-colors"
                                                title="Lihat Members">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            <form action="{{ route('admin.divisi.destroy', $item->id) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi {{ $item->divisi_name }}? Tindakan ini tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-[#d32f2f] hover:text-[#b71c1c] p-1 sm:p-2 rounded-full hover:bg-red-50 transition-colors"
                                                    title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 md:px-6 py-6 md:py-8 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <div
                                                class="w-12 h-12 md:w-16 md:h-16 bg-gray-100 rounded-full flex items-center justify-center mb-2 md:mb-3">
                                                <i class="fas fa-school text-gray-300 text-xl md:text-2xl"></i>
                                            </div>
                                            <p class="text-sm md:text-lg font-medium text-gray-600">Tidak ada data divisi
                                            </p>
                                            <p class="text-xs md:text-sm mt-1 text-gray-500">Klik "Tambah Divisi" untuk
                                                menambahkan data baru</p>
                                            <button onclick="openModal()"
                                                class="mt-3 text-[#2e7d32] hover:text-[#1b5e20] font-medium">
                                                Tambah Divisi
                                            </button>
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
                        Showing <span class="font-medium">{{ $divisi->firstItem() }}</span>
                        to <span class="font-medium">{{ $divisi->lastItem() }}</span>
                        of <span class="font-medium">{{ $divisi->total() }}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        {{-- Tombol Previous --}}
                        @if ($divisi->onFirstPage())
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
                            <a href="{{ $divisi->previousPageUrl() }}"
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
                            $currentPage = $divisi->currentPage();
                            $lastPage = $divisi->lastPage();
                            $start = max($currentPage - 1, 1);
                            $end = min($currentPage + 1, $lastPage);
                        @endphp
                        {{-- Halaman pertama --}}
                        @if ($start > 1)
                            <a href="{{ $divisi->url(1) }}"
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
                                <a href="{{ $divisi->url($page) }}"
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
                            <a href="{{ $divisi->url($lastPage) }}"
                                class="relative inline-flex items-center px-2 sm:px-4 py-1 sm:py-2 border border-gray-300 text-xs sm:text-sm font-medium {{ $currentPage == $lastPage ? 'bg-[#2e7d32] text-white' : 'bg-white text-gray-500 hover:bg-gray-50' }}">
                                {{ $lastPage }}
                            </a>
                        @endif
                        {{-- Tombol Next --}}
                        @if ($divisi->hasMorePages())
                            <a href="{{ $divisi->nextPageUrl() }}"
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
    <!-- Modal Tambah/Edit Divisi -->
    <div id="divisiModal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4"
        onclick="closeModal()">
        <div class="bg-white rounded-lg max-w-md w-full mx-auto shadow-xl transform transition-all"
            onclick="event.stopPropagation()">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-school text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-[#1a472a]" id="modalTitle">
                        Tambah Divisi Baru
                    </h3>
                </div>
            </div>

            <div class="px-6 py-4">
                <form id="divisiForm" action="{{ route('admin.divisi.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <!-- Nama Divisi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Divisi <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="divisi" id="divisiInput" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32] focus:border-[#2e7d32] transition-colors"
                            placeholder="Contoh: Information Technology" required>
                    </div>
                    <!-- Warna Cover -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Warna Cover</label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                            <!-- Color Picker -->
                            <input type="color" id="colorPicker"
                                class="w-full sm:w-14 h-14 p-1 border border-gray-300 rounded-md cursor-pointer"
                                value="#2e7d32">
                            <!-- HEX Value -->
                            <input type="text" name="cover_color" id="colorHex"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32] focus:border-[#2e7d32] transition-colors"
                                placeholder="#2e7d32">
                        </div>
                    </div>
                </form>
            </div>

            <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">
                    Batal
                </button>
                <button type="submit" form="divisiForm"
                    class="px-4 py-2 bg-[#2e7d32] text-white rounded-md hover:bg-[#1b5e20] transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        /* Responsive adjustments */
        @media (max-width: 640px) {
            .flex-wrap {
                justify-content: center;
            }
        }

        /* Custom scrollbar for table */
        .overflow-x-auto::-webkit-scrollbar {
            height: 6px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Smooth transitions */
        .transition-colors {
            transition: color 0.2s ease, background-color 0.2s ease;
        }

        .transition-shadow {
            transition: box-shadow 0.2s ease;
        }
    </style>
@endpush
@push('scripts')
    <script>
        const picker = document.getElementById('colorPicker');
        const hexInput = document.getElementById('colorHex');

        // Set default HEX saat load
        hexInput.value = picker.value;

        // Update HEX setiap warna berubah (picker to hex)
        picker.addEventListener('input', () => {
            hexInput.value = picker.value;
        });

        // Update picker jika HEX diubah manual (hex to picker, jika valid)
        hexInput.addEventListener('input', () => {
            const hex = hexInput.value.trim();
            if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
                picker.value = hex;
            }
        });

        function openModal() {
            // Remove existing _method if any
            let existingMethod = document.querySelector('#divisiForm input[name="_method"]');
            if (existingMethod) {
                existingMethod.remove();
            }
            // Reset fields
            document.getElementById('divisiModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Tambah Divisi Baru';
            document.getElementById('divisiForm').action = '{{ route('admin.divisi.store') }}';
            document.getElementById('divisiForm').method = 'POST';
            document.getElementById('divisiInput').value = '';
            // Reset color to default
            document.getElementById('colorPicker').value = '#2e7d32';
            document.getElementById('colorHex').value = '#2e7d32';
        }

        function closeModal() {
            document.getElementById('divisiModal').classList.add('hidden');
        }

        function editDivisi(divisi) {
            // Remove existing _method if any, then add PUT
            let existingMethod = document.querySelector('#divisiForm input[name="_method"]');
            if (existingMethod) {
                existingMethod.value = 'PUT';
            } else {
                let methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                document.getElementById('divisiForm').appendChild(methodInput);
            }
            // Set fields
            document.getElementById('divisiModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Edit Divisi';
            document.getElementById('divisiForm').action = '{{ route('admin.divisi.update', ':id') }}'.replace(':id',
                divisi.id);
            document.getElementById('divisiForm').method = 'POST';
            document.getElementById('divisiInput').value = divisi.divisi_name;
            document.getElementById('colorPicker').value = divisi.color_cover;
            document.getElementById('colorHex').value = divisi.color_cover;
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // Bulk delete functionality
        const selectAll = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

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
    </script>
@endpush