@extends('layouts.app')

@section('title', 'Warning Peminjaman Terlambat')

@section('content')
<div class="space-y-4 md:space-y-6">
    <!-- Header -->
    <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-lg md:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-900">Warning Peminjaman Terlambat</h2>
                <p class="text-xs md:text-sm text-gray-600">Daftar siswi yang terlambat mengembalikan pita</p>
            </div>
        </div>
    </div>

    @if($terlambat->count() > 0)
        <!-- Alert Warning -->
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 md:p-4">
            <div class="flex gap-2 md:gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400 text-sm md:text-base"></i>
                </div>
                <div>
                    <h3 class="text-xs md:text-sm font-medium text-red-800 mb-1 md:mb-2">
                        Ada {{ $terlambat->count() }} peminjaman yang terlambat!
                    </h3>
                    <div class="text-xs md:text-sm text-red-700">
                        <p>Segera hubungi siswi berikut untuk mengembalikan pita atau konfirmasi status mereka.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-red-900 uppercase tracking-wider">
                                Siswi
                            </th>
                            <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-red-900 uppercase tracking-wider hidden md:table-cell">
                                Tgl Pinjam
                            </th>
                            <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-red-900 uppercase tracking-wider hidden lg:table-cell">
                                Estimasi Selesai
                            </th>
                            <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-red-900 uppercase tracking-wider">
                                Terlambat
                            </th>
                            <th class="px-3 md:px-6 py-2 md:py-3 text-left text-xs font-medium text-red-900 uppercase tracking-wider hidden sm:table-cell">
                                Jumlah
                            </th>
                            <th class="px-3 md:px-6 py-2 md:py-3 text-center text-xs font-medium text-red-900 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($terlambat as $item)
                            <tr class="hover:bg-red-50 transition-colors duration-150">
                                <td class="px-3 md:px-6 py-3 md:py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 md:w-10 md:h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-red-600 text-xs md:text-sm"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-xs md:text-sm font-medium text-gray-900 truncate">{{ $item->user->nama }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->user->nis }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->user->kelas->kelas ?? 'No Class' }}</div>
                                            <div class="text-xs text-gray-500 md:hidden mt-0.5">
                                                Pinjam: {{ $item->tanggal_pinjam->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 md:px-6 py-3 md:py-4 text-xs md:text-sm text-gray-900 hidden md:table-cell">
                                    {{ $item->tanggal_pinjam->format('d/m/Y') }}
                                </td>
                                <td class="px-3 md:px-6 py-3 md:py-4 text-xs md:text-sm text-gray-900 hidden lg:table-cell">
                                    {{ $item->estimasi_selesai_haid->format('d/m/Y') }}
                                </td>
                                <td class="px-3 md:px-6 py-3 md:py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="px-1.5 md:px-2 py-0.5 md:py-1 inline-flex text-xs leading-4 md:leading-5 font-semibold rounded-full bg-red-100 text-red-800 w-fit">
                                            {{ floor($item->hariTerlambat()) }} hari
                                        </span>
                                        <span class="text-xs text-gray-500 lg:hidden">
                                            Est: {{ $item->estimasi_selesai_haid->format('d/m') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 md:px-6 py-3 md:py-4 text-xs md:text-sm text-gray-900 hidden sm:table-cell">
                                    <span class="font-semibold">{{ $item->jumlah_pita }}</span> pita
                                </td>
                                <td class="px-3 md:px-6 py-3 md:py-4 whitespace-nowrap">
                                    <div class="flex justify-center">
                                        <button onclick="konfirmasiKembali({{ $item->id }}, '{{ $item->user->nama }}')"
                                                class="bg-green-600 hover:bg-green-700 text-white px-2 md:px-3 py-1 md:py-1.5 rounded text-xs md:text-sm transition-colors">
                                            <i class="fas fa-check-circle mr-1 text-xs"></i>
                                            <span class="hidden sm:inline">Kembalikan</span>
                                            <span class="sm:hidden">OK</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
            <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-100">
                <div class="flex items-center gap-2 md:gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs md:text-sm text-gray-600">Total Terlambat</p>
                        <p class="text-lg md:text-2xl font-bold text-red-600">{{ $terlambat->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-100">
                <div class="flex items-center gap-2 md:gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock text-orange-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs md:text-sm text-gray-600">Rata-rata Keterlambatan</p>
                        <p class="text-lg md:text-2xl font-bold text-orange-600">
                            {{ floor($terlambat->avg(fn($item) => $item->hariTerlambat()) ?? 0) }} hari
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-3 md:p-4 rounded-lg shadow-sm border border-gray-100">
                <div class="flex items-center gap-2 md:gap-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-ribbon text-yellow-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs md:text-sm text-gray-600">Total Pita Belum Kembali</p>
                        <p class="text-lg md:text-2xl font-bold text-yellow-600">{{ $terlambat->sum('jumlah_pita') }}</p>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- No Data State -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 md:p-6">
            <div class="flex flex-col items-center justify-center text-center py-6 md:py-8">
                <div class="w-16 h-16 md:w-20 md:h-20 bg-green-100 rounded-full flex items-center justify-center mb-3 md:mb-4">
                    <i class="fas fa-check-circle text-green-400 text-3xl md:text-4xl"></i>
                </div>
                <h3 class="text-base md:text-lg font-medium text-green-800 mb-1 md:mb-2">
                    Tidak ada peminjaman yang terlambat
                </h3>
                <p class="text-xs md:text-sm text-green-700">
                    Semua peminjaman pita dalam status normal atau sudah dikembalikan.
                </p>
                <a href="{{ route('guru-bk.peminjaman.index') }}" 
                   class="mt-3 md:mt-4 px-4 py-2 text-xs md:text-sm bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    Lihat Semua Peminjaman
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Modal Konfirmasi -->
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
                        <div class="mt-2 p-2 bg-yellow-50 rounded text-xs text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            Pita ini sudah terlambat dikembalikan
                        </div>
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

<script>
function konfirmasiKembali(id, nama) {
    document.getElementById('modalMessage').textContent = `Apakah pita dari ${nama} sudah dikembalikan?`;
    document.getElementById('formKembali').action = `/guru-bk/peminjaman-pita/${id}/kembali`;
    document.getElementById('konfirmasiModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('konfirmasiModal').classList.add('hidden');
}

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>
@endsection