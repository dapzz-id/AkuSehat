@extends('layouts.app')

@section('title', 'Edit License Key')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Edit License Key</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola konfigurasi, penetapan instansi, masa aktif, dan kuota license key.</p>
        </div>
        <a href="{{ route('superadmin.license.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-lg">
            <p class="font-semibold text-sm">Ada kesalahan input:</p>
            <ul class="list-disc list-inside text-xs mt-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-soft p-6 border border-gray-100">
        <form action="{{ route('superadmin.license.update', $license->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- License Info Header (Read Only) -->
                <div class="bg-emerald-50/70 border border-emerald-200 p-5 rounded-xl">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">License Key</p>
                            <code class="text-base font-mono font-bold text-emerald-900 mt-1 block">{{ $license->key }}</code>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Instansi Ditugaskan</p>
                            <p class="text-sm font-bold text-emerald-950 mt-1">
                                {{ $license->sekolah->nama_instansi ?? $license->sekolah->nama_sekolah ?? 'Belum Ditugaskan' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Tanggal Berakhir</p>
                            <p class="text-sm font-bold text-emerald-950 mt-1">
                                {{ $license->tanggal_berakhir ? $license->tanggal_berakhir->format('d/m/Y') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Status Saat Ini</p>
                            <div class="mt-1">
                                @if($license->status === 'active')
                                    <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs rounded-full font-semibold">Aktif</span>
                                @else
                                    <span class="px-2.5 py-0.5 bg-rose-100 text-rose-800 border border-rose-200 text-xs rounded-full font-semibold">Expired</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Instansi Penugasan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Instansi / Sekolah Pemilik</label>
                        <select name="instansi_id" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm bg-white">
                            <option value="">-- Belum Ditugaskan (Publik/Trial) --</option>
                            @foreach($instansi as $ins)
                                <option value="{{ $ins->id }}" {{ old('instansi_id', $license->instansi_id) == $ins->id ? 'selected' : '' }}>
                                    {{ $ins->nama_instansi ?? $ins->nama_sekolah }} ({{ $ins->jenis_instansi ?? 'Sekolah' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hubungkan license key ini dengan profil instansi terkait.</p>
                    </div>

                    {{-- Kuota Pengguna --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kuota Pengguna Maksimal <span class="text-red-500">*</span></label>
                        <input type="number" name="kuota_pengguna" value="{{ old('kuota_pengguna', $license->kuota_pengguna) }}" required min="1" max="100000"
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm">
                        <p class="text-xs text-gray-500 mt-1">Jumlah kuota user yang dapat bergabung menggunakan license ini.</p>
                    </div>

                    {{-- Tanggal Berakhir --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Berakhir Masa Aktif <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_berakhir" value="{{ old('tanggal_berakhir', $license->tanggal_berakhir ? $license->tanggal_berakhir->format('Y-m-d') : '') }}" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm">
                        <p class="text-xs text-gray-500 mt-1">Batas kadaluarsa license key.</p>
                    </div>

                    {{-- Status License --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status License <span class="text-red-500">*</span></label>
                        <select name="status" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm bg-white">
                            <option value="active" {{ old('status', $license->status) == 'active' ? 'selected' : '' }}>Aktif (Active)</option>
                            <option value="expired" {{ old('status', $license->status) == 'expired' ? 'selected' : '' }}>Kadaluarsa (Expired)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Ubah status operasional license key.</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-8 space-x-3 pt-5 border-t border-gray-100">
                <a href="{{ route('superadmin.license.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm font-medium">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 text-sm font-semibold shadow-md shadow-emerald-700/20">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection