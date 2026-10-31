@extends('layouts.app')

@section('title', 'Generate License Key')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Generate License Key</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Buat kode lisensi baru untuk sekolah, universitas, atau korporasi.</p>
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
        <form action="{{ route('superadmin.license.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Kode License --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kode License Key <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <input type="text" name="key" id="licenseKeyInput" value="{{ old('key', $generatedKey) }}" required
                                class="w-full px-3.5 py-2.5 font-mono font-semibold border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm bg-gray-50/50">
                            <button type="button" onclick="generateNewKey()" class="px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg border border-gray-300 text-xs font-semibold flex items-center gap-1.5 whitespace-nowrap">
                                <i class="fas fa-arrows-rotate"></i> Acak
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Format default unik standar sistem AkuSehat.</p>
                    </div>

                    {{-- Instansi Penugasan --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tugaskan ke Instansi / Sekolah</label>
                        <select name="instansi_id" class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm bg-white">
                            <option value="">-- Belum Ditugaskan (Publik/Trial) --</option>
                            @foreach($instansi as $ins)
                                <option value="{{ $ins->id }}" {{ old('instansi_id') == $ins->id ? 'selected' : '' }}>
                                    {{ $ins->nama_instansi ?? $ins->nama_sekolah }} ({{ $ins->jenis_instansi ?? 'Sekolah' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Dapat dikosongkan jika license akan didistribusikan nanti.</p>
                    </div>

                    {{-- Kuota Pengguna --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kuota Pengguna Maksimal <span class="text-red-500">*</span></label>
                        <input type="number" name="kuota_pengguna" value="{{ old('kuota_pengguna', 500) }}" required min="1" max="100000"
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm">
                        <p class="text-xs text-gray-500 mt-1">Jumlah user maksimal yang diizinkan untuk lisensi ini.</p>
                    </div>

                    {{-- Tanggal Berakhir --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Berakhir Masa Aktif <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_berakhir" value="{{ old('tanggal_berakhir', now()->addYear()->format('Y-m-d')) }}" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm">
                        <p class="text-xs text-gray-500 mt-1">Default 1 tahun dari hari ini.</p>
                    </div>

                    {{-- Status License --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status License <span class="text-red-500">*</span></label>
                        <select name="status" required
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm bg-white">
                            <option value="active" selected>Aktif (Active)</option>
                            <option value="expired">Kadaluarsa (Expired)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end mt-8 space-x-3 pt-5 border-t border-gray-100">
                <a href="{{ route('superadmin.license.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors duration-200 text-sm font-medium">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 text-sm font-semibold shadow-md shadow-emerald-700/20">
                    <i class="fas fa-key mr-2"></i>Generate License
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function generateNewKey() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        function segment(len) {
            let res = '';
            for (let i = 0; i < len; i++) res += chars.charAt(Math.floor(Math.random() * chars.length));
            return res;
        }
        const key = 'RDVZ-' + segment(4) + '-' + segment(4) + '-' + segment(4);
        document.getElementById('licenseKeyInput').value = key;
    }
</script>
@endpush
