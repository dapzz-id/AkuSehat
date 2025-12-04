@extends('layouts.app')

@section('title', 'Tambah Data Kesehatan')

@section('content')
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold text-gray-900">Tambah Data Kesehatan</h2>
            <p class="text-gray-600">Masukkan data kesehatan member</p>
        </div>

        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('admin.kesehatan.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="member-select" class="block text-sm font-medium text-gray-700 mb-2">Member</label>
                        <select id="member-select" name="id_user" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-[#007acc]">
                            <option value="">Pilih Member</option>
                            @foreach ($member as $s)
                                <option value="{{ $s->id }}" {{ old('id_user') == $s->id ? 'selected' : '' }}>
                                    {{ $s->divisi->divisi_name ?? '-' }} •
                                    {{ $s->nama }} ({{ $s->nomor_induk }}) •
                                    {{ $s->jk === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_user')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="bb" value="{{ old('bb') }}" required
                            class="w-full border border-gray-300 rounded-md px-3 py-3.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: 60.5">
                        @error('bb')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tinggi Badan (cm)</label>
                        <input type="number" name="tb" value="{{ old('tb') }}" required
                            class="w-full border border-gray-300 rounded-md px-3 py-3.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: 165">
                        @error('tb')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sistol (mmHg)</label>
                        <input type="number" name="sistol" value="{{ old('sistol') }}" required
                            class="w-full border border-gray-300 rounded-md px-3 py-3.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: 120">
                        @error('sistol')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Diastol (mmHg)</label>
                        <input type="number" name="diastol" value="{{ old('diastol') }}" required
                            class="w-full border border-gray-300 rounded-md px-3 py-3.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Contoh: 80">
                        @error('diastol')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi Telinga</label>
                        <input type="text" name="kondisi_telinga" value="{{ old('kondisi_telinga') }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-3.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Normal / Ada gangguan (kosongkan jika tidak ada)">
                        @error('kondisi_telinga')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi Gigi</label>
                        <input type="text" name="kondisi_gigi" value="{{ old('kondisi_gigi') }}"
                            class="w-full border border-gray-300 rounded-md px-3 py-3.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Sehat / Ada karies / dll (kosongkan jika tidak ada)">
                        @error('kondisi_gigi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Perilaku Beresiko</label>
                        <input name="perilaku_beresiko"
                            class="w-full border border-gray-300 rounded-md px-3 py-3.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Merokok, alkohol, dll (kosongkan jika tidak ada)">{{ old('perilaku_beresiko') }}</input>
                        @error('perilaku_beresiko')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gangguan Reproduksi</label>
                        <textarea name="gangguan_reproduksi" rows="3"
                            class="w-full border border-gray-300 rounded-md px-3 py-3.5 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Gangguan menstruasi, dll (kosongkan jika tidak ada)">{{ old('gangguan_reproduksi') }}</textarea>
                        @error('gangguan_reproduksi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-6">
                    <a href="{{ route('admin.kesehatan.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-[#308a34] text-white rounded-md hover:bg-[#1B5E20] transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        new TomSelect("#member-select", {
            placeholder: "Cari member...",
            allowEmptyOption: true,
            maxOptions: 1000,
        });
    </script>
@endpush