@extends('layouts.app')

@section('title', 'Monitoring Aktivitas & Olahraga')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/20">
                    <i class="fas fa-running text-base"></i>
                </div>
                <span>Monitoring Aktivitas & Olahraga</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pantau keaktifan fisik, jarak tempuh lari, dan kalori terbakar seluruh anggota institusi.
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(auth()->user()->isAdminInstansi())
                <div class="px-3.5 py-2 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-bold inline-flex items-center gap-2 shadow-sm">
                    <i class="fas fa-chart-pie text-emerald-600"></i>
                    <span>Mode Laporan & Monitoring (Read-Only)</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total KM -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Jarak Tempuh</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-route text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ round($totalDistanceMeters / 1000, 1) }}</span>
                <span class="text-xs font-bold text-emerald-600">Kilometer</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Akumulasi seluruh anggota</p>
        </div>

        <!-- Total Sesi -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Sesi Lari</span>
                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                    <i class="fas fa-stopwatch text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $totalSessions }}</span>
                <span class="text-xs font-bold text-teal-600">Sesi Olahraga</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Workout tercatat</p>
        </div>

        <!-- Total Kalori -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Kalori Terbakar</span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i class="fas fa-fire text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ number_format($totalCalories, 0, ',', '.') }}</span>
                <span class="text-xs font-bold text-rose-600">Kkal</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Energi metabolisme</p>
        </div>

        <!-- Total Langkah -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Estimasi Langkah</span>
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i class="fas fa-shoe-prints text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ number_format($totalSteps, 0, ',', '.') }}</span>
                <span class="text-xs font-bold text-sky-600">Langkah</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Pedometer tracker</p>
        </div>
    </div>

    <!-- Grafik Analisis Aktivitas Olahraga -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Grafik 1: Distribusi Jenis Aktivitas Olahraga -->
        <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Distribusi Jenis Olahraga</h3>
                        <p class="text-[11px] text-slate-400">Preferensi tipe workout anggota</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                    {{ $totalSessions }} Sesi
                </span>
            </div>

            <div class="relative flex items-center justify-center my-auto py-3">
                <div class="w-44 h-44 sm:w-48 sm:h-48 relative">
                    <canvas id="activityTypeChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 text-[11px]">
                <div class="flex items-center justify-between p-2 rounded-lg bg-emerald-50/60 border border-emerald-200/80">
                    <span class="text-emerald-700 font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Lari
                    </span>
                    <span class="font-bold text-emerald-800">{{ $activityTypeChartData['data'][0] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg bg-sky-50/60 border border-sky-200/80">
                    <span class="text-sky-700 font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span> Jalan Santai
                    </span>
                    <span class="font-bold text-sky-800">{{ $activityTypeChartData['data'][1] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg bg-amber-50/60 border border-amber-200/80">
                    <span class="text-amber-700 font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Bersepeda
                    </span>
                    <span class="font-bold text-amber-800">{{ $activityTypeChartData['data'][2] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg bg-purple-50/60 border border-purple-200/80">
                    <span class="text-purple-700 font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Lainnya
                    </span>
                    <span class="font-bold text-purple-800">{{ $activityTypeChartData['data'][3] }}</span>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Jarak Tempuh per Divisi -->
        <div class="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center text-xs font-bold">
                        <i class="fas fa-chart-column"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Akumulasi Jarak Olahraga per Divisi (KM)</h3>
                        <p class="text-[11px] text-slate-400">Total kilometer tempuh seluruh anggota per kelas/divisi</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-teal-800 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200">
                    Top Divisi
                </span>
            </div>

            <div class="relative w-full h-56 sm:h-64 my-auto">
                <canvas id="divisionKmChart"></canvas>
            </div>

            <p class="text-[11px] text-slate-400 pt-2 border-t border-slate-100 flex items-center gap-1">
                <i class="fas fa-info-circle text-teal-500"></i>
                <span>Data dihitung otomatis berdasarkan akumulasi jarak GPS & sensor aktivitas pengguna.</span>
            </p>
        </div>
    </div>

    <!-- Leaderboard Antar-Divisi -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2 mb-1">
            <i class="fas fa-trophy text-amber-500"></i>
            <span>Peringkat Kebugaran Antar-Divisi</span>
        </h2>
        <p class="text-xs text-slate-500 mb-4">
            Kompetisi sehat antarkelas atau antardepartemen berdasarkan akumulasi jarak tempuh.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($divisionStats as $index => $div)
                <div class="p-3.5 rounded-xl border border-slate-200/80 bg-slate-50/50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-sm {{ $index === 0 ? 'bg-amber-400 text-white shadow-sm' : ($index === 1 ? 'bg-slate-300 text-slate-700' : ($index === 2 ? 'bg-amber-700 text-white' : 'bg-slate-100 text-slate-500')) }}">
                        #{{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xs font-bold text-slate-800 truncate">{{ $div['nama_divisi'] }}</h3>
                        <p class="text-[10px] text-slate-400">{{ $div['user_count'] }} Anggota &bull; {{ $div['total_sessions'] }} Sesi</p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-black text-emerald-600">{{ $div['total_km'] }}</span>
                        <span class="text-[10px] font-bold text-slate-400 block -mt-1">KM</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-4 text-center text-xs text-slate-400">
                    Belum ada data divisi tercatat.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Log Aktivitas Anggota (Tabel) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <!-- Filter Bar -->
        <div class="p-4 sm:p-5 border-b border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-800">Log Aktivitas Terbaru</h3>
                <p class="text-xs text-slate-500">Daftar riwayat sesi olahraga anggota institusi</p>
            </div>

            <form action="{{ route('admin.activities.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                <select name="divisi_id" onchange="this.form.submit()" class="px-3 py-1.5 text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-600">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>{{ $div->divisi_name }}</option>
                    @endforeach
                </select>

                <select name="activity_type" onchange="this.form.submit()" class="px-3 py-1.5 text-xs text-slate-700 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-emerald-600">
                    <option value="">Semua Olahraga</option>
                    <option value="run" {{ request('activity_type') == 'run' ? 'selected' : '' }}>Lari</option>
                    <option value="walk" {{ request('activity_type') == 'walk' ? 'selected' : '' }}>Jalan Santai</option>
                    <option value="cycling" {{ request('activity_type') == 'cycling' ? 'selected' : '' }}>Bersepeda</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-slate-500 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3">Nama Anggota</th>
                        <th class="px-4 py-3">Olahraga</th>
                        <th class="px-4 py-3">Jarak</th>
                        <th class="px-4 py-3">Durasi</th>
                        <th class="px-4 py-3">Pace Rata-rata</th>
                        <th class="px-4 py-3">Kalori</th>
                        <th class="px-4 py-3">Tanggal Waktu</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($activities as $act)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3.5">
                                <span class="font-bold text-slate-800 block text-xs">{{ $act->user_nama }}</span>
                                <span class="text-[10px] text-slate-400">{{ $act->divisi_name ?? 'Tanpa Divisi' }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $act->activity_type === 'run' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($act->activity_type === 'walk' ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                    <i class="fas {{ $act->activity_type === 'run' ? 'fa-running' : ($act->activity_type === 'walk' ? 'fa-person-walking' : 'fa-bicycle') }}"></i>
                                    <span>{{ ucfirst($act->activity_type) }}</span>
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="font-black text-slate-800 text-sm">{{ number_format($act->distance_meters / 1000, 2) }}</span>
                                <span class="text-[10px] font-bold text-slate-400">KM</span>
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-slate-700">
                                {{ floor($act->duration_seconds / 60) }}m {{ $act->duration_seconds % 60 }}s
                            </td>
                            <td class="px-4 py-3.5 font-mono text-slate-600">
                                {{ $act->avg_pace ?? '-' }}/km
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-rose-600">
                                {{ round($act->calories) }} kcal
                            </td>
                            <td class="px-4 py-3.5 text-slate-400 text-[11px]">
                                {{ \Carbon\Carbon::parse($act->started_at)->locale('id')->isoFormat('D MMM Y, HH:mm') }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <button type="button"
                                        onclick="openActivityDetail(this); return false;"
                                        class="view-activity-detail px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/80 rounded-lg text-xs font-semibold inline-flex items-center gap-1.5 transition-colors cursor-pointer shadow-sm"
                                        data-nama="{{ $act->user_nama }}"
                                        data-divisi="{{ $act->divisi_name ?? '-' }}"
                                        data-nomor-induk="{{ $act->nomor_induk ?? '-' }}"
                                        data-title="{{ $act->title ?? 'Sesi Olahraga' }}"
                                        data-type="{{ ucfirst($act->activity_type) }}"
                                        data-distance="{{ number_format($act->distance_meters / 1000, 2) }} KM"
                                        data-duration="{{ floor($act->duration_seconds / 60) }} menit {{ $act->duration_seconds % 60 }} detik"
                                        data-pace="{{ $act->avg_pace ?? '-' }}/km"
                                        data-speed="{{ $act->max_speed ? $act->max_speed . ' km/jam' : '-' }}"
                                        data-calories="{{ round($act->calories) }} kcal"
                                        data-steps="{{ number_format($act->steps) }} langkah"
                                        data-started="{{ \Carbon\Carbon::parse($act->started_at)->locale('id')->isoFormat('D MMMM Y, HH:mm') }}"
                                        data-finished="{{ $act->finished_at ? \Carbon\Carbon::parse($act->finished_at)->locale('id')->isoFormat('D MMMM Y, HH:mm') : '-' }}"
                                        data-notes="{{ $act->notes ?? '-' }}">
                                    <i class="fas fa-file-lines text-xs"></i>
                                    <span>Lihat Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <i class="fas fa-person-running text-3xl mb-2 text-slate-300 block"></i>
                                Belum ada aktivitas olahraga tercatat di institusi ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="p-4 border-t border-slate-200/80">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Detail Aktivitas Olahraga -->
<div id="activityDetailModal" class="fixed inset-0 !m-0 !mt-0 top-0 left-0 right-0 bottom-0 bg-slate-950/70 backdrop-blur-sm hidden z-[999999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="if(event.target === this) closeActivityDetailModal()">
    <div class="relative bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl space-y-4 max-h-[92vh] overflow-y-auto my-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                    <i class="fas fa-running"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-800" id="actModalNama">Detail Aktivitas</h3>
                    <p class="text-[11px] text-slate-400" id="actModalSub">-</p>
                </div>
            </div>
            <button type="button" onclick="closeActivityDetailModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        @if(auth()->user()->isAdminInstansi())
        <div class="p-3 bg-emerald-50/80 border border-emerald-200/80 rounded-xl text-emerald-800 text-xs flex items-center gap-2">
            <i class="fas fa-eye text-emerald-600 text-sm shrink-0"></i>
            <span><strong>Mode Pantau (Read-Only):</strong> Admin Instansi hanya memiliki hak akses rekapitulasi data olahraga anggota tanpa izin modifikasi rekaman GPS/sensor.</span>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Jenis Olahraga</span>
                <span class="font-bold text-slate-800 text-sm mt-0.5 block" id="actModalType">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Jarak</span>
                <span class="font-black text-emerald-600 text-sm mt-0.5 block" id="actModalDistance">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Durasi Total</span>
                <span class="font-bold text-slate-800 mt-0.5 block" id="actModalDuration">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Pace Rata-rata</span>
                <span class="font-bold text-slate-800 mt-0.5 block" id="actModalPace">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Kalori Terbakar</span>
                <span class="font-bold text-rose-600 mt-0.5 block" id="actModalCalories">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Langkah (Pedometer)</span>
                <span class="font-bold text-sky-600 mt-0.5 block" id="actModalSteps">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Kecepatan Maksimal</span>
                <span class="font-bold text-slate-800 mt-0.5 block" id="actModalSpeed">-</span>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Waktu Mulai</span>
                <span class="font-medium text-slate-700 mt-0.5 block text-[11px]" id="actModalStarted">-</span>
            </div>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
            <span class="text-slate-400 block text-[10px] uppercase font-bold mb-1">Catatan Workout</span>
            <p class="text-slate-700 font-medium leading-relaxed" id="actModalNotes">-</p>
        </div>

        <div class="flex justify-end pt-2 border-t border-slate-100">
            <button type="button" onclick="closeActivityDetailModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
window.closeActivityDetailModal = function() {
    const m = document.getElementById('activityDetailModal');
    if (m) {
        m.classList.add('hidden');
        m.style.display = 'none';
        m.style.setProperty('display', 'none', 'important');
    }
};

window.openActivityDetail = function(btn) {
    try {
        if (!btn) {
            btn = (window.event && window.event.target) ? window.event.target.closest('button, .view-activity-detail') : null;
        } else if (btn.closest) {
            btn = btn.closest('button, .view-activity-detail') || btn;
        }
        if (!btn) return;

        const getVal = function(attr, fallback) {
            return btn.getAttribute(attr) || (btn.dataset ? btn.dataset[attr.replace(/^data-/, '').replace(/-([a-z])/g, g => g[1].toUpperCase())] : null) || fallback || '-';
        };

        const setTxt = function(id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };

        setTxt('actModalNama', getVal('data-nama', 'Anggota'));
        const divisi = getVal('data-divisi', '-');
        const noInduk = getVal('data-nomor-induk', '-');
        setTxt('actModalSub', divisi + ' • Nomor Induk: ' + noInduk);
        setTxt('actModalType', getVal('data-type', '-'));
        setTxt('actModalDistance', getVal('data-distance', '-'));
        setTxt('actModalDuration', getVal('data-duration', '-'));
        setTxt('actModalPace', getVal('data-pace', '-'));
        setTxt('actModalCalories', getVal('data-calories', '-'));
        setTxt('actModalSteps', getVal('data-steps', '-'));
        setTxt('actModalSpeed', getVal('data-speed', '-'));
        setTxt('actModalStarted', getVal('data-started', '-'));
        setTxt('actModalNotes', getVal('data-notes', 'Tidak ada catatan tambahan.'));

        const m = document.getElementById('activityDetailModal');
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
        console.error('Error opening activity detail modal:', err);
        const m = document.getElementById('activityDetailModal');
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

// Delegasi klik tombol Lihat Detail Aktivitas (Fallback)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-activity-detail');
    if (btn) {
        e.preventDefault();
        openActivityDetail(btn);
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeActivityDetailModal();
    }
});

// Inisialisasi Grafik Analitik Aktivitas Olahraga
document.addEventListener('DOMContentLoaded', function() {
    // 1. Grafik Jenis Olahraga (Doughnut)
    const typeCtx = document.getElementById('activityTypeChart');
    if (typeCtx && typeof Chart !== 'undefined') {
        const labels = {!! json_encode($activityTypeChartData['labels']) !!};
        const dataValues = {!! json_encode($activityTypeChartData['data']) !!};
        const bgColors = {!! json_encode($activityTypeChartData['colors']) !!};
        const total = {{ $totalSessions > 0 ? $totalSessions : 1 }};

        new Chart(typeCtx, {
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
                                return ` ${context.label}: ${val} sesi (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '72%'
            }
        });
    }

    // 2. Grafik Total Jarak Tempuh per Divisi (Bar)
    const divCtx = document.getElementById('divisionKmChart');
    if (divCtx && typeof Chart !== 'undefined') {
        const divLabels = {!! json_encode($divisionKmChartData['labels']) !!};
        const divValues = {!! json_encode($divisionKmChartData['data']) !!};

        new Chart(divCtx, {
            type: 'bar',
            data: {
                labels: divLabels.length ? divLabels : ['Belum ada data'],
                datasets: [{
                    label: 'Total Jarak (KM)',
                    data: divValues.length ? divValues : [0],
                    backgroundColor: 'rgba(20, 184, 166, 0.85)',
                    borderColor: '#0d9488',
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
                                return ` ${context.raw} Kilometer`;
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
