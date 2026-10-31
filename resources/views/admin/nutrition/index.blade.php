@extends('layouts.app')

@section('title', 'Nutrisi & Pemantau Pola Makan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-md shadow-amber-500/20">
                    <i class="fas fa-apple-whole text-base"></i>
                </div>
                <span>Nutrisi & Pemantau Pola Makan Sehat</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pencatatan asupan menu harian, keseimbangan gizi 4 sehat 5 sempurna, dan estimasi kalori.
            </p>
        </div>
    </div>

    <!-- Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Catatan Makan</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fas fa-utensils text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $totalLogs }}</span>
                <span class="text-xs font-bold text-amber-600">Entri Menu</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Log makanan anggota</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata Kalori</span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fas fa-fire text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $avgCalories }}</span>
                <span class="text-xs font-bold text-rose-600">Kkal / Porsi</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Estimasi energi per porsi</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rasio Gizi Seimbang</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-shield-halved text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $balancedRatio }}%</span>
                <span class="text-xs font-bold text-emerald-600">Gizi Terpenuhi</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Menu seimbang bergizi</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.nutrition.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama makanan atau nama anggota..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
            </div>

            <div class="w-full sm:w-44">
                <select name="meal_type" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">Semua Waktu Makan</option>
                    <option value="sarapan" {{ request('meal_type') === 'sarapan' ? 'selected' : '' }}>Sarapan</option>
                    <option value="makan_siang" {{ request('meal_type') === 'makan_siang' ? 'selected' : '' }}>Makan Siang</option>
                    <option value="makan_malam" {{ request('meal_type') === 'makan_malam' ? 'selected' : '' }}>Makan Malam</option>
                    <option value="camilan" {{ request('meal_type') === 'camilan' ? 'selected' : '' }}>Camilan</option>
                </select>
            </div>

            <div class="w-full sm:w-48">
                <select name="divisi_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->divisi_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.nutrition.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors text-center">
                Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3.5">Tanggal</th>
                        <th class="px-4 py-3.5">Anggota</th>
                        <th class="px-4 py-3.5">Waktu Makan</th>
                        <th class="px-4 py-3.5">Menu Makanan</th>
                        <th class="px-4 py-3.5">Kalori</th>
                        <th class="px-4 py-3.5">Status Gizi</th>
                        <th class="px-4 py-3.5">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $item)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3.5 text-slate-600">
                            {{ \Carbon\Carbon::parse($item->log_date)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-slate-800">{{ $item->user_nama }}</div>
                            <div class="text-[10px] text-slate-400">{{ $item->nomor_induk }}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 capitalize">
                                {{ str_replace('_', ' ', $item->meal_type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-slate-800">
                            {{ $item->food_name }}
                        </td>
                        <td class="px-4 py-3.5 text-slate-700 font-bold">
                            {{ number_format($item->calories) }} Kkal
                        </td>
                        <td class="px-4 py-3.5">
                            @if($item->is_balanced)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">Seimbang</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">Biasa</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-slate-400 text-[11px]">
                            {{ $item->notes ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                            <i class="fas fa-apple-whole text-3xl mb-2 text-slate-300 block"></i>
                            Belum ada log catatan pola makan nutrisi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
