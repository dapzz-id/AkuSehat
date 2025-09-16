@extends('layouts.app')

@section('title', 'Edit Data HB')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-8 rounded-lg shadow-sm">
        <h2 class="text-2xl font-bold text-gray-900">Edit Data Hemoglobin (HB)</h2>
        <p class="text-gray-600">Perbarui data kadar hemoglobin siswa</p>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <form action="{{ route('admin.hb.update', $hb->id_hb) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Siswa</label>
                    <select name="id_user" required 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Pilih Siswa</option>
                        @foreach($siswa as $s)
                            <option value="{{ $s->id }}" {{ (old('id_user', $hb->id_user) == $s->id) ? 'selected' : '' }}>
                                {{ $s->nama }} ({{ $s->nis }}) - {{ $s->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_user')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                    <select name="id_kelas" required 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Pilih Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ (old('id_kelas', $hb->id_kelas) == $k->id) ? 'selected' : '' }}>
                                {{ $k->kelas }} ({{ $k->jurusan }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_kelas')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pemeriksaan</label>
                    <input type="date" name="tgl" value="{{ old('tgl', $hb->tgl->format('Y-m-d')) }}" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
                    @error('tgl')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kadar HB (g/dL)
                        <span class="text-xs text-gray-500 block">Normal: L = 13-17, P = 12-15</span>
                    </label>
                    <input type="number" step="0.1" name="hb" value="{{ old('hb', $hb->hb) }}" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"
                           placeholder="Contoh: 12.5" min="0" max="25">
                    @error('hb')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-yellow-50 p-4 rounded-md">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Status Saat Ini</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p><strong>HB:</strong> {{ $hb->hb }} g/dL</p>
                            <p><strong>Status:</strong> {{ $hb->status }}</p>
                            <p><strong>Pesan:</strong> {{ $hb->pesan }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('admin.hb.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-[#308a34] text-white rounded-md hover:bg-[#1B5E20] transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Update Data HB
                </button>
            </div>
        </form>
    </div>
</div>
@endsection