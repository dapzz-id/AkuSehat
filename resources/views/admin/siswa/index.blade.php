@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-[#1b5e20] text-xl"></i>
                </div>

                <div>
                    <h2 class="text-2xl font-bold text-[#1a472a]">Data Siswa</h2>
                    <p class="text-[#388e3c]">Manajemen data siswa dan kesehatan</p>
                </div>
            </div>
            <div class="relative w-full md:w-auto">
                <input type="text" placeholder="Cari siswa..." 
                       class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#2e7d32] focus:border-[#2e7d32] transition-colors"
                       id="searchSiswa">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-lg md:text-xl"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-600">Total Siswa</p>
                    <p class="text-xl md:text-2xl font-semibold text-gray-900">{{ $dataCount->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-male text-blue-600 text-lg md:text-xl"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-600">Laki-laki</p>
                    <p class="text-xl md:text-2xl font-semibold text-gray-900">{{ $dataCount->where('jk', 'L')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-female text-pink-600 text-lg md:text-xl"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-600">Perempuan</p>
                    <p class="text-xl md:text-2xl font-semibold text-gray-900">{{ $dataCount->where('jk', 'P')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-heartbeat text-green-600 text-lg md:text-xl"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-600">Ada Data Kesehatan</p>
                    <p class="text-xl md:text-2xl font-semibold text-gray-900">
                        {{ $dataCount->filter(fn($item) => $item->kesehatan->count() > 0)->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[#e8f5e9]">
                    <tr>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            Siswa
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            NIS
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden md:table-cell">
                            Kelas
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            Jenis Kelamin
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden lg:table-cell">
                            Data Kesehatan
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden lg:table-cell">
                            Data HB
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            Aksi
                        </th>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="siswaTable">
                    @forelse($siswa as $item)
                        <tr class="hover:bg-[#f1f8e9] transition-colors duration-150">
                            <td class="px-4 md:px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 md:w-10 md:h-10 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-gray-600 text-xs md:text-sm"></i>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->nama }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-[120px] md:max-w-none">{{ $item->username }}</div>
                                        <div class="text-xs text-gray-500 md:hidden mt-1">
                                            Kelas: {{ $item->kelas->kelas ?? 'Belum Ada' }}
                                        </div>
                                        <div class="text-xs text-gray-500 lg:hidden mt-1">
                                            Kesehatan: {{ $item->kesehatan->count() }} data
                                        </div>
                                        <div class="text-xs text-gray-500 lg:hidden mt-1">
                                            HB: {{ $item->hb->count() }} data
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-4 text-sm text-gray-900">
                                {{ $item->nis }}
                            </td>
                            <td class="px-4 md:px-6 py-4 text-sm text-gray-900 hidden md:table-cell">
                                <div>
                                    <div class="font-medium">{{ $item->kelas->kelas ?? 'Belum Ada' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->kelas->jurusan ?? '' }}</div>
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->jk === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ $item->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-4 hidden lg:table-cell">
                                <div class="flex items-center">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->kesehatan->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $item->kesehatan->count() }} data
                                    </span>
                                    @if($item->kesehatan->count() > 0)
                                        <div class="ml-2 text-xs text-gray-500">
                                            {{ $item->kesehatan()->latest('tgl')->first()->tgl->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-4 hidden lg:table-cell">
                                <div class="flex items-center">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->hb->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $item->hb->count() }} data
                                    </span>
                                    @if($item->hb->count() > 0)
                                        <div class="ml-2 text-xs text-gray-500">
                                            HB: {{ $item->hb()->latest('tgl')->first()->hb }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                @php
                                    $siswaTerakhir = $item->kesehatan()->latest('tgl')->first();
                                @endphp
                                @if($siswaTerakhir)
                                    @php
                                        $statusClass = match($siswaTerakhir->status) {
                                            'Normal' => 'bg-[#c8e6c9] text-[#1b5e20]',
                                            'Kurus' => 'bg-[#fff9c4] text-[#f57f17]',
                                            'Overweight' => 'bg-[#ffe0b2] text-[#ef6c00]',
                                            'Obesitas' => 'bg-[#ffcdd2] text-[#c62828]',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $siswaTerakhir->status }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        -
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 md:px-6 py-4 text-base whitespace-nowrap font-medium">
                                <div class="flex items-center justify-center text-base">
                                    <a href="" 
                                       class="text-[#2e7d32] hover:text-[#1b5e20] p-1 transition-colors duration-200"
                                       title="Edit">
                                        <i class="fas fa-edit text-lg"></i>
                                    </a>
                                    <form action="" 
                                          method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus data kesehatan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[#d32f2f] hover:text-[#b71c1c] p-1 transition-colors duration-200"
                                                title="Hapus">
                                            <i class="fas fa-trash text-lg"></i>
                                        </button>
                                    </form>
                                    <a href="#" 
                                       class="text-[#0288d1] hover:text-[#01579b] p-1 transition-colors duration-200"
                                       title="Detail">
                                        <i class="fas fa-eye text-lg"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-users text-gray-300 text-2xl"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-600">Tidak ada data siswa</p>
                                    <p class="text-sm mt-1 text-gray-500">Data siswa akan muncul di sini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Custom Pagination -->
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between px-6 py-4 bg-[#f9fafb] border-t border-gray-200"">
            <div>
                <p class="text-sm text-gray-700 ">
                    Showing <span class="font-medium">{{ $siswa->firstItem() }}</span>
                    to <span class="font-medium">{{ $siswa->lastItem() }}</span>
                    of <span class="font-medium">{{ $siswa->total() }}</span> results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    {{-- Tombol Previous --}}
                    @if ($siswa->onFirstPage())
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
                        <a href="{{ $siswa->previousPageUrl() }}"
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
                        $currentPage = $siswa->currentPage();
                        $lastPage = $siswa->lastPage();
                        $start = max($currentPage - 2, 1);
                        $end = min($currentPage + 2, $lastPage);
                    @endphp

                    {{-- Halaman pertama --}}
                    @if ($start > 1)
                        <a href="{{ $siswa->url(1) }}"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300  text-sm font-medium {{ $currentPage == 1 ? 'bg-[#2e7d32] text-white' : 'bg-white  text-gray-500  hover:bg-gray-50 ' }}">
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
                                class="z-10 bg-success-50 dark:bg-[#1b5e20] border-[#2e7d32] text-[#1b5e20] dark:text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $siswa->url($page) }}"
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
                        <a href="{{ $siswa->url($lastPage) }}"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300  text-sm font-medium {{ $currentPage == $lastPage ? 'bg-[#2e7d32] text-white' : 'bg-white  text-gray-500  hover:bg-gray-50 ' }}">
                            {{ $lastPage }}
                        </a>
                    @endif

                    {{-- Tombol Next --}}
                    @if ($siswa->hasMorePages())
                        <a href="{{ $siswa->nextPageUrl() }}"
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
@endsection

@push('scripts')
<script>
    document.getElementById('searchSiswa').addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#siswaTable tr');
        
        rows.forEach(row => {
            const nama = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
            const nis = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const kelas = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            
            if (nama.includes(searchTerm) || nis.includes(searchTerm) || kelas.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endpush