@extends('layouts.app')

@section('title', 'Edit Data HB')

@section('content')
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold text-gray-900">Edit Data Hemoglobin (HB)</h2>
            <p class="text-gray-600">Perbarui data kadar hemoglobin member</p>
        </div>

        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('admin.hb.update', $hb->id_hb) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="member-select" class="block text-sm font-medium text-gray-700 mb-2">Member</label>
                        <select id="member-select" name="id_user" required disabled
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#007acc]">
                            <option value="">Pilih Member</option>
                            @foreach ($member as $s)
                                <option value="{{ $s->id }}"
                                    {{ old('id_user', $hb->id_user) == $s->id ? 'selected' : '' }}>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Kadar HB (g/dL)
                        </label>
                        <input type="number" step="0.1" name="hb" value="{{ old('hb', $hb->hb) }}" required
                            class="w-full border border-gray-300 rounded-md px-3 py-3.5 focus:outline-none focus:ring-2 focus:ring-[#007acc]"
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

                <div class="bg-blue-50 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Referensi Kadar HB Normal</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside">
                                    <li><strong>Laki-laki:</strong> 13.0 - 17.0 g/dL</li>
                                    <li><strong>Perempuan:</strong> 12.0 - 15.0 g/dL</li>
                                    <li><strong>Anemia:</strong> Di bawah nilai normal</li>
                                    <li><strong>Tinggi:</strong> Di atas nilai normal</li>
                                </ul>
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

@push('scripts')
    <script>
        new TomSelect("#member-select", {
            placeholder: "Cari member...",
            allowEmptyOption: true,
            maxOptions: 1000,
        });
    </script>
@endpush