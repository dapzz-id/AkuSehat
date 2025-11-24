@extends('layouts.app')

@section('title', 'Dashboard Guru BK')

@section('content')
<div class="space-y-4 md:space-y-6">
    <!-- Header -->
    <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-nurse text-pink-600 text-lg md:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-900">Dashboard Guru BK</h2>
                <p class="text-xs md:text-sm text-gray-600">Selamat datang, {{ auth()->user()->nama }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 md:gap-6">
        <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-female text-pink-600 text-lg md:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Total Siswi</p>
                    <p class="text-lg md:text-2xl font-semibold text-gray-900">{{ $data['total_siswi'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clipboard-list text-blue-600 text-lg md:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Pinjaman Aktif</p>
                    <p class="text-lg md:text-2xl font-semibold text-gray-900">{{ $data['pinjaman_aktif'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-3 md:p-6 rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex flex-col md:flex-row md:items-center gap-2">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-600 text-lg md:text-xl"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm font-medium text-gray-600">Terlambat</p>
                    <p class="text-lg md:text-2xl font-semibold text-gray-900">{{ $data['warning_count'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Warning Alert -->
    @if($data['warning_count'] > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 md:p-4 rounded">
            <div class="flex gap-2 md:gap-3">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400 text-sm md:text-base"></i>
                </div>
                <div>
                    <p class="text-xs md:text-sm text-yellow-700">
                        <strong>Perhatian!</strong> Ada {{ $data['warning_count'] }} peminjaman pita yang sudah melewati estimasi waktu pengembalian.
                        <a href="{{ route('guru-bk.warning') }}" class="font-medium underline hover:text-yellow-800">
                            Lihat detail
                        </a>
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm border border-gray-100">
        <h3 class="text-base md:text-lg font-semibold text-gray-900 mb-3 md:mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4">
            <a href="{{ route('guru-bk.peminjaman.create') }}" 
               class="flex items-center p-3 md:p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <i class="fas fa-plus text-blue-600 text-lg md:text-xl mr-2 md:mr-3"></i>
                <span class="text-xs md:text-sm font-medium text-blue-800">Pinjam Pita Baru</span>
            </a>
            
            <a href="{{ route('guru-bk.peminjaman.index') }}" 
               class="flex items-center p-3 md:p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <i class="fas fa-list text-green-600 text-lg md:text-xl mr-2 md:mr-3"></i>
                <span class="text-xs md:text-sm font-medium text-green-800">Lihat Semua Peminjaman</span>
            </a>
            
            <a href="{{ route('guru-bk.warning') }}" 
               class="flex items-center p-3 md:p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-lg md:text-xl mr-2 md:mr-3"></i>
                <span class="text-xs md:text-sm font-medium text-yellow-800">Cek Warning</span>
            </a>
        </div>
    </div>
</div>
@endsection