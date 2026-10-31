@extends('layouts.app')

@section('title', 'Tambah Instansi / Institusi')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Tambah Instansi / Institusi</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Daftarkan Sekolah, Universitas, Perusahaan, Lembaga Pemerintahan, atau Fasilitas Kesehatan ke dalam ekosistem AkuSehat.</p>
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

            <form action="{{ route('superadmin.instansi.store') }}" method="POST">
                @csrf
                @method('POST')

                <div class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2">
                    {{-- Section Header: Klasifikasi & Profil Institusi --}}
                    <div class="md:col-span-2 flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                                <i class="fas fa-landmark"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-800">Klasifikasi & Profil Institusi</h2>
                                <p class="text-xs text-gray-500">Tentukan klasifikasi entitas resmi dan identitas institusi yang terverifikasi.</p>
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
                            <option value="Sekolah" {{ old('jenis_instansi', 'Sekolah') == 'Sekolah' ? 'selected' : '' }}>Sekolah (SD / SMP / SMA / SMK / Madrasah)</option>
                            <option value="Universitas" {{ old('jenis_instansi') == 'Universitas' ? 'selected' : '' }}>Universitas / Institut / Perguruan Tinggi</option>
                            <option value="Perusahaan" {{ old('jenis_instansi') == 'Perusahaan' ? 'selected' : '' }}>Perusahaan Swasta / Korporat (Corporate Wellness)</option>
                            <option value="Pemerintah" {{ old('jenis_instansi') == 'Pemerintah' ? 'selected' : '' }}>Instansi Pemerintah / BUMN / Kedinasan</option>
                            <option value="Klinik" {{ old('jenis_instansi') == 'Klinik' ? 'selected' : '' }}>Fasilitas Kesehatan / Klinik / Rumah Sakit</option>
                            <option value="Lainnya" {{ old('jenis_instansi') == 'Lainnya' ? 'selected' : '' }}>Komunitas / Yayasan / Lainnya</option>
                        </select>
                        <p class="text-xs text-emerald-700 mt-1.5">Setiap jenis instansi memiliki penyesuaian hak akses dan modul operasional yang fleksibel.</p>
                    </div>

                    {{-- Nama Instansi --}}
                    <div>
                        <label id="label_nama_instansi" class="block text-sm font-medium text-gray-700 mb-2">Nama Sekolah / Instansi <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nama_instansi" id="nama_instansi" value="{{ old('nama_instansi', old('nama_sekolah')) }}" required
                            placeholder="cth: SMK Telesandi Bekasi / PT Maju Jaya"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base @error('nama_instansi') border-red-500 @enderror">
                        @error('nama_instansi')
                            <p class="mt-1 text-xs sm:text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- NPSN / Kode Instansi --}}
                    <div>
                        <label id="label_npsn" class="block text-sm font-medium text-gray-700 mb-2">
                            <span>NPSN</span> <span id="npsn_required_star" class="text-red-500">*</span>
                        </label>
                        <input type="text" name="npsn" id="npsn" value="{{ old('npsn') }}" required
                            placeholder="cth: 20108901 atau KODE_INSTANSI"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base @error('npsn') border-red-500 @enderror">
                        <p id="npsn_hint" class="text-xs text-gray-500 mt-1">Digunakan juga sebagai username login Admin Instansi default.</p>
                        @error('npsn')
                            <p class="mt-1 text-xs sm:text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenjang / Kategori --}}
                    <div id="container_jenjang">
                        <label id="label_jenjang" class="block text-sm font-medium text-gray-700 mb-2">Jenjang / Kategori <span
                                id="jenjang_required_star" class="text-red-500">*</span></label>
                        <select name="jenjang" id="jenjang" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                            <option value="">Pilih Jenjang</option>
                            @foreach (['SMP', 'SMA', 'SMK', 'SLB', 'MTS', 'MA', 'SD'] as $j)
                                <option value="{{ $j }}" {{ old('jenjang') == $j ? 'selected' : '' }}>
                                    {{ $j }}</option>
                            @endforeach
                            <option value="Perguruan Tinggi" {{ old('jenjang') == 'Perguruan Tinggi' ? 'selected' : '' }}>Perguruan Tinggi (Diploma / Sarjana)</option>
                            <option value="Umum" {{ old('jenjang') == 'Umum' ? 'selected' : '' }}>Umum / Non-Pendidikan</option>
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
                    <a href="{{ route('superadmin.instansi.index') }}"
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
            const jenisSelect = document.getElementById('jenis_instansi');
            const labelNama = document.getElementById('label_nama_instansi');
            const labelNpsn = document.getElementById('label_npsn');
            const inputNpsn = document.getElementById('npsn');
            const npsnStar = document.getElementById('npsn_required_star');
            const npsnHint = document.getElementById('npsn_hint');
            const selectJenjang = document.getElementById('jenjang');
            const jenjangStar = document.getElementById('jenjang_required_star');
            const containerJenjang = document.getElementById('container_jenjang');

            function updateFormForJenis(jenis) {
                if (jenis === 'Sekolah') {
                    labelNama.innerHTML = 'Nama Sekolah <span class="text-red-500">*</span>';
                    labelNpsn.innerHTML = 'NPSN (Nomor Pokok Sekolah Nasional) <span class="text-red-500">*</span>';
                    inputNpsn.placeholder = 'cth: 20108901';
                    inputNpsn.required = true;
                    if (npsnStar) npsnStar.style.display = 'inline';
                    if (jenjangStar) jenjangStar.style.display = 'inline';
                    selectJenjang.required = true;
                    npsnHint.innerText = 'NPSN resmi Kemendikbudristek / Kemenag. Digunakan sebagai username admin.';
                } else if (jenis === 'Universitas') {
                    labelNama.innerHTML = 'Nama Perguruan Tinggi / Universitas <span class="text-red-500">*</span>';
                    labelNpsn.innerHTML = 'Kode Kampus / Singkatan (Opsional)';
                    inputNpsn.placeholder = 'cth: UNIV-UI (kosongkan jika ingin digenerate otomatis)';
                    inputNpsn.required = false;
                    if (npsnStar) npsnStar.style.display = 'none';
                    if (jenjangStar) jenjangStar.style.display = 'none';
                    selectJenjang.required = false;
                    selectJenjang.value = 'Perguruan Tinggi';
                    npsnHint.innerText = 'Kode ID kampus atau biarkan kosong untuk digenerate otomatis.';
                } else if (jenis === 'Perusahaan') {
                    labelNama.innerHTML = 'Nama Perusahaan / Korporasi <span class="text-red-500">*</span>';
                    labelNpsn.innerHTML = 'Kode Perusahaan / ID Instansi (Opsional)';
                    inputNpsn.placeholder = 'cth: TELKOM / CORP-01 (kosongkan jika otomatis)';
                    inputNpsn.required = false;
                    if (npsnStar) npsnStar.style.display = 'none';
                    if (jenjangStar) jenjangStar.style.display = 'none';
                    selectJenjang.required = false;
                    selectJenjang.value = 'Umum';
                    npsnHint.innerText = 'Kode entitas atau kosongkan untuk auto-generate akun login.';
                } else {
                    labelNama.innerHTML = 'Nama Instansi / Lembaga <span class="text-red-500">*</span>';
                    labelNpsn.innerHTML = 'Kode Registrasi / ID Instansi (Opsional)';
                    inputNpsn.placeholder = 'cth: KODE-01 (kosongkan jika otomatis)';
                    inputNpsn.required = false;
                    if (npsnStar) npsnStar.style.display = 'none';
                    if (jenjangStar) jenjangStar.style.display = 'none';
                    selectJenjang.required = false;
                    selectJenjang.value = 'Umum';
                    npsnHint.innerText = 'Kode unik instansi untuk login Admin Instansi default.';
                }
            }

            if (jenisSelect) {
                jenisSelect.addEventListener('change', function() {
                    updateFormForJenis(this.value);
                });
                updateFormForJenis(jenisSelect.value);
            }

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
