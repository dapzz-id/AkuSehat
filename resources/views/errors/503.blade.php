<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Pemeliharaan Sistem | Aku Sehat</title>
    <link rel="icon" href="{{ asset('src/aku_sehat_icon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-950 min-h-screen flex items-center justify-center p-4 text-slate-100">
    <div class="max-w-lg w-full bg-slate-900/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-emerald-800/40 p-8 sm:p-10 text-center relative overflow-hidden">
        <!-- Glow accents -->
        <div class="absolute -top-20 -right-20 w-48 h-48 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -left-20 w-48 h-48 bg-teal-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 space-y-5">
            <!-- Maintenance Icon -->
            <div class="w-20 h-20 bg-emerald-500/10 border-2 border-emerald-500/30 text-emerald-400 rounded-3xl flex items-center justify-center mx-auto shadow-lg shadow-emerald-900/40 text-3xl">
                <i class="fas fa-screwdriver-wrench animate-pulse"></i>
            </div>

            <!-- Status Badge -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                <span>Mode Pemeliharaan (Maintenance Mode)</span>
            </div>

            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
                    Sistem Sedang Dalam Pemeliharaan
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-3 leading-relaxed">
                    Website Aku Sehat saat ini sedang dalam peningkatan performa server, keamanan, atau pembaruan sistem rutin. Seluruh data Anda aman dan layanan akan segera kembali aktif.
                </p>
            </div>

            <!-- SuperAdmin Notice -->
            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 text-left text-xs text-slate-300 space-y-1.5">
                <div class="flex items-center gap-2 text-emerald-400 font-bold">
                    <i class="fas fa-shield-halved"></i>
                    <span>Akses Super Administrator:</span>
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Jika Anda adalah Super Administrator, silakan masuk melalui tautan di bawah ini untuk mengakses dashboard dan mengelola sistem.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                <a href="{{ route('login') }}" 
                   class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold border border-emerald-800 shadow-lg shadow-emerald-900/30 transition-all">
                    <i class="fas fa-right-to-bracket"></i>
                    <span>Masuk sebagai SuperAdmin</span>
                </a>
                
                @if(auth()->check())
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white/10 hover:bg-white/15 text-slate-300 text-xs font-semibold border border-white/15 transition-all">
                            <i class="fas fa-arrow-right-from-bracket"></i>
                            <span>Keluar Akun</span>
                        </button>
                    </form>
                @endif
            </div>

            <p class="text-[11px] text-slate-500 pt-2">
                &copy; {{ date('Y') }} Aku Sehat &bull; PT Neura Cakrawira Solusi
            </p>
        </div>
    </div>
</body>
</html>
