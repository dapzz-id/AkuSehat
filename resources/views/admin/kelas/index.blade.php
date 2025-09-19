@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[#1a472a]">Data Kelas</h2>
                <p class="text-[#388e3c]">Manajemen data kelas dan jurusan</p>
            </div>
            <button onclick="openModal()" 
                    class="bg-[#2e7d32] hover:bg-[#1b5e20] text-white px-4 py-2 rounded-md text-sm font-medium transition-colors flex items-center justify-center shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                <i class="fas fa-plus"></i>
                <span class="ml-2">Tambah Kelas</span>
            </button>
        </div>
    </div>

    <!-- Statistics Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-school text-green-600 text-lg md:text-xl"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-600">Total Kelas</p>
                    <p class="text-xl md:text-2xl font-semibold text-gray-900">{{ $kelas->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-lg md:text-xl"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-600">Total Siswa</p>
                    <p class="text-xl md:text-2xl font-semibold text-gray-900">{{ $kelas->sum('jumlah_siswa') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-purple-600 text-lg md:text-xl"></i>
                </div>
                <div class="ml-3 md:ml-4">
                    <p class="text-xs md:text-sm font-medium text-gray-600">Jurusan</p>
                    <p class="text-xl md:text-2xl font-semibold text-gray-900">
                        {{ $kelas->groupBy('jurusan')->count() }}
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
                            Nama Kelas
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            Jurusan
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden md:table-cell">
                            Jumlah Siswa
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden lg:table-cell">
                            Data Kesehatan
                        </th>
                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-[#1b5e20] uppercase tracking-wider hidden xl:table-cell">
                            Tanggal Dibuat
                        </th>
                        <th class="px-4 md:px-6 py-3 text-center text-xs font-medium text-[#1b5e20] uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($kelas as $item)
                        <tr class="hover:bg-[#f1f8e9] transition-colors duration-150">
                            <td class="px-4 md:px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $item->kelas }}</div>
                                <div class="text-xs text-gray-500 md:hidden mt-1">
                                    Siswa: {{ $item->jumlah_siswa }}
                                </div>
                                <div class="text-xs text-gray-500 lg:hidden mt-1">
                                    Kesehatan: {{ $item->count_kesehatan }} data
                                </div>
                                <div class="text-xs text-gray-500 xl:hidden mt-1">
                                    Dibuat: {{ $item->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                @php
                                    $classColor = '';
                                    if (str_contains(strtolower($item->kelas), 'rpl')) $classColor = 'rpl';
                                    elseif (str_contains(strtolower($item->kelas), 'tkj')) $classColor = 'tkj';
                                    elseif (str_contains(strtolower($item->kelas), 'dkv')) $classColor = 'dkv';
                                    elseif (str_contains(strtolower($item->kelas), 'transmisi')) $classColor = 'transmisi';
                                @endphp
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $classColor }}">
                                    {{ $item->jurusan }}
                                </span>
                            </td>
                            <td class="px-4 md:px-6 py-4 text-sm text-gray-900 hidden md:table-cell">
                                <div class="flex items-center">
                                    <i class="fas fa-users text-gray-400 mr-2"></i>
                                    {{ $item->jumlah_siswa }} siswa
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-4 text-sm text-gray-900 hidden lg:table-cell">
                                <div class="flex items-center">
                                    <i class="fas fa-heartbeat text-gray-400 mr-2"></i>
                                    {{ $item->count_kesehatan }} data
                                </div>
                            </td>
                            <td class="px-4 md:px-6 py-4 text-sm text-gray-500 hidden xl:table-cell">
                                {{ $item->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-4 md:px-6 py-4">
                                <div class="flex items-center justify-center text-base gap-2">
                                    <button onclick="editKelas({{ json_encode($item) }})" 
                                            class="text-[#2e7d32] hover:text-[#1b5e20] p-2 rounded-full hover:bg-green-50 transition-colors" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ route('admin.siswa.index') }}?kelas={{ $item->id }}" 
                                       class="text-[#0288d1] hover:text-[#01579b] p-2 rounded-full hover:bg-blue-50 transition-colors" title="Lihat Siswa">
                                        <i class="fas fa-users"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                        <i class="fas fa-school text-gray-300 text-2xl"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-600">Tidak ada data kelas</p>
                                    <p class="text-sm mt-1 text-gray-500">Klik "Tambah Kelas" untuk menambahkan data baru</p>
                                    <button onclick="openModal()" class="mt-3 text-[#2e7d32] hover:text-[#1b5e20] font-medium">
                                        Tambah Kelas
                                    </button>
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
                    Showing <span class="font-medium">{{ $kelas->firstItem() }}</span>
                    to <span class="font-medium">{{ $kelas->lastItem() }}</span>
                    of <span class="font-medium">{{ $kelas->total() }}</span> results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    {{-- Tombol Previous --}}
                    @if ($kelas->onFirstPage())
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
                        <a href="{{ $kelas->previousPageUrl() }}"
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
                        $currentPage = $kelas->currentPage();
                        $lastPage = $kelas->lastPage();
                        $start = max($currentPage - 2, 1);
                        $end = min($currentPage + 2, $lastPage);
                    @endphp

                    {{-- Halaman pertama --}}
                    @if ($start > 1)
                        <a href="{{ $kelas->url(1) }}"
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
                            <a href="{{ $kelas->url($page) }}"
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
                        <a href="{{ $kelas->url($lastPage) }}"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300  text-sm font-medium {{ $currentPage == $lastPage ? 'bg-[#2e7d32] text-white' : 'bg-white  text-gray-500  hover:bg-gray-50 ' }}">
                            {{ $lastPage }}
                        </a>
                    @endif

                    {{-- Tombol Next --}}
                    @if ($kelas->hasMorePages())
                        <a href="{{ $kelas->nextPageUrl() }}"
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

<!-- Modal Tambah/Edit Kelas -->
<div id="kelasModal" class="fixed inset-0 bg-gray-600/40 bg-opacity-50 hidden z-50 flex items-center justify-center p-4" onclick="closeModal()">
    <div class="bg-white rounded-lg max-w-md w-full mx-auto shadow-xl transform transition-all" onclick="event.stopPropagation()">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-school text-green-600"></i>
                </div>
                <h3 class="text-lg font-medium text-[#1a472a]" id="modalTitle">
                    Tambah Kelas Baru
                </h3>
            </div>
        </div>
        
        <div class="px-6 py-4">
            <form id="kelasForm" action="{{ route('admin.kelas.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas</label>
                    <input type="text" name="kelas" id="kelasInput" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32] focus:border-[#2e7d32] transition-colors"
                           placeholder="Contoh: X TKJ 1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
                    <select name="jurusan" id="jurusanSelect" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32] focus:border-[#2e7d32] transition-colors">
                        <option value="">Pilih Jurusan</option>
                        <option value="RPL">Rekayasa Perangkat Lunak (RPL)</option>
                        <option value="DKV">Desain Komunikasi Visual (DKV)</option>
                        <option value="TRANSMISI">Transmisi Telekomunikasi (TELCO)</option>
                        <option value="TKJ">Teknik Komputer dan Jaringan (TKJ)</option>
                    </select>
                </div>
            </form>
        </div>
        
        <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
            <button type="button" onclick="closeModal()"
                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">
                Batal
            </button>
            <button type="submit" form="kelasForm"
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
    .rpl {
        background: linear-gradient(135deg, #e0b300, #b38f00);
        color: #ffffff;
    }

    .tkj {
        background: linear-gradient(135deg, #007acc, #005fa3);
        color: #ffffff;
    }

    .dkv {
        background: linear-gradient(135deg, #cc0066, #99004d);
        color: #ffffff;
    }

    .transmisi {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: #ffffff;
    }

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
    function openModal() {
        document.getElementById('kelasModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Tambah Kelas Baru';
        document.getElementById('kelasForm').action = '{{ route("admin.kelas.store") }}';
        document.getElementById('kelasForm').method = 'POST';
        document.getElementById('kelasInput').value = '';
        document.getElementById('jurusanSelect').value = '';
    }

    function closeModal() {
        document.getElementById('kelasModal').classList.add('hidden');
    }

    function editKelas(kelas) {
        document.getElementById('kelasModal').classList.remove('hidden');
        document.getElementById('modalTitle').textContent = 'Edit Kelas';
        document.getElementById('kelasForm').action = '{{ route("admin.kelas.update", ":id") }}'.replace(':id', kelas.id);
        document.getElementById('kelasForm').method = 'POST';
        document.getElementById('kelasForm').innerHTML += '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('kelasInput').value = kelas.kelas;
        document.getElementById('jurusanSelect').value = kelas.jurusan;
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
</script>
@endpush