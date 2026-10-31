<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | AkuSehat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-red-50/30 to-emerald-50/20 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8 text-center relative overflow-hidden">
        <!-- Accent Glow -->
        <div class="absolute -top-16 -right-16 w-36 h-36 bg-red-500/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-16 -left-16 w-36 h-36 bg-emerald-500/10 rounded-full blur-2xl"></div>

        <div class="relative z-10">
            <!-- Icon Lock -->
            <div class="w-20 h-20 bg-red-50 border-2 border-red-100 text-red-600 rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-sm text-3xl">
                <i class="fas fa-shield-halved"></i>
            </div>

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 uppercase tracking-wider mb-3">
                <i class="fas fa-lock text-[10px]"></i> HTTP 403 Forbidden
            </span>

            @php
                $msg = $exception->getMessage() ?: '';
                $isSuperAdminRoute = str_contains($msg, 'SuperAdmin') || str_contains($msg, 'Administrator Pusat') || request()->is('superadmin*');
            @endphp

            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                {{ $isSuperAdminRoute ? 'Akses Khusus Administrator Pusat' : 'Akses Lintas Institusi Ditolak' }}
            </h1>
            
            <p class="text-sm text-slate-600 mt-2.5 leading-relaxed">
                {{ $msg ?: 'Anda tidak memiliki izin untuk melihat atau mengakses halaman atau data ini. Seluruh data dilindungi dengan enkripsi dan isolasi tenant UUID terpadu.' }}
            </p>

            <div class="mt-4 p-3.5 bg-slate-50 border border-slate-100 rounded-2xl text-left text-xs text-slate-600 space-y-1.5">
                <div class="flex items-center gap-2 text-slate-800 font-semibold">
                    <i class="fas fa-shield-halved text-emerald-600"></i>
                    <span>Kebijakan Privasi & Isolasi Data Multi-Tenant:</span>
                </div>
                <p class="leading-relaxed text-slate-500">
                    Data dan rekam medis milik suatu institusi/instansi diisolasi secara ketat dan tidak dapat diakses oleh institusi/instansi lain demi menjaga kerahasiaan data pribadi, privasi kesehatan, serta kepatuhan perlindungan data seluruh anggota, karyawan, atau siswa/staf.
                </p>
                @if($isSuperAdminRoute && auth()->check() && !auth()->user()->isSuperAdmin())
                    <div class="pt-2 mt-2 border-t border-slate-200/70 text-[11px] text-amber-800 flex items-start gap-1.5">
                        <i class="fas fa-triangle-exclamation text-amber-600 mt-0.5 flex-shrink-0"></i>
                        <span>Akun Anda saat ini masuk sebagai <strong>{{ auth()->user()->level }}</strong> ({{ auth()->user()->nama }}). Silakan login dengan akun <strong>SuperAdmin</strong> jika Anda adalah Administrator Pusat.</span>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex flex-col sm:flex-row items-center gap-2.5">
                <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#2e7d32] hover:bg-[#1b5e20] text-white text-sm font-semibold shadow-md shadow-emerald-700/20 transition-all duration-200">
                    <i class="fas fa-house"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
                <button type="button" onclick="window.history.back()" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    <span>Halaman Sebelumnya</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
