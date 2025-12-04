@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <style>
        :root {
            --primary: #2e7d32;
            --primary-light: #4caf50;
            --primary-dark: #1b5e20;
            --accent: #8bc34a;
            --text-primary: #1a472a;
            --text-secondary: #388e3c;
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid var(--primary);
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px -10px rgba(46, 125, 50, 0.3);
        }
        
        .glow {
            text-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
        }
        
        .stats-icon {
            background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
        }
        
        .quick-action {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .quick-action:hover {
            transform: translateX(5px);
            border-left-color: var(--primary);
        }
        
        .health-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            overflow: hidden;
            position: relative;
        }
        
        .health-card::before {
            content: "";
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: rgba(255, 255, 255, 0.1);
            transform: rotate(30deg);
            z-index: 0;
        }

        .health-card > * {
            position: relative;
            z-index: 1;
        }
        
        @media (max-width: 768px) {
            .health-card {
                width: 100% !important;
            }
        }
    </style>

    <div class="bg-white p-4 md:p-6 rounded-lg shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dashboard text-[#1b5e20] text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-[#1a472a]">Dashboard Admin</h2>
                    <p class="text-[#388e3c]">Selamat datang, {{ auth()->user()->nama }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm card-hover">
            <div class="flex items-center">
                <div class="w-12 h-12 stats-icon rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Member</p>
                    <p class="text-2xl font-semibold text-[#1a472a]">{{ $data['total_member'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm card-hover">
            <div class="flex items-center">
                <div class="w-12 h-12 stats-icon rounded-lg flex items-center justify-center">
                    <i class="fas fa-school text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Divisi</p>
                    <p class="text-2xl font-semibold text-[#1a472a]">{{ $data['total_divisi'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm card-hover">
            <div class="flex items-center">
                <div class="w-12 h-12 stats-icon rounded-lg flex items-center justify-center">
                    <i class="fas fa-stethoscope text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Kesehatan ({{ now()->year }})</p>
                    <p class="text-2xl font-semibold text-[#1a472a]">{{ $data['data_kesehatan_tahun_ini'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm card-hover">
            <div class="flex items-center">
                <div class="w-12 h-12 stats-icon rounded-lg flex items-center justify-center">
                    <i class="fas fa-tint text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Hemoglobin ({{ now()->year }})</p>
                    <p class="text-2xl font-semibold text-[#1a472a]">{{ $data['data_hb_tahun_ini'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <h3 class="text-lg font-semibold text-[#1a472a] mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.kesehatan.create') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg quick-action">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-plus text-green-600"></i>
                </div>
                <span class="font-medium text-green-800">Tambah Data Kesehatan</span>
            </a>
            
            <a href="{{ route('admin.hb.create') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg quick-action">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-plus text-green-600"></i>
                </div>
                <span class="font-medium text-green-800">Tambah Data HB</span>
            </a>
            
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg quick-action">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-list text-green-600"></i>
                </div>
                <span class="font-medium text-green-800">Lihat Data Member</span>
            </a>
            
            <a href="{{ route('admin.divisi.index') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg quick-action">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-list text-green-600"></i>
                </div>
                <span class="font-medium text-green-800">Lihat Data Divisi</span>
            </a>
        </div>
    </div>

    <!-- Divider -->
    <div class="border-t border-gray-300/80 transform scale-y-50 mb-10 mt-6"></div>

    <!-- Health Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-4 gap-6">
        @forelse ($dataByClass as $item)

            <div style="--primary: {{ $item->color_cover }}; --primary-dark: {{ $item->color_cover }};" class="health-card w-full h-48 rounded-xl flex items-center justify-between p-6 text-white card-hover">
                <div>
                    <div class="text-5xl font-bold glow">{{ $item->member }}</div>
                    <div class="text-xl font-semibold mt-2">{{ $item->divisi_name }}</div>
                    <p class="mt-1 text-sm">Member: {{ $item->total_member }}</p>
                    <p class="text-sm">
                        Kesehatan: 
                        {{ $item->users->sum(fn($u) =>
                            $u->kesehatan->filter(fn($k) => \Carbon\Carbon::parse($k->tgl)->year == now()->year)->count()
                        ) }}
                    </p>

                    <p class="text-sm">
                        HB:
                        {{ $item->users->sum(fn($u) =>
                            $u->hb->filter(fn($h) => \Carbon\Carbon::parse($h->tgl)->year == now()->year)->count()
                        ) }}
                    </p>
                </div>
                <div class="flex flex-col items-end">
                    <svg class="w-14 h-14 transition duration-300 ease-in-out transform hover:scale-105" onclick="window.location='{{ route('admin.users.index') }}?divisi={{ $item->id }}'" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="{{ route('admin.users.index') }}?divisi={{ $item->id }}"
                        style="--primary: {{ $item->color_cover }}; --primary-dark: {{ $item->color_cover }};"
                        class="mt-3 text-white font-semibold py-2 px-5 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                        Detail &gt;
                    </a>
                </div>
            </div>
        @empty
            <p class="text-gray-600">Belum ada data divisi.</p>
        @endforelse
    </div>
</div>
@endsection
