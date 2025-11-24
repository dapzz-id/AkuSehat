@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Tambah User</h1>
        <a href="{{ route('superadmin.users.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-soft p-6">
        <form action="{{ route('superadmin.users.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent @error('nama') border-red-500 @enderror">
                    @error('nama')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent @error('username') border-red-500 @enderror">
                    @error('username')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role <span class="text-red-500">*</span></label>
                    <select name="level" id="level" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent @error('level') border-red-500 @enderror">
                        <option value="">Pilih Role</option>
                        <option value="Admin PMR" {{ old('level') == 'Admin PMR' ? 'selected' : '' }}>Admin PMR</option>
                        <option value="Guru BK" {{ old('level') == 'Guru BK' ? 'selected' : '' }}>Guru BK</option>
                        <option value="Guru Olahraga" {{ old('level') == 'Guru Olahraga' ? 'selected' : '' }}>Guru Olahraga</option>
                        <option value="Siswa" {{ old('level') == 'Siswa' ? 'selected' : '' }}>Siswa</option>
                    </select>
                    @error('level')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="jk" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent @error('jk') border-red-500 @enderror">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('jk') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jk') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jk')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sekolah <span class="text-red-500">*</span></label>
                    <select name="sekolah_id" id="sekolah_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent @error('sekolah_id') border-red-500 @enderror">
                        <option value="">Pilih Sekolah</option>
                        @foreach($sekolah as $s)
                        <option value="{{ $s->id }}" {{ old('sekolah_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nama_sekolah }} - {{ $s->jenjang }}
                        </option>
                        @endforeach
                    </select>
                    @error('sekolah_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div id="nisField" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent @error('nis') border-red-500 @enderror">
                    @error('nis')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div id="tglField" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Lahir</label>
                    <input type="date" name="tgl" value="{{ old('tgl') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent @error('tgl') border-red-500 @enderror">
                    @error('tgl')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div id="kelasField" class="md:col-span-2" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                    <select name="id_kelas" id="id_kelas"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent @error('id_kelas') border-red-500 @enderror">
                        <option value="">Pilih Kelas</option>
                        @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('id_kelas') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                    @error('id_kelas')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end mt-6 space-x-3">
                <a href="{{ route('superadmin.users.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const levelSelect = document.getElementById('level');
    const nisField = document.getElementById('nisField');
    const tglField = document.getElementById('tglField');
    const kelasField = document.getElementById('kelasField');

    function toggleFields() {
        const level = levelSelect.value;
        
        if (level === 'Siswa') {
            nisField.style.display = 'block';
            tglField.style.display = 'block';
            kelasField.style.display = 'block';
        } else {
            nisField.style.display = 'none';
            tglField.style.display = 'none';
            kelasField.style.display = 'none';
        }
    }

    levelSelect.addEventListener('change', toggleFields);
    toggleFields(); // Initial check

    // Initialize Tom Select
    new TomSelect('#sekolah_id', {
        placeholder: 'Pilih Sekolah',
        allowEmptyOption: false,
        create: false
    });

    new TomSelect('#id_kelas', {
        placeholder: 'Pilih Kelas',
        allowEmptyOption: true,
        create: false
    });
});
</script>
@endpush