@extends('layouts.app')

@section('title', 'Monitoring Vitalitas & Hidrasi')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-sky-600 text-white flex items-center justify-center shadow-md shadow-sky-600/20">
                    <i class="fas fa-heart-pulse text-base"></i>
                </div>
                <span>Monitoring Vitalitas & Hidrasi</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pantau kepatuhan asupan air harian, indeks kualitas tidur, dan status mood anggota institusi.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(auth()->user()->isAdminInstansi())
                <div class="px-3.5 py-2 bg-sky-50 border border-sky-200 text-sky-800 rounded-xl text-xs font-bold inline-flex items-center gap-2 shadow-sm">
                    <i class="fas fa-chart-pie text-sky-600"></i>
                    <span>Mode Laporan & Monitoring (Read-Only)</span>
                </div>
            @endif

            <form action="{{ route('admin.vital-logs.index') }}" method="GET" class="flex items-center gap-2">
                <input type="date" 
                       name="date" 
                       value="{{ $dateFilter }}" 
                       onchange="this.form.submit()"
                       class="px-3 py-1.5 text-xs text-slate-700 bg-white border border-slate-200 rounded-xl shadow-sm focus:outline-none focus:border-sky-600 font-medium">
            </form>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Log Anggota -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Check-In</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-clipboard-user text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $totalLogs }}</span>
                <span class="text-xs font-bold text-indigo-600">Anggota</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Mengisi log pada tanggal ini</p>
        </div>

        <!-- Rata-rata Hidrasi -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata Air</span>
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i class="fas fa-droplet text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ number_format($avgWater, 0, ',', '.') }}</span>
                <span class="text-xs font-bold text-sky-600">ml / hari</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Target standar: 2.000 ml</p>
        </div>

        <!-- Rata-rata Tidur -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-rata Tidur</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i class="fas fa-moon text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $avgSleep }}</span>
                <span class="text-xs font-bold text-purple-600">Jam</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Durasi istirahat malam</p>
        </div>

        <!-- Rata-rata Mood -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Indeks Mood</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fas fa-smile text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $avgMood }}</span>
                <span class="text-xs font-bold text-amber-600">/ 5.0</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Level energi & kenyamanan</p>
        </div>
    </div>

    <!-- Grafik Analisis Vitalitas & Kualitas Tidur -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Grafik 1: Distribusi Durasi Tidur -->
        <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Distribusi Kualitas & Jam Tidur</h3>
                        <p class="text-[11px] text-slate-400">Kepatuhan durasi istirahat (WHO)</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-purple-800 bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200">
                    {{ $totalLogs }} Data
                </span>
            </div>

            <div class="relative flex items-center justify-center my-auto py-3">
                <div class="w-44 h-44 sm:w-48 sm:h-48 relative">
                    <canvas id="vitalSleepChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-1.5 pt-3 border-t border-slate-100 text-[10px]">
                <div class="p-2 rounded-lg bg-rose-50/70 border border-rose-200/80 text-center">
                    <span class="text-rose-700 font-medium block">Kurang</span>
                    <span class="font-bold text-rose-800 text-xs">{{ $sleepChartData['data'][0] }}</span>
                </div>
                <div class="p-2 rounded-lg bg-emerald-50/70 border border-emerald-200/80 text-center">
                    <span class="text-emerald-700 font-medium block">Ideal</span>
                    <span class="font-bold text-emerald-800 text-xs">{{ $sleepChartData['data'][1] }}</span>
                </div>
                <div class="p-2 rounded-lg bg-indigo-50/70 border border-indigo-200/80 text-center">
                    <span class="text-indigo-700 font-medium block">Panjang</span>
                    <span class="font-bold text-indigo-800 text-xs">{{ $sleepChartData['data'][2] }}</span>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Rata-rata Konsumsi Air per Divisi -->
        <div class="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-droplet"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Capaian Hidrasi Rata-rata per Divisi (ml)</h3>
                        <p class="text-[11px] text-slate-400">Rata-rata konsumsi air harian anggota per divisi</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-sky-800 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-200">
                    Standar: 2.000 ml
                </span>
            </div>

            <div class="relative w-full h-56 sm:h-64 my-auto">
                <canvas id="vitalWaterChart"></canvas>
            </div>

            <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-100 flex items-center gap-1">
                <i class="fas fa-info-circle text-sky-500"></i>
                <span>Angka dihitung berdasarkan log harian anggota pada tanggal yang dipilih.</span>
            </p>
        </div>
    </div>

    <!-- Tabel Detail Log Vitalitas -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-5 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-800">Daftar Log Harian Anggota</h3>
                <p class="text-xs text-slate-500">Status biomarker & kebiasaan hidup sehat per anggota</p>
            </div>

            <form action="{{ route('admin.vital-logs.index') }}" method="GET" class="flex items-center gap-2">
                <input type="hidden" name="date" value="{{ $dateFilter }}">
                <select name="divisi_id" onchange="this.form.submit()" class="px-3 py-1.5 text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-sky-600">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>{{ $div->divisi_name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3">Nama Anggota</th>
                        <th class="px-4 py-3">Konsumsi Air</th>
                        <th class="px-4 py-3">Jam Tidur</th>
                        <th class="px-4 py-3">Mood & Energi</th>
                        <th class="px-4 py-3">Tekanan Darah</th>
                        <th class="px-4 py-3">Berat Badan</th>
                        <th class="px-4 py-3">Terakhir Update</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        @php
                            $waterPercent = min(100, round(($log->water_intake_ml / 2000) * 100));
                            $emojis = [1 => '😫 Stres', 2 => '😔 Lelah', 3 => '😐 Biasa', 4 => '😊 Baik', 5 => '🤩 Hebat'];
                            $moodText = $emojis[$log->mood_score] ?? '-';
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-slate-800 block text-xs">{{ $log->user_nama }}</span>
                                <span class="text-[10px] text-slate-400">{{ $log->divisi_name ?? 'Tanpa Divisi' }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-sky-700 text-sm">{{ $log->water_intake_ml }} ml</span>
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full font-bold {{ $waterPercent >= 100 ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800' }}">
                                        {{ $waterPercent }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-slate-700">
                                {{ $log->sleep_hours ? $log->sleep_hours . ' Jam' : '-' }}
                            </td>
                            <td class="px-4 py-3.5">
                                @if($log->mood_score)
                                    <span class="font-semibold text-slate-800">{{ $moodText }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-700">
                                @if($log->systolic && $log->diastolic)
                                    <span class="font-bold {{ $log->systolic >= 140 || $log->diastolic >= 90 ? 'text-rose-600' : 'text-slate-800' }}">
                                        {{ $log->systolic }}/{{ $log->diastolic }}
                                    </span> mmHg
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-slate-700">
                                {{ $log->weight_kg ? $log->weight_kg . ' kg' : '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-slate-400 text-[11px]">
                                {{ \Carbon\Carbon::parse($log->updated_at)->locale('id')->isoFormat('HH:mm') }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <button type="button"
                                        onclick="openVitalDetail(this); return false;"
                                        class="view-vital-detail px-2.5 py-1 bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-200/80 rounded-lg text-xs font-semibold inline-flex items-center gap-1.5 transition-colors cursor-pointer shadow-sm"
                                        data-nama="{{ $log->user_nama }}"
                                        data-divisi="{{ $log->divisi_name ?? '-' }}"
                                        data-nomor-induk="{{ $log->nomor_induk ?? '-' }}"
                                        data-date="{{ \Carbon\Carbon::parse($log->log_date)->locale('id')->isoFormat('D MMMM Y') }}"
                                        data-water="{{ $log->water_intake_ml }} ml ({{ $waterPercent }}% dari 2.000 ml)"
                                        data-sleep="{{ $log->sleep_hours ? $log->sleep_hours . ' Jam' : '-' }}"
                                        data-mood="{{ $moodText }}"
                                        data-tensi="{{ ($log->systolic && $log->diastolic) ? $log->systolic . '/' . $log->diastolic . ' mmHg' : '-' }}"
                                        data-gula="{{ $log->blood_sugar ? $log->blood_sugar . ' mg/dL' : '-' }}"
                                        data-bb="{{ $log->weight_kg ? $log->weight_kg . ' kg' : '-' }}"
                                        data-notes="{{ $log->notes ?? '-' }}">
                                    <i class="fas fa-file-lines text-xs"></i>
                                    <span>Lihat Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fas fa-heart-pulse text-3xl mb-2 text-slate-300 block"></i>
                                Belum ada catatan vitalitas pada tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200/80">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Detail Log Vitalitas & Tidur -->
<div id="vitalDetailModal" class="fixed inset-0 !m-0 !mt-0 top-0 left-0 right-0 bottom-0 bg-slate-950/70 backdrop-blur-sm hidden z-[999999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="if(event.target === this) closeVitalDetailModal()">
    <div class="relative bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl space-y-4 max-h-[92vh] overflow-y-auto my-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm">
                    <i class="fas fa-heart-pulse"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-800" id="vitalModalNama">Detail Vitalitas</h3>
                    <p class="text-[11px] text-slate-400" id="vitalModalSub">-</p>
                </div>
            </div>
            <button type="button" onclick="closeVitalDetailModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        @if(auth()->user()->isAdminInstansi())
        <div class="p-3 bg-sky-50/80 border border-sky-200/80 rounded-xl text-sky-800 text-xs flex items-center gap-2">
            <i class="fas fa-eye text-sky-600 text-sm shrink-0"></i>
            <span><strong>Mode Pantau (Read-Only):</strong> Admin Instansi memiliki hak pantau biomarker harian tanpa izin mengubah entri rekam biologis anggota.</span>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Tanggal Log</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 block" id="vitalModalDate">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Konsumsi Air</span>
                <span class="font-black text-sky-600 text-sm mt-0.5 block" id="vitalModalWater">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Durasi Tidur Malam</span>
                <span class="font-bold text-purple-700 text-sm mt-0.5 block" id="vitalModalSleep">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Mood & Tingkat Energi</span>
                <span class="font-bold text-amber-700 text-sm mt-0.5 block" id="vitalModalMood">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Tekanan Darah</span>
                <span class="font-bold text-slate-800 mt-0.5 block" id="vitalModalTensi">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Gula Darah</span>
                <span class="font-bold text-slate-800 mt-0.5 block" id="vitalModalGula">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 col-span-2">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Berat Badan</span>
                <span class="font-bold text-slate-800 mt-0.5 block" id="vitalModalBb">-</span>
            </div>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
            <span class="text-slate-400 block text-[10px] uppercase font-bold mb-1">Catatan Tambahan Harian</span>
            <p class="text-slate-700 font-medium leading-relaxed" id="vitalModalNotes">-</p>
        </div>

        <div class="flex justify-end pt-2 border-t border-slate-100">
            <button type="button" onclick="closeVitalDetailModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
window.closeVitalDetailModal = function() {
    const m = document.getElementById('vitalDetailModal');
    if (m) {
        m.classList.add('hidden');
        m.style.display = 'none';
        m.style.setProperty('display', 'none', 'important');
    }
};

window.openVitalDetail = function(btn) {
    try {
        if (!btn) {
            btn = (window.event && window.event.target) ? window.event.target.closest('button, .view-vital-detail') : null;
        } else if (btn.closest) {
            btn = btn.closest('button, .view-vital-detail') || btn;
        }
        if (!btn) return;

        const getVal = function(attr, fallback) {
            return btn.getAttribute(attr) || (btn.dataset ? btn.dataset[attr.replace(/^data-/, '').replace(/-([a-z])/g, g => g[1].toUpperCase())] : null) || fallback || '-';
        };

        const setTxt = function(id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };

        setTxt('vitalModalNama', getVal('data-nama', 'Anggota'));
        const divisi = getVal('data-divisi', '-');
        const noInduk = getVal('data-nomor-induk', '-');
        setTxt('vitalModalSub', divisi + ' • Nomor Induk: ' + noInduk);
        setTxt('vitalModalDate', getVal('data-date', '-'));
        setTxt('vitalModalWater', getVal('data-water', '-'));
        setTxt('vitalModalSleep', getVal('data-sleep', '-'));
        setTxt('vitalModalMood', getVal('data-mood', '-'));
        setTxt('vitalModalTensi', getVal('data-tensi', '-'));
        setTxt('vitalModalGula', getVal('data-gula', '-'));
        setTxt('vitalModalBb', getVal('data-bb', '-'));
        setTxt('vitalModalNotes', getVal('data-notes', 'Tidak ada catatan tambahan.'));

        const m = document.getElementById('vitalDetailModal');
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
        console.error('Error opening vital detail modal:', err);
        const m = document.getElementById('vitalDetailModal');
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

// Delegasi klik tombol Lihat Detail Vitalitas (Fallback)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-vital-detail');
    if (btn) {
        e.preventDefault();
        openVitalDetail(btn);
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeVitalDetailModal();
    }
});

// Inisialisasi Grafik Analitik Vitalitas & Tidur
document.addEventListener('DOMContentLoaded', function() {
    // 1. Grafik Durasi Tidur (Doughnut)
    const sleepCtx = document.getElementById('vitalSleepChart');
    if (sleepCtx && typeof Chart !== 'undefined') {
        const labels = {!! json_encode($sleepChartData['labels']) !!};
        const dataValues = {!! json_encode($sleepChartData['data']) !!};
        const bgColors = {!! json_encode($sleepChartData['colors']) !!};
        const total = {{ $totalLogs > 0 ? $totalLogs : 1 }};

        new Chart(sleepCtx, {
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
                                return ` ${context.label}: ${val} anggota (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '72%'
            }
        });
    }

    // 2. Grafik Rata-rata Konsumsi Air per Divisi (Bar)
    const waterCtx = document.getElementById('vitalWaterChart');
    if (waterCtx && typeof Chart !== 'undefined') {
        const waterLabels = {!! json_encode($divisionWaterChartData['labels']) !!};
        const waterValues = {!! json_encode($divisionWaterChartData['data']) !!};

        new Chart(waterCtx, {
            type: 'bar',
            data: {
                labels: waterLabels.length ? waterLabels : ['Belum ada data'],
                datasets: [{
                    label: 'Rata-rata Air (ml)',
                    data: waterValues.length ? waterValues : [0],
                    backgroundColor: 'rgba(14, 165, 233, 0.85)',
                    borderColor: '#0284c7',
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
                                return ` ${context.raw} ml / hari`;
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
