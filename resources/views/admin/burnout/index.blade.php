@extends('layouts.app')

@section('title', 'Skrining Burnout & Stres')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-md shadow-purple-600/20">
                    <i class="fas fa-brain text-base"></i>
                </div>
                <span>Skrining Stres, Burnout & Well-being</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Deteksi dini kelelahan mental, beban kerja/belajar, dan tingkat stres untuk kesejahteraan holistik.
            </p>
        </div>
        <div class="shrink-0">
            @if(auth()->user()->isAdminInstansi())
                <div class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-purple-50 border border-purple-200/80 text-purple-700 font-bold text-xs shadow-sm">
                    <i class="fas fa-chart-pie text-purple-500"></i>
                    <span>Mode Laporan & Analitik (Read-Only)</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Skrining</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $totalScreenings }}</span>
                <span class="text-xs font-bold text-purple-600">Responden</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Kuesioner terisi</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata Skor Stres</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-gauge-high text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $avgStressScore }}</span>
                <span class="text-xs font-bold text-indigo-600">/ 100</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Skala stres 0-100</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Risiko Sedang</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fas fa-triangle-exclamation text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $moderateCount }}</span>
                <span class="text-xs font-bold text-amber-600">Perlu Pemulihan</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Perlu pemantauan ringan</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Burnout Tinggi</span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fas fa-fire-flame-curved text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $highRiskCount }}</span>
                <span class="text-xs font-bold text-rose-600">Prioritas Konseling</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Butuh intervensi segera</p>
        </div>
    </div>

    <!-- Grafik Analisis Risiko Burnout & Well-being -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Grafik 1: Distribusi Tingkat Risiko Burnout -->
        <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xs font-bold shadow-sm">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Distribusi Risiko Burnout</h3>
                        <p class="text-[11px] text-slate-400">Proporsi responden per kategori risiko</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
                    {{ $totalScreenings }} Responden
                </span>
            </div>
            
            <div class="relative flex items-center justify-center my-auto py-3">
                <div class="w-44 h-44 sm:w-48 sm:h-48 relative">
                    <canvas id="burnoutRiskChart"></canvas>
                </div>
            </div>

            <!-- Mini Legend Badges -->
            <div class="grid grid-cols-3 gap-2 pt-3 border-t border-slate-100 text-[11px]">
                <div class="flex flex-col items-center justify-center p-2 rounded-xl bg-emerald-50/80 border border-emerald-200/60 text-center">
                    <span class="text-[10px] text-emerald-700 font-medium flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Rendah
                    </span>
                    <span class="font-bold text-emerald-900 text-sm mt-0.5">{{ $lowRiskCount }}</span>
                </div>
                <div class="flex flex-col items-center justify-center p-2 rounded-xl bg-amber-50/80 border border-amber-200/60 text-center">
                    <span class="text-[10px] text-amber-700 font-medium flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Sedang
                    </span>
                    <span class="font-bold text-amber-900 text-sm mt-0.5">{{ $moderateCount }}</span>
                </div>
                <div class="flex flex-col items-center justify-center p-2 rounded-xl bg-rose-50/80 border border-rose-200/60 text-center">
                    <span class="text-[10px] text-rose-700 font-medium flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Tinggi
                    </span>
                    <span class="font-bold text-rose-900 text-sm mt-0.5">{{ $highRiskCount }}</span>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Rata-rata Skor Stres per Divisi -->
        <div class="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold shadow-sm">
                        <i class="fas fa-chart-column"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Rata-rata Skor Stres per Divisi / Kelas</h3>
                        <p class="text-[11px] text-slate-400">Komparasi tingkat stres kelompok (Skala 0 - 100)</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-md border border-indigo-200/60">
                    Beban Divisi
                </span>
            </div>

            <div class="relative w-full h-52 sm:h-56 my-auto">
                <canvas id="divisionStressChart"></canvas>
            </div>

            <p class="text-[11px] text-slate-400 pt-3 border-t border-slate-100 flex items-center gap-1.5">
                <i class="fas fa-circle-info text-purple-500"></i>
                Divisi dengan skor stres rata-rata di atas 60 memerlukan perhatian khusus dan intervensi manajemen/konseling.
            </p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.burnout.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama anggota..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div class="w-full sm:w-44">
                <select name="burnout_level" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua Tingkat Burnout</option>
                    <option value="rendah" {{ request('burnout_level') === 'rendah' ? 'selected' : '' }}>Rendah (Aman)</option>
                    <option value="sedang" {{ request('burnout_level') === 'sedang' ? 'selected' : '' }}>Sedang (Waspada)</option>
                    <option value="tinggi" {{ request('burnout_level') === 'tinggi' ? 'selected' : '' }}>Tinggi (Kritis)</option>
                </select>
            </div>

            <div class="w-full sm:w-48">
                <select name="divisi_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->divisi_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                Filter
            </button>
            <a href="{{ route('admin.burnout.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors text-center">
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
                        <th class="px-4 py-3.5">Tanggal Skrining</th>
                        <th class="px-4 py-3.5">Anggota</th>
                        <th class="px-4 py-3.5">Skor Stres</th>
                        <th class="px-4 py-3.5">Tingkat Burnout</th>
                        <th class="px-4 py-3.5">Rating Beban (1-5)</th>
                        <th class="px-4 py-3.5">Catatan / Gejala</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($screenings as $item)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3.5 text-slate-600">
                            {{ \Carbon\Carbon::parse($item->screening_date)->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-bold text-slate-800">{{ $item->user_nama }}</div>
                            <div class="text-[10px] text-slate-400">{{ $item->nomor_induk }}</div>
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="font-bold {{ $item->stress_score > 60 ? 'text-rose-600' : ($item->stress_score > 35 ? 'text-amber-600' : 'text-emerald-600') }}">
                                {{ $item->stress_score }} / 100
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            @if($item->burnout_level === 'tinggi')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 animate-pulse">
                                    <i class="fas fa-circle-exclamation mr-1"></i>Tinggi
                                </span>
                            @elseif($item->burnout_level === 'sedang')
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">
                                    Sedang
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">
                                    Rendah (Sehat)
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-slate-700 font-semibold">
                            ⭐ {{ $item->workload_rating }} / 5
                        </td>
                        <td class="px-4 py-3.5 text-slate-500 text-[11px]">
                            {{ $item->notes ?: '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                            <i class="fas fa-brain text-3xl mb-2 text-slate-300 block"></i>
                            Belum ada skrining stres atau burnout yang tercatat.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-100">
            {{ $screenings->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Grafik Risiko Burnout (Doughnut)
    const riskCtx = document.getElementById('burnoutRiskChart');
    if (riskCtx && typeof Chart !== 'undefined') {
        const labels = {!! json_encode($burnoutChartData['labels']) !!};
        const dataValues = {!! json_encode($burnoutChartData['data']) !!};
        const bgColors = {!! json_encode($burnoutChartData['colors']) !!};
        const total = {{ $totalScreenings > 0 ? $totalScreenings : 1 }};

        new Chart(riskCtx, {
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

    // 2. Grafik Rata-rata Skor Stres per Divisi (Bar)
    const divCtx = document.getElementById('divisionStressChart');
    if (divCtx && typeof Chart !== 'undefined') {
        const divLabels = {!! json_encode($divisionChartData['labels']) !!};
        const divValues = {!! json_encode($divisionChartData['data']) !!};

        new Chart(divCtx, {
            type: 'bar',
            data: {
                labels: divLabels.length ? divLabels : ['Belum ada data divisi'],
                datasets: [{
                    label: 'Rata-rata Skor Stres (0-100)',
                    data: divValues.length ? divValues : [0],
                    backgroundColor: divValues.map(v => v > 60 ? 'rgba(239, 68, 68, 0.85)' : (v > 35 ? 'rgba(245, 158, 11, 0.85)' : 'rgba(16, 185, 129, 0.85)')),
                    borderRadius: 6,
                    maxBarThickness: 32
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
                                return ` Skor Stres: ${context.raw} / 100`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20,
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
