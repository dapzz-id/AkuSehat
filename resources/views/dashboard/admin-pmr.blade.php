@extends('layouts.app')

@section('title', 'Dashboard Admin PMR')

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

        /* Warna khusus per jurusan */
        .rpl {
            --primary: #e0b300; /* kuning lebih soft */
            --primary-dark: #b38f00;
        }

        .tkj {
            --primary: #007acc; /* biru */
            --primary-dark: #005fa3;
        }

        .mm {
            --primary: #cc0066; /* magenta */
            --primary-dark: #99004d;
        }

        .transmisi {
            --primary: #28a745; /* hijau */
            --primary-dark: #1e7e34;
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
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-dashboard text-red-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-[#1a472a]">Dashboard Admin PMR</h2>
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
                    <p class="text-sm font-medium text-gray-600">Total Siswa</p>
                    <p class="text-2xl font-semibold text-[#1a472a]">{{ $data['total_siswa'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm card-hover">
            <div class="flex items-center">
                <div class="w-12 h-12 stats-icon rounded-lg flex items-center justify-center">
                    <i class="fas fa-school text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Kelas</p>
                    <p class="text-2xl font-semibold text-[#1a472a]">{{ $data['total_kelas'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm card-hover">
            <div class="flex items-center">
                <div class="w-12 h-12 stats-icon rounded-lg flex items-center justify-center">
                    <i class="fas fa-stethoscope text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Data Kesehatan (Bulan Ini)</p>
                    <p class="text-2xl font-semibold text-[#1a472a]">{{ $data['data_kesehatan_bulan_ini'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm card-hover">
            <div class="flex items-center">
                <div class="w-12 h-12 stats-icon rounded-lg flex items-center justify-center">
                    <i class="fas fa-tint text-white text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Data HB (Bulan Ini)</p>
                    <p class="text-2xl font-semibold text-[#1a472a]">{{ $data['data_hb_bulan_ini'] }}</p>
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
            
            <a href="{{ route('admin.siswa.index') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg quick-action">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-list text-green-600"></i>
                </div>
                <span class="font-medium text-green-800">Lihat Data Siswa</span>
            </a>
            
            <a href="{{ route('admin.kelas.index') }}" 
               class="flex items-center p-4 bg-green-50 rounded-lg quick-action">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-list text-green-600"></i>
                </div>
                <span class="font-medium text-green-800">Lihat Data Kelas</span>
            </a>
        </div>
    </div>

    <!-- Divider -->
    <div class="border-t border-gray-300/80 transform scale-y-50 mb-10 mt-6"></div>

    <!-- Health Summary Card -->
    <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-4 gap-6">
        @forelse ($dataByClass as $item)
            @php
                $classColor = '';
                if (str_contains(strtolower($item->kelas), 'rpl')) $classColor = 'rpl';
                elseif (str_contains(strtolower($item->kelas), 'tkj')) $classColor = 'tkj';
                elseif (str_contains(strtolower($item->kelas), 'dkv')) $classColor = 'dkv';
                elseif (str_contains(strtolower($item->kelas), 'transmisi')) $classColor = 'transmisi';
            @endphp

            <div class="health-card {{ $classColor }} w-full h-48 rounded-xl flex items-center justify-between p-6 text-white card-hover">
                <div>
                    <div class="text-5xl font-bold glow">{{ $item->siswa }}</div>
                    <div class="text-xl font-semibold mt-2">{{ $item->kelas }}</div>
                    <p class="mt-1 text-sm">Kesehatan: {{ $item->kesehatan->count() }}</p>
                    <p class="text-sm">HB: {{ $item->hb->count() }}</p>
                </div>
                <div class="flex flex-col items-end">
                    <svg class="w-14 h-14 transition duration-300 ease-in-out transform hover:scale-105" onclick="window.location='{{ route('admin.siswa.index') }}?kelas={{ $item->id }}'" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <a href="{{ route('admin.siswa.index') }}?kelas={{ $item->id }}" 
                        class="mt-3 {{ $classColor }} text-white font-semibold py-2 px-5 rounded-lg transition duration-300 ease-in-out transform hover:scale-105">
                        Detail &gt;
                    </a>
                </div>
            </div>
        @empty
            <p class="text-gray-600">Belum ada data kelas.</p>
        @endforelse
    </div>
</div>
@endsection
