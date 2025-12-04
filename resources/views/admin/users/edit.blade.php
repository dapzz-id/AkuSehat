@extends('layouts.app')

@section('title', 'Edit Users')

@section('content')
    <div class="space-y-4 md:space-y-6">
        <div class="flex items-center gap-2 md:gap-3">
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900 p-1 md:p-0">
                <i class="fas fa-arrow-left text-sm md:text-base"></i>
            </a>
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-900">Edit Data Users</h2>
                <p class="text-xs md:text-sm text-gray-600">Perbarui data users</p>
            </div>
        </div>

        <div class="bg-white shadow-sm rounded-lg">
            <form action="{{ route('admin.users.update', $member->id) }}" method="POST"
                class="p-4 md:p-6 space-y-4 md:space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                            Nomor Induk <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nomor_induk" value="{{ old('nomor_induk', $member->nomor_induk) }}"
                            required
                            class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-3.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                            placeholder="Contoh: 001234">
                        @error('nomor_induk')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama" value="{{ old('nama', $member->nama) }}" required
                            class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-3.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32]">
                        @error('nama')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username" value="{{ old('username', $member->username) }}" required
                            class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-3.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32]">
                        @error('username')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                            Password Baru <span class="text-xs text-gray-500">(Kosongkan jika tidak ingin mengubah)</span>
                        </label>
                        <input type="password" name="password"
                            class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-3.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                            placeholder="Minimal 6 karakter">
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                            Divisi
                        </label>
                        <select id="divisi-select" name="id_divisi"
                            class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-1.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32]">
                            <option value="">Pilih Divisi</option>
                            @foreach ($divisi as $k)
                                <option value="{{ $k->id }}"
                                    {{ old('id_divisi', $member->id_divisi) == $k->id ? 'selected' : '' }}>
                                    {{ $k->divisi_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_divisi')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs md:text-sm font-medium text-gray-700 mb-1.5 md:mb-2">
                            Peran
                        </label>
                        <select id="peran-select" name="level"
                            class="w-full text-sm md:text-base border border-gray-300 rounded-md px-3 py-1.5 md:py-2 focus:outline-none focus:ring-2 focus:ring-[#2e7d32]">
                            <option value="">Pilih Peran</option>
                            <option value="Admin" {{ old('level', $member->level) == 'Admin' ? 'selected' : '' }}>
                                Admin
                            </option>
                            <option value="Health Consultant"
                                {{ old('level', $member->level) == 'Health Consultant' ? 'selected' : '' }}>
                                Health Consultant
                            </option>
                            <option value="Health Monitor"
                                {{ old('level', $member->level) == 'Health Monitor' ? 'selected' : '' }}>
                                Health Monitor
                            </option>
                            <option value="Member" {{ old('level', $member->level) == 'Member' ? 'selected' : '' }}>
                                Member
                            </option>
                        </select>
                        @error('level')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs md:text-sm font-medium text-gray-700 mb-2">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <!-- Laki-laki -->
                        <label
                            class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition
                            hover:bg-green-50
                            {{ old('jk') == 'L' ? 'border-green-600 bg-green-50' : 'border-gray-300' }}">
                            <input type="radio" name="jk" value="L" required
                                class="h-7 w-5 text-green-700 focus:ring-green-600"
                                {{ old('jk', $member->jk) == 'L' ? 'checked' : '' }}>
                            <span class="text-sm md:text-base font-medium">Laki-laki</span>
                        </label>
                        <!-- Perempuan -->
                        <label
                            class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition
                            hover:bg-green-50
                            {{ old('jk') == 'P' ? 'border-green-600 bg-green-50' : 'border-gray-300' }}">
                            <input type="radio" name="jk" value="P" required
                                class="h-7 w-5 text-green-700 focus:ring-green-600"
                                {{ old('jk', $member->jk) == 'P' ? 'checked' : '' }}>
                            <span class="text-sm md:text-base font-medium">Perempuan</span>
                        </label>
                    </div>
                    @error('jk')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Stats Info -->
                <div class="bg-yellow-50 p-3 md:p-4 rounded-md">
                    <div class="flex gap-2 md:gap-3">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-yellow-400 text-sm md:text-base"></i>
                        </div>
                        <div>
                            <h3 class="text-xs md:text-sm font-medium text-yellow-800 mb-1 md:mb-2">Statistik Users</h3>
                            <div class="text-xs md:text-sm text-yellow-700 space-y-0.5 md:space-y-1">
                                <p>• Data Kesehatan: <span class="font-medium">{{ $member->kesehatan->count() }}
                                        data</span></p>
                                <p>• Data HB: <span class="font-medium">{{ $member->hb->count() }} data</span></p>
                                <p>• Terdaftar sejak: <span
                                        class="font-medium">{{ $member->created_at->format('d/m/Y') }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row justify-end gap-2 md:gap-3 pt-4 md:pt-6">
                    <a href="{{ route('admin.users.index') }}"
                        class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm border border-gray-300 rounded-md text-center text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto px-4 py-2 text-xs md:text-sm bg-[#2e7d32] text-white rounded-md hover:bg-[#1b5e20] transition-colors">
                        <i class="fas fa-save mr-1.5 text-xs"></i>
                        Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        new TomSelect("#divisi-select", {
            placeholder: "Cari divisi...",
            allowEmptyOption: true,
            maxOptions: 1000,
        });

        new TomSelect("#peran-select", {
            placeholder: "Cari peran...",
            allowEmptyOption: true,
            maxOptions: 1000,
        });
    </script>
@endpush