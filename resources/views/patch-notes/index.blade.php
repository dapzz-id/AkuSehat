@extends('layouts.app')

@section('title', 'Riwayat Pembaruan & Catatan Patch')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header Banner -->
    <div class="relative bg-gradient-to-r from-emerald-900 via-teal-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl overflow-hidden border border-emerald-800/40">
        <div class="absolute -right-8 -top-8 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <i class="fas fa-code-branch"></i>
                    <span>Catatan Rilis & Pembaruan Sistem</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Riwayat Pembaruan (Patch Notes)</h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-xl leading-relaxed">
                    Lihat rekam jejak fitur baru, peningkatan keamanan, optimalisasi kinerja database, serta pembaruan aplikasi mobile Aku Sehat.
                </p>
            </div>

            <!-- Version Status Cards -->
            <div class="flex items-center gap-3">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3.5 border border-white/15 text-center min-w-[120px]">
                    <span class="text-[10px] uppercase font-bold text-emerald-300 tracking-wider block">Versi Web</span>
                    <span class="text-lg font-extrabold text-white font-mono mt-0.5 block">
                        {{ $latestWeb ? 'v' . $latestWeb->version : 'v1.0.0' }}
                    </span>
                    <span class="text-[10px] text-slate-300 block">Terkini</span>
                </div>

                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3.5 border border-white/15 text-center min-w-[120px]">
                    <span class="text-[10px] uppercase font-bold text-green-300 tracking-wider block">Versi Mobile</span>
                    <span class="text-lg font-extrabold text-white font-mono mt-0.5 block">
                        {{ $latestAndroid ? 'v' . $latestAndroid->version : 'v1.0.0' }}
                    </span>
                    @if($latestAndroid)
                        <a href="{{ $latestAndroid->link }}" target="_blank" class="text-[10px] text-green-300 hover:underline flex items-center justify-center gap-1 mt-0.5">
                            <i class="fab fa-google-play"></i> Play Store
                        </a>
                    @else
                        <span class="text-[10px] text-slate-300 block">Terkini</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-3 sm:p-4 flex items-center justify-between gap-3 overflow-x-auto">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-500 uppercase mr-1 hidden sm:inline">Filter Platform:</span>
            <a href="{{ route('patch_notes.index') }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ !request('platform') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                Semua Platform
            </a>
            <a href="{{ route('patch_notes.index', ['platform' => 'web']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ request('platform') === 'web' ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="fas fa-globe mr-1"></i> Website
            </a>
            <a href="{{ route('patch_notes.index', ['platform' => 'android']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ request('platform') === 'android' ? 'bg-green-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="fab fa-android mr-1"></i> Android
            </a>
            <a href="{{ route('patch_notes.index', ['platform' => 'ios']) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ request('platform') === 'ios' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                <i class="fab fa-apple mr-1"></i> iOS
            </a>
        </div>

        @if(auth()->check() && auth()->user()->isSuperAdmin())
            <a href="{{ route('superadmin.update.index') }}" class="px-3 py-1.5 rounded-xl text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors whitespace-nowrap flex items-center gap-1.5">
                <i class="fas fa-gear text-[11px]"></i>
                <span>Kelola Rilis (Superadmin)</span>
            </a>
        @endif
    </div>

    <!-- Timeline of Updates -->
    <div class="space-y-4">
        @forelse($updates as $update)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden transition-all hover:border-emerald-300">
                <!-- Top strip header -->
                <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <!-- Platform Badge -->
                        @if($update->platform === 'android')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-green-100 text-green-800 font-bold text-xs">
                                <i class="fab fa-android text-sm"></i> Android
                            </span>
                        @elseif($update->platform === 'ios')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-blue-100 text-blue-800 font-bold text-xs">
                                <i class="fab fa-apple text-sm"></i> iOS
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-teal-100 text-teal-800 font-bold text-xs">
                                <i class="fas fa-globe text-sm"></i> Web Dashboard
                            </span>
                        @endif

                        <!-- Version Pill -->
                        <span class="px-2.5 py-0.5 rounded-md bg-white border border-slate-200 font-mono font-bold text-xs text-slate-900">
                            v{{ $update->version }}
                        </span>

                        @if($update->is_mandatory)
                            <span class="px-2 py-0.5 rounded-md bg-rose-100 text-rose-700 border border-rose-200 font-bold text-[10px] uppercase">
                                Wajib Update
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-4 text-xs text-slate-500">
                        <span class="flex items-center gap-1.5">
                            <i class="far fa-calendar-alt text-slate-400"></i>
                            {{ $update->release_date ? $update->release_date->locale('id')->isoFormat('D MMMM Y') : '-' }}
                        </span>
                        <span class="text-slate-300">&bull;</span>
                        <span class="text-slate-500 font-medium">{{ $update->developer }}</span>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6 space-y-4">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">{{ $update->name }}</h2>
                        @if($update->description)
                            <p class="text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">{{ $update->description }}</p>
                        @endif
                    </div>

                    @if($update->changelog)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                            <p class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fas fa-list-check text-emerald-600"></i>
                                <span>Rincian Perubahan & Peningkatan:</span>
                            </p>
                            <div class="text-xs font-mono text-slate-700 leading-relaxed whitespace-pre-line">
                                {{ $update->changelog }}
                            </div>
                        </div>
                    @endif

                    @if($update->link)
                        <div class="pt-3 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-t border-slate-100 mt-2">
                            <span class="text-xs text-slate-500 font-medium">
                                {{ $update->platform === 'web' ? 'Tautan domain/portal rilis website ini:' : 'Dapatkan rilis resmi aplikasi ini:' }}
                            </span>
                            @if($update->platform === 'android')
                                <a href="{{ $update->link }}" target="_blank" rel="noopener noreferrer"
                                   style="background-color: #047857 !important; color: #ffffff !important; border: 1px solid #065f46; cursor: pointer !important;"
                                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 shadow-md shadow-emerald-900/15 transition-all">
                                    <i class="fab fa-google-play text-sm" style="color: #ffffff;"></i>
                                    <span style="color: #ffffff;">Buka di Google Play Store</span>
                                </a>
                            @elseif($update->platform === 'ios')
                                <a href="{{ $update->link }}" target="_blank" rel="noopener noreferrer"
                                   style="background-color: #0f172a !important; color: #ffffff !important; border: 1px solid #020617; cursor: pointer !important;"
                                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-black active:bg-slate-950 shadow-md shadow-slate-900/15 transition-all">
                                    <i class="fab fa-apple text-sm" style="color: #ffffff;"></i>
                                    <span style="color: #ffffff;">Buka di App Store</span>
                                </a>
                            @else
                                <a href="{{ $update->link }}" target="_blank" rel="noopener noreferrer"
                                   style="background-color: #0f766e !important; color: #ffffff !important; border: 1px solid #115e59; cursor: pointer !important;"
                                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-teal-700 hover:bg-teal-800 shadow-sm transition-all">
                                    <i class="fas fa-arrow-up-right-from-square text-sm" style="color: #ffffff;"></i>
                                    <span style="color: #ffffff;">Buka Tautan Rilis Website</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-slate-400 space-y-3">
                <i class="fas fa-box-open text-4xl text-slate-300"></i>
                <h3 class="text-base font-bold text-slate-700">Belum Ada Catatan Pembaruan</h3>
                <p class="text-xs text-slate-500">Tidak ada riwayat rilis untuk filter platform yang dipilih.</p>
            </div>
        @endforelse

        @if($updates->hasPages())
            <div class="pt-2">
                {{ $updates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
