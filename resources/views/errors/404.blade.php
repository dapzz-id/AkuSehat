<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Tidak Ditemukan | AkuSehat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-slate-100 to-emerald-50/20 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8 text-center relative overflow-hidden">
        <div class="relative z-10">
            <div class="w-20 h-20 bg-amber-50 border-2 border-amber-100 text-amber-600 rounded-3xl flex items-center justify-center mx-auto mb-5 shadow-sm text-3xl">
                <i class="fas fa-magnifying-glass"></i>
            </div>

            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wider mb-3">
                <i class="fas fa-circle-exclamation text-[10px]"></i> HTTP 404 Not Found
            </span>

            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Data Tidak Ditemukan</h1>
            
            <p class="text-sm text-slate-600 mt-2.5 leading-relaxed">
                Halaman atau data dengan pengenal UUID tersebut tidak ditemukan pada institusi/instansi Anda atau telah dipindahkan.
            </p>

            <div class="mt-6 flex flex-col sm:flex-row items-center gap-2.5">
                <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-[#2e7d32] hover:bg-[#1b5e20] text-white text-sm font-semibold shadow-md shadow-emerald-700/20 transition-all duration-200">
                    <i class="fas fa-house"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
                <button type="button" onclick="window.history.back()" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition-all duration-200">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
