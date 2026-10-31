@extends('layouts.app')

@section('title', 'Konseling Mental & SafeSpace')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-start sm:items-center gap-3">
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-md shadow-indigo-600/25 shrink-0 mt-0.5 sm:mt-0">
                <i class="fas fa-comments text-base sm:text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg sm:text-2xl font-black text-slate-800 tracking-tight leading-tight">
                    Konseling Mental & SafeSpace Konfidensial
                </h1>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Layanan konseling privat 1-on-1 (Guru BK / Pembina UKS / Konselor) dengan persetujuan jadwal dan data terisolasi per-instansi.
                </p>
            </div>
        </div>
        <div class="shrink-0">
            @if(auth()->user()->isAdminInstansi())
                <div class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-indigo-50 border border-indigo-200/80 text-indigo-700 font-bold text-xs shadow-sm">
                    <i class="fas fa-chart-pie text-indigo-500"></i>
                    <span>Mode Laporan & Monitoring (Read-Only)</span>
                </div>
            @else
                <button type="button" onclick="event.preventDefault(); openCreateModal()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-md shadow-indigo-600/20 transition-all">
                    <i class="fas fa-calendar-plus"></i>
                    <span>+ Jadwalkan Konsultasi</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pengajuan</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-folder-open text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $totalSessions }}</span>
                <span class="text-xs font-bold text-indigo-600">Sesi</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Semua status</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Respon</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-hourglass-half text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $pendingSessions }}</span>
                <span class="text-xs font-bold text-amber-600">Perlu Dijadwalkan</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Antrean konsultasi</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Terjadwal Aktif</span>
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-calendar-check text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $scheduledSessions }}</span>
                <span class="text-xs font-bold text-sky-600">Akan Datang</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Jadwal konsultasi</p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Selesai Dibantu</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-circle-check text-sm"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-black text-slate-800">{{ $completedSessions }}</span>
                <span class="text-xs font-bold text-emerald-600">Terselesaikan</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Sesi sukses</p>
        </div>
    </div>

    <!-- Grafik & Analisis Data Konseling -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <!-- Grafik 1: Distribusi Status Konseling -->
        <div class="lg:col-span-5 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-bold shadow-sm">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Distribusi Status Konseling</h3>
                        <p class="text-[11px] text-slate-400">Proporsi alur dan progres sesi</p>
                    </div>
                </div>
                <span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg">
                    {{ $totalSessions }} Total
                </span>
            </div>
            
            <div class="relative flex items-center justify-center my-auto py-3">
                <div class="w-44 h-44 sm:w-48 sm:h-48 relative">
                    <canvas id="counselingStatusChart"></canvas>
                </div>
            </div>

            <!-- Mini Legend Badges -->
            <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 text-[11px]">
                <div class="flex items-center justify-between p-2 rounded-xl bg-amber-50/80 border border-amber-200/60">
                    <span class="text-amber-800 font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Menunggu
                    </span>
                    <span class="font-bold text-amber-900">{{ $pendingSessions }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-xl bg-sky-50/80 border border-sky-200/60">
                    <span class="text-sky-800 font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span> Terjadwal
                    </span>
                    <span class="font-bold text-sky-900">{{ $scheduledSessions }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-xl bg-emerald-50/80 border border-emerald-200/60">
                    <span class="text-emerald-800 font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Selesai
                    </span>
                    <span class="font-bold text-emerald-900">{{ $completedSessions }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-xl bg-rose-50/80 border border-rose-200/60">
                    <span class="text-rose-800 font-medium flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Ditolak/Batal
                    </span>
                    <span class="font-bold text-rose-900">{{ $cancelledSessions }}</span>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Topik Bimbingan & Permasalahan Terbanyak -->
        <div class="lg:col-span-7 bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xs font-bold shadow-sm">
                        <i class="fas fa-chart-simple"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-800">Topik Permasalahan & Bimbingan Terbanyak</h3>
                        <p class="text-[11px] text-slate-400">Analisis tren topik pengajuan konseling di instansi</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200/60">
                    Tren Utama
                </span>
            </div>

            <div class="relative w-full h-52 sm:h-56 my-auto">
                <canvas id="counselingTopicChart"></canvas>
            </div>

            <p class="text-[11px] text-slate-400 pt-3 border-t border-slate-100 flex items-center gap-1.5">
                <i class="fas fa-circle-info text-indigo-500"></i>
                Data grafik diperbarui secara real-time berdasarkan catatan pengajuan konseling di instansi Anda.
            </p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.counseling.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari topik permasalahan atau nama..."
                       class="w-full pl-9 pr-4 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="w-full sm:w-48">
                <select name="status" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="terjadwal" {{ request('status') === 'terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="w-full sm:w-48">
                <select name="divisi_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('divisi_id') == $div->id ? 'selected' : '' }}>
                            {{ $div->divisi_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 sm:flex-none px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors text-center">
                    Filter
                </button>
                <a href="{{ route('admin.counseling.index') }}" class="flex-1 sm:flex-none px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>


    <!-- Table of Sessions -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-4 py-3.5">Anggota / Klien</th>
                        <th class="px-4 py-3.5">Divisi</th>
                        <th class="px-4 py-3.5">Topik Masalah</th>
                        <th class="px-4 py-3.5">Jadwal Sesi</th>
                        <th class="px-4 py-3.5">Konselor Bertugas</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($sessions as $item)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="px-4 py-3.5">
                            <a href="{{ route('admin.counseling.index', ['search' => $item->user->nama ?? '']) }}" class="font-bold text-slate-800 hover:text-emerald-700 hover:underline block" title="Klik untuk melihat seluruh riwayat konsultasi {{ $item->user->nama ?? '' }}">
                                {{ $item->user->nama ?? '-' }}
                            </a>
                            <div class="text-[10px] text-slate-400">
                                @if($item->is_confidential)
                                    <span class="text-indigo-600 font-semibold"><i class="fas fa-lock mr-1"></i>Privat / Rahasia</span>
                                @else
                                    <span>Publik</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600">
                            {{ $item->user->divisi->divisi_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="font-semibold text-slate-800">{{ $item->topic }}</div>
                            @if($item->ruangan)
                                <div class="text-[10px] text-indigo-600 font-semibold mt-0.5 flex items-center gap-1">
                                    <i class="fas fa-door-open text-[9px]"></i>
                                    <span>Ruangan: {{ $item->ruangan }}</span>
                                </div>
                            @endif
                            @if($item->notes)
                                <div class="text-[10px] text-slate-400 truncate max-w-xs mt-0.5" title="Catatan Pengajuan: {{ $item->notes }}">
                                    <i class="fas fa-info-circle text-[9px] mr-0.5"></i>{{ $item->notes }}
                                </div>
                            @endif
                            @if($item->hasil_konseling)
                                <div class="text-[10px] text-emerald-600 font-medium truncate max-w-xs mt-0.5" title="Catatan Hasil: {{ $item->hasil_konseling }}">
                                    <i class="fas fa-file-medical text-[9px] mr-0.5"></i>Hasil: {{ $item->hasil_konseling }}
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-slate-600">
                            {{ \Carbon\Carbon::parse($item->session_date)->format('d M Y, H:i') }}
                        </td>
                        <td class="px-4 py-3.5">
                            @if($item->consultant)
                                <a href="{{ route('admin.counseling.index', ['search' => $item->consultant->nama]) }}" class="px-2 py-1 rounded-md bg-indigo-50 text-indigo-700 font-medium text-[11px] inline-flex items-center hover:bg-indigo-100 transition-colors" title="Lihat seluruh sesi yang ditangani {{ $item->consultant->nama }}">
                                    <i class="fas fa-user-tie mr-1"></i>{{ $item->consultant->nama }}
                                </a>
                            @else
                                <span class="text-slate-400 italic">Belum ditentukan</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            @if($item->status === 'menunggu')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                    <span>Menunggu Persetujuan</span>
                                </span>
                            @elseif($item->status === 'terjadwal')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                    <i class="fas fa-calendar-check text-[9px]"></i>
                                    <span>Disetujui / Terjadwal</span>
                                </span>
                            @elseif($item->status === 'selesai')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i class="fas fa-circle-check text-[9px]"></i>
                                    <span>Selesai</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <i class="fas fa-ban text-[9px]"></i>
                                    <span>Ditolak / Batal</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                @if(auth()->user()->isAdminInstansi())
                                    <button type="button"
                                            onclick="openCounselingDetail(this); return false;"
                                            class="view-counseling-detail px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold border border-indigo-200/80 transition-colors inline-flex items-center gap-1.5 shadow-sm cursor-pointer"
                                            data-id="{{ $item->id }}"
                                            data-nama="{{ $item->user->nama ?? 'Anggota' }}"
                                            data-nomor-induk="{{ $item->user->nomor_induk ?? '-' }}"
                                            data-divisi="{{ $item->user->divisi->divisi_name ?? '-' }}"
                                            data-topic="{{ $item->topic }}"
                                            data-status="{{ $item->status }}"
                                            data-confidential="{{ $item->is_confidential ? '1' : '0' }}"
                                            data-ruangan="{{ $item->ruangan ?? 'Belum ditentukan' }}"
                                            data-notes="{{ $item->notes ?? 'Tidak ada catatan tambahan' }}"
                                            data-hasil="{{ $item->hasil_konseling ?? 'Belum ada catatan hasil bimbingan' }}"
                                            data-date="{{ $item->session_date ? \Carbon\Carbon::parse($item->session_date)->locale('id')->isoFormat('D MMMM Y, HH:mm') : '-' }}"
                                            data-consultant="{{ $item->consultant->nama ?? 'Belum ditentukan' }}{{ $item->consultant && $item->consultant->level ? ' (' . $item->consultant->level . ')' : '' }}"
                                            data-created="{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}">
                                        <i class="fas fa-file-lines text-xs"></i>
                                        <span>Lihat Detail</span>
                                    </button>
                                @else
                                    @php
                                        $isAssignedConsultant = (int) auth()->id() === (int) $item->consultant_id;
                                    @endphp

                                    @if($item->status === 'menunggu')
                                        @if($isAssignedConsultant)
                                            <!-- Tombol Setujui Cepat (Hanya Konselor Terkait) -->
                                            <form method="POST" action="{{ route('admin.counseling.approve', $item->id) }}" class="inline" onsubmit="return confirm('Setujui jadwal konsultasi ini?')">
                                                @csrf
                                                <button type="submit" title="Setujui Jadwal" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition-all flex items-center gap-1">
                                                    <i class="fas fa-check text-[10px]"></i>
                                                    <span>Setujui</span>
                                                </button>
                                            </form>

                                            <!-- Tombol Tolak Cepat (Hanya Konselor Terkait) -->
                                            <button type="button" onclick="event.preventDefault(); openRejectModal({{ $item->id }}, '{{ addslashes($item->user->nama ?? 'Anggota') }}', '{{ addslashes($item->topic) }}')"
                                                    title="Tolak Pengajuan" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-xs font-semibold border border-rose-200 transition-colors flex items-center gap-1">
                                                <i class="fas fa-times text-[10px]"></i>
                                                <span>Tolak</span>
                                            </button>
                                        @else
                                            <!-- Indikator Menunggu untuk Pengguna Lain (Admin / Konselor Lain) -->
                                            <span class="px-2 py-1 bg-amber-50 text-amber-700 rounded-lg text-[10px] font-medium border border-amber-200 inline-flex items-center gap-1" title="Persetujuan dilakukan langsung oleh {{ $item->consultant ? $item->consultant->nama : 'konselor bersangkutan' }}">
                                                <i class="fas fa-user-clock text-[9px]"></i>
                                                <span>Menunggu {{ $item->consultant ? Str::words($item->consultant->nama, 2, '..') : 'Konselor' }}</span>
                                            </span>
                                        @endif
                                    @endif

                                    <button type="button"
                                            class="btn-open-session px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition-colors cursor-pointer"
                                            data-session="{{ base64_encode(json_encode($item)) }}">
                                        Detail / Kelola
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                            <i class="fas fa-comments text-3xl mb-2 text-slate-300 block"></i>
                            Belum ada jadwal konsultasi konseling untuk instansi ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3 border-t border-slate-100">
            {{ $sessions->links() }}
        </div>
    </div>
</div>
@endsection

@push('modals')
<!-- Modal Jadwalkan Konsultasi Baru -->
<div id="createModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto">
    <div class="relative bg-white rounded-2xl max-w-lg w-full p-4 sm:p-6 shadow-2xl space-y-4 max-h-[92vh] overflow-y-auto my-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="pr-2">
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 leading-snug">Jadwalkan Konsultasi Kesehatan / SafeSpace</h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Khusus anggota instansi {{ auth()->user()->instansi->nama_instansi ?? '' }}</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <i class="fas fa-times text-base sm:text-lg"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.counseling.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Anggota / Klien <span class="text-rose-500">*</span></label>
                <select name="user_id" required class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-indigo-500 bg-white">
                    <option value="">-- Cari Nama Anggota --</option>
                    @foreach($members as $m)
                        <option value="{{ $m->id }}">{{ $m->nama }} ({{ $m->nomor_induk ?? $m->username }}) - {{ $m->divisi->divisi_name ?? 'Tanpa Divisi' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal & Waktu Sesi <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="session_date" required min="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-indigo-500 bg-white">
                </div>

                @php
                    $isStaffConsultant = in_array(auth()->user()->level, ['Health Consultant', 'Health Monitor']);
                    $hcLabel = $instansi ? $instansi->getRoleLabel('Health Consultant') : 'Health Consultant';
                    $hmLabel = $instansi ? $instansi->getRoleLabel('Health Monitor') : 'Health Monitor';
                @endphp

                @if($isStaffConsultant)
                    <!-- Role Health Monitor / Health Consultant: Otomatis Akun Sendiri (Tidak Bisa Pilih Petugas Lain) -->
                    <input type="hidden" name="consultant_id" value="{{ auth()->id() }}">
                    <div class="p-3.5 bg-indigo-50/70 border border-indigo-200/80 rounded-xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-sm shadow-indigo-600/30">
                                <i class="fas {{ auth()->user()->level === 'Health Consultant' ? 'fa-brain' : 'fa-stethoscope' }}"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">{{ auth()->user()->nama }}</p>
                                <p class="text-[11px] text-indigo-700 font-medium">
                                    {{ auth()->user()->level === 'Health Consultant' ? $hcLabel : $hmLabel }}
                                    <span class="text-slate-400 font-normal">(Akun Anda Sendiri)</span>
                                </p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-800 border border-indigo-200">
                            Konselor Bertugas
                        </span>
                    </div>

                    <!-- Ceklis Status: Khusus Health Monitor & Health Consultant (Bukan Dropdown) -->
                    <div class="p-3 bg-emerald-50/60 border border-emerald-200/80 rounded-xl">
                        <label class="flex items-start gap-2.5 cursor-pointer">
                            <input type="checkbox" name="is_active_direct" value="1" id="isActiveDirect" checked class="mt-0.5 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <div>
                                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                    <i class="fas fa-bolt text-amber-500 text-[11px]"></i>
                                    <span>Langsung Aktifkan Jadwal Sesi (Disetujui)</span>
                                </span>
                                <p class="text-[11px] text-slate-500 leading-normal mt-0.5">
                                    Centang jika sesi ini langsung aktif / disetujui sekarang. Jika tidak dicentang, sesi akan berstatus <em>Menunggu Persetujuan</em>.
                                </p>
                            </div>
                        </label>
                    </div>
                @else
                    <!-- Role Admin Instansi: Wajib Memilih Kategori & Petugas (Status Dihapus, Otomatis Menunggu) -->
                    <div class="space-y-3 p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                1. Pilih Kategori Layanan / Petugas <span class="text-rose-500">*</span>
                            </label>
                            <select id="createRoleSelect" onchange="onRoleCategoryChange('createRoleSelect', 'createConsultantSelect')" required class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-indigo-500 bg-white font-medium">
                                <option value="">-- Pilih Kategori Petugas (Konselor / Monitor) --</option>
                                <option value="Health Consultant">🧠 Health Consultant ({{ $hcLabel }})</option>
                                <option value="Health Monitor">🩺 Health Monitor ({{ $hmLabel }})</option>
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1 leading-normal">
                                Pilih <strong>Health Consultant</strong> untuk bimbingan konseling/mental, atau <strong>Health Monitor</strong> untuk penanganan fisik/UKS/reproduksi.
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">
                                2. Pilih Petugas / Konselor Penanggung Jawab <span class="text-rose-500">*</span>
                            </label>
                            <select name="consultant_id" id="createConsultantSelect" required disabled class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-indigo-500 bg-slate-100 text-slate-400 font-medium">
                                <option value="">-- Pilih Kategori Petugas di Atas Terlebih Dahulu --</option>
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1" id="createConsultantHelp">
                                Daftar nama petugas instansi ini akan muncul setelah kategori di atas dipilih.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Topik Permasalahan / Konsultasi <span class="text-rose-500">*</span></label>
                <input type="text" name="topic" required placeholder="Contoh: Bimbingan Stres Ujian, Gangguan Haid, Burnout Kerja..."
                       class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-indigo-500 bg-white">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        <i class="fas fa-door-open text-indigo-500 mr-1"></i>Ruangan / Lokasi Sesi
                    </label>
                    <input type="text" name="ruangan" placeholder="Contoh: Ruang BK 1, Ruang UKS, Meet..."
                           class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-indigo-500 bg-white">
                    <p class="text-[10px] text-slate-400 mt-0.5">Lokasi tatap muka atau link meet</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        <i class="fas fa-comment-dots text-indigo-500 mr-1"></i>Catatan Tambahan Pengajuan
                    </label>
                    <input type="text" name="notes" placeholder="Catatan awal / keluhan pengantar..."
                           class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-indigo-500 bg-white">
                    <p class="text-[10px] text-slate-400 mt-0.5">Catatan pengantar dari pembuat jadwal</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_confidential" value="1" id="is_conf" checked class="rounded text-indigo-600">
                <label for="is_conf" class="text-xs text-slate-600 font-medium">Jaga kerahasiaan sesi (hanya konselor & klien yang dapat melihat)</label>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold text-center transition-colors">Batal</button>
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-md text-center transition-all">Simpan & Jadwalkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tolak Pengajuan -->
<div id="rejectModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
    <div class="relative bg-white rounded-2xl max-w-md w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto">
        <h3 class="text-sm sm:text-base font-extrabold text-slate-900 flex items-center gap-2 text-rose-600">
            <i class="fas fa-times-circle"></i>
            <span>Tolak Pengajuan Booking Konseling</span>
        </h3>
        <p class="text-xs text-slate-600" id="rejectSubtitle"></p>

        <form id="rejectForm" method="POST" action="" data-confirm="Tolak permohonan bimbingan konseling ini dan kirimkan alasan ke anggota?" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea name="alasan" required rows="3" placeholder="Sampaikan alasan (misal: jadwal bentrok, konselor sedang dinas luar, silakan ajukan tanggal lain...)"
                          class="w-full text-xs rounded-xl border border-slate-200 p-2.5 focus:ring-2 focus:ring-rose-500 bg-white"></textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeRejectModal()" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold text-center">Batal</button>
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-md text-center">Tolak Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detail Sesi Konseling (Dedicated Read-Only Modal) -->
<div id="counselingDetailModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm hidden z-[999999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto" onclick="if(event.target === this) closeCounselingDetailModal()">
    <div class="relative bg-white rounded-2xl max-w-lg w-full p-4 sm:p-6 shadow-2xl space-y-4 max-h-[92vh] overflow-y-auto my-auto" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                    <i class="fas fa-heart-pulse text-base"></i>
                </div>
                <div>
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-800">Detail Sesi Konseling</h3>
                    <p class="text-[11px] text-slate-500" id="detailKlienSub">Klien: -</p>
                </div>
            </div>
            <button type="button" onclick="closeCounselingDetailModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                <i class="fas fa-times text-base"></i>
            </button>
        </div>

        <!-- Read Only Badge -->
        <div class="p-3 bg-indigo-50/80 border border-indigo-200/80 rounded-xl text-indigo-700 text-xs flex items-center gap-2">
            <i class="fas fa-eye text-indigo-500 text-sm shrink-0"></i>
            <span class="leading-relaxed"><strong>Mode Pemantauan (Read-Only):</strong> Rekapitulasi laporan sesi konseling dan bimbingan anggota instansi.</span>
        </div>

        <!-- Topik & Status -->
        <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/70 space-y-2.5">
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Topik Permasalahan</span>
                    <h4 class="text-sm font-bold text-slate-900 mt-0.5" id="detailTopic">-</h4>
                </div>
                <div id="detailStatusBadge">
                    <!-- badge status injected here -->
                </div>
            </div>
            <div class="flex items-center gap-2 pt-1 border-t border-slate-200/60 text-xs text-slate-600" id="detailConfidentialRow">
                <i class="fas fa-user-shield text-indigo-500"></i>
                <span id="detailConfidentialText">Sesi Bersifat Rahasia / Privat</span>
            </div>
        </div>

        <!-- Jadwal, Ruangan & Konselor -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1">
                    <i class="fas fa-calendar-alt text-indigo-500 mr-1"></i>Jadwal Sesi
                </span>
                <span class="text-xs font-semibold text-slate-800" id="detailDate">-</span>
            </div>
            <div class="p-3 bg-slate-50/60 rounded-xl border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block mb-1">
                    <i class="fas fa-door-open text-indigo-500 mr-1"></i>Ruangan / Lokasi
                </span>
                <span class="text-xs font-semibold text-slate-800" id="detailRuangan">-</span>
            </div>
        </div>

        <!-- Petugas / Konselor Penanggung Jawab -->
        <div class="p-3 bg-indigo-50/40 rounded-xl border border-indigo-100/70">
            <span class="text-[10px] uppercase font-bold text-indigo-600 tracking-wider block mb-1">
                <i class="fas fa-user-tie text-indigo-600 mr-1"></i>Petugas / Konselor Bertugas
            </span>
            <span class="text-xs font-bold text-slate-800" id="detailConsultant">-</span>
        </div>

        <!-- Catatan Tambahan Pengajuan -->
        <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/70 space-y-1">
            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">
                <i class="fas fa-comment-dots text-slate-400 mr-1"></i>Catatan Pengajuan (Awal)
            </span>
            <p class="text-xs text-slate-700 leading-relaxed italic" id="detailNotes">-</p>
        </div>

        <!-- Catatan Hasil Konseling / Bimbingan -->
        <div class="p-3.5 bg-emerald-50/60 rounded-xl border border-emerald-200/80 space-y-1.5">
            <div class="text-[11px] font-bold text-emerald-900 flex items-center gap-1.5">
                <i class="fas fa-file-signature text-emerald-600"></i>
                <span>Catatan Hasil Bimbingan & Rekomendasi</span>
            </div>
            <p class="text-xs text-slate-700 leading-relaxed" id="detailHasil">-</p>
        </div>

        <!-- Footer Info -->
        <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1">
            <span>Dibuat: <strong id="detailCreated" class="text-slate-600 font-medium">-</strong></span>
        </div>

        <!-- Tombol Tutup -->
        <div class="pt-2 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeCounselingDetailModal()" class="w-full sm:w-auto px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Kelola & Evaluasi Sesi -->
<div id="sessionModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-3 sm:p-4 overflow-y-auto" style="display: none;">
    <div class="relative bg-white rounded-2xl max-w-lg w-full p-4 sm:p-6 shadow-2xl space-y-4 max-h-[92vh] overflow-y-auto my-auto">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800">
                    {{ auth()->user()->isAdminInstansi() ? 'Laporan & Detail Sesi Konseling' : 'Detail & Evaluasi Sesi Konseling' }}
                </h3>
                <p class="text-[11px] text-slate-400" id="modalClientInfo">Klien: -</p>
            </div>
            <button type="button" onclick="closeSessionModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                <i class="fas fa-times text-base sm:text-lg"></i>
            </button>
        </div>

        <form id="sessionForm" method="POST" action="" class="space-y-4">
            @csrf

            @if(auth()->user()->isAdminInstansi())
            <div class="p-3 bg-indigo-50/80 border border-indigo-200/80 rounded-xl text-indigo-700 text-xs flex items-center gap-2">
                <i class="fas fa-eye text-indigo-500 text-sm shrink-0"></i>
                <span class="leading-relaxed"><strong>Mode Laporan (Read-Only):</strong> Admin Instansi memiliki hak pantau dan rekapitulasi data konseling tanpa wewenang mengubah data klinis/bimbingan.</span>
            </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Sesi</label>
                <select name="status" id="modalStatus" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 bg-white font-medium">
                    <option value="menunggu">Menunggu Persetujuan</option>
                    <option value="terjadwal">Disetujui / Terjadwal</option>
                    <option value="selesai">Selesai Dilaksanakan</option>
                    <option value="dibatalkan">Dibatalkan / Ditolak</option>
                </select>
            </div>

            <!-- Cascading Dropdowns Edit Sesi -->
            <div class="space-y-3 p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">1. Kategori Petugas</label>
                    <select id="modalRoleSelect" onchange="onRoleCategoryChange('modalRoleSelect', 'modalConsultant')" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 bg-white font-medium">
                        <option value="">-- Pilih Kategori Petugas --</option>
                        <option value="Health Consultant">🧠 Health Consultant ({{ $hcLabel }})</option>
                        <option value="Health Monitor">🩺 Health Monitor ({{ $hmLabel }})</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">2. Petugas / Konselor Penanggung Jawab</label>
                    <select name="consultant_id" id="modalConsultant" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 bg-slate-100 text-slate-400 font-medium">
                        <option value="">-- Pilih Kategori Petugas di Atas Terlebih Dahulu --</option>
                    </select>
                </div>
            </div>

            <!-- Bagian 1: Ruangan & Catatan Tambahan Pengajuan -->
            <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/70 space-y-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                    <i class="fas fa-calendar-day text-slate-400"></i>
                    <span>Informasi Jadwal & Ruangan / Lokasi</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            <i class="fas fa-door-open text-indigo-500 mr-1"></i>Ruangan / Lokasi Sesi
                        </label>
                        <input type="text" name="ruangan" id="modalRuangan" placeholder="Contoh: Ruang BK 1 / Ruang UKS"
                               class="w-full text-xs rounded-xl border border-slate-200 p-2.5 bg-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">
                            <i class="fas fa-clock text-indigo-500 mr-1"></i>Jadwal Sesi
                        </label>
                        <input type="datetime-local" name="session_date" id="modalSessionDate"
                               class="w-full text-xs rounded-xl border border-slate-200 p-2.5 bg-white focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        <i class="fas fa-comment-dots text-indigo-500 mr-1"></i>Catatan Tambahan Pengajuan (Awal)
                    </label>
                    <textarea name="notes" id="modalNotes" rows="2" class="w-full text-xs rounded-xl border border-slate-200 p-2.5 bg-white focus:ring-2 focus:ring-indigo-500" placeholder="Catatan awal keluhan atau preferensi saat booking diajukan"></textarea>
                    <p class="text-[10px] text-slate-400 mt-0.5">Catatan pengantar dari pembuat jadwal.</p>
                </div>
            </div>

            <!-- Bagian 2: Catatan Hasil Konseling / Bimbingan -->
            <div class="p-3.5 bg-emerald-50/50 rounded-xl border border-emerald-200/80 space-y-2">
                <label class="block text-xs font-bold text-emerald-900">
                    <i class="fas fa-file-signature text-emerald-600 mr-1"></i>Catatan Hasil Konseling / Bimbingan
                </label>
                <textarea name="hasil_konseling" id="modalHasilKonseling" rows="3" class="w-full text-xs rounded-xl border border-emerald-200 p-2.5 bg-white focus:ring-2 focus:ring-emerald-500" placeholder="Diisi oleh konselor: evaluasi sesi bimbingan, catatan medis/psikologis, kesepakatan solusi, atau rekomendasi tindak lanjut..."></textarea>
                <p class="text-[10px] text-emerald-700 leading-normal">
                    <i class="fas fa-info-circle mr-0.5"></i>Khusus hasil observasi, bimbingan konselor, dan rekomendasi setelah/saat sesi konseling dilaksanakan.
                </p>
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeSessionModal()" class="w-full sm:w-auto px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold text-center hover:bg-slate-200 transition-colors">Tutup</button>
                @if(!auth()->user()->isAdminInstansi())
                <button type="submit" class="w-full sm:w-auto px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-xs font-semibold shadow-md text-center hover:bg-indigo-700 transition-colors">Simpan Perubahan</button>
                @endif
            </div>
        </form>
    </div>
</div>
@endpush

@push('scripts')
<script>
window.openCounselingDetail = function(btn) {
    try {
        if (!btn) {
            btn = (window.event && window.event.target) ? window.event.target.closest('button, .view-counseling-detail') : null;
        } else if (btn.closest) {
            btn = btn.closest('button, .view-counseling-detail') || btn;
        }
        if (!btn) return;

        const getVal = function(attr, fallback) {
            return btn.getAttribute(attr) || fallback || '-';
        };

        const nama = getVal('data-nama', 'Anggota');
        const divisi = getVal('data-divisi', '-');
        const noInduk = getVal('data-nomor-induk', '-');

        const elSub = document.getElementById('detailKlienSub');
        if (elSub) elSub.textContent = `Klien: ${nama} (${divisi} • No: ${noInduk})`;

        const elTopic = document.getElementById('detailTopic');
        if (elTopic) elTopic.textContent = getVal('data-topic', '-');

        const elDate = document.getElementById('detailDate');
        if (elDate) elDate.textContent = getVal('data-date', '-');

        const elRuangan = document.getElementById('detailRuangan');
        if (elRuangan) elRuangan.textContent = getVal('data-ruangan', 'Belum ditentukan');

        const elConsultant = document.getElementById('detailConsultant');
        if (elConsultant) elConsultant.textContent = getVal('data-consultant', 'Belum ditentukan');

        const elNotes = document.getElementById('detailNotes');
        if (elNotes) elNotes.textContent = getVal('data-notes', 'Tidak ada catatan tambahan pengajuan.');

        const elHasil = document.getElementById('detailHasil');
        if (elHasil) elHasil.textContent = getVal('data-hasil', 'Belum ada catatan hasil observasi bimbingan.');

        const elCreated = document.getElementById('detailCreated');
        if (elCreated) elCreated.textContent = getVal('data-created', '-');

        // Status badge
        const status = getVal('data-status', 'menunggu');
        let badgeHtml = '';
        if (status === 'menunggu') {
            badgeHtml = '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span><span>Menunggu Persetujuan</span></span>';
        } else if (status === 'terjadwal') {
            badgeHtml = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200"><i class="fas fa-calendar-check text-[9px]"></i><span>Disetujui / Terjadwal</span></span>';
        } else if (status === 'selesai') {
            badgeHtml = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fas fa-circle-check text-[9px]"></i><span>Selesai Dilaksanakan</span></span>';
        } else {
            badgeHtml = '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="fas fa-ban text-[9px]"></i><span>Ditolak / Batal</span></span>';
        }
        const elBadge = document.getElementById('detailStatusBadge');
        if (elBadge) elBadge.innerHTML = badgeHtml;

        const isConf = getVal('data-confidential', '0') === '1';
        const elConf = document.getElementById('detailConfidentialText');
        if (elConf) elConf.textContent = isConf ? 'Sesi Bersifat Rahasia / Privat (SafeSpace)' : 'Sesi Terbuka / Standar';

        const detailModal = document.getElementById('counselingDetailModal');
        if (detailModal) {
            detailModal.classList.remove('hidden');
            detailModal.style.removeProperty('display');
            detailModal.style.display = 'flex';
            detailModal.style.setProperty('display', 'flex', 'important');
            detailModal.style.zIndex = '999999';
            detailModal.style.opacity = '1';
            detailModal.style.pointerEvents = 'auto';
        }
    } catch (err) {
        console.error('Error opening counseling detail modal:', err);
        const detailModal = document.getElementById('counselingDetailModal');
        if (detailModal) {
            detailModal.classList.remove('hidden');
            detailModal.style.display = 'flex';
            detailModal.style.setProperty('display', 'flex', 'important');
            detailModal.style.zIndex = '999999';
        }
    }
};

window.closeCounselingDetailModal = function() {
    const detailModal = document.getElementById('counselingDetailModal');
    if (detailModal) {
        detailModal.classList.add('hidden');
        detailModal.style.display = 'none';
        detailModal.style.setProperty('display', 'none', 'important');
    }
};

@php
    $consultantsPayload = $consultants->map(function($c) use ($hcLabel, $hmLabel) {
        return [
            'id' => $c->id,
            'nama' => $c->nama,
            'level' => $c->level,
            'label' => $c->level === 'Health Consultant' ? $hcLabel : $hmLabel,
        ];
    })->values();
@endphp
const consultantsData = {!! json_encode($consultantsPayload) !!};

function onRoleCategoryChange(roleSelectId, consultantSelectId, selectedConsultantId = null) {
    const roleSelect = document.getElementById(roleSelectId);
    const consultantSelect = document.getElementById(consultantSelectId);
    if (!roleSelect || !consultantSelect) return;

    const selectedRole = roleSelect.value;
    consultantSelect.innerHTML = '';

    if (!selectedRole) {
        consultantSelect.disabled = true;
        consultantSelect.classList.remove('bg-white', 'text-slate-800');
        consultantSelect.classList.add('bg-slate-100', 'text-slate-400');
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = '-- Pilih Kategori Petugas di Atas Terlebih Dahulu --';
        consultantSelect.appendChild(opt);
        return;
    }

    const filtered = consultantsData.filter(c => c.level === selectedRole);

    consultantSelect.disabled = false;
    consultantSelect.classList.remove('bg-slate-100', 'text-slate-400');
    consultantSelect.classList.add('bg-white', 'text-slate-800');

    if (filtered.length === 0) {
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = `-- Belum ada petugas ${selectedRole} terdaftar di instansi ini --`;
        consultantSelect.appendChild(opt);
        return;
    }

    const defaultOpt = document.createElement('option');
    defaultOpt.value = '';
    defaultOpt.textContent = `-- Pilih Petugas / Konselor (${filtered.length} tersedia) --`;
    consultantSelect.appendChild(defaultOpt);

    filtered.forEach(c => {
        const opt = document.createElement('option');
        opt.value = c.id;
        opt.textContent = `${c.nama} — ${c.label}`;
        if (selectedConsultantId && String(c.id) === String(selectedConsultantId)) {
            opt.selected = true;
        }
        consultantSelect.appendChild(opt);
    });
}

function openCreateModal() {
    const createModal = document.getElementById('createModal');
    createModal.classList.remove('hidden');

    const roleSelect = document.getElementById('createRoleSelect');
    if (roleSelect) {
        roleSelect.value = '';
        onRoleCategoryChange('createRoleSelect', 'createConsultantSelect', null);
    }
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

function openRejectModal(id, nama, topic) {
    document.getElementById('rejectForm').action = '/admin/konseling/' + id + '/reject';
    document.getElementById('rejectSubtitle').innerText = 'Menolak booking dari: ' + nama + ' (Topik: ' + topic + ')';
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

const isAdminInstansi = {{ auth()->user()->isAdminInstansi() ? 'true' : 'false' }};

function openSessionModal(item) {
    document.getElementById('sessionForm').action = '/admin/konseling/' + item.id + '/status';
    document.getElementById('modalStatus').value = item.status;
    document.getElementById('modalRuangan').value = item.ruangan || '';
    document.getElementById('modalNotes').value = item.notes || '';
    document.getElementById('modalHasilKonseling').value = item.hasil_konseling || '';

    if (item.session_date) {
        try {
            const d = new Date(item.session_date);
            const pad = (n) => String(n).padStart(2, '0');
            const formatted = `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            document.getElementById('modalSessionDate').value = formatted;
        } catch (e) {}
    } else {
        document.getElementById('modalSessionDate').value = '';
    }

    const clientNama = (item.user && item.user.nama) ? item.user.nama : 'Anggota';
    const topicNama = item.topic ? ' — ' + item.topic : '';
    document.getElementById('modalClientInfo').textContent = 'Klien: ' + clientNama + topicNama;

    let role = '';
    if (item.consultant_id) {
        const found = consultantsData.find(c => String(c.id) === String(item.consultant_id));
        if (found) {
            role = found.level;
        } else if (item.consultant && item.consultant.level) {
            role = item.consultant.level;
        }
    }

    const roleSelect = document.getElementById('modalRoleSelect');
    roleSelect.value = role;
    onRoleCategoryChange('modalRoleSelect', 'modalConsultant', item.consultant_id);

    // Jika Admin Instansi: Buat seluruh input form disabled (Read-Only)
    const inputs = document.getElementById('sessionForm').querySelectorAll('input, select, textarea');
    inputs.forEach(el => {
        if (isAdminInstansi) {
            el.setAttribute('disabled', 'disabled');
            el.classList.add('bg-slate-50', 'cursor-not-allowed', 'text-slate-700');
        } else {
            el.removeAttribute('disabled');
            el.classList.remove('cursor-not-allowed');
        }
    });

    const sessionModal = document.getElementById('sessionModal');
    if (sessionModal) {
        sessionModal.classList.remove('hidden');
        sessionModal.style.display = 'flex';
    }
}

function closeSessionModal() {
    const sessionModal = document.getElementById('sessionModal');
    if (sessionModal) {
        sessionModal.classList.add('hidden');
        sessionModal.style.display = 'none';
    }
}

// Delegasi klik tombol Lihat Detail Khusus (Admin Instansi / Mode Read-Only)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.view-counseling-detail');
    if (btn) {
        e.preventDefault();
        openCounselingDetail(btn);
    }
});

// Delegasi klik tombol Detail / Kelola Sesi Operasional (Konselor / Monitor)
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-open-session');
    if (btn) {
        e.preventDefault();
        try {
            const raw = btn.getAttribute('data-session');
            if (raw) {
                const jsonStr = decodeURIComponent(escape(atob(raw)));
                const item = JSON.parse(jsonStr);
                openSessionModal(item);
            }
        } catch (err) {
            console.error('Gagal membuka modal detail sesi:', err);
        }
    }
});

// Tutup modal via tombol Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCounselingDetailModal();
        closeSessionModal();
        closeCreateModal();
        closeRejectModal();
    }
});

// Inisialisasi Grafik Analitik Konseling
document.addEventListener('DOMContentLoaded', function() {
    // 1. Grafik Status Konseling (Doughnut)
    const statusCtx = document.getElementById('counselingStatusChart');
    if (statusCtx && typeof Chart !== 'undefined') {
        const labels = {!! json_encode($statusChartData['labels']) !!};
        const dataValues = {!! json_encode($statusChartData['data']) !!};
        const bgColors = {!! json_encode($statusChartData['colors']) !!};
        const total = {{ $totalSessions > 0 ? $totalSessions : 1 }};

        new Chart(statusCtx, {
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

    // 2. Grafik Topik Konseling Terbanyak (Horizontal Bar)
    const topicCtx = document.getElementById('counselingTopicChart');
    if (topicCtx && typeof Chart !== 'undefined') {
        const topicLabels = {!! json_encode($topicChartData['labels']) !!};
        const topicValues = {!! json_encode($topicChartData['data']) !!};

        new Chart(topicCtx, {
            type: 'bar',
            data: {
                labels: topicLabels.length ? topicLabels : ['Belum ada data topik'],
                datasets: [{
                    label: 'Pengajuan Sesi',
                    data: topicValues.length ? topicValues : [0],
                    backgroundColor: 'rgba(99, 102, 241, 0.85)',
                    borderColor: '#6366f1',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    maxBarThickness: 24
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` ${context.raw} pengajuan bimbingan`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { size: 10 }
                        },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
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

