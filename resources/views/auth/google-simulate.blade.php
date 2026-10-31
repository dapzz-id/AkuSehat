<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Sign-In Sandbox | Aku Sehat</title>
    <link rel="icon" href="{{ asset('src/aku_sehat_icon.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden">
        <div class="p-6 text-center border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-slate-800">Simulasi Sign in with Google</h2>
            <p class="text-xs text-slate-500 mt-1">
                Lingkungan pengujian lokal. Masukkan email akun yang <strong>sudah didaftarkan oleh Superadmin atau Admin Instansi</strong> untuk menguji alur login Google.
            </p>
        </div>

        <form action="{{ route('auth.google.callback') }}" method="GET" class="p-6 space-y-4">
            <input type="hidden" name="simulated" value="1">

            <!-- Security Policy Warning -->
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 flex items-start gap-2.5">
                <i class="fas fa-shield-halved text-amber-600 mt-0.5 text-sm flex-shrink-0"></i>
                <div class="leading-relaxed">
                    <strong>Penting:</strong> Akun baru <em>tidak dibuat otomatis</em> saat login Google. Email harus sudah terdaftar pada salah satu akun di sistem.
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Akun Google</label>
                <input type="text" name="name" value="Pengguna Google" required
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#308a34]">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Email Google (Terdaftar)</label>
                <input type="email" name="email" value="" required placeholder="Masukkan email yang sudah terdaftar di sistem"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-[#308a34]">
            </div>

            <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-[11px] text-blue-800">
                <i class="fas fa-info-circle mr-1"></i>
                Untuk mengaktifkan Google OAuth resmi dengan Google Cloud Console, isi <code class="font-bold">GOOGLE_CLIENT_ID</code> dan <code class="font-bold">GOOGLE_CLIENT_SECRET</code> di file <code class="font-bold">.env</code>.
            </div>

            <button type="submit" class="w-full py-3 bg-[#308a34] hover:bg-[#1B5E20] text-white rounded-xl font-bold text-sm shadow-md transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#fff" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#fff" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#fff" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#fff" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Masuk dengan Akun Ini</span>
            </button>

            <a href="{{ route('login') }}" class="block text-center text-xs text-slate-500 hover:text-slate-800 pt-2">
                &larr; Kembali ke halaman login biasa
            </a>
        </form>
    </div>
</body>
</html>
