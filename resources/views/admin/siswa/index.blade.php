@extends('layouts.app')

@section('title', 'Data Siswa')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Data Siswa</h2>
        <div class="flex space-x-2">
            <div class="relative">
                <input type="text" placeholder="Cari siswa..." 
                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       id="searchSiswa">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Siswa</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $dataCount->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-male text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Laki-laki</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $dataCount->where('jk', 'L')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-female text-pink-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Perempuan</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $dataCount->where('jk', 'P')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-heartbeat text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Ada Data Kesehatan</p>
                    <p class="text-2xl font-semibold text-gray-900">
                        {{ $dataCount->filter(fn($item) => $item->kesehatan->count() > 0)->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Siswa
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            NIS
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kelas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            JK
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data Kesehatan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data HB
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status Terakhir
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($siswa as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->nama }}</div>
                                        <div class="text-sm text-gray-500">{{ $item->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item->nis }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>
                                    <div class="font-medium">{{ $item->kelas->kelas ?? 'Belum Ada' }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->kelas->jurusan ?? '' }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->jk === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ $item->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->kesehatan->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $item->kesehatan->count() }} data
                                    </span>
                                    @if($item->kesehatan->count() > 0)
                                        <div class="ml-2 text-xs text-gray-500">
                                            Terakhir: {{ $item->kesehatan()->latest('tgl')->first()->tgl->format('d/m/Y') }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->hb->count() > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $item->hb->count() }} data
                                    </span>
                                    @if($item->hb->count() > 0)
                                        <div class="ml-2 text-xs text-gray-500">
                                            HB: {{ $item->hb()->latest('tgl')->first()->hb }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $kesehatanTerakhir = $item->kesehatan()->latest('tgl')->first();
                                @endphp
                                @if($kesehatanTerakhir)
                                    @php
                                        $statusClass = match($kesehatanTerakhir->status) {
                                            'Normal' => 'bg-green-100 text-green-800',
                                            'Kurus' => 'bg-yellow-100 text-yellow-800',
                                            'Overweight' => 'bg-orange-100 text-orange-800',
                                            'Obesitas' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                        {{ $kesehatanTerakhir->status }}
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Belum Ada Data
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center py-8">
                                    <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                                    <p>Tidak ada data siswa</p>
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
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300  text-sm font-medium {{ $currentPage == $lastPage ? 'bg-primary-500 text-white' : 'bg-white  text-gray-500  hover:bg-gray-50 ' }}">
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

<script>
// Simple search functionality
document.getElementById('searchSiswa').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
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
@endsection