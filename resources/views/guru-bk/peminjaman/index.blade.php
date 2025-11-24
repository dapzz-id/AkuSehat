@extends('layouts.app')

@section('title', 'Peminjaman Pita')

@section('content')
<div class="space-y-4 md:space-y-6">
    <!-- Header -->
    <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clipboard-list text-pink-600 text-lg md:text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">Peminjaman Pita</h2>
                    <p class="text-xs md:text-sm text-gray-600">Manajemen peminjaman pita siswi</p>
                </div>
            </div>
            <a href="{{ route('guru-bk.peminjaman.create') }}" 
               class="w-full sm:w-auto bg-pink-600 hover:bg-pink-700 text-white px-3 md:px-4 py-1.5 md:py-2 rounded-md text-xs md:text-sm font-medium transition-colors inline-flex items-center justify-center">
                <i class="fas fa-plus text-xs md:text-sm mr-1.5 md:mr-2"></i>
                Pinjam Baru
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-100">
        <div class="flex flex-wrap gap-2">
            <button onclick="filterStatus('all')" 
                    class="filter-btn active px-3 md:px-4 py-1.5 md:py-2 text-xs md:text-sm rounded-md font-medium transition-colors">
                Semua
            </button>
            <button onclick="filterStatus('dipinjam')" 
                    class="filter-btn px-3 md:px-4 py-1.5 md:py-2 text-xs md:text-sm rounded-md font-medium transition-colors">
                Dipinjam
            </button>
            <button onclick="filterStatus('terlambat')" 
                    class="filter-btn px-3 md:px-4 py-1.5 md:py-2 text-xs md:text-sm rounded-md font-medium transition-colors">
                Terlambat
            </button>
            <button onclick="filterStatus('dikembalikan')" 
                    class="filter-btn px-3 md:px-4 py-1.5 md:py-2 text-xs md:text-sm rounded-md font-medium transition-colors">
                Dikembalikan
            </button>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-pink-50">
                    <tr>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-pink-900 uppercase tracking-wider">
                            Siswi
                        </th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-pink-900 uppercase tracking-wider hidden md:table-cell">
                            Tgl Pinjam
                        </th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-pink-900 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-pink-900 uppercase tracking-wider hidden lg:table-cell">
                            Estimasi Selesai
                        </th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-pink-900 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-3 md:px-6 py-2 md:py-3 text-center text-xs font-medium text-pink-900 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="peminjamanTable">
                    @forelse($peminjaman as $item)
                        <tr class="hover:bg-pink-50 transition-colors duration-150" data-status="{{ $item->status }}">
                            <td class="px-3 md:px-6 py-3 md:py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 md:w-10 md:h-10 bg-pink-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-pink-600 text-xs md:text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs md:text-sm font-medium text-gray-900 truncate">{{ $item->user->nama }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->user->nis }}</div>
                                        <div class="text-xs text-gray-500 md:hidden mt-0.5">
                                            {{ $item->tanggal_pinjam->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4 text-xs md:text-sm text-gray-900 hidden md:table-cell">
                                {{ $item->tanggal_pinjam->format('d/m/Y') }}
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4 text-xs md:text-sm text-gray-900">
                                <span class="font-semibold">{{ $item->jumlah_pita }}</span> pita
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4 text-xs md:text-sm text-gray-900 hidden lg:table-cell">
                                {{ $item->estimasi_selesai_haid->format('d/m/Y') }}
                                @if($item->isTerlambat())
                                    <span class="text-red-500 text-xs block mt-0.5">
                                        ({{ $item->hariTerlambat() }} hari terlambat)
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4">
                                @php
                                    $statusClass = match($item->status) {
                                        'dipinjam' => 'bg-blue-100 text-blue-800',
                                        'dikembalikan' => 'bg-green-100 text-green-800',
                                        'terlambat' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-1.5 md:px-2 py-0.5 md:py-1 inline-flex text-xs leading-4 md:leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1 md:gap-2">
                                    @if($item->status !== 'dikembalikan')
                                        <button onclick="konfirmasiKembali({{ $item->id }}, '{{ $item->user->nama }}')"
                                                class="text-green-600 hover:text-green-900 p-1 transition-colors"
                                                title="Kembalikan">
                                            <i class="fas fa-check-circle text-sm md:text-base"></i>
                                        </button>
                                    @endif
                                    
                                    <button onclick="konfirmasiHapus({{ $item->id }}, '{{ $item->user->nama }}')"
                                            class="text-red-600 hover:text-red-900 p-1 transition-colors"
                                            title="Hapus">
                                        <i class="fas fa-trash text-sm md:text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 md:px-6 py-6 md:py-8 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <div class="w-12 h-12 md:w-16 md:h-16 bg-gray-100 rounded-full flex items-center justify-center mb-2 md:mb-3">
                                        <i class="fas fa-clipboard-list text-gray-300 text-xl md:text-2xl"></i>
                                    </div>
                                    <p class="text-sm md:text-lg font-medium text-gray-600">Tidak ada data peminjaman</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($peminjaman->hasPages())
        <div class="px-4 md:px-6 py-3 md:py-4 bg-gray-50 border-t border-gray-200">
            {{ $peminjaman->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Konfirmasi Kembalikan -->
<div id="konfirmasiModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50" onclick="closeModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md" onclick="event.stopPropagation()">
            <div class="p-4 md:p-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check-circle text-green-600 text-lg md:text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">
                            Konfirmasi Pengembalian
                        </h3>
                        <p class="text-xs md:text-sm text-gray-600" id="modalMessage"></p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 md:px-6 flex flex-col-reverse sm:flex-row justify-end gap-2">
                <button type="button" onclick="closeModal()"
                        class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </button>
                <form id="formKembali" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="tanggal_kembali" value="{{ date('Y-m-d') }}">
                    <button type="submit"
                            class="w-full px-4 py-2 text-xs md:text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        Ya, Kembalikan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div id="hapusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50" onclick="closeHapusModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md" onclick="event.stopPropagation()">
            <div class="p-4 md:p-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-600 text-lg md:text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base md:text-lg font-medium text-gray-900 mb-2">
                            Konfirmasi Hapus
                        </h3>
                        <p class="text-xs md:text-sm text-gray-600" id="hapusMessage"></p>
                        <div class="mt-2 p-2 bg-yellow-50 rounded text-xs text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            Data yang sudah dihapus tidak dapat dikembalikan
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 md:px-6 flex flex-col-reverse sm:flex-row justify-end gap-2">
                <button type="button" onclick="closeHapusModal()"
                        class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm font-medium rounded-md border border-gray-300 text-gray-700 bg-white hover:bg-gray-50">
                    Batal
                </button>
                <form id="formHapus" method="POST" class="w-full sm:w-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2 text-xs md:text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.filter-btn {
    background-color: #f3f4f6;
    color: #6b7280;
}
.filter-btn.active {
    background-color: #ec4899;
    color: white;
}
</style>

<script>
function filterStatus(status) {
    const rows = document.querySelectorAll('#peminjamanTable tr[data-status]');
    const buttons = document.querySelectorAll('.filter-btn');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function konfirmasiKembali(id, nama) {
    document.getElementById('modalMessage').textContent = `Apakah pita dari ${nama} sudah dikembalikan?`;
    document.getElementById('formKembali').action = `/guru-bk/peminjaman-pita/${id}/kembali`;
    document.getElementById('konfirmasiModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('konfirmasiModal').classList.add('hidden');
}

function konfirmasiHapus(id, nama) {
    document.getElementById('hapusMessage').textContent = `Apakah Anda yakin ingin menghapus data peminjaman pita A.N ${nama}?`;
    document.getElementById('formHapus').action = `/guru-bk/peminjaman-pita/${id}`;
    document.getElementById('hapusModal').classList.remove('hidden');
}

function closeHapusModal() {
    document.getElementById('hapusModal').classList.add('hidden');
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
        closeHapusModal();
    }
});
</script>
@endsection