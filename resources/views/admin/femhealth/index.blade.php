@extends('layouts.app')

@section('title', 'Siklus Menstruasi & Peminjaman Pita UKS / Cuti Haid')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-pink-600 text-white flex items-center justify-center shadow-md shadow-pink-600/20">
                    <i class="fas fa-venus text-base"></i>
                </div>
                <span>Siklus Menstruasi & Kesehatan Reproduksi Wanita</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Sistem terintegrasi: <strong class="text-slate-700">Health Monitor UKS</strong>, <strong class="text-slate-700">Health Consultant (BK)</strong>, <strong class="text-slate-700">Admin Instansi</strong>, dan <strong class="text-slate-700">Siswi/Karyawati</strong>.
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()->isAdminInstansi())
                <div class="px-3.5 py-2 rounded-xl text-xs font-bold bg-pink-50 border border-pink-200 text-pink-700 flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-chart-pie text-pink-600"></i>
                    <span>Mode Laporan & Monitoring (Read-Only)</span>
                </div>
            @else
                <button type="button" onclick="openCatatPitaModal()"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-pink-600 hover:bg-pink-700 text-white shadow-sm shadow-pink-600/20 flex items-center gap-1.5 transition-colors">
                    <i class="fas fa-ribbon"></i>
                    <span>+ Catat Pinjam Pita UKS</span>
                </button>
                <button type="button" onclick="openCatatHaidModal()"
                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white shadow-sm shadow-purple-600/20 flex items-center gap-1.5 transition-colors">
                    <i class="fas fa-calendar-plus"></i>
                    <span>+ Mulai Haid Siswi</span>
                </button>
            @endif
        </div>
    </div>

    <!-- WARNING BANNER: KETERLAMBATAN PITA (> 7 HARI) & GANGGUAN REPRODUKSI -->
    @if($warnings->count() > 0)
    <div class="rounded-2xl border-2 border-rose-300 bg-gradient-to-r from-rose-50 via-pink-50 to-amber-50 p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-rose-600/20 animate-pulse">
                    <i class="fas fa-triangle-exclamation text-lg"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black text-rose-900 uppercase tracking-wide flex items-center gap-2">
                        <span>Peringatan Health Monitor: {{ $warnings->count() }} Siswi Melebihi Batas Waktu 7 Hari</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-600 text-white font-bold">Wajib Evaluasi</span>
                    </h2>
                    <p class="text-xs text-rose-700 mt-0.5 leading-relaxed">
                        Standar siklus haid wajar dan masa peminjaman pita adalah <strong>maksimal 7 hari (seminggu)</strong>. Siswi berikut belum mengembalikan pita atau mengalami haid berkepanjangan (indikasi menorrhagia/anemia). Health Monitor berwenang mengenakan denda/sanksi kedisiplinan serta merujuk siswi ke Health Consultant (Guru BK / Dokter UKS).
                    </p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto bg-white rounded-xl border border-rose-200">
            <table class="w-full text-left text-xs">
                <thead class="bg-rose-100/60 text-rose-900 uppercase tracking-wider text-[10px] font-bold">
                    <tr>
                        <th class="px-4 py-2.5">Siswi</th>
                        <th class="px-4 py-2.5">Kelas / Divisi</th>
                        <th class="px-4 py-2.5">Tgl Pinjam & Batas</th>
                        <th class="px-4 py-2.5">Keterlambatan</th>
                        <th class="px-4 py-2.5">Denda & Sanksi</th>
                        <th class="px-4 py-2.5">Status Medis</th>
                        <th class="px-4 py-2.5 text-center">Tindakan Health Monitor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rose-100">
                    @foreach($warnings as $w)
                    @php
                        $daysLate = $w->hariTerlambat();
                        $estimasiDenda = $w->hitungDenda(1000);
                    @endphp
                    <tr class="hover:bg-rose-50/50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-800">{{ $w->user->nama ?? '-' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $w->user->nomor_induk ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            {{ $w->user->divisi->divisi_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-slate-600">
                            <div>Pinjam: {{ $w->tanggal_pinjam ? \Carbon\Carbon::parse($w->tanggal_pinjam)->format('d M') : '-' }}</div>
                            <div class="text-[10px] text-rose-600 font-semibold">Batas: {{ $w->estimasi_selesai_haid ? \Carbon\Carbon::parse($w->estimasi_selesai_haid)->format('d M Y') : '-' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 border border-rose-300 animate-pulse">
                                <i class="fas fa-clock"></i>
                                <span>Terlambat {{ $daysLate }} Hari</span>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-800">
                                Rp {{ number_format($w->denda_nominal ?: $estimasiDenda, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px]">
                                @if($w->denda_status === 'lunas')
                                    <span class="text-emerald-600 font-semibold"><i class="fas fa-check mr-0.5"></i>Lunas</span>
                                @else
                                    <span class="text-rose-600 font-semibold"><i class="fas fa-xmark mr-0.5"></i>Belum Lunas</span>
                                @endif
                                @if($w->sanksi_catatan)
                                    <span class="text-slate-500 italic block mt-0.5">{{ $w->sanksi_catatan }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($w->health_flag === 'rujuk_konseling')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-700 border border-purple-200">
                                    <i class="fas fa-user-doctor mr-1"></i>Dirujuk Konseling
                                </span>
                            @elseif($w->health_flag === 'perhatian_khusus')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">
                                    <i class="fas fa-eye mr-1"></i>Perhatian Khusus
                                </span>
                            @else
                                <span class="text-slate-400 text-[11px]">Normal</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if(auth()->user()->isAdminInstansi())
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                    <i class="fas fa-circle-exclamation text-rose-500"></i>
                                    <span>Monitoring UKS</span>
                                </span>
                            @else
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Kelola Sanksi & Denda -->
                                    <button type="button" onclick="openSanksiModal({{ json_encode($w) }}, {{ $daysLate }}, {{ $estimasiDenda }})"
                                            class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-800 rounded-lg text-xs font-bold transition-colors"
                                            title="Beri Sanksi / Atur Denda / Rujuk Konseling">
                                        <i class="fas fa-gavel mr-1"></i>Sanksi & Denda
                                    </button>

                                    <!-- Tombol Tandai Pita Dikembalikan -->
                                    <form action="{{ route('admin.femhealth.return_pita', $w->id) }}" method="POST"
                                          onsubmit="return confirm('Konfirmasi bahwa pita telah dikembalikan oleh {{ $w->user->nama }}?')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors">
                                            <i class="fas fa-check mr-1"></i>Kembalikan
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Log Haid</span>
                <div class="w-8 h-8 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center text-xs">
                    <i class="fas fa-calendar-heart"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-slate-800">{{ $totalHaidLogs }}</span>
                <span class="text-xs font-bold text-pink-600">Siklus</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">Database institusi</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sedang Haid Hari Ini</span>
                <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                    <i class="fas fa-droplet"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-purple-700">{{ $currentlyHaid->count() }}</span>
                <span class="text-xs font-bold text-purple-600">Siswi Aktif</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">Siklus berlangsung</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pita UKS Dipinjam</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs">
                    <i class="fas fa-ribbon"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-rose-600">{{ $totalPitaAktif }}</span>
                <span class="text-xs font-bold text-rose-600">Pita Beredar</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">Inventaris terpakai</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pita Terlambat (> 7hr)</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-amber-600">{{ $totalPitaTerlambat }}</span>
                <span class="text-xs font-bold text-amber-600">Perlu Denda</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">Melewati batas waktu</p>
        </div>

        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</span>
                <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xs">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-sky-700">{{ $totalMenungguVerif }}</span>
                <span class="text-xs font-bold text-sky-600">Pengajuan</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-0.5">Butuh approval UKS</p>
        </div>
    </div>

    <!-- Grafik Analitik FemHealth & Peminjaman Pita UKS -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Grafik 1: Status Inventaris & Peminjaman Pita UKS -->
        <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center text-xs font-bold shadow-sm">
                        <i class="fas fa-ribbon"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Status Peminjaman Pita UKS</h3>
                        <p class="text-[11px] text-slate-400">Proporsi pita beredar vs terlambat</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-pink-700 bg-pink-50 px-2.5 py-1 rounded-md border border-pink-200">
                    Inventaris UKS
                </span>
            </div>

            <div class="relative flex items-center justify-center my-auto py-3">
                <div class="w-44 h-44 sm:w-48 sm:h-48 relative">
                    <canvas id="femPitaChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 text-[11px]">
                <div class="flex items-center justify-between p-2 rounded-lg bg-emerald-50/60 border border-emerald-200 text-emerald-800">
                    <span class="font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Dikembalikan
                    </span>
                    <span class="font-bold">{{ $totalPitaSelesai }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg bg-pink-50/60 border border-pink-200 text-pink-800">
                    <span class="font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-pink-500"></span> Dipinjam
                    </span>
                    <span class="font-bold">{{ $totalPitaAktif }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg bg-rose-50/60 border border-rose-200 text-rose-800">
                    <span class="font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Terlambat
                    </span>
                    <span class="font-bold">{{ $totalPitaTerlambat }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg bg-amber-50/60 border border-amber-200 text-amber-800">
                    <span class="font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Menunggu
                    </span>
                    <span class="font-bold">{{ $totalMenungguVerif }}</span>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Distribusi Durasi Siklus Menstruasi -->
        <div class="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs font-bold shadow-sm">
                        <i class="fas fa-chart-column"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Distribusi Durasi Siklus Menstruasi</h3>
                        <p class="text-[11px] text-slate-400">Klasifikasi durasi haid siswi (Standar medis: 3–7 hari)</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-md border border-purple-200">
                    Rata-rata: {{ $avgDurasi }} Hari
                </span>
            </div>

            <div class="relative w-full h-52 sm:h-56 my-auto">
                <canvas id="femHaidChart"></canvas>
            </div>

            <p class="text-[11px] text-slate-400 pt-3 border-t border-slate-100 flex items-center gap-1.5">
                <i class="fas fa-circle-info text-pink-600"></i>
                Durasi haid &gt; 7 hari secara medis diwaspadai sebagai menorrhagia yang berisiko memicu anemia defisiensi besi.
            </p>
        </div>
    </div>

    <!-- KALENDER HAID & REKAP BULANAN -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-pink-100 text-pink-700 flex items-center justify-center">
                    <i class="fas fa-calendar-days text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Kalender Pemantauan Haid & Kesehatan Reproduksi</h2>
                    <p class="text-xs text-slate-500">Visualisasi hari-hari haid siswi dalam periode <strong class="text-pink-600">{{ $monthName }}</strong></p>
                </div>
            </div>

            <!-- Month Navigator Form -->
            <form method="GET" action="{{ route('admin.femhealth.index') }}" class="flex items-center gap-2">
                @php
                    $prevMonth = $selectedMonth == 1 ? 12 : $selectedMonth - 1;
                    $prevYear = $selectedMonth == 1 ? $selectedYear - 1 : $selectedYear;
                    $nextMonth = $selectedMonth == 12 ? 1 : $selectedMonth + 1;
                    $nextYear = $selectedMonth == 12 ? $selectedYear + 1 : $selectedYear;
                @endphp
                <a href="{{ route('admin.femhealth.index', ['month' => $prevMonth, 'year' => $prevYear]) }}"
                   class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 text-xs">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <span class="text-xs font-bold text-slate-800 min-w-[120px] text-center">
                    {{ $monthName }}
                </span>
                <a href="{{ route('admin.femhealth.index', ['month' => $nextMonth, 'year' => $nextYear]) }}"
                   class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 text-xs">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </form>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-1.5 text-center">
            @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
                <div class="py-2 text-[11px] font-bold text-slate-500 uppercase tracking-wider bg-slate-50 rounded-lg">
                    {{ $dayName }}
                </div>
            @endforeach

            <!-- Empty slots before first day of month -->
            @for($i = 0; $i < $firstDayOfWeek; $i++)
                <div class="h-20 bg-slate-50/40 rounded-xl border border-dashed border-slate-100"></div>
            @endfor

            <!-- Days of Month -->
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $dateStr = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $d);
                    $isToday = $dateStr === now()->toDateString();
                    $events = $calendarEvents[$d] ?? collect();
                    $count = $events->count();
                @endphp
                <div class="min-h-20 p-1.5 rounded-xl border transition-all text-left flex flex-col justify-between {{ $isToday ? 'border-pink-500 bg-pink-50/30 shadow-sm' : ($count > 0 ? 'border-pink-200 bg-pink-50/10' : 'border-slate-100 hover:border-slate-200') }}">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold {{ $isToday ? 'w-5 h-5 rounded-full bg-pink-600 text-white flex items-center justify-center text-[10px]' : 'text-slate-700' }}">
                            {{ $d }}
                        </span>
                        @if($count > 0)
                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full bg-pink-100 text-pink-700">
                                {{ $count }} Haid
                            </span>
                        @endif
                    </div>

                    @if($count > 0)
                        <div class="mt-1 space-y-0.5 overflow-hidden">
                            @foreach($events->take(2) as $ev)
                                <div class="text-[9px] font-medium text-pink-800 bg-pink-100/80 px-1 py-0.5 rounded truncate" title="{{ $ev->user->nama ?? '-' }}">
                                    • {{ $ev->user->nama ?? '-' }}
                                </div>
                            @endforeach
                            @if($count > 2)
                                <div class="text-[8px] text-pink-600 font-bold text-right">+{{ $count - 2 }} lainnya</div>
                            @endif
                        </div>
                    @else
                        <div class="text-[9px] text-slate-300 italic">-</div>
                    @endif
                </div>
            @endfor
        </div>
    </div>

    <!-- SISWI SEDANG HAID HARI INI -->
    @if($currentlyHaid->count() > 0)
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-pink-600 animate-ping"></span>
                <span>Daftar Siswi Sedang Haid Aktif (Hari Ini)</span>
            </h2>
            <span class="text-xs text-pink-600 font-semibold">{{ $currentlyHaid->count() }} siswi berlangsung</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($currentlyHaid as $ch)
            @php
                $mulai = \Carbon\Carbon::parse($ch->tanggal_mulai);
                $hariKe = max(1, $mulai->diffInDays(now()) + 1);
            @endphp
            <div class="p-3.5 rounded-xl border {{ $hariKe > 7 ? 'border-rose-300 bg-rose-50/40' : 'border-slate-200 bg-slate-50/50' }} flex items-center justify-between">
                <div>
                    <div class="font-bold text-xs text-slate-800">{{ $ch->user->nama ?? '-' }}</div>
                    <div class="text-[10px] text-slate-500">{{ $ch->user->divisi->divisi_name ?? '-' }} • Mulai: {{ $mulai->format('d M') }}</div>
                    <div class="mt-1">
                        @if($hariKe > 7)
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-100 text-rose-700 border border-rose-300">
                                Hari Ke-{{ $hariKe }} (Waspada > 7 Hari)
                            </span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-pink-100 text-pink-700">
                                Hari Ke-{{ $hariKe }}
                            </span>
                        @endif
                    </div>
                </div>

                @if(!auth()->user()->isAdminInstansi())
                <form action="{{ route('admin.femhealth.stop_haid', $ch->id) }}" method="POST"
                      onsubmit="return confirm('Konfirmasi bahwa haid {{ $ch->user->nama }} telah selesai hari ini?')">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-[11px] font-bold transition-colors">
                        Selesai Haid
                    </button>
                </form>
                @else
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                    Sedang Berlangsung
                </span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Tables Grid: Log Pita UKS & Riwayat Haid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Log Peminjaman Pita UKS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center text-xs">
                        <i class="fas fa-ribbon"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-800">Manajemen Peminjaman Pita UKS</h2>
                </div>
                <span class="text-[11px] text-slate-400 font-medium">{{ $pitaLogs->total() }} data</span>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/70 text-slate-500 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-4 py-3">Siswi</th>
                            <th class="px-4 py-3">Tgl Pinjam</th>
                            <th class="px-4 py-3">Batas 7 Hari</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-center">Aksi UKS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pitaLogs as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $item->user_nama }}</div>
                                <div class="text-[10px] text-slate-400">{{ $item->nomor_induk }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $item->tanggal_pinjam ? \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ $item->estimasi_selesai_haid ? \Carbon\Carbon::parse($item->estimasi_selesai_haid)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($item->status === 'dikembalikan')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">Dikembalikan</span>
                                @elseif($item->status === 'terlambat')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 animate-pulse">Terlambat</span>
                                @elseif($item->status === 'menunggu')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">Menunggu Verif</span>
                                @elseif($item->status === 'ditolak')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">Ditolak</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-pink-100 text-pink-700">Dipinjam</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(auth()->user()->isAdminInstansi())
                                    <button type="button"
                                            onclick="openPitaDetail(this); return false;"
                                            class="view-pita-detail px-2.5 py-1 bg-pink-50 hover:bg-pink-100 text-pink-700 rounded-lg text-xs font-semibold border border-pink-200/80 transition-colors inline-flex items-center gap-1.5 shadow-sm cursor-pointer"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->user->nama ?? 'Siswi' }}"
                                            data-nomor-induk="{{ $item->user->nomor_induk ?? '-' }}"
                                            data-divisi="{{ $item->user->divisi->divisi_name ?? '-' }}"
                                            data-status="{{ $item->status }}"
                                            data-tgl-pinjam="{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->locale('id')->isoFormat('D MMMM Y') }}"
                                            data-tgl-jatuh-tempo="{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->addDays(7)->locale('id')->isoFormat('D MMMM Y') }}"
                                            data-tgl-kembali="{{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->locale('id')->isoFormat('D MMMM Y') : 'Belum Dikembalikan' }}"
                                            data-late-days="{{ $item->status === 'terlambat' ? max(0, (int) now()->diffInDays(\Carbon\Carbon::parse($item->tanggal_pinjam)->addDays(7))) : 0 }}"
                                            data-denda="Rp {{ number_format($item->denda_nominal ?? 0, 0, ',', '.') }} ({{ ucfirst(str_replace('_', ' ', $item->denda_status ?? 'nihil')) }})"
                                            data-keterangan="{{ $item->keterangan ?? 'Tidak ada catatan peminjaman' }}"
                                            data-sanksi="{{ $item->sanksi_catatan ?? 'Tidak ada catatan sanksi / UKS' }}"
                                            data-flag="{{ $item->health_flag ?? 'normal' }}">
                                        <i class="fas fa-file-lines text-xs"></i>
                                        <span>Lihat Detail</span>
                                    </button>
                                @else
                                    @if($item->status === 'menunggu')
                                        <div class="flex items-center justify-center gap-1">
                                            <form action="{{ route('admin.femhealth.verify_pita', $item->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="accept">
                                                <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold">
                                                    Setujui
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.femhealth.verify_pita', $item->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="px-2 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded text-[10px] font-bold">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($item->status === 'dipinjam' || $item->status === 'terlambat')
                                        <form action="{{ route('admin.femhealth.return_pita', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 bg-slate-800 hover:bg-slate-900 text-white rounded text-[10px] font-bold">
                                                Kembalikan
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 text-[10px]">Selesai</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                Belum ada riwayat peminjaman pita UKS.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-t border-slate-100">
                {{ $pitaLogs->links() }}
            </div>
        </div>

        <!-- Log Siklus Haid -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center text-xs">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h2 class="text-sm font-bold text-slate-800">Catatan Rekap Siklus Menstruasi</h2>
                </div>
                <span class="text-[11px] text-slate-400 font-medium">{{ $haidLogs->total() }} data</span>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/70 text-slate-500 uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="px-4 py-3">Siswi</th>
                            <th class="px-4 py-3">Mulai</th>
                            <th class="px-4 py-3">Durasi</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Keluhan / Catatan</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($haidLogs as $item)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800">{{ $item->user_nama }}</div>
                                <div class="text-[10px] text-slate-400">{{ $item->nomor_induk }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md">
                                    {{ $item->durasi_hari ?: '-' }} Hari
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($item->status === 'berlangsung')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-pink-100 text-pink-700">Aktif</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">Selesai</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <div class="text-[11px] text-slate-500 truncate max-w-xs">{{ $item->catatan ?: '-' }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button"
                                        onclick="openHaidDetail(this); return false;"
                                        class="view-haid-detail px-2.5 py-1 bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg text-xs font-semibold border border-purple-200/80 transition-colors inline-flex items-center gap-1 shadow-sm cursor-pointer"
                                        data-id="{{ $item->id }}"
                                        data-nama="{{ $item->user_nama }}"
                                        data-nomor-induk="{{ $item->nomor_induk }}"
                                        data-divisi="{{ $item->divisi_name ?? '-' }}"
                                        data-tgl-mulai="{{ \Carbon\Carbon::parse($item->tanggal_mulai)->locale('id')->isoFormat('D MMMM Y') }}"
                                        data-durasi="{{ $item->durasi_hari ? $item->durasi_hari . ' Hari' : 'Sedang Berjalan' }}"
                                        data-status="{{ $item->status }}"
                                        data-catatan="{{ $item->catatan ?: 'Tidak ada catatan atau keluhan khusus.' }}">
                                    <i class="fas fa-file-lines text-xs"></i>
                                    <span>Lihat Detail</span>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                Belum ada data log siklus menstruasi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3 border-t border-slate-100">
                {{ $haidLogs->links() }}
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CATAT PINJAM PITA UKS -->
<div id="catatPitaModal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4" onclick="closeCatatPitaModal()">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-ribbon text-pink-600"></i>
                <span>Catat Peminjaman Pita UKS</span>
            </h3>
            <button type="button" onclick="closeCatatPitaModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.femhealth.store_pita') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Siswi <span class="text-rose-500">*</span></label>
                <select name="id_user" required class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-pink-500">
                    <option value="">-- Pilih Siswi --</option>
                    @foreach($femaleMembers as $fm)
                        <option value="{{ $fm->id }}">{{ $fm->nama }} ({{ $fm->divisi->divisi_name ?? 'Tanpa Kelas' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pinjam <span class="text-rose-500">*</span></label>
                <input type="date" name="tanggal_pinjam" value="{{ now()->format('Y-m-d') }}" required
                       class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-pink-500">
                <p class="text-[11px] text-slate-400 mt-1">Batas pengembalian standar adalah 7 hari setelah tanggal pinjam.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="2" class="w-full text-xs rounded-xl border border-slate-200 p-2.5" placeholder="Contoh: Pita dipinjam saat jam olahraga / UKS"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="closeCatatPitaModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-xl text-xs font-bold">Simpan Peminjaman</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: CATAT MULAI HAID -->
<div id="catatHaidModal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4" onclick="closeCatatHaidModal()">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-calendar-plus text-purple-600"></i>
                <span>Catat Mulai Siklus Haid</span>
            </h3>
            <button type="button" onclick="closeCatatHaidModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.femhealth.store_haid') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Siswi <span class="text-rose-500">*</span></label>
                <select name="id_user" required class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-purple-500">
                    <option value="">-- Pilih Siswi --</option>
                    @foreach($femaleMembers as $fm)
                        <option value="{{ $fm->id }}">{{ $fm->nama }} ({{ $fm->divisi->divisi_name ?? 'Tanpa Kelas' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai Haid <span class="text-rose-500">*</span></label>
                <input type="date" name="tanggal_mulai" value="{{ now()->format('Y-m-d') }}" required
                       class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-purple-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Gejala Awal</label>
                <textarea name="catatan" rows="2" class="w-full text-xs rounded-xl border border-slate-200 p-2.5" placeholder="Contoh: Dismenore ringan, kram perut"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="closeCatatHaidModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold">Mulai Siklus Haid</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: KELOLA SANKSI, DENDA & RUJUK KONSELING -->
<div id="sanksiModal" class="fixed inset-0 bg-black/40 hidden z-50 flex items-center justify-center p-4" onclick="closeSanksiModal()">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-gavel text-amber-600"></i>
                <span>Tindakan Health Monitor: Sanksi, Denda & Medis</span>
            </h3>
            <button type="button" onclick="closeSanksiModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="sanksiForm" method="POST" action="" class="space-y-4">
            @csrf
            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900">
                Siswi: <strong id="modalSiswiNama">-</strong> • Terlambat: <strong id="modalLateDays">-</strong> Hari
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Denda (Rp)</label>
                    <input type="number" name="denda_nominal" id="modalDendaNominal" min="0" step="500" required
                           class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-amber-500 font-bold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Status Pembayaran</label>
                    <select name="denda_status" id="modalDendaStatus" class="w-full text-xs rounded-xl border border-slate-200 p-2.5">
                        <option value="belum_lunas">Belum Lunas</option>
                        <option value="lunas">Lunas</option>
                        <option value="nihil">Bebas Denda</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Sanksi Edukatif / Kedisiplinan UKS</label>
                <input type="text" name="sanksi_catatan" id="modalSanksiCatatan"
                       class="w-full text-xs rounded-xl border border-slate-200 p-2.5"
                       placeholder="Contoh: Piket kebersihan ruang UKS 2 hari / Edukasi inventaris">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status Kesehatan Reproduksi</label>
                <select name="health_flag" id="modalHealthFlag" class="w-full text-xs rounded-xl border border-slate-200 p-2.5">
                    <option value="normal">Normal (Hanya lupa mengembalikan pita)</option>
                    <option value="perhatian_khusus">Perhatian Khusus (Ada keluhan nyeri/darah masih keluar)</option>
                    <option value="rujuk_konseling">Rujuk ke Health Consultant / Guru BK / Dokter UKS</option>
                </select>
            </div>

            <div class="p-3 bg-purple-50 rounded-xl border border-purple-200 flex items-start gap-2.5">
                <input type="checkbox" name="buat_tiket_konseling" id="buatTiketCheck" value="1" class="mt-0.5 rounded text-purple-600 focus:ring-purple-500">
                <label for="buatTiketCheck" class="text-xs text-purple-900 cursor-pointer">
                    <strong>Buat Tiket Konseling Privat Otomatis</strong><br>
                    <span class="text-[11px] text-purple-700">Jadwalkan sesi konseling SafeSpace bersama Health Consultant (Guru BK) untuk pemeriksaan kesehatan reproduksi.</span>
                </label>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="closeSanksiModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold">Simpan Keputusan</button>
            </div>
        </form>
    </div>
<!-- MODAL: DETAIL PEMINJAMAN PITA UKS (READ-ONLY) -->
<div id="pitaDetailModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm hidden z-[999999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="if(event.target === this) closePitaDetailModal()">
    <div class="relative bg-white rounded-2xl max-w-lg w-full p-4 sm:p-6 shadow-2xl space-y-4 max-h-[92vh] overflow-y-auto my-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-pink-50 border border-pink-100 flex items-center justify-center text-pink-600 shrink-0">
                    <i class="fas fa-ribbon text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-800">Detail Peminjaman Pita UKS</h3>
                    <p class="text-[11px] text-slate-500" id="pitaModalSiswiSub">Siswi: -</p>
                </div>
            </div>
            <button type="button" onclick="closePitaDetailModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        <div class="p-3 bg-pink-50/80 border border-pink-200/80 rounded-xl text-pink-700 text-xs flex items-center gap-2">
            <i class="fas fa-eye text-pink-500 text-sm shrink-0"></i>
            <span class="leading-relaxed"><strong>Mode Laporan (Read-Only):</strong> Detail rekapitulasi inventaris dan kepatuhan pengembalian pita menstruasi UKS.</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1">Status Peminjaman</span>
                <div id="pitaModalStatusBadge">-</div>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1">Status Denda / Sanksi</span>
                <span class="text-xs font-bold text-slate-800" id="pitaModalDenda">-</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
            <div class="p-2.5 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Tgl Pinjam</span>
                <span class="text-xs font-semibold text-slate-800" id="pitaModalTglPinjam">-</span>
            </div>
            <div class="p-2.5 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Jatuh Tempo (7 Hari)</span>
                <span class="text-xs font-semibold text-slate-800" id="pitaModalTglTempo">-</span>
            </div>
            <div class="p-2.5 bg-slate-50/70 rounded-xl border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Tgl Kembali</span>
                <span class="text-xs font-semibold text-slate-800" id="pitaModalTglKembali">-</span>
            </div>
        </div>

        <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-200/70 space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Keterangan Pengambilan</span>
            <p class="text-xs text-slate-700 leading-relaxed italic" id="pitaModalKeterangan">-</p>
        </div>

        <div class="p-3 bg-slate-50/70 rounded-xl border border-slate-200/70 space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">Catatan Sanksi / Tindakan UKS</span>
            <p class="text-xs text-slate-700 leading-relaxed" id="pitaModalSanksi">-</p>
        </div>

        <div class="pt-2 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closePitaDetailModal()" class="w-full sm:w-auto px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- MODAL: DETAIL SIKLUS HAID (READ-ONLY) -->
<div id="haidDetailModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm hidden z-[999999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="if(event.target === this) closeHaidDetailModal()">
    <div class="relative bg-white rounded-2xl max-w-md w-full p-4 sm:p-6 shadow-2xl space-y-4 max-h-[92vh] overflow-y-auto my-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 flex items-center justify-center text-purple-600 shrink-0">
                    <i class="fas fa-calendar-check text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-800">Detail Siklus Menstruasi</h3>
                    <p class="text-[11px] text-slate-500" id="haidModalSiswiSub">Siswi: -</p>
                </div>
            </div>
            <button type="button" onclick="closeHaidDetailModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        <div class="p-3 bg-purple-50/80 border border-purple-200/80 rounded-xl text-purple-700 text-xs flex items-center gap-2">
            <i class="fas fa-eye text-purple-500 text-sm shrink-0"></i>
            <span class="leading-relaxed"><strong>Mode Pemantauan (Read-Only):</strong> Catatan siklus menstruasi dan keluhan kesehatan siswi.</span>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Tanggal Mulai</span>
                <span class="text-xs font-bold text-slate-800" id="haidModalTglMulai">-</span>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Durasi Siklus</span>
                <span class="text-xs font-bold text-purple-700" id="haidModalDurasi">-</span>
            </div>
        </div>

        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Status Siklus</span>
            <div id="haidModalStatusBadge">-</div>
        </div>

        <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/70 space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">
                <i class="fas fa-notes-medical text-purple-500 mr-1"></i>Keluhan & Catatan Khusus
            </span>
            <p class="text-xs text-slate-700 leading-relaxed italic" id="haidModalCatatan">-</p>
        </div>

        <div class="pt-2 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeHaidDetailModal()" class="w-full sm:w-auto px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function openCatatPitaModal() {
    const m = document.getElementById('catatPitaModal');
    if (m) { m.classList.remove('hidden'); m.style.display = 'flex'; }
}
function closeCatatPitaModal() {
    const m = document.getElementById('catatPitaModal');
    if (m) { m.classList.add('hidden'); m.style.display = 'none'; }
}

function openCatatHaidModal() {
    const m = document.getElementById('catatHaidModal');
    if (m) { m.classList.remove('hidden'); m.style.display = 'flex'; }
}
function closeCatatHaidModal() {
    const m = document.getElementById('catatHaidModal');
    if (m) { m.classList.add('hidden'); m.style.display = 'none'; }
}

function closePitaDetailModal() {
    const m = document.getElementById('pitaDetailModal');
    if (m) { m.classList.add('hidden'); m.style.display = 'none'; }
}

function closeHaidDetailModal() {
    const m = document.getElementById('haidDetailModal');
    if (m) { m.classList.add('hidden'); m.style.display = 'none'; }
}

// Global function Buka Detail Peminjaman Pita
window.openPitaDetail = function(btn) {
    try {
        if (!btn) {
            btn = (window.event && window.event.target) ? window.event.target.closest('button, .view-pita-detail') : null;
        } else if (btn.closest) {
            btn = btn.closest('button, .view-pita-detail') || btn;
        }
        if (!btn) return;

        const getVal = function(attr, fallback) {
            return btn.getAttribute(attr) || fallback || '-';
        };

        const nama = getVal('data-nama', 'Siswi');
        const divisi = getVal('data-divisi', '-');
        const noInduk = getVal('data-nomor-induk', '-');
        const elSub = document.getElementById('pitaModalSiswiSub');
        if (elSub) elSub.textContent = `${nama} (${divisi} • No: ${noInduk})`;

        const elTglPinjam = document.getElementById('pitaModalTglPinjam');
        if (elTglPinjam) elTglPinjam.textContent = getVal('data-tgl-pinjam', '-');

        const elTglTempo = document.getElementById('pitaModalTglTempo');
        if (elTglTempo) elTglTempo.textContent = getVal('data-tgl-jatuh-tempo', '-');

        const elTglKembali = document.getElementById('pitaModalTglKembali');
        if (elTglKembali) elTglKembali.textContent = getVal('data-tgl-kembali', '-');

        const elDenda = document.getElementById('pitaModalDenda');
        if (elDenda) elDenda.textContent = getVal('data-denda', 'Rp 0 (Nihil)');

        const elKet = document.getElementById('pitaModalKeterangan');
        if (elKet) elKet.textContent = getVal('data-keterangan', 'Tidak ada keterangan.');

        const elSanksi = document.getElementById('pitaModalSanksi');
        if (elSanksi) elSanksi.textContent = getVal('data-sanksi', 'Tidak ada catatan sanksi.');

        const status = getVal('data-status', 'dipinjam');
        let badgeHtml = '';
        if (status === 'terlambat') {
            badgeHtml = '<span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Terlambat (' + getVal('data-late-days', '0') + ' hari)</span>';
        } else if (status === 'dikembalikan') {
            badgeHtml = '<span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">Sudah Dikembalikan</span>';
        } else if (status === 'menunggu') {
            badgeHtml = '<span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">Menunggu Verifikasi</span>';
        } else if (status === 'ditolak') {
            badgeHtml = '<span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">Ditolak</span>';
        } else {
            badgeHtml = '<span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-pink-100 text-pink-700">Sedang Dipinjam</span>';
        }
        const elBadge = document.getElementById('pitaModalStatusBadge');
        if (elBadge) elBadge.innerHTML = badgeHtml;

        const m = document.getElementById('pitaDetailModal');
        if (m) {
            m.classList.remove('hidden');
            m.style.removeProperty('display');
            m.style.display = 'flex';
            m.style.setProperty('display', 'flex', 'important');
            m.style.zIndex = '999999';
            m.style.opacity = '1';
            m.style.pointerEvents = 'auto';
        }
    } catch (err) {
        console.error('Error opening pita detail modal:', err);
        const m = document.getElementById('pitaDetailModal');
        if (m) {
            m.classList.remove('hidden');
            m.style.display = 'flex';
            m.style.setProperty('display', 'flex', 'important');
            m.style.zIndex = '999999';
        }
    }
};

// Global function Buka Detail Haid
window.openHaidDetail = function(btn) {
    try {
        if (!btn) {
            btn = (window.event && window.event.target) ? window.event.target.closest('button, .view-haid-detail') : null;
        } else if (btn.closest) {
            btn = btn.closest('button, .view-haid-detail') || btn;
        }
        if (!btn) return;

        const getVal = function(attr, fallback) {
            return btn.getAttribute(attr) || fallback || '-';
        };

        const nama = getVal('data-nama', 'Siswi');
        const divisi = getVal('data-divisi', '-');
        const noInduk = getVal('data-nomor-induk', '-');
        const elSub = document.getElementById('haidModalSiswiSub');
        if (elSub) elSub.textContent = `${nama} (${divisi} • No: ${noInduk})`;

        const elTglMulai = document.getElementById('haidModalTglMulai');
        if (elTglMulai) elTglMulai.textContent = getVal('data-tgl-mulai', '-');

        const elDurasi = document.getElementById('haidModalDurasi');
        if (elDurasi) elDurasi.textContent = getVal('data-durasi', '-');

        const elCatatan = document.getElementById('haidModalCatatan');
        if (elCatatan) elCatatan.textContent = getVal('data-catatan', 'Tidak ada keluhan yang dicatat.');

        const status = getVal('data-status', 'selesai');
        let badgeHtml = '';
        if (status === 'berlangsung') {
            badgeHtml = '<span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-pink-100 text-pink-700">Sedang Berlangsung (Aktif)</span>';
        } else {
            badgeHtml = '<span class="px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">Selesai</span>';
        }
        const elBadge = document.getElementById('haidModalStatusBadge');
        if (elBadge) elBadge.innerHTML = badgeHtml;

        const m = document.getElementById('haidDetailModal');
        if (m) {
            m.classList.remove('hidden');
            m.style.removeProperty('display');
            m.style.display = 'flex';
            m.style.setProperty('display', 'flex', 'important');
            m.style.zIndex = '999999';
            m.style.opacity = '1';
            m.style.pointerEvents = 'auto';
        }
    } catch (err) {
        console.error('Error opening haid detail modal:', err);
        const m = document.getElementById('haidDetailModal');
        if (m) {
            m.classList.remove('hidden');
            m.style.display = 'flex';
            m.style.setProperty('display', 'flex', 'important');
            m.style.zIndex = '999999';
        }
    }
};

// Delegasi klik tombol Lihat Detail Peminjaman Pita (Fallback)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-pita-detail');
    if (btn) {
        e.preventDefault();
        openPitaDetail(btn);
    }
});

// Delegasi klik tombol Lihat Detail Haid (Fallback)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-haid-detail');
    if (btn) {
        e.preventDefault();
        openHaidDetail(btn);
    }
});

function openSanksiModal(peminjaman, lateDays, estimasiDenda) {
    document.getElementById('sanksiForm').action = '/admin/femhealth/peminjaman/' + peminjaman.id + '/sanksi';
    document.getElementById('modalSiswiNama').textContent = (peminjaman.user ? peminjaman.user.nama : '-') + ' (' + (peminjaman.user && peminjaman.user.divisi ? peminjaman.user.divisi.divisi_name : '-') + ')';
    document.getElementById('modalLateDays').textContent = lateDays;
    document.getElementById('modalDendaNominal').value = peminjaman.denda_nominal || estimasiDenda;
    document.getElementById('modalDendaStatus').value = peminjaman.denda_status || (lateDays > 0 ? 'belum_lunas' : 'nihil');
    document.getElementById('modalSanksiCatatan').value = peminjaman.sanksi_catatan || '';
    document.getElementById('modalHealthFlag').value = peminjaman.health_flag || (lateDays >= 3 ? 'rujuk_konseling' : 'normal');
    document.getElementById('buatTiketCheck').checked = (peminjaman.health_flag === 'rujuk_konseling' || lateDays >= 3);
    const m = document.getElementById('sanksiModal');
    if (m) { m.classList.remove('hidden'); m.style.display = 'flex'; }
}
function closeSanksiModal() {
    const m = document.getElementById('sanksiModal');
    if (m) { m.classList.add('hidden'); m.style.display = 'none'; }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePitaDetailModal();
        closeHaidDetailModal();
        closeCatatPitaModal();
        closeCatatHaidModal();
        closeSanksiModal();
    }
});

// Inisialisasi Grafik Analisis FemHealth
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    // 1. Donut Chart - Status Peminjaman Pita UKS
    const femPitaCtx = document.getElementById('femPitaChart');
    if (femPitaCtx) {
        const pitaData = @json($pitaStatusChartData ?? ['labels' => [], 'data' => [], 'colors' => []]);
        new Chart(femPitaCtx, {
            type: 'doughnut',
            data: {
                labels: pitaData.labels,
                datasets: [{
                    data: pitaData.data,
                    backgroundColor: pitaData.colors || ['#10b981', '#ec4899', '#f43f5e', '#f59e0b'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return ` ${label}: ${value} pita (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Bar Chart - Distribusi Durasi Siklus Menstruasi
    const femHaidCtx = document.getElementById('femHaidChart');
    if (femHaidCtx) {
        const haidData = @json($haidDurationChartData ?? ['labels' => [], 'data' => [], 'colors' => []]);
        new Chart(femHaidCtx, {
            type: 'bar',
            data: {
                labels: haidData.labels,
                datasets: [{
                    label: 'Jumlah Siswi',
                    data: haidData.data,
                    backgroundColor: ['rgba(245, 158, 11, 0.75)', 'rgba(16, 185, 129, 0.75)', 'rgba(225, 29, 72, 0.75)'],
                    borderColor: ['#f59e0b', '#10b981', '#e11d48'],
                    borderWidth: 1.5,
                    borderRadius: 6
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
                                return ` Frekuensi: ${context.parsed.y} siklus`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
