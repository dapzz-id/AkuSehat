@extends('layouts.app')

@section('title', 'Pinjam Pita Baru')

@section('content')
<div class="space-y-4 md:space-y-6">
    <div class="flex items-center gap-2 md:gap-3">
        <a href="{{ route('guru-bk.peminjaman.index') }}" class="text-gray-600 hover:text-gray-900 p-1 md:p-0">
            <i class="fas fa-arrow-left text-sm md:text-base"></i>
        </a>
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-900">Pinjam Pita Baru</h2>
            <p class="text-xs md:text-sm text-gray-600">Catat peminjaman pita untuk siswi yang sedang haid</p>
        </div>
    </div>

    @if($siswi->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 md:p-6">
            <div class="flex gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-400 text-lg"></i>
                </div>
                <div>
                    <h3 class="text-sm md:text-base font-medium text-yellow-800 mb-2">
                        Tidak Ada Siswi yang Dapat Meminjam Pita
                    </h3>
                    <div class="text-xs md:text-sm text-yellow-700 space-y-1">
                        <p>Tidak ada siswi yang memenuhi kriteria untuk meminjam pita saat ini.</p>
                        <p class="font-medium mt-2">Kriteria peminjaman:</p>
                        <ul class="list-disc list-inside ml-2 space-y-1">
                            <li>Siswi perempuan (Gender: P)</li>
                            <li>Memiliki data haid yang sedang berlangsung</li>
                            <li>Data haid dimulai hari ini atau kemarin</li>
                            <li>Belum memiliki peminjaman pita aktif</li>
                            <li>Estimasi selesai haid belum lewat</li>
                        </ul>
                    </div>
                    <a href="{{ route('guru-bk.peminjaman.index') }}" 
                       class="mt-4 inline-flex items-center px-4 py-2 text-xs md:text-sm bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali ke Daftar Peminjaman
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('guru-bk.peminjaman.store') }}" method="POST" class="p-4 md:p-6 space-y-4 md:space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 gap-4 md:gap-6">
                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                            Pilih Siswi <span class="text-red-500">*</span>
                        </label>
                        <select name="id_user" id="siswaSelect" required 
                                class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-1.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-pink-500"
                                onchange="updateInfo()">
                            <option value="">-- Pilih Siswi yang Sedang Haid --</option>
                            @foreach($siswi as $s)
                                @php
                                    $dataHaid = $s->dataHaid->first();
                                    $tanggalMulai = optional($dataHaid)->tanggal_mulai;
                                    $estimasi = optional($dataHaid)->tanggal_selesai;
                                @endphp
                                <option value="{{ $s->id }}" 
                                        data-mulai="{{ $tanggalMulai ? $tanggalMulai->format('d/m/Y') : '' }}"
                                        data-estimasi="{{ $estimasi ? $estimasi->format('d/m/Y') : '' }}"
                                        data-hari="{{ $tanggalMulai ? $tanggalMulai->diffInDays(now()) : '' }}"
                                        {{ old('id_user') == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama }} ({{ $s->nis }}) - {{ $s->kelas->kelas ?? 'No Class' }} 
                                    | Haid: {{ $tanggalMulai ? $tanggalMulai->format('d/m/Y') . ' - ' . $estimasi->format('d/m/Y') : 'Belum ada data' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_user')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Siswi Terpilih -->
                    <div id="infoSiswi" class="hidden bg-blue-50 p-3 md:p-4 rounded-md">
                        <div class="flex gap-2 md:gap-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400 text-sm md:text-base"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xs md:text-sm font-medium text-blue-800 mb-2">Informasi Data Haid</h3>
                                <div class="text-xs md:text-sm text-blue-700 space-y-1">
                                    <p>• Tanggal Mulai Haid: <span id="infoMulai" class="font-semibold"></span></p>
                                    <p>• Estimasi Selesai: <span id="infoEstimasi" class="font-semibold"></span></p>
                                    <p>• Hari ke: <span id="infoHari" class="font-semibold"></span></p>
                                    <p class="mt-2 text-xs italic">* Tanggal pinjam akan otomatis diset hari ini</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                            Jumlah Pita <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="jumlah_pita" min="1" max="20" value="{{ old('jumlah_pita', 1) }}" required
                               class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-1.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-pink-500"
                               placeholder="Masukkan jumlah pita (1-20)">
                        @error('jumlah_pita')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                            Keterangan
                        </label>
                        <textarea name="keterangan" rows="3"
                                  class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-1.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-pink-500"
                                  placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="bg-yellow-50 p-3 md:p-4 rounded-md border border-yellow-200">
                    <div class="flex gap-2 md:gap-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-sm md:text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-xs md:text-sm font-medium text-yellow-800 mb-1 md:mb-2">Catatan Penting</h3>
                            <div class="text-xs md:text-sm text-yellow-700 space-y-1">
                                <p>• Hanya siswi yang sedang dalam masa haid yang dapat meminjam pita</p>
                                <p>• Data haid harus dimulai hari ini atau kemarin</p>
                                <p>• Tanggal pinjam otomatis diset hari ini ({{ now()->format('d/m/Y') }})</p>
                                <p>• Estimasi selesai diambil dari data haid (7 hari dari tanggal mulai)</p>
                                <p>• Siswi akan mendapat warning jika belum mengembalikan setelah estimasi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 md:gap-3 pt-4 md:pt-6">
                    <a href="{{ route('guru-bk.peminjaman.index') }}" 
                       class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm border border-gray-300 rounded-md text-center text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm bg-pink-600 text-white rounded-md hover:bg-pink-700 transition-colors">
                        <i class="fas fa-save mr-1.5 text-xs"></i>
                        Simpan Data Peminjaman
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

<script>
function updateInfo() {
    const select = document.getElementById('siswaSelect');
    const option = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('infoSiswi');
    
    if (option.value) {
        document.getElementById('infoMulai').textContent = option.dataset.mulai || '-';
        document.getElementById('infoEstimasi').textContent = option.dataset.estimasi || '-';
        document.getElementById('infoHari').textContent = option.dataset.hari ? 'Hari ke-' + (parseInt(option.dataset.hari) + 1) : '-';
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
}

// Update info on page load if old value exists
window.addEventListener('load', function() {
    updateInfo();
});
</script>
@endsection
