@extends('layouts.app')
@section('title', 'Data Users')
@section('content')
    <div class="space-y-4 md:space-y-6">
        <!-- Header -->
        <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-3 md:gap-4">
                <div class="flex items-center gap-2 md:gap-3">
                    <div
                        class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-users text-[#1b5e20] text-lg md:text-xl"></i>
                    </div>

                    <div>
                        <h2 class="text-xl md:text-2xl font-bold text-[#1a472a]">Data Users</h2>
                        <p class="text-xs md:text-sm text-[#388e3c]">Manajemen data users dan kesehatan</p>
                    </div>
                </div>

                <div class="flex flex-col gap-2 w-full lg:w-auto">
                    <div class="flex flex-wrap gap-2">
                        <button onclick="openImportModal()"
                            class="flex-1 sm:flex-none bg-blue-600 hover:bg-blue-700 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-file-import text-xs md:text-sm mr-1.5 md:mr-2"></i>
                            <span>Import</span>
                        </button>

                        <button onclick="openExportModal('excel')"
                            class="flex-1 sm:flex-none bg-green-600 hover:bg-green-700 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-file-excel text-xs md:text-sm mr-1.5 md:mr-2"></i>
                            <span>Export Excel</span>
                        </button>

                        <a href="{{ route('admin.users.create') }}"
                            class="flex-1 sm:flex-none bg-[#2e7d32] hover:bg-[#1b5e20] text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                            <i class="fas fa-plus text-xs md:text-sm mr-1.5 md:mr-2"></i>
                            <span class="hidden sm:inline">Tambah</span>
                            <span class="sm:hidden">Tambah</span>
                        </a>

                        <button id="bulkChangeDivisiBtn" onclick="openChangeDivisiModal()"
                            class="flex-1 sm:flex-none bg-[#0288d1] hover:bg-[#01579b] text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            <i class="fas fa-edit text-xs md:text-sm mr-1.5 md:mr-2"></i>
                            <span>Ubah Divisi Terpilih</span>
                        </button>

                        <button type="submit" form="bulkDeleteForm" id="bulkDeleteBtn"
                            class="flex-1 sm:flex-none bg-[#d32f2f] hover:bg-[#b71c1c] text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                            <i class="fas fa-trash text-xs md:text-sm mr-1.5 md:mr-2"></i>
                            <span>Hapus Terpilih</span>
                        </button>
                    </div>

                    <form id="searchForm" action="{{ route('admin.users.index') }}" method="GET" class="w-full flex-1 mt-1 flex flex-col sm:flex-row gap-2">
                        <div class="relative w-full sm:flex-1">
                            <input type="text" name="search" id="searchInput" value="{{ request('search') }}" placeholder="Cari users..."
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
                        <a href="{{ route('admin.users.index') }}"
                            class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                            Reset
                        </a>
                    </form>
                </div>
            </div>
        </div>

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
                        <p class="text-lg md:text-2xl font-semibold text-gray-900">{{ $dataCount->count() }}</p>
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
                            {{ $dataCount->where('jk', 'L')->count() }}</p>
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
                            {{ $dataCount->where('jk', 'P')->count() }}</p>
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
                            {{ $dataCount->filter(fn($item) => $item->kesehatan->count() > 0)->count() }}
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
                                    $memberTerakhir = $item->kesehatan()->latest('tgl')->first();
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
                                @endphp

                                <tr class="hover:bg-[#f1f8e9] transition-colors duration-150" data-id="{{ $item->id }}"
                                    data-nama="{{ $item->nama }}"
                                    data-divisi="{{ $item->divisi->divisi_name ?? '-' }}">
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
                                                    Divisi: {{ $item->divisi->divisi_name ?? '-' }}
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
                                        class="px-2 sm:px-3 md:px-6 py-2 sm:py-3 md:py-4 text-xs md:text-sm text-gray-900 hidden md:table-cell">
                                        <div class="font-medium">{{ $item->divisi->divisi_name ?? '-' }}</div>
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
                                    <td colspan="8" class="px-4 md:px-6 py-6 md:py-8 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-400">
                                            <div
                                                class="w-12 h-12 md:w-16 md:h-16 bg-gray-100 rounded-full flex items-center justify-center mb-2 md:mb-3">
                                                <i class="fas fa-users text-gray-300 text-xl md:text-2xl"></i>
                                            </div>

                                            <p class="text-sm md:text-lg font-medium text-gray-600">Tidak ada data users
                                            </p>
                                            <p class="text-xs md:text-sm mt-1 text-gray-500">Tambahkan users pertama</p>
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
                                        <option value="{{ $d->id }}">{{ $d->divisi_name }}</option>
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

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeImportModal();
                closeExportModal();
                closeChangeDivisiModal();
            }
        });

        const selectAll = document.getElementById('selectAll');
        const rowCheckboxes = document.querySelectorAll('.rowCheckbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkChangeDivisiBtn = document.getElementById('bulkChangeDivisiBtn');

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
            bulkDeleteBtn.disabled = checkedCount === 0;
            bulkChangeDivisiBtn.disabled = checkedCount === 0;
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
@endsection