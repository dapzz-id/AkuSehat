@extends('layouts.app')

@section('title', 'Ergonomi Kerja & Postur Sedentary')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-cyan-600 text-white flex items-center justify-center shadow-md shadow-cyan-600/20">
                    <i class="fas fa-chair text-base"></i>
                </div>
                <span>Ergonomi Kerja & Pemantauan Sedentary</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pantau durasi duduk terus menerus, frekuensi peregangan aktif, dan deteksi nyeri musculoskeletal.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(auth()->user()->isAdminInstansi())
                <div class="px-3.5 py-2 bg-cyan-50 border border-cyan-200 text-cyan-800 rounded-xl text-xs font-bold inline-flex items-center gap-2 shadow-sm">
                    <i class="fas fa-chart-pie text-cyan-600"></i>
                    <span>Mode Laporan & Monitoring (Read-Only)</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Log Ergonomi</span>
                <div class="w-9 h-9 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $totalLogs }}</span>
                <span class="text-xs font-bold text-cyan-600">Catatan</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Hari kerja/belajar</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata Duduk Diam</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ round($avgSedentaryMins / 60, 1) }}</span>
                <span class="text-xs font-bold text-amber-600">Jam / Hari</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">{{ $avgSedentaryMins }} menit waktu sedentary</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Skor Postur Rata-rata</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-shield-halved text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $avgScore }}</span>
                <span class="text-xs font-bold text-emerald-600">/ 100</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Kualitas ergonomi</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Keluhan Nyeri</span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fas fa-bed-pulse text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $totalComplaints }}</span>
                <span class="text-xs font-bold text-rose-600">Laporan Sakit</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Leher, punggung, pergelangan</p>
        </div>
    </div>

    <!-- Grafik Analisis Ergonomi & Postur -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Grafik 1: Distribusi Kategori Skor Ergonomi -->
        <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-shield-heart"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Distribusi Kualitas Skor Ergonomi</h3>
                        <p class="text-[11px] text-slate-400">Kepatuhan ergonomi duduk & peregangan</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                    {{ $totalLogs }} Data
                </span>
            </div>

            <div class="relative flex items-center justify-center my-auto py-3">
                <div class="w-44 h-44 sm:w-48 sm:h-48 relative">
                    <canvas id="ergonomicsScoreChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-1.5 pt-3 border-t border-slate-100 text-[10px]">
                <div class="p-2 rounded-lg bg-emerald-50/70 border border-emerald-200/80 text-center">
                    <span class="text-emerald-700 font-medium block">Prima (≥75)</span>
                    <span class="font-bold text-emerald-800 text-xs">{{ $ergonomicsScoreChartData['data'][0] }}</span>
                </div>
                <div class="p-2 rounded-lg bg-amber-50/70 border border-amber-200/80 text-center">
                    <span class="text-amber-700 font-medium block">Waspada (50-74)</span>
                    <span class="font-bold text-amber-800 text-xs">{{ $ergonomicsScoreChartData['data'][1] }}</span>
                </div>
                <div class="p-2 rounded-lg bg-rose-50/70 border border-rose-200/80 text-center">
                    <span class="text-rose-700 font-medium block">Berisiko (<50)</span>
                    <span class="font-bold text-rose-800 text-xs">{{ $ergonomicsScoreChartData['data'][2] }}</span>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Rata-rata Durasi Duduk per Divisi -->
        <div class="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-chair"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Rata-rata Waktu Duduk Sedentary per Divisi (Menit)</h3>
                        <p class="text-[11px] text-slate-400">Tingkat inaktivitas fisik saat jam kerja/sekolah</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-cyan-800 bg-cyan-50 px-2 py-0.5 rounded-full border border-cyan-200">
                    Durasi Harian
                </span>
            </div>

            <div class="relative w-full h-56 sm:h-64 my-auto">
                <canvas id="divisionSedentaryChart"></canvas>
            </div>

            <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-100 flex items-center gap-1">
                <i class="fas fa-info-circle text-cyan-500"></i>
                <span>Direkomendasikan melakukan peregangan tiap 45-60 menit duduk terus menerus.</span>
            </p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.ergonomics.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama anggota..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
            </div>

            <div class="w-full sm:w-48">
                <select name="divisi_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->divisi_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.ergonomics.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors text-center">
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
                        <th class="px-4 py-3.5">Durasi Duduk (Menit)</th>
                        <th class="px-4 py-3.5">Peregangan (Break)</th>
                        <th class="px-4 py-3.5">Keluhan Postur</th>
                        <th class="px-4 py-3.5">Skor Ergonomi</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
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
                            <div class="text-[10px] text-slate-400">{{ $item->divisi_name ?? 'Tanpa Divisi' }} • {{ $item->nomor_induk ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3.5 font-semibold text-slate-700">
                            {{ $item->sedentary_minutes }} Menit
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="px-2 py-0.5 rounded-md bg-cyan-50 text-cyan-700 font-semibold">
                                {{ $item->breaks_taken }}x Peregangan
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($item->posture_complaint)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-rose-100 text-rose-700">
                                    <i class="fas fa-triangle-exclamation mr-1"></i>{{ $item->posture_complaint }}
                                </span>
                            @else
                                <span class="text-slate-400">Tidak ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 font-bold">
                            <span class="{{ $item->score >= 75 ? 'text-emerald-600' : ($item->score >= 50 ? 'text-amber-600' : 'text-rose-600') }}">
                                {{ $item->score }} / 100
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <button type="button"
                                    onclick="openErgoDetail(this); return false;"
                                    class="view-ergo-detail px-2.5 py-1 bg-cyan-50 hover:bg-cyan-100 text-cyan-700 border border-cyan-200/80 rounded-lg text-xs font-semibold inline-flex items-center gap-1.5 transition-colors cursor-pointer shadow-sm"
                                    data-nama="{{ $item->user_nama }}"
                                    data-divisi="{{ $item->divisi_name ?? '-' }}"
                                    data-nomor-induk="{{ $item->nomor_induk ?? '-' }}"
                                    data-date="{{ \Carbon\Carbon::parse($item->log_date)->locale('id')->isoFormat('D MMMM Y') }}"
                                    data-sedentary="{{ $item->sedentary_minutes }} Menit ({{ round($item->sedentary_minutes / 60, 1) }} Jam)"
                                    data-breaks="{{ $item->breaks_taken }}x sesi peregangan aktif"
                                    data-complaint="{{ $item->posture_complaint ?: 'Tidak ada keluhan rasa sakit' }}"
                                    data-score="{{ $item->score }} / 100">
                                <i class="fas fa-file-lines text-xs"></i>
                                <span>Lihat Detail</span>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                            <i class="fas fa-chair text-3xl mb-2 text-slate-300 block"></i>
                            Belum ada catatan log ergonomi yang tersimpan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Detail Log Ergonomi & Postur -->
<div id="ergoDetailModal" class="fixed inset-0 !m-0 !mt-0 top-0 left-0 right-0 bottom-0 bg-slate-950/70 backdrop-blur-sm hidden z-[999999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="if(event.target === this) closeErgoDetailModal()">
    <div class="relative bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl space-y-4 max-h-[92vh] overflow-y-auto my-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center font-bold text-sm">
                    <i class="fas fa-chair"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-800" id="ergoModalNama">Detail Ergonomi</h3>
                    <p class="text-[11px] text-slate-400" id="ergoModalSub">-</p>
                </div>
            </div>
            <button type="button" onclick="closeErgoDetailModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        @if(auth()->user()->isAdminInstansi())
        <div class="p-3 bg-cyan-50/80 border border-cyan-200/80 rounded-xl text-cyan-800 text-xs flex items-center gap-2">
            <i class="fas fa-eye text-cyan-600 text-sm shrink-0"></i>
            <span><strong>Mode Pantau (Read-Only):</strong> Admin Instansi memiliki hak pantau indeks ergonomi tanpa wewenang mengubah data sensor postur anggota.</span>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal Pemantauan</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 block" id="ergoModalDate">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Skor Ergonomi</span>
                <span class="font-black text-emerald-600 text-sm mt-0.5 block" id="ergoModalScore">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Waktu Duduk Sedentary</span>
                <span class="font-bold text-slate-800 mt-0.5 block" id="ergoModalSedentary">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Frekuensi Peregangan</span>
                <span class="font-bold text-cyan-700 mt-0.5 block" id="ergoModalBreaks">-</span>
            </div>
        </div>

        <div class="p-3.5 rounded-xl bg-rose-50/60 border border-rose-200/80 text-xs">
            <span class="text-rose-600 block text-[10px] uppercase font-bold mb-1">
                <i class="fas fa-triangle-exclamation mr-1"></i>Keluhan Postur / Nyeri Fisik
            </span>
            <p class="text-rose-900 font-semibold leading-relaxed" id="ergoModalComplaint">-</p>
        </div>

        <div class="flex justify-end pt-2 border-t border-slate-100">
            <button type="button" onclick="closeErgoDetailModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
window.closeErgoDetailModal = function() {
    const m = document.getElementById('ergoDetailModal');
    if (m) {
        m.classList.add('hidden');
        m.style.display = 'none';
        m.style.setProperty('display', 'none', 'important');
    }
};

window.openErgoDetail = function(btn) {
    try {
        if (!btn) {
            btn = (window.event && window.event.target) ? window.event.target.closest('button, .view-ergo-detail') : null;
        } else if (btn.closest) {
            btn = btn.closest('button, .view-ergo-detail') || btn;
        }
        if (!btn) return;

        const getVal = function(attr, fallback) {
            return btn.getAttribute(attr) || (btn.dataset ? btn.dataset[attr.replace(/^data-/, '').replace(/-([a-z])/g, g => g[1].toUpperCase())] : null) || fallback || '-';
        };

        const setTxt = function(id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };

        setTxt('ergoModalNama', getVal('data-nama', 'Anggota'));
        const divisi = getVal('data-divisi', '-');
        const noInduk = getVal('data-nomor-induk', '-');
        setTxt('ergoModalSub', divisi + ' • Nomor Induk: ' + noInduk);
        setTxt('ergoModalDate', getVal('data-date', '-'));
        setTxt('ergoModalScore', getVal('data-score', '-'));
        setTxt('ergoModalSedentary', getVal('data-sedentary', '-'));
        setTxt('ergoModalBreaks', getVal('data-breaks', '-'));
        setTxt('ergoModalComplaint', getVal('data-complaint', '-'));

        const m = document.getElementById('ergoDetailModal');
        if (m) {
            m.classList.remove('hidden');
            m.style.removeProperty('display');
            m.style.display = 'flex';
            m.style.setProperty('display', 'flex', 'important');
            m.style.zIndex = '999999';
            m.style.opacity = '1';
            m.style.pointerEvents = 'auto';
            m.style.margin = '0';
            m.style.marginTop = '0';
            m.style.top = '0';
        }
    } catch (err) {
        console.error('Error opening ergo detail modal:', err);
        const m = document.getElementById('ergoDetailModal');
        if (m) {
            m.classList.remove('hidden');
            m.style.display = 'flex';
            m.style.setProperty('display', 'flex', 'important');
            m.style.zIndex = '999999';
            m.style.margin = '0';
            m.style.marginTop = '0';
            m.style.top = '0';
        }
    }
};

// Delegasi klik tombol Lihat Detail Ergonomi (Fallback)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-ergo-detail');
    if (btn) {
        e.preventDefault();
        openErgoDetail(btn);
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeErgoDetailModal();
    }
});

