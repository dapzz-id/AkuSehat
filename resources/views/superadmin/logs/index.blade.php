@extends('layouts.app')

@section('title', 'Log & Audit Aktivitas Sistem')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-700 text-white flex items-center justify-center shadow-md shadow-emerald-700/20">
                    <i class="fas fa-clock-rotate-left text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight">Audit Trail & Log Aktivitas</h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Pemantauan jejak audit komprehensif: login, logout, pembayaran, perubahan hak akses, & pemeliharaan sistem.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Audit Logger Aktif</span>
            </span>
        </div>
    </div>

    <!-- 1. Stats Counter Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Log Tercatat</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1">{{ number_format($totalLogsCount) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Seluruh riwayat audit sistem</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xl">
                    <i class="fas fa-database"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-500">Aktivitas Otentikasi</p>
                    <h3 class="text-2xl font-black text-blue-900 mt-1">{{ number_format($authEventsCount) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Login & Logout sesi pengguna</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                    <i class="fas fa-right-to-bracket"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-600">Pembayaran & Billing</p>
                    <h3 class="text-2xl font-black text-emerald-900 mt-1">{{ number_format($billingEventsCount) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Transaksi & lisensi instansi</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-purple-600">Aksi Administratif</p>
                    <h3 class="text-2xl font-black text-purple-900 mt-1">{{ number_format($sensitiveActionsCount) }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1">User, fitur & maintenance</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl">
                    <i class="fas fa-shield-halved"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 w-full" style="width: 100%;">
        <!-- Trend Line Chart (Spans 2 Columns on LG) -->
        <div class="lg:col-span-2 min-w-0 w-full bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between" style="min-width: 0;">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-4 mb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-chart-line text-emerald-600"></i>
                        <span>Tren Aktivitas Sistem (14 Hari Terakhir)</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Volume log aktivitas harian lintas seluruh institusi dan platform</p>
                </div>
            </div>
            <div class="relative w-full min-w-0" style="height: 280px; min-height: 280px; min-width: 0;">
                <canvas id="auditTrendChart" style="width: 100% !important; height: 100% !important; display: block;"></canvas>
            </div>
        </div>

        <!-- Doughnut Distribution Chart (Spans 1 Column on LG) -->
        <div class="lg:col-span-1 min-w-0 w-full bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm flex flex-col justify-between" style="min-width: 0;">
            <div>
                <div class="pb-4 mb-4 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-cyan-600"></i>
                        <span>Distribusi Kategori</span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">Proporsi kejadian berdasarkan jenis aktivitas</p>
                </div>
                <div class="relative w-full min-w-0 flex items-center justify-center" style="height: 200px; min-height: 200px; min-width: 0;">
                    <canvas id="auditPieChart" style="max-height: 200px !important; display: block;"></canvas>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 gap-2.5 text-xs text-slate-600">
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500 flex-shrink-0"></span> <span class="truncate font-medium">Otentikasi</span></div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-emerald-500 flex-shrink-0"></span> <span class="truncate font-medium">Pembayaran</span></div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-purple-500 flex-shrink-0"></span> <span class="truncate font-medium">User & Hak</span></div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-rose-500 flex-shrink-0"></span> <span class="truncate font-medium">Maintenance</span></div>
            </div>
        </div>
    </div>

    <!-- 3. Filter Bar & Search -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
        <form method="GET" action="{{ route('superadmin.logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 items-end">
            <!-- Search Text (5 cols) -->
            <div class="lg:col-span-5">
                <label for="search" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Pencarian Log</label>
                <div class="relative">
                    <i class="fas fa-search text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 text-xs"></i>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari user, email, IP, instansi, atau deskripsi..."
                           class="w-full pl-9 pr-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600">
                </div>
            </div>

            <!-- Category Filter (3 cols) -->
            <div class="lg:col-span-3">
                <label for="category" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Kategori</label>
                <select id="category" 
                        name="category" 
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600">
                    <option value="all" {{ request('category') === 'all' || !request('category') ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="auth" {{ request('category') === 'auth' ? 'selected' : '' }}>Otentikasi (Login / Logout)</option>
                    <option value="billing" {{ request('category') === 'billing' ? 'selected' : '' }}>Pembayaran & Lisensi</option>
                    <option value="user" {{ request('category') === 'user' ? 'selected' : '' }}>Manajemen User</option>
                    <option value="maintenance" {{ request('category') === 'maintenance' ? 'selected' : '' }}>Mode Maintenance</option>
                    <option value="module" {{ request('category') === 'module' ? 'selected' : '' }}>Kontrol Fitur & Modul</option>
                    <option value="instansi" {{ request('category') === 'instansi' ? 'selected' : '' }}>Institusi</option>
                    <option value="system" {{ request('category') === 'system' ? 'selected' : '' }}>Sistem</option>
                </select>
            </div>

            <!-- Time Range (2 cols) -->
            <div class="lg:col-span-2">
                <label for="range" class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Rentang Waktu</label>
                <select id="range" 
                        name="range" 
                        class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600">
                    <option value="today" {{ request('range') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="7" {{ request('range') === '7' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="14" {{ request('range') === '14' || !request('range') ? 'selected' : '' }}>14 Hari Terakhir</option>
                    <option value="30" {{ request('range') === '30' ? 'selected' : '' }}>30 Hari Terakhir</option>
                </select>
            </div>

            <!-- Actions (2 cols) -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-sm">
                    <i class="fas fa-filter text-xs"></i>
                    <span>Terapkan</span>
                </button>
                @if(request()->hasAny(['search', 'category', 'range']))
                    <a href="{{ route('superadmin.logs.index') }}" title="Reset Filter" class="inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-colors">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- 4. Audit Log Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Aktivitas & Keterangan</th>
                        <th class="py-3 px-4">Pelaku / Pengguna</th>
                        <th class="py-3 px-4">Institusi</th>
                        <th class="py-3 px-4">Koneksi & Perangkat</th>
                        <th class="py-3 px-4 text-center">Payload</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Waktu -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="font-semibold text-slate-800 block">{{ $log->created_at->diffForHumans() }}</span>
                                <span class="text-[10px] text-slate-400 block font-mono">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                            </td>

                            <!-- Kategori -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                @php
                                    $badgeStyle = [
                                        'auth' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'billing' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                        'user' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'maintenance' => 'bg-rose-100 text-rose-800 border-rose-200',
                                        'module' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                        'instansi' => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'system' => 'bg-slate-200 text-slate-800 border-slate-300',
                                    ][$log->category] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $badgeStyle }}">
                                    <i class="fas {{ $log->category_icon }} text-[9px]"></i>
                                    <span>{{ $log->category_label }}</span>
                                </span>
                            </td>

                            <!-- Aktivitas & Keterangan -->
                            <td class="py-3 px-4 min-w-[240px]">
                                <div class="font-mono text-[11px] font-semibold text-slate-800 mb-0.5">
                                    {{ $log->action }}
                                </div>
                                <div class="text-slate-600 line-clamp-2 leading-relaxed">
                                    {{ $log->description }}
                                </div>
                            </td>

                            <!-- Pengguna -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($log->user_name)
                                    <div class="font-bold text-slate-800">{{ $log->user_name }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $log->user_email }}</div>
                                    @if($log->user_role || $log->user_level)
                                        <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-100 text-slate-600 mt-0.5">
                                            {{ $log->user_role ?? $log->user_level }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Sistem / Tamu</span>
                                @endif
                            </td>

                            <!-- Institusi -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($log->instansi_name)
                                    <div class="font-medium text-slate-800 truncate max-w-[160px]" title="{{ $log->instansi_name }}">
                                        {{ $log->instansi_name }}
                                    </div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Koneksi & Perangkat -->
                            <td class="py-3 px-4 whitespace-nowrap font-mono text-[11px]">
                                <div class="flex items-center gap-1.5 text-slate-700 font-semibold">
                                    <i class="fas fa-network-wired text-[10px] text-slate-400"></i>
                                    <span>{{ $log->ip_address ?? '127.0.0.1' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 truncate max-w-[180px] font-sans" title="{{ $log->user_agent }}">
                                    {{ $log->device_summary }}
                                </div>
                            </td>

                            <!-- Payload / JSON Modal Button -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if(!empty($log->properties))
                                    <button type="button" 
                                            onclick='openLogPayloadModal(@json($log->properties), "{{ $log->action }}", "{{ $log->created_at->format('Y-m-d H:i:s') }}")' 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 border border-slate-200 transition-colors">
                                        <i class="fas fa-code text-[10px]"></i>
                                        <span>Data</span>
                                    </button>
                                @else
                                    <span class="text-slate-300 text-[10px]">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300 text-2xl border border-slate-100">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <p class="text-sm font-semibold text-slate-600">Belum ada catatan log aktivitas yang sesuai.</p>
                                <p class="text-xs text-slate-400 mt-1">Gunakan tombol reset atau sesuaikan parameter filter di atas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Detail Payload JSON -->
<div id="logPayloadModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-xl rounded-2xl border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-in fade-in duration-200">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
            <div>
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-code text-emerald-600"></i>
                    <span>Payload & Data Teknis Log</span>
                </h3>
                <p id="modalLogSubtitle" class="text-[11px] text-slate-500 mt-0.5 font-mono"></p>
            </div>
            <button type="button" onclick="closeLogPayloadModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 flex items-center justify-center transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div style="background-color: #0b1120 !important; border: 1px solid #1e293b;" class="m-5 rounded-2xl p-4 overflow-y-auto flex-1 shadow-inner">
            <div class="flex items-center justify-between pb-2.5 mb-2.5 border-b border-slate-800 text-[11px]">
                <span class="font-mono flex items-center gap-1.5 text-emerald-400 font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> JSON Payload Viewer
                </span>
                <button type="button" onclick="copyModalJson()" id="btnCopyJson" 
                        style="background-color: #1e293b; color: #f8fafc;"
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-slate-200 hover:bg-slate-700 hover:text-white transition-colors text-xs font-semibold cursor-pointer">
                    <i class="far fa-copy text-slate-300"></i>
                    <span>Salin JSON</span>
                </button>
            </div>
            <pre id="modalJsonDisplay" 
                 style="color: #38bdf8 !important; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important; font-size: 13px !important; line-height: 1.6 !important;" 
                 class="whitespace-pre-wrap break-all text-sky-400 font-mono"></pre>
        </div>

        <div class="px-5 py-3 border-t border-slate-100 flex justify-end bg-white">
            <button type="button" onclick="closeLogPayloadModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inisialisasi Chart Tren Aktivitas (Line Chart)
        const trendCanvas = document.getElementById('auditTrendChart');
        if (trendCanvas) {
            const ctxTrend = trendCanvas.getContext('2d');
            
            // Gradient fill
            const gradient = ctxTrend.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.28)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: @json($trendLabels),
                    datasets: [{
                        label: 'Jumlah Kejadian',
                        data: @json($trendCounts),
                        borderColor: '#10b981',
                        borderWidth: 2.5,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.parsed.y} aktivitas dicatat`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, family: 'sans-serif' }, color: '#64748b' }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f1f5f9' },
                            ticks: { 
                                stepSize: 1,
                                font: { size: 11, family: 'sans-serif' }, 
                                color: '#64748b' 
                            }
                        }
                    }
                }
            });
        }

        // 2. Inisialisasi Chart Distribusi Kategori (Doughnut Chart)
        const pieCanvas = document.getElementById('auditPieChart');
        if (pieCanvas) {
            const ctxPie = pieCanvas.getContext('2d');
            const pieDataValues = @json($pieData);
            const totalData = pieDataValues.reduce((a, b) => a + b, 0);

            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: @json($pieLabels),
                    datasets: [{
                        data: totalData > 0 ? pieDataValues : [1],
                        backgroundColor: totalData > 0 ? @json($pieColors) : ['#e2e8f0'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            bodyFont: { size: 11 },
                            padding: 8,
                            cornerRadius: 8,
                            enabled: totalData > 0
                        }
                    },
                    cutout: '68%'
                }
            });
        }
    });

    // Modal Payload Logic
    function openLogPayloadModal(data, action, timestamp) {
        const modal = document.getElementById('logPayloadModal');
        const subtitle = document.getElementById('modalLogSubtitle');
        const display = document.getElementById('modalJsonDisplay');

        subtitle.innerText = `${action} • ${timestamp}`;
        display.innerText = JSON.stringify(data, null, 2);

        modal.classList.remove('hidden');
    }

    function closeLogPayloadModal() {
        const modal = document.getElementById('logPayloadModal');
        modal.classList.add('hidden');
    }

    function copyModalJson() {
        const display = document.getElementById('modalJsonDisplay');
        if (!display) return;
        const text = display.innerText;
        navigator.clipboard.writeText(text).then(() => {
            const btn = document.getElementById('btnCopyJson');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-check text-emerald-400"></i> <span class="text-emerald-400">Tersalin!</span>';
                setTimeout(() => {
                    btn.innerHTML = '<i class="far fa-copy text-slate-300"></i> <span>Salin JSON</span>';
                }, 2000);
            }
        }).catch(() => {
            alert('Gagal menyalin teks ke clipboard.');
        });
    }

    // Close modal on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLogPayloadModal();
        }
    });
</script>
@endpush
