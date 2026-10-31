@extends('layouts.app')

@section('title', 'Paspor Vaksinasi & Imunisasi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-600/20">
                    <i class="fas fa-syringe text-base"></i>
                </div>
                <span>Paspor Vaksinasi & Imunisasi Digital</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Rekam jejak vaksinasi (HPV pencegah kanker serviks, Hepatitis B, TT, Influenza) dan alergi obat.
            </p>
        </div>
    </div>

    <!-- Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Dosis Diberikan</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-syringe text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $totalVaccinations }}</span>
                <span class="text-xs font-bold text-blue-600">Dosis Vaksin</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Akumulasi vaksinasi tersertifikasi</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Anggota Terlindungi</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-user-shield text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $uniqueUsersVaccinated }}</span>
                <span class="text-xs font-bold text-emerald-600">Anggota</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Memiliki paspor imunisasi aktif</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.vaccine.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama vaksin, batch, atau nama anggota..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="w-full sm:w-48">
                <select name="divisi_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->divisi_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.vaccine.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors text-center">
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
                        <th class="px-4 py-3.5">Tanggal Imunisasi</th>
                        <th class="px-4 py-3.5">Anggota Penerima</th>
                        <th class="px-4 py-3.5">Nama Vaksin</th>
                        <th class="px-4 py-3.5">Dosis Ke-</th>
                        <th class="px-4 py-3.5">Nomor Batch</th>
                        <th class="px-4 py-3.5">Petugas Medis</th>
                        <th class="px-4 py-3.5">Catatan Alergi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($records as $item)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3.5 text-slate-600">
                            {{ \Carbon\Carbon::parse($item->administered_date)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-slate-800">{{ $item->user_nama }}</div>
                            <div class="text-[10px] text-slate-400">{{ $item->nomor_induk }}</div>
                        </td>
                        <td class="px-4 py-3.5 font-bold text-slate-800">
                            <i class="fas fa-shield-virus text-blue-500 mr-1.5"></i>{{ $item->vaccine_name }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700">
                                Dosis #{{ $item->dose_number }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 font-mono text-[11px]">
                            {{ $item->batch_number ?: '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-slate-600">
                            {{ $item->administered_by ?: 'Tim Medis UKS / Puskesmas' }}
                        </td>
                        <td class="px-4 py-3.5">
                            @if($item->allergy_notes)
                                <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-medium text-[11px]">
                                    {{ $item->allergy_notes }}
                                </span>
                            @else
                                <span class="text-slate-400">Nihil Alergi</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                            <i class="fas fa-syringe text-3xl mb-2 text-slate-300 block"></i>
                            Belum ada riwayat paspor vaksinasi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-100">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