// Inisialisasi Grafik Analitik Ergonomi & Postur
document.addEventListener('DOMContentLoaded', function() {
    // 1. Grafik Skor Ergonomi (Doughnut)
    const ergoCtx = document.getElementById('ergonomicsScoreChart');
    if (ergoCtx && typeof Chart !== 'undefined') {
        const labels = {!! json_encode($ergonomicsScoreChartData['labels']) !!};
        const dataValues = {!! json_encode($ergonomicsScoreChartData['data']) !!};
        const bgColors = {!! json_encode($ergonomicsScoreChartData['colors']) !!};
        const total = {{ $totalLogs > 0 ? $totalLogs : 1 }};

        new Chart(ergoCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: bgColors,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const val = context.raw || 0;
                                const pct = Math.round((val / total) * 100);
                                return ` ${context.label}: ${val} (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '72%'
            }
        });
    }

    // 2. Grafik Durasi Duduk per Divisi (Bar)
    const sedCtx = document.getElementById('divisionSedentaryChart');
    if (sedCtx && typeof Chart !== 'undefined') {
        const sedLabels = {!! json_encode($divisionSedentaryChartData['labels']) !!};
        const sedValues = {!! json_encode($divisionSedentaryChartData['data']) !!};

        new Chart(sedCtx, {
            type: 'bar',
            data: {
                labels: sedLabels.length ? sedLabels : ['Belum ada data'],
                datasets: [{
                    label: 'Rata-rata Menit Duduk',
                    data: sedValues.length ? sedValues : [0],
                    backgroundColor: 'rgba(6, 182, 212, 0.85)',
                    borderColor: '#0891b2',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    maxBarThickness: 36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` ${context.raw} Menit / hari`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 10 }
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: {
                            font: { size: 11, weight: '600' },
                            color: '#334155'
                        },
                        grid: { display: false }
                    }
                }
            }
        });
    }
});
</script>
@endpush
