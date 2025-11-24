@extends('layouts.app')

@section('title', 'Edit License Key')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-800">Edit License Key</h1>
        <a href="{{ route('superadmin.license.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-soft p-6">
        <form action="{{ route('superadmin.license.update', $license->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- License Info (Read Only) -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-blue-800">Sekolah</p>
                            <p class="text-lg font-semibold text-blue-900">{{ $license->sekolah->nama_sekolah }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">License Key</p>
                            <code class="text-lg font-mono font-semibold text-blue-900">{{ $license->license_key }}</code>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Periode</p>
                            <p class="text-sm text-blue-900">
                                {{ $license->tanggal_mulai->format('d/m/Y') }} - {{ $license->tanggal_berakhir->format('d/m/Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Durasi</p>
                            <p class="text-sm text-blue-900">{{ $license->durasi_bulan }} Bulan</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent @error('status') border-red-500 @enderror">
                        <option value="aktif" {{ old('status', $license->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="expired" {{ old('status', $license->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="nonaktif" {{ old('status', $license->status) == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Ubah status license (Aktif/Expired/Nonaktif)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                    <textarea name="keterangan" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $license->keterangan) }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-400 mt-1 mr-3"></i>
                        <div>
                            <p class="text-sm text-yellow-700 font-semibold mb-1">Informasi:</p>
                            <ul class="text-sm text-yellow-600 list-disc list-inside space-y-1">
                                <li>License key dan periode tidak dapat diubah</li>
                                <li>Untuk perpanjang license, gunakan fitur "Renew" di halaman index</li>
                                <li>Status dapat diubah sesuai kebutuhan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-6 space-x-3">
                <a href="{{ route('superadmin.license.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection