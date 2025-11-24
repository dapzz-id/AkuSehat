@extends('layouts.app')

@section('title', 'Tambah Member')

@section('content')
<div class="space-y-4 md:space-y-6">
    <div class="flex items-center gap-2 md:gap-3">
        <a href="{{ route('admin.member.index') }}" class="text-gray-600 hover:text-gray-900 p-1 md:p-0">
            <i class="fas fa-arrow-left text-sm md:text-base"></i>
        </a>
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-gray-900">Tambah Member Baru</h2>
            <p class="text-xs md:text-sm text-gray-600">Masukkan data member baru</p>
        </div>
    </div>

    <div class="bg-white shadow-sm rounded-lg">
        <form action="{{ route('admin.member.store') }}" method="POST" class="p-4 md:p-6 space-y-4 md:space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                        NIS <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nis" value="{{ old('nis') }}" required
                           class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-1.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                           placeholder="Contoh: 001234">
                    @error('nis')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                           class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-1.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                           placeholder="Contoh: Ahmad Pratama">
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                        Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                           class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-1.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                           placeholder="Contoh: ahmad001">
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" required
                           class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-1.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                           placeholder="Minimal 6 karakter">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                        Divisi
                    </label>
                    <select name="id_kelas"
                            class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-1.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32]">
                        <option value="">Pilih Divisi</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('id_kelas') == $k->id ? 'selected' : '' }}>
                                {{ $k->kelas }} ({{ $k->jurusan }})
                            </option>
                        @endforeach
                    </select>
                    @error('id_kelas')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-3 md:gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="jk" value="L" {{ old('jk') == 'L' ? 'checked' : '' }} required
                                   class="form-radio text-[#2e7d32] focus:ring-[#2e7d32]">
                            <span class="ml-1.5 md:ml-2 text-xs md:text-sm">Laki-laki</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="jk" value="P" {{ old('jk') == 'P' ? 'checked' : '' }} required
                                   class="form-radio text-[#2e7d32] focus:ring-[#2e7d32]">
                            <span class="ml-1.5 md:ml-2 text-xs md:text-sm">Perempuan</span>
                        </label>
                    </div>
                    @error('jk')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-blue-50 p-3 md:p-4 rounded-md">
                <div class="flex gap-2 md:gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400 text-sm md:text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-xs md:text-sm font-medium text-blue-800 mb-1 md:mb-2">Informasi</h3>
                        <div class="text-xs md:text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-0.5 md:space-y-1">
                                <li>NIS dan Username harus unik (tidak boleh sama dengan member lain)</li>
                                <li>Password akan di-enkripsi otomatis</li>
                                <li>Member dapat login menggunakan username dan password ini</li>
                                <li>Divisi bisa dikosongkan jika belum ditentukan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 md:gap-3 pt-4 md:pt-6">
                <a href="{{ route('admin.member.index') }}" 
                   class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm border border-gray-300 rounded-md text-center text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm bg-[#2e7d32] text-white rounded-md hover:bg-[#1b5e20] transition-colors">
                    <i class="fas fa-save mr-1.5 text-xs"></i>
                    Simpan Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection