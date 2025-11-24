@extends('layouts.app')

@section('title', 'Dashboard SuperAdmin')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800">Dashboard Super Admin</h1>
        <div class="text-xs sm:text-sm lg:text-base text-gray-600">
            <i class="far fa-calendar-alt mr-2"></i>
            {{ now()->isoFormat('dddd, D MMMM Y') }}
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2 xl:grid-cols-4">
        <!-- Total Sekolah Card -->
        <div class="bg-white rounded-lg shadow-soft p-4 sm:p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Sekolah</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-gray-900">{{ $totalSekolah }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-blue-100 rounded-full">
                    <i class="text-xl sm:text-2xl text-blue-600 fas fa-school"></i>
                </div>
            </div>
        </div>

        <!-- License Aktif Card -->
        <div class="bg-white rounded-lg shadow-soft p-4 sm:p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">License Aktif</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-gray-900">{{ $licenseAktif }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-green-100 rounded-full">
                    <i class="text-xl sm:text-2xl text-green-600 fas fa-key"></i>
                </div>
            </div>
        </div>

        <!-- License Expired Card -->
        <div class="bg-white rounded-lg shadow-soft p-4 sm:p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">License Expired</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-gray-900">{{ $licenseExpired }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-yellow-100 rounded-full">
                    <i class="text-xl sm:text-2xl text-yellow-600 fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>

        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-soft p-4 sm:p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600">Total Users</p>
                    <p class="mt-1 sm:mt-2 text-2xl sm:text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
                </div>
                <div class="p-2 sm:p-3 bg-purple-100 rounded-full">
                    <i class="text-xl sm:text-2xl text-purple-600 fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- License Akan Expired Warning -->
    @if($licenseAkanExpired->count() > 0)
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 sm:p-4 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-400 mt-0.5 sm:mt-1 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                <div class="flex-1">
                    <h3 class="text-base sm:text-lg font-semibold text-yellow-800 mb-1 sm:mb-2">Peringatan License</h3>
                    <p class="text-xs sm:text-sm text-yellow-700 mb-2 sm:mb-3">Terdapat {{ $licenseAkanExpired->count() }} license yang akan expired dalam 30 hari:</p>
                    <div class="space-y-2">
                        @foreach($licenseAkanExpired as $license)
                        <div class="bg-white p-2 sm:p-3 rounded border border-yellow-200">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                <div class="flex-1">
                                    <p class="text-sm sm:text-base font-semibold text-gray-800 break-words">{{ $license->sekolah->nama_sekolah ?? "-" }}</p>
                                    <p class="text-xs sm:text-sm flex md:flex-row flex-col md:items-center text-gray-600 break-all">License Key: <code class="px-2 py-1 text-center mt-1 ml-0 md:ml-2 md:mt-0 bg-gray-100 text-gray-800 rounded font-mono">{{ $license->key }}</code></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs sm:text-sm text-red-600 font-semibold">{{ round($license->getDaysRemaining()) }} hari lagi</p>
                                    <p class="text-xs text-gray-500">{{ $license->tanggal_berakhir->format('d/m/Y') }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Sekolah Terbaru -->
        <div class="bg-white rounded-lg shadow-soft p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Sekolah Terbaru</h3>
            <div class="space-y-2 sm:space-y-3">
                @forelse($sekolahTerbaru as $sekolah)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-2 sm:p-3 bg-gray-50 rounded-lg gap-2">
                    <div class="flex items-center">
                        <div class="p-1 sm:p-2 bg-blue-100 rounded-full mr-2 sm:mr-3">
                            <i class="fas fa-school text-blue-600 text-sm sm:text-base"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm sm:text-base font-semibold text-gray-800 truncate">{{ $sekolah->nama_sekolah }}</p>
                            <p class="text-xs sm:text-sm text-gray-600 truncate">{{ $sekolah->jenjang }} - {{ $sekolah->kota }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500 self-end sm:self-auto">{{ $sekolah->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-gray-500 text-center py-3 sm:py-4 text-sm sm:text-base">Belum ada data sekolah</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Update Info & Maintenance Status -->
    <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-2">
        <!-- Latest Update -->
        <div class="bg-white rounded-lg shadow-soft p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Update Aplikasi Terbaru</h3>
            @if($latestUpdate)
            <div class="border-l-4 border-green-500 bg-green-50 p-3 sm:p-4 rounded">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-1 sm:mb-2 gap-2">
                    <h4 class="text-sm sm:text-base font-semibold text-green-800 break-words">{{ $latestUpdate->name }}</h4>
                    <span class="px-2 sm:px-3 py-1 bg-green-200 text-green-800 text-xs rounded-full font-semibold w-fit">v{{ $latestUpdate->version }}</span>
                </div>
                <p class="text-xs sm:text-sm text-green-700 mb-1 sm:mb-2 break-words">{{ $latestUpdate->description }}</p>
                <p class="text-xs text-green-600">Dirilis: {{ $latestUpdate->release_date->format('d M Y') }}</p>
            </div>
            @else
            <p class="text-gray-500 text-center py-3 sm:py-4 text-sm sm:text-base">Belum ada update</p>
            @endif
        </div>

        <!-- Maintenance Status -->
        <div class="bg-white rounded-lg shadow-soft p-4 sm:p-6">
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Status Maintenance</h3>
            <div class="space-y-2 sm:space-y-3">
                <div class="flex items-center justify-between p-2 sm:p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-desktop text-gray-600 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                        <span class="text-sm sm:text-base font-medium text-gray-700">Website</span>
                    </div>
                    <span class="px-2 sm:px-3 py-1 {{ $maintenanceWeb ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' }} text-xs rounded-full font-semibold">
                        {{ $maintenanceWeb ? 'Maintenance' : 'Online' }}
                    </span>
                </div>
                <div class="flex items-center justify-between p-2 sm:p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-mobile-alt text-gray-600 mr-2 sm:mr-3 text-sm sm:text-base"></i>
                        <span class="text-sm sm:text-base font-medium text-gray-700">Mobile</span>
                    </div>
                    <span class="px-2 sm:px-3 py-1 {{ $maintenanceMobile ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' }} text-xs rounded-full font-semibold">
                        {{ $maintenanceMobile ? 'Maintenance' : 'Online' }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection