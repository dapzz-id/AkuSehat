@extends('layouts.app')

@section('title', 'Manajemen Pembaruan Aplikasi & Sistem')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    <i class="fas fa-code-branch mr-1"></i> Release Center
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">Manajemen Pembaruan Aplikasi & Web</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Atur versi rilis aplikasi mobile (Play Store / App Store) serta patch pembaruan website realtime.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('patch_notes.index') }}" target="_blank" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-2">
                <i class="fas fa-book-open text-emerald-600"></i>
                <span>Lihat Halaman Patch</span>
            </a>
            <a href="{{ route('superadmin.update.create') }}" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 border border-emerald-800 shadow-md shadow-emerald-900/15 transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Rilis Pembaruan Baru</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @php
            $latestAndroid = \App\Models\SoftwareApp::where('platform', 'android')->where('status', 'active')->orderBy('release_date', 'desc')->first();
            $latestIos = \App\Models\SoftwareApp::where('platform', 'ios')->where('status', 'active')->orderBy('release_date', 'desc')->first();
            $latestWeb = \App\Models\SoftwareApp::where('platform', 'web')->where('status', 'active')->orderBy('release_date', 'desc')->first();
        @endphp

        <!-- Android Card -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-soft flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-green-50 border border-green-200 text-green-700 flex items-center justify-center text-xl">
                    <i class="fab fa-android"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Android Mobile</p>
                    <p class="text-base font-bold text-slate-900 mt-0.5">
                        {{ $latestAndroid ? 'v' . $latestAndroid->version : 'Belum Ada Rilis' }}
                    </p>
                    @if($latestAndroid && $latestAndroid->is_mandatory)
                        <span class="inline-block mt-1 text-[10px] font-bold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">
                            Wajib Update
                        </span>
                    @endif
                </div>
            </div>
            @if($latestAndroid)
                <a href="{{ $latestAndroid->link }}" target="_blank" class="p-2 text-slate-400 hover:text-green-700 transition-colors" title="Buka Link Play Store">
                    <i class="fas fa-external-link-alt text-xs"></i>
                </a>
            @endif
        </div>

        <!-- iOS Card -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-soft flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center text-xl">
                    <i class="fab fa-apple"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">iOS Mobile</p>
                    <p class="text-base font-bold text-slate-900 mt-0.5">
                        {{ $latestIos ? 'v' . $latestIos->version : 'Belum Ada Rilis' }}
                    </p>
                    @if($latestIos && $latestIos->is_mandatory)
                        <span class="inline-block mt-1 text-[10px] font-bold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">
                            Wajib Update
                        </span>
                    @endif
                </div>
            </div>
            @if($latestIos)
                <a href="{{ $latestIos->link }}" target="_blank" class="p-2 text-slate-400 hover:text-blue-700 transition-colors" title="Buka Link App Store">
                    <i class="fas fa-external-link-alt text-xs"></i>
                </a>
            @endif
        </div>

        <!-- Web Card -->
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-soft flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-teal-50 border border-teal-200 text-teal-700 flex items-center justify-center text-xl">
                    <i class="fas fa-globe"></i>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">Web Dashboard</p>
                    <p class="text-base font-bold text-slate-900 mt-0.5">
                        {{ $latestWeb ? 'v' . $latestWeb->version : 'Belum Ada Rilis' }}
                    </p>
                    <span class="inline-block mt-1 text-[10px] font-medium text-teal-700 bg-teal-50 px-1.5 py-0.5 rounded border border-teal-200">
                        Realtime Auto-Reload
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-4">
        <form method="GET" action="{{ route('superadmin.update.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <!-- Platform Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
                <a href="{{ route('superadmin.update.index', array_merge(request()->except('platform', 'page'), [])) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ !request('platform') ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    Semua Platform
                </a>
                <a href="{{ route('superadmin.update.index', array_merge(request()->except('page'), ['platform' => 'android'])) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ request('platform') === 'android' ? 'bg-green-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    <i class="fab fa-android mr-1"></i> Android
                </a>
                <a href="{{ route('superadmin.update.index', array_merge(request()->except('page'), ['platform' => 'ios'])) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ request('platform') === 'ios' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    <i class="fab fa-apple mr-1"></i> iOS
                </a>
                <a href="{{ route('superadmin.update.index', array_merge(request()->except('page'), ['platform' => 'web'])) }}"
                   class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap {{ request('platform') === 'web' ? 'bg-teal-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
                    <i class="fas fa-globe mr-1"></i> Website
                </a>
            </div>

            <!-- Search input -->
            <div class="flex items-center gap-2">
                <div class="relative w-full sm:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari versi atau catatan..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                @if(request()->anyFilled(['platform', 'search', 'status']))
                    <a href="{{ route('superadmin.update.index') }}" class="p-2 text-slate-400 hover:text-rose-600 text-xs" title="Reset Filter">
                        <i class="fas fa-times-circle"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section (Desktop) -->
    <div class="hidden sm:block bg-white rounded-2xl border border-slate-200/80 shadow-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 uppercase tracking-wider font-bold">
                        <th class="px-5 py-3.5">Platform</th>
                        <th class="px-5 py-3.5">Versi</th>
                        <th class="px-5 py-3.5">Nama & Catatan Rilis</th>
                        <th class="px-5 py-3.5">Tanggal Rilis</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($updates as $update)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($update->platform === 'android')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-green-50 text-green-700 border border-green-200 font-bold uppercase text-[11px]">
                                    <i class="fab fa-android text-sm"></i> Android
                                </span>
                            @elseif($update->platform === 'ios')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-bold uppercase text-[11px]">
                                    <i class="fab fa-apple text-sm"></i> iOS
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 border border-teal-200 font-bold uppercase text-[11px]">
                                    <i class="fas fa-globe text-sm"></i> Web
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 bg-slate-100 text-slate-900 rounded-md font-mono font-bold text-xs border border-slate-200">
                                v{{ $update->version }}
                            </span>
                            @if($update->min_version)
                                <span class="block text-[10px] text-slate-400 mt-1 font-mono">Min: v{{ $update->min_version }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 max-w-md">
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-slate-900 text-sm">{{ $update->name }}</p>
                                @if($update->is_mandatory)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                        Wajib Update
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $update->description ?? 'Tidak ada ringkasan' }}</p>
                            @if($update->changelog)
                                <button type="button" onclick="showChangelogModal('{{ addslashes($update->name) }}', '{{ $update->version }}', `{{ addslashes($update->changelog) }}`)"
                                        class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 hover:underline mt-1 inline-flex items-center gap-1">
                                    <i class="fas fa-list-check text-[10px]"></i>
                                    <span>Lihat Patch Notes Lengkap</span>
                                </button>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-slate-600">
                            {{ $update->release_date ? $update->release_date->format('d/m/Y') : '-' }}
                            <span class="block text-[10px] text-slate-400">{{ $update->developer }}</span>
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if($update->isActive())
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    Draft
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap text-center">
                            <div class="inline-flex items-center gap-1.5">
                                @if($update->link)
                                    <a href="{{ route('superadmin.update.download', ['id' => $update->id]) }}" target="_blank"
                                       class="p-2 text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors"
                                       title="Buka Link Toko / Unduh">
                                        <i class="fas fa-external-link-alt text-xs"></i>
                                    </a>
                                @else
                                    <span class="p-2 text-slate-300 cursor-not-allowed" title="Tidak ada link eksternal">
                                        <i class="fas fa-minus text-xs"></i>
                                    </span>
                                @endif
                                <a href="{{ route('superadmin.update.edit', $update->id) }}"
                                   class="p-2 text-slate-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg transition-colors"
                                   title="Edit Update">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('superadmin.update.destroy', $update->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus riwayat update {{ $update->name }} (v{{ $update->version }})?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-600 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                            <i class="fas fa-cloud-arrow-up text-3xl mb-2 text-slate-300"></i>
                            <p class="font-medium">Belum ada riwayat update yang diterbitkan</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($updates->hasPages())
        <div class="px-5 py-3 border-t border-slate-200">
            {{ $updates->links() }}
        </div>
        @endif
    </div>

    <!-- Cards Section (Mobile) -->
    <div class="sm:hidden space-y-3">
        @forelse($updates as $update)
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-soft p-4 space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase
                        @if($update->platform === 'android') bg-green-50 text-green-700 border border-green-200
                        @elseif($update->platform === 'ios') bg-blue-50 text-blue-700 border border-blue-200
                        @else bg-teal-50 text-teal-700 border border-teal-200 @endif">
                        {{ $update->platform }}
                    </span>
                    <span class="font-mono font-bold text-xs bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                        v{{ $update->version }}
                    </span>
                </div>
                <div>
                    @if($update->isActive())
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">Draft</span>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="font-bold text-slate-900 text-sm">{{ $update->name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $update->description ?? 'Tidak ada ringkasan' }}</p>
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                <span>Rilis: {{ $update->release_date ? $update->release_date->format('d/m/Y') : '-' }}</span>
                <span>{{ $update->developer }}</span>
            </div>

            <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('superadmin.update.download', ['id' => $update->id]) }}" target="_blank"
                   class="flex-1 py-2 text-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                    <i class="fas fa-external-link-alt mr-1"></i> Tautan
                </a>
                <a href="{{ route('superadmin.update.edit', $update->id) }}"
                   class="flex-1 py-2 text-center rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-semibold border border-amber-200">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                <form action="{{ route('superadmin.update.destroy', $update->id) }}" method="POST" class="inline"
                      onsubmit="return confirm('Hapus update {{ $update->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400">
            <p>Belum ada update aplikasi</p>
        </div>
        @endforelse

        @if($updates->hasPages())
        <div class="pt-2">
            {{ $updates->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Changelog Detail -->
<div id="changelogModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="p-5 bg-gradient-to-r from-emerald-800 to-teal-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-book-open text-emerald-300"></i>
                <h3 class="font-bold text-base" id="modalTitle">Patch Notes</h3>
            </div>
            <button type="button" onclick="closeChangelogModal()" class="text-white/80 hover:text-white">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="p-6 space-y-3">
            <p class="text-xs font-bold text-slate-500 uppercase">Rincian Perubahan & Pembaruan:</p>
            <div id="modalContent" class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-xs font-mono text-slate-800 leading-relaxed whitespace-pre-line max-h-80 overflow-y-auto">
            </div>
            <div class="flex justify-end pt-2">
                <button type="button" onclick="closeChangelogModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showChangelogModal(name, version, changelog) {
        document.getElementById('modalTitle').innerText = name + ' (v' + version + ')';
        document.getElementById('modalContent').innerText = changelog || 'Tidak ada catatan perubahan mendalam.';
        document.getElementById('changelogModal').classList.remove('hidden');
    }

    function closeChangelogModal() {
        document.getElementById('changelogModal').classList.add('hidden');
    }
</script>
@endpush
@endsection