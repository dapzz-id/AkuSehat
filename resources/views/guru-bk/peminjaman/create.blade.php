@extends('layouts.app')

@section('title', 'Pinjam Pita Baru')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Pinjam Pita Baru</h2>
        <p class="text-gray-600">Catat peminjaman pita untuk siswi</p>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <form action="{{ route('guru-bk.peminjaman.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Siswi</label>
                    <select name="id_user" required 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Pilih Siswi</option>
                        @foreach($siswi as $s)
                            <option value="{{ $s->id }}" {{ old('id_user') == $s->id ? 'selected' : '' }}>
                                {{ $s->nama }} ({{ $s->nis }}) - {{ $s->kelas->kelas ?? 'No Class' }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_user')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tanggal_pinjam')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pita</label>
                    <input type="number" name="jumlah_pita" min="1" value="{{ old('jumlah_pita', 5) }}" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Masukkan jumlah pita">
                    @error('jumlah_pita')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estimasi Selesai Haid</label>
                    <input type="date" name="estimasi_selesai_haid" value="{{ old('estimasi_selesai_haid') }}" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('estimasi_selesai_haid')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <textarea name="keterangan" rows="3"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                @error('keterangan')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('guru-bk.peminjaman.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
// Auto calculate estimated end date (7 days from start)
document.querySelector('input[name="tanggal_pinjam"]').addEventListener('change', function() {
    const startDate = new Date(this.value);
    const endDate = new Date(startDate);
    endDate.setDate(startDate.getDate() + 7);
    
    const estimationField = document.querySelector('input[name="estimasi_selesai_haid"]');
    estimationField.value = endDate.toISOString().split('T')[0];
});
@endpush