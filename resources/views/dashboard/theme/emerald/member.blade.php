<!-- Header Greeting -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
            Halo, {{ $user->nama }}! 👋
        </h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-1">
            Selamat datang di portal kesehatan pribadi Anda. Pantau kebugaran dan kondisi fisik Anda di sini.
        </p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        @if($instansi)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                <i class="fas fa-building-columns"></i> Terhubung: {{ $instansi->nama_instansi }}
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                <i class="fas fa-user"></i> Akun Pribadi
            </span>
        @endif
    </div>
</div>

<!-- Notification Alert -->
@if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3">
        <i class="fas fa-circle-exclamation text-rose-600 text-lg"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

<!-- Institution Connection Card -->
@if(!$instansi)
    <div class="bg-gradient-to-br from-emerald-900 via-emerald-800 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl">
            <i class="fas fa-link"></i>
        </div>
        <div class="relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 text-emerald-300 border border-white/20 mb-3">
                <i class="fas fa-building-circle-check"></i>
                <span>Tautkan Akun ke Instansi / Institusi</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold tracking-tight mb-2">
                Punya Kode Undangan dari Sekolah atau Kantor?
            </h2>
            <p class="text-xs sm:text-sm text-emerald-100/80 mb-5 leading-relaxed">
                Masukkan kode undangan (invite code) atau NPSN instansi Anda di bawah ini agar data kesehatan Anda terhubung langsung dengan Health Monitor & Konsultan instansi Anda.
            </p>

            <form action="{{ route('link.instansi') }}" method="POST" class="flex flex-col sm:flex-row gap-3 max-w-md">
                @csrf
                <input type="text" name="invite_code" required
                       placeholder="Contoh: SMKT-5542 atau NPSN"
                       class="flex-1 px-4 py-3 rounded-xl bg-white text-slate-900 text-sm font-mono uppercase tracking-wider placeholder:text-slate-400 placeholder:normal-case placeholder:font-sans focus:outline-none focus:ring-2 focus:ring-emerald-400">
                <button type="submit" class="px-5 py-3 rounded-xl bg-[#308a34] hover:bg-[#1B5E20] font-bold text-sm text-white transition-colors shadow-lg shadow-black/20 whitespace-nowrap">
                    Hubungkan Sekarang
                </button>
            </form>
        </div>
    </div>
@else
    <div class="bg-white rounded-2xl shadow-soft p-5 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xl font-bold">
                <i class="fas fa-building-columns"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-base">{{ $instansi->nama_instansi }}</h3>
                <p class="text-xs text-slate-500">
                    Kode Undangan: <span class="font-mono font-bold text-slate-800">{{ $instansi->invite_code ?? $instansi->npsn }}</span> &bull; Status: <span class="text-emerald-700 font-semibold">Aktif Terhubung</span>
                </p>
            </div>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                <i class="fas fa-shield-check text-emerald-600"></i> Lisensi Instansi Aktif
            </span>
        </div>
    </div>
@endif

<!-- Metrics Cards Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <!-- Physical Measurement Card -->
    <div class="bg-white rounded-2xl shadow-soft p-5 border border-slate-100">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pemeriksaan Fisik</span>
            <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center text-sm">
                <i class="fas fa-weight-scale"></i>
            </span>
        </div>
        @if($kesehatan)
            <div class="space-y-2">
                <div class="flex items-baseline justify-between">
                    <div>
                        <span class="text-2xl font-black text-slate-800">{{ $kesehatan->tb ?? '-' }}</span>
                        <span class="text-xs text-slate-500 ml-0.5">cm</span>
                    </div>
                    <div>
                        <span class="text-2xl font-black text-slate-800">{{ $kesehatan->bb ?? '-' }}</span>
                        <span class="text-xs text-slate-500 ml-0.5">kg</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Status BMI:</span>
                    <span class="font-bold text-emerald-800">{{ $kesehatan->status_bmi ?? 'Normal' }}</span>
                </div>
            </div>
        @else
            <p class="text-xs text-slate-400 py-3">Belum ada catatan pengukuran fisik terbaru.</p>
        @endif
    </div>

    <!-- Hemoglobin Card -->
    <div class="bg-white rounded-2xl shadow-soft p-5 border border-slate-100">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Kadar Hemoglobin (Hb)</span>
            <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-sm">
                <i class="fas fa-droplet"></i>
            </span>
        </div>
        @if($hb)
            <div class="space-y-2">
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-black text-slate-800">{{ $hb->hb ?? '-' }}</span>
                    <span class="text-xs text-slate-500">g/dL</span>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Status Hb:</span>
                    <span class="font-bold {{ ($hb->status ?? '') === 'Normal' ? 'text-emerald-800' : 'text-amber-700' }}">
                        {{ $hb->status ?? 'Normal' }}
                    </span>
                </div>
            </div>
        @else
            <p class="text-xs text-slate-400 py-3">Belum ada data skrining Hb terbaru.</p>
        @endif
    </div>

    <!-- Activity Overview Card -->
    <div class="bg-white rounded-2xl shadow-soft p-5 border border-slate-100 sm:col-span-2 lg:col-span-1">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Aktivitas & Kebugaran</span>
            <span class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-sm">
                <i class="fas fa-running"></i>
            </span>
        </div>
        <div class="space-y-2">
            <div class="flex items-baseline justify-between">
                <div>
                    <span class="text-2xl font-black text-slate-800">{{ $activities->count() }}</span>
                    <span class="text-xs text-slate-500 ml-1">Sesi Lari</span>
                </div>
                <div>
                    <span class="text-2xl font-black text-slate-800">{{ $vitalLogs->count() }}</span>
                    <span class="text-xs text-slate-500 ml-1">Log Vital</span>
                </div>
            </div>
            <div class="pt-2 border-t border-slate-100 text-xs text-slate-500">
                Sinkron otomatis melalui aplikasi mobile Aku Sehat.
            </div>
        </div>
    </div>
</div>
