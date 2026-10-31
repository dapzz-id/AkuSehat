@extends('layouts.app')

@section('title', 'Edit Instansi / Institusi')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Edit Instansi / Institusi</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Ubah profil dan konfigurasi instansi atau entitas.</p>
            </div>
            <a href="{{ route('superadmin.instansi.index') }}"
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

            <form action="{{ route('superadmin.instansi.update', $sekolah->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="active">

                <div class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2">
                    {{-- Section Header: Klasifikasi & Profil Institusi --}}
                    <div class="md:col-span-2 flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                                <i class="fas fa-landmark"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-800">Klasifikasi & Profil Institusi</h2>
                                <p class="text-xs text-gray-500">Kelola dan perbarui klasifikasi entitas resmi dan identitas legalitas institusi.</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <i class="fas fa-shield-halved text-emerald-600"></i>
                            <span>Wewenang SuperAdmin</span>
                        </span>
                    </div>

                    {{-- Jenis Instansi --}}
                    <div class="md:col-span-2 bg-emerald-50/60 p-4 rounded-xl border border-emerald-100">
                        <label class="block text-sm font-semibold text-emerald-900 mb-2">
                            <i class="fas fa-building mr-1.5 text-emerald-600"></i>Jenis Instansi / Entitas <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis_instansi" id="jenis_instansi" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-emerald-300 rounded-lg bg-white focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base font-medium text-gray-800">
                            @php
                                $currentJenis = old('jenis_instansi', $sekolah->jenis_instansi ?? 'Sekolah');
                            @endphp
                            <option value="Sekolah" {{ $currentJenis == 'Sekolah' ? 'selected' : '' }}>Sekolah (SD / SMP / SMA / SMK / Madrasah)</option>
                            <option value="Universitas" {{ $currentJenis == 'Universitas' ? 'selected' : '' }}>Universitas / Perguruan Tinggi</option>
                            <option value="Perusahaan" {{ $currentJenis == 'Perusahaan' ? 'selected' : '' }}>Perusahaan Swasta / Korporat (Corporate Wellness)</option>
                            <option value="Pemerintah" {{ $currentJenis == 'Pemerintah' ? 'selected' : '' }}>Instansi Pemerintah / BUMN / Kedinasan</option>
                            <option value="Klinik" {{ $currentJenis == 'Klinik' ? 'selected' : '' }}>Fasilitas Kesehatan / Klinik / Rumah Sakit</option>
                            <option value="Lainnya" {{ $currentJenis == 'Lainnya' ? 'selected' : '' }}>Komunitas / Yayasan / Lainnya</option>
                        </select>
                    </div>

                    {{-- Nama Sekolah / Instansi --}}
                    <div>
                        <label id="label_nama_instansi" class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah / Instansi <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_instansi" id="nama_instansi" value="{{ old('nama_instansi', old('nama_sekolah', $sekolah->nama_instansi ?? $sekolah->nama_sekolah)) }}"
                            required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base @error('nama_instansi') border-red-500 @enderror">
                        @error('nama_instansi')
                            <p class="mt-1 text-xs sm:text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NPSN / Kode Instansi --}}
                    <div>
                        <label id="label_npsn" class="block text-sm font-medium text-gray-700 mb-2">NPSN / Kode Instansi <span
                                id="npsn_required_star" class="text-red-500">*</span></label>
                        <input type="text" name="npsn" id="npsn" value="{{ old('npsn', $sekolah->npsn) }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base @error('npsn') border-red-500 @enderror">
                        @error('npsn')
                            <p class="mt-1 text-xs sm:text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenjang / Kategori --}}
                    <div id="container_jenjang">
                        <label id="label_jenjang" class="block text-sm font-medium text-gray-700 mb-2">Jenjang / Kategori <span
                                id="jenjang_required_star" class="text-red-500">*</span></label>
                        <select name="jenjang" id="jenjang"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                            <option value="">Pilih Jenjang</option>
                            @foreach (['SMP', 'SMA', 'SMK', 'SLB', 'MTS', 'MA', 'SD'] as $jenjang)
                                <option value="{{ $jenjang }}"
                                    {{ old('jenjang', $sekolah->jenjang) == $jenjang ? 'selected' : '' }}>
                                    {{ $jenjang }}</option>
                            @endforeach
                            <option value="Perguruan Tinggi" {{ old('jenjang', $sekolah->jenjang) == 'Perguruan Tinggi' ? 'selected' : '' }}>Perguruan Tinggi</option>
                            <option value="Umum" {{ old('jenjang', $sekolah->jenjang) == 'Umum' || old('jenjang', $sekolah->jenjang) == 'Non-Pendidikan' ? 'selected' : '' }}>Umum / Non-Pendidikan</option>
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
                                        {{ old('provinsi', $sekolah->provinsi) == ($prov['nama_wilayah'] === 'P A P U A' ? 'PAPUA' : $prov['nama_wilayah']) ? 'selected' : '' }}>
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
                        <input type="text" name="kode_pos" value="{{ old('kode_pos', $sekolah->kode_pos) }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                    </div>

                    {{-- No Telepon --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">No. Telepon</label>
                        <input type="text" name="telepon" value="{{ old('telepon', $sekolah->telepon) }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $sekolah->email) }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                    </div>

                    {{-- Website --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">URL Website</label>
                        <input type="text" name="website" value="{{ old('website', $sekolah->website) }}"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat <span
                                class="text-red-500">*</span></label>
                        <textarea name="alamat" rows="3" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">{{ old('alamat', $sekolah->alamat) }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-end mt-6 space-y-3 sm:space-y-0 sm:space-x-3">
                    <a href="{{ route('superadmin.instansi.index') }}"
                        class="w-full sm:w-auto px-3 sm:px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 text-center text-sm sm:text-base">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto px-3 sm:px-4 py-2 bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 text-sm sm:text-base">
                        <i class="fas fa-save mr-2"></i>Update
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
                placeholder: 'Pilih Provinsi'
            });
            const kotaSelect = new TomSelect('#kota', {
                placeholder: 'Pilih Kota'
            });
            kotaSelect.disable();

            const oldProvinsi = "{{ strtolower(old('provinsi', $sekolah->provinsi)) }}";
            const oldKota = "{{ strtolower(old('kota', $sekolah->kota)) }}";

            if (oldProvinsi) {
                const selectedOption = Array.from(document.querySelectorAll('#provinsi option')).find(opt =>
                    opt.value.toLowerCase() === oldProvinsi
                );
                if (selectedOption) {
                    provSelect.setValue(selectedOption.value);
                    const kodeProvinsi = selectedOption.getAttribute('data-code');
                    fetch(`{{ route('superadmin.instansi.kabupaten') }}?province_id=${kodeProvinsi}`)
                        .then(res => res.json())
                        .then(data => {
                            kotaSelect.clearOptions();
                            data.forEach(kab => {
                                kotaSelect.addOption({
                                    value: kab.nama_wilayah,
                                    text: kab.nama_wilayah
                                });
                            });
                            kotaSelect.enable();
                            kotaSelect.refreshOptions(false);
                            const selectedKota = data.find(kab => kab.nama_wilayah.toLowerCase() === oldKota);
                            if (selectedKota) kotaSelect.setValue(selectedKota.nama_wilayah);
                        });
                }
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

                fetch(`{{ route('superadmin.instansi.kabupaten') }}?province_id=${kodeProvinsi}`)
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
                        } else {
                            kotaSelect.addOption({
                                value: '',
                                text: 'Tidak ada data'
                            });
                        }
                        kotaSelect.refreshOptions(false);
                    })
                    .catch(err => {
                        kotaSelect.clearOptions();
                        kotaSelect.addOption({
                            value: '',
                            text: 'Gagal memuat data'
                        });
                        kotaSelect.refreshOptions(false);
                        console.error(err);
                    });
            });
        });
    </script>
@endpush
