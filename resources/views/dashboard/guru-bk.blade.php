@extends('layouts.app')

@section('title', 'Dashboard Guru BK')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Dashboard Guru BK</h2>
        <p class="text-gray-600">Selamat datang, {{ auth()->user()->nama }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-female text-pink-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Siswi</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $data['total_siswi'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pinjaman Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $data['pinjaman_aktif'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Terlambat</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $data['pinjaman_terlambat'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-bell text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Warning</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $data['warning_count'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Warning Alert -->
    @if($data['warning_count'] > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
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
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('guru-bk.peminjaman.create') }}" 
               class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                <i class="fas fa-plus text-blue-600 text-xl mr-3"></i>
                <span class="font-medium text-blue-800">Pinjam Pita Baru</span>
            </a>
            
            <a href="{{ route('guru-bk.peminjaman.index') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                <i class="fas fa-list text-green-600 text-xl mr-3"></i>
                <span class="font-medium text-green-800">Lihat Semua Peminjaman</span>
            </a>
            
            <a href="{{ route('guru-bk.warning') }}" 
               class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                <span class="font-medium text-yellow-800">Cek Warning</span>
            </a>
        </div>
    </div>
</div>
@endsection