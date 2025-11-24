@extends('layouts.app')

@section('title', 'Tambah Sekolah')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Tambah Sekolah</h1>
            <a href="{{ route('superadmin.sekolah.index') }}"
                class="text-center px-3 sm:px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200 text-sm sm:text-base">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-lg shadow-soft p-4 sm:p-6">
            {{-- TAMPILKAN RINGKASAN ERROR VALIDASI --}}
            @if ($errors->any())
                <div class="mb-4 border-l-4 border-red-500 bg-red-50 p-3 text-sm text-red-800">
                    <strong>Ada kesalahan saat menyimpan:</strong>
                    <ul class="mt-1 list-disc list-inside">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('superadmin.sekolah.store') }}" method="POST">
                @csrf
                @method('POST')

                <div class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2">
                    {{-- Nama Sekolah --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah') }}" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base @error('nama_sekolah') border-red-500 @enderror">
                        @error('nama_sekolah')
                            <p class="mt-1 text-xs sm:text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NPSN --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">NPSN <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="npsn" value="{{ old('npsn') }}" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base @error('npsn') border-red-500 @enderror">
                        @error('npsn')
                            <p class="mt-1 text-xs sm:text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenjang --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenjang <span
                                class="text-red-500">*</span></label>
                        <select name="jenjang" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                            <option value="">Pilih Jenjang</option>
                            @foreach (['SMP', 'SMA', 'SMK', 'SLB', 'MTS', 'MA'] as $jenjang)
                                <option value="{{ $jenjang }}" {{ old('jenjang') == $jenjang ? 'selected' : '' }}>
                                    {{ $jenjang }}</option>
                            @endforeach
                        </select>
                        @error('jenjang')
                            <p class="mt-1 text-xs sm:text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Provinsi --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi <span
                                class="text-red-500">*</span></label>
                        @if (!empty($province))
                            <select id="provinsi" name="provinsi" required
                                class="tom-select w-full px-2 py-2 border border-gray-300 rounded-lg text-sm sm:text-base">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($province as $prov)
                                    <option value="{{ $prov['nama_wilayah'] }}" data-code="{{ $prov['kode_wilayah'] }}"
                                        {{ old('provinsi') == ($prov['nama_wilayah'] === 'P A P U A' ? 'PAPUA' : $prov['nama_wilayah']) ? 'selected' : '' }}>
                                        {{ $prov['nama_wilayah'] === 'P A P U A' ? 'PAPUA' : $prov['nama_wilayah'] }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <p class="text-red-500 text-sm">Gagal memuat data provinsi.</p>
                        @endif
                        @error('provinsi')
                            <p class="mt-1 text-xs sm:text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kota --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kota <span
                                class="text-red-500">*</span></label>
                        <select id="kota" name="kota" required disabled
                            class="tom-select w-full px-2 py-2 border border-gray-300 rounded-lg text-sm sm:text-base @error('kota') border-red-500 @enderror">
                            <option value="">Pilih Kota</option>
                        </select>
                        @error('kota')
                            <p class="mt-1 text-xs sm:text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kode Pos --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                        <input type="text" name="kode_pos" value="{{ old('kode_pos') }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                    </div>

                    {{-- No Telepon --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                        <input type="text" name="telepon" value="{{ old('telepon') }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                    </div>

                    {{-- Website --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL Website</label>
                        <input type="text" name="website" value="{{ old('website') }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat <span
                                class="text-red-500">*</span></label>
                        <textarea name="alamat" rows="3" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">{{ old('alamat') }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-end mt-6 space-y-3 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('superadmin.sekolah.index') }}"
                        class="w-full sm:w-auto px-3 sm:px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 text-center text-sm sm:text-base">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto px-3 sm:px-4 py-2 bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 text-sm sm:text-base">
                        <i class="fas fa-save mr-2"></i>Create
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const provSelect = new TomSelect('#provinsi', {
                placeholder: 'Pilih Provinsi',
                render: {
                    option: (data, escape) =>
                        `<div class="text-sm sm:text-base">${escape(data.text)}</div>`,
                    item: (data, escape) => `<div class="text-sm sm:text-base">${escape(data.text)}</div>`
                }
            });

            const kotaSelect = new TomSelect('#kota', {
                placeholder: 'Pilih Kota',
                create: false,
                render: {
                    option: (data, escape) =>
                        `<div class="text-sm sm:text-base">${escape(data.text)}</div>`,
                    item: (data, escape) => `<div class="text-sm sm:text-base">${escape(data.text)}</div>`
                }
            });

            kotaSelect.disable();

            // Ambil nilai old()
            const oldProv = "{{ strtolower(old('provinsi')) }}";
            const oldKota = "{{ strtolower(old('kota')) }}";

            function loadKota(kodeProvinsi, callback = null) {
                fetch(`{{ route('superadmin.sekolah.kabupaten') }}?province_id=${kodeProvinsi}`)
                    .then(response => response.json())
                    .then(data => {
                        kotaSelect.clearOptions();

                        if (data.length > 0) {
                            data.forEach(kab => {
                                kotaSelect.addOption({
                                    value: kab.nama_wilayah,
                                    text: kab.nama_wilayah
                                });
                            });
                            kotaSelect.enable();

                            // Jika ada old('kota'), pilih otomatis
                            if (oldKota) {
                                const found = data.find(kab => kab.nama_wilayah.toLowerCase() === oldKota);
                                if (found) {
                                    kotaSelect.setValue(found.nama_wilayah);
                                }
                            }

                            kotaSelect.refreshOptions(false);
                        } else {
                            kotaSelect.addOption({
                                value: '',
                                text: 'Tidak ada data'
                            });
                        }

                        if (callback) callback();
                    })
                    .catch(err => {
                        kotaSelect.clearOptions();
                        kotaSelect.addOption({
                            value: '',
                            text: 'Gagal memuat data'
                        });
                        kotaSelect.refreshOptions(false);
                        console.error('Gagal memuat data kota:', err);
                    });
            }

            provSelect.on('change', function(value) {
                const optionEl = document.querySelector(`#provinsi option[value="${value}"]`);
                const kodeProvinsi = optionEl ? optionEl.getAttribute('data-code') : null;

                if (!kodeProvinsi) {
                    kotaSelect.clearOptions();
                    kotaSelect.disable();
                    return;
                }

                kotaSelect.disable();
                kotaSelect.clearOptions();
                kotaSelect.addOption({
                    value: '',
                    text: 'Memuat...'
                });
                kotaSelect.refreshOptions(false);

                loadKota(kodeProvinsi);
            });

            // Jika ada old('provinsi'), maka otomatis muat daftar kota-nya
            if (oldProv) {
                const selectedOption = Array.from(document.querySelectorAll('#provinsi option'))
                    .find(opt => opt.value.toLowerCase() === oldProv);

                if (selectedOption) {
                    const kodeProvinsi = selectedOption.getAttribute('data-code');
                    provSelect.setValue(selectedOption.value);
                    loadKota(kodeProvinsi);
                }
            }
        });
    </script>
@endpush
