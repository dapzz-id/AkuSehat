@extends('layouts.app')

@section('title', 'Data HB')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tint text-red-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-[#1a472a]">Data Hemoglobin (HB)</h2>
                    <p class="text-[#388e3c]">Manajemen data hemoglobin siswa</p>
                </div>
            </div>
            <a href="{{ route('admin.hb.create') }}" 
               class="bg-[#2e7d32] hover:bg-[#1b5e20] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Data HB
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#e8f5e9]">
                    <tr>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            Siswa
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden md:table-cell">
                            Kelas
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            HB (g/dL)
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden lg:table-cell">
                            Pesan
                        </th>
                        <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($hb as $item)
                        <tr class="hover:bg-[#f1f8e9] transition-colors duration-150">
                            <!-- Siswa -->
                            <td class="px-4 md:px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $item->user->nama }}</div>
                                <div class="text-gray-500 text-xs">{{ $item->user->nis }} ({{ $item->user->jk === 'L' ? 'L' : 'P' }})</div>
                                <div class="text-sm text-gray-500 md:hidden mt-1">
                                    Kelas: {{ $item->kelas->kelas }}
                                </div>
                                <div class="text-sm text-gray-500 lg:hidden mt-1">
                                    Pesan: {{ Str::limit($item->pesan, 30) }}
                                </div>
                            </td>
                            <!-- Kelas -->
                            <td class="px-4 md:px-6 py-4 text-gray-700 hidden md:table-cell">
                                {{ $item->kelas->kelas }}
                            </td>
                            <!-- Tanggal -->
                            <td class="px-4 md:px-6 py-4 text-gray-700">
                                {{ $item->tgl->format('d/m/Y') }}
                            </td>
                            <!-- HB -->
                            <td class="px-4 md:px-6 py-4 font-semibold text-gray-900">
                                {{ $item->hb }}
                            </td>
                            <!-- Status -->
                            <td class="px-4 md:px-6 py-4">
                                @php
                                    $statusClass = match($item->status) {
                                        'Normal' => 'bg-[#c8e6c9] text-[#1b5e20]',
                                        'Anemia' => 'bg-[#ffcdd2] text-[#c62828]',
                                        'Tinggi' => 'bg-[#ffe0b2] text-[#ef6c00]',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $statusClass }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <!-- Pesan -->
                            <td class="px-4 md:px-6 py-4 text-gray-600 max-w-xs hidden lg:table-cell" title="{{ $item->pesan }}">
                                {{ $item->pesan }}
                            </td>
                            <!-- Aksi -->
                            <td class="px-4 md:px-6 py-4">
                                <div class="flex items-center justify-center text-base">
                                    <a href="{{ route('admin.hb.edit', $item->id_hb) }}" 
                                       class="text-[#2e7d32] hover:text-[#1b5e20] transition-colors duration-200 p-2 rounded-full hover:bg-green-50"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.hb.destroy', $item->id_hb) }}" method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus data HB ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-[#d32f2f] hover:text-[#b71c1c] transition-colors duration-200 p-2 rounded-full hover:bg-red-50"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <a href="#" 
                                       class="text-[#0288d1] hover:text-[#01579b] transition-colors duration-200 p-2 rounded-full hover:bg-blue-50"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-tint text-red-300 text-2xl"></i>
                                    </div>
                                    <p class="text-lg font-medium">Belum ada data HB</p>
                                    <p class="text-sm mt-1">Klik "Tambah Data HB" untuk menambahkan data baru</p>
                                    <a href="{{ route('admin.hb.create') }}" 
                                       class="mt-3 inline-flex items-center bg-[#2e7d32] hover:bg-[#1b5e20] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                        <i class="fas fa-plus mr-2"></i>
                                        Tambah Data HB
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between px-6 py-4">
            <div>
                <p class="text-sm text-gray-700 ">
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
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300  bg-gray-100  text-sm font-medium text-gray-500 dark:text-gray-400">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $hb->previousPageUrl() }}"
                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300  bg-white  text-sm font-medium text-gray-500  hover:bg-gray-50 ">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
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
                        $start = max($currentPage - 2, 1);
                        $end = min($currentPage + 2, $lastPage);
                    @endphp

                    {{-- Halaman pertama --}}
                    @if ($start > 1)
                        <a href="{{ $hb->url(1) }}"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300  text-sm font-medium {{ $currentPage == 1 ? 'bg-primary-500 text-white' : 'bg-white  text-gray-500  hover:bg-gray-50 ' }}">
                            1
                        </a>
                        @if ($start > 2)
                            <span
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300  bg-gray-100  text-sm font-medium text-gray-500 dark:text-gray-400">…</span>
                        @endif
                    @endif

                    {{-- Halaman di sekitar current --}}
                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $currentPage)
                            <span
                                class="z-10 bg-primary-50 dark:bg-primary-900 border-primary-500 dark:border-primary-500 text-primary-600 dark:text-primary-200 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $hb->url($page) }}"
                                class="bg-white  border-gray-300  text-gray-500  hover:bg-gray-50  relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    {{-- Halaman terakhir --}}
                    @if ($end < $lastPage)
                        @if ($end < $lastPage - 1)
                            <span
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300  bg-gray-100  text-sm font-medium text-gray-500 dark:text-gray-400">…</span>
                        @endif
                        <a href="{{ $hb->url($lastPage) }}"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300  text-sm font-medium {{ $currentPage == $lastPage ? 'bg-primary-500 text-white' : 'bg-white  text-gray-500  hover:bg-gray-50 ' }}">
                            {{ $lastPage }}
                        </a>
                    @endif

                    {{-- Tombol Next --}}
                    @if ($hb->hasMorePages())
                        <a href="{{ $hb->nextPageUrl() }}"
                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300  bg-white  text-sm font-medium text-gray-500  hover:bg-gray-50 ">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span
                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300  bg-gray-100  text-sm font-medium text-gray-500 dark:text-gray-400">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
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

<style>
    /* Custom pagination styling */
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .pagination li {
        margin: 0 0.25rem;
    }
    
    .pagination li a,
    .pagination li span {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        color: #4b5563;
        font-size: 0.875rem;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .pagination li a:hover {
        background-color: #e8f5e9;
        border-color: #2e7d32;
        color: #2e7d32;
    }
    
    .pagination li.active span {
        background-color: #2e7d32;
        border-color: #2e7d32;
        color: white;
    }
    
    /* Responsive adjustments */
    @media (max-width: 640px) {
        .pagination {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .pagination li {
            margin: 0;
        }
    }
</style>
@endsection