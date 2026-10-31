<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Daftar Akun Baru | Aku Sehat</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('src/aku_sehat_icon.png') }}" type="image/png">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Vite Assets -->
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="h-full bg-slate-900 font-sans antialiased text-slate-800">
    <div class="min-h-full flex flex-col lg:flex-row">
        <!-- Left Side: Brand Visual & Value Props (Visible on lg+) -->
        <div class="hidden lg:flex lg:w-5/12 xl:w-5/12 relative overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950 p-12 lg:p-16 flex-col justify-between text-white">
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:24px_24px]"></div>
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Brand Header -->
            <div class="relative z-10">
                <a href="/" class="flex items-center gap-3 group">
                    <img src="{{ asset('src/aku_sehat_icon.png') }}" class="w-10 h-10 object-contain group-hover:scale-105 transition-transform" alt="Aku Sehat Icon">
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-white leading-tight">Aku Sehat</h1>
                        <p class="text-[11px] text-emerald-200/80">Sistem Informasi Kesehatan Terpadu</p>
                    </div>
                </a>
            </div>

            <!-- Central Hero -->
            <div class="relative z-10 my-auto py-8">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-400/20 text-emerald-300 text-xs font-medium mb-5 backdrop-blur-md">
                    <i class="fas fa-heart-pulse text-emerald-400"></i>
                    <span>Satu Akun untuk Segala Kebutuhan Kesehatan</span>
                </div>
                
                <h2 class="text-2xl xl:text-3xl font-extrabold text-white leading-snug mb-3">
                    Mulai Perjalanan Hidup Sehat & Bugar Hari Ini.
                </h2>
                <p class="text-sm text-emerald-100/80 mb-6 leading-relaxed">
                    Daftar sebagai <strong>Akun Pribadi</strong> untuk pelacakan aktivitas mandiri, atau hubungkan ke <strong>Instansi / Sekolah</strong> dengan kode undangan untuk integrasi rekam medis instansi.
                </p>

                <!-- Value Highlights -->
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-md">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 flex items-center justify-center text-emerald-300 text-sm">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="text-xs">
                            <span class="font-bold text-white block">Akun Pribadi Bebas Akses</span>
                            <span class="text-emerald-200/70">Gunakan fitur monitoring, run tracking, dan catatan fisik secara mandiri.</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 backdrop-blur-md">
                        <div class="w-8 h-8 rounded-lg bg-teal-500/20 flex items-center justify-center text-teal-300 text-sm">
                            <i class="fas fa-link"></i>
                        </div>
                        <div class="text-xs">
                            <span class="font-bold text-white block">Link Instansi dengan Mudah</span>
                            <span class="text-emerald-200/70">Cukup masukkan kode undangan atau NPSN untuk sinkronisasi otomatis.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Signature -->
            <div class="relative z-10 pt-4 border-t border-emerald-800/60 flex items-center justify-between text-xs text-emerald-200/70">
                <p>Dikembangkan oleh <span class="text-white font-semibold">Neura Cakrawira Asia</span></p>
            </div>
        </div>

        <!-- Right Side: Clean Modern Registration Form -->
        <div class="w-full lg:w-7/12 xl:w-7/12 flex flex-col justify-center items-center px-6 py-10 sm:px-12 lg:px-16 bg-slate-50 relative min-h-screen">
            <div class="w-full max-w-lg relative z-10">
                <!-- Header Title -->
                <div class="mb-6 text-center sm:text-left">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Akun Aku Sehat</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Buat akun pribadi baru atau gabung ke instansi Anda dalam hitungan detik.
                    </p>
                </div>

                <!-- Registration Card -->
                <div class="bg-white rounded-2xl shadow-soft border border-slate-200 p-6 sm:p-8">
                    <!-- Error Notification -->
                    @if ($errors->any())
                        <div class="mb-5 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm">
                            <div class="flex items-start gap-2.5">
                                <i class="fas fa-circle-exclamation text-rose-500 mt-0.5 text-base flex-shrink-0"></i>
                                <div class="flex-1 space-y-1">
                                    <p class="font-bold">Periksa kembali data Anda:</p>
                                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Google Sign-Up Button -->
                    <div class="mb-5">
                        <a href="{{ route('auth.google') }}" 
                           class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-xl text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:border-slate-400 transition-all shadow-sm">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            <span>Daftar Cepat dengan Google</span>
                        </a>
                    </div>

                    <!-- Divider -->
                    <div class="relative flex py-2 items-center mb-5">
                        <div class="flex-grow border-t border-slate-200"></div>
                        <span class="flex-shrink mx-4 text-xs font-medium text-slate-400 uppercase tracking-wider">atau lengkapi formulir</span>
                        <div class="flex-grow border-t border-slate-200"></div>
                    </div>

                    <form action="{{ route('register') }}" method="POST" class="space-y-4" id="registerForm">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div>
                            <label for="nama" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative rounded-xl">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-id-card text-sm"></i>
                                </div>
                                <input type="text" id="nama" name="nama" required value="{{ old('nama') }}"
                                       placeholder="Nama lengkap Anda"
                                       class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                            </div>
                        </div>

                        <!-- Username & Email Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="username" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                    Username <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative rounded-xl">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <i class="fas fa-user text-sm"></i>
                                    </div>
                                    <input type="text" id="username" name="username" required value="{{ old('username') }}"
                                           placeholder="huruf_angka"
                                           class="w-full pl-10 pr-3 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                    Alamat Email <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative rounded-xl">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                        <i class="fas fa-envelope text-sm"></i>
                                    </div>
                                    <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                           placeholder="nama@email.com"
                                           class="w-full pl-10 pr-3 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                                </div>
                            </div>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                                Jenis Kelamin <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer border border-slate-300 rounded-xl p-2.5 flex items-center justify-center gap-2 hover:border-emerald-500 transition-colors">
                                    <input type="radio" name="jk" value="L" {{ old('jk', 'L') === 'L' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-xs font-semibold text-slate-700"><i class="fas fa-mars text-blue-500 mr-1"></i> Laki-laki</span>
                                </label>
                                <label class="cursor-pointer border border-slate-300 rounded-xl p-2.5 flex items-center justify-center gap-2 hover:border-emerald-500 transition-colors">
                                    <input type="radio" name="jk" value="P" {{ old('jk') === 'P' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-xs font-semibold text-slate-700"><i class="fas fa-venus text-pink-500 mr-1"></i> Perempuan</span>
                                </label>
                            </div>
                        </div>

                        <!-- Password Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="password" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                    Password <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative rounded-xl">
                                    <input type="password" id="password" name="password" required placeholder="Minimal 6 karakter"
                                           class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                                </div>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                    Konfirmasi Password <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative rounded-xl">
                                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password"
                                           class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-300 rounded-xl focus:bg-white focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                                </div>
                            </div>
                        </div>

                        <!-- Kode Undangan / Invite Code Instansi (Opsional) -->
                        <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl space-y-1.5">
                            <label for="invite_code" class="block text-xs font-bold text-emerald-950 uppercase flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <i class="fas fa-building-columns text-emerald-700"></i>
                                    Kode Link / Undangan Instansi
                                </span>
                                <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full">
                                    Opsional
                                </span>
                            </label>
                            <input type="text" id="invite_code" name="invite_code" value="{{ old('invite_code') }}"
                                   placeholder="Contoh: SMKT-5542 atau NPSN sekolah"
                                   class="w-full px-3.5 py-2 text-sm bg-white border border-emerald-300 rounded-xl uppercase font-mono tracking-wider focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20">
                            <p class="text-[11px] text-emerald-800/80 leading-snug">
                                <i class="fas fa-circle-info mr-0.5"></i> Masukkan kode jika Anda diundang instansi/sekolah. Kosongkan untuk mendaftar sebagai <strong>Akun Pribadi</strong> (bisa ditautkan nanti).
                            </p>
                        </div>

                        <!-- Checkbox Kebijakan Privasi -->
                        <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl">
                            <div class="flex items-start gap-2.5">
                                <input type="checkbox" id="privacyConsent" name="kebijakan_privasi" value="1"
                                       class="w-4 h-4 mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                <label for="privacyConsent" class="text-xs text-slate-700 leading-snug cursor-pointer select-none">
                                    Saya telah membaca dan menyetujui 
                                    <button type="button" id="btnOpenPrivacyModal" class="text-emerald-800 font-bold underline hover:text-emerald-900 inline-block">
                                        Kebijakan Privasi
                                    </button>
                                    <span class="text-rose-500 font-bold">*</span>
                                </label>
                            </div>
                            <p id="privacyWarningText" class="text-[11px] text-amber-700 mt-1 pl-6 hidden flex items-center gap-1">
                                <i class="fas fa-circle-info"></i> Harap baca dan scroll Kebijakan Privasi hingga ke bawah.
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="btnSubmitRegister"
                                class="w-full py-3 px-4 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 hover:from-emerald-700 hover:via-emerald-800 hover:to-teal-800 shadow-md shadow-emerald-700/25 transition-all flex items-center justify-center gap-2">
                            <span>Daftar Akun Sekarang</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </button>
                    </form>

                    <!-- Link back to Login -->
                    <div class="mt-5 pt-4 border-t border-slate-100 text-center text-xs text-slate-500">
                        Sudah memiliki akun?
                        <a href="{{ route('login') }}" class="font-bold text-emerald-800 hover:text-emerald-900 hover:underline ml-1">
                            Masuk di Sini
                        </a>
                    </div>
                </div>

                <!-- Footer Copyright -->
                <div class="mt-6 text-center text-xs text-slate-400">
                    Aku Sehat &copy; {{ date('Y') }} &bull; Neura Cakrawira Asia
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL KEBIJAKAN PRIVASI DENGAN SCROLL LOCK -->
    <div id="privacyModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-2xl w-full flex flex-col max-h-[85vh] overflow-hidden animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                        <i class="fas fa-file-shield"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">Kebijakan Privasi Layanan</h3>
                        <p class="text-xs text-slate-500">Tanggal berlaku: 21 Oktober 2026</p>
                    </div>
                </div>
                <button type="button" id="btnClosePrivacyModal" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-slate-100">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Scroll Notice -->
            <div id="scrollBanner" class="bg-amber-50 border-b border-amber-200 px-5 py-2.5 flex items-center justify-between text-xs text-amber-800">
                <span class="flex items-center gap-2">
                    <i class="fas fa-arrow-down-long animate-bounce text-amber-600"></i>
                    <span>Harap baca dan <strong>scroll hingga ke bagian paling bawah</strong> untuk menyetujui.</span>
                </span>
                <span id="scrollPercentage" class="font-bold font-mono">0%</span>
            </div>

            <!-- Scrollable Content Box -->
            <div id="privacyScrollBox" class="p-5 sm:p-6 overflow-y-auto space-y-4 text-xs sm:text-sm text-slate-700 leading-relaxed max-h-[50vh] divide-y divide-slate-100">
                <div class="space-y-2">
                    <p>
                        Kebijakan Privasi ini menjelaskan bagaimana <strong>Neura Cakrawira Asia</strong> (“kami”) mengumpulkan, menggunakan, dan membagikan data pribadi saat Anda mengunjungi <a href="https://akusehat.neuracakrawira.asia/" target="_blank" class="text-emerald-800 underline font-semibold">akusehat.neuracakrawira.asia</a> atau menggunakan aplikasi <strong>Aku Sehat</strong>, serta pilihan dan hak yang Anda miliki.
                    </p>
                </div>

                <div class="pt-3 space-y-1.5">
                    <h4 class="font-bold text-slate-900 text-sm">Siapa kami</h4>
                    <p>Layanan ini dikelola oleh <strong>Neura Cakrawira Asia</strong>, berdomisili di Indonesia sebagai pengendali data pribadi. Email: neuracakrawira@yahoo.com | Telepon: +62 895-3831-07479.</p>
                </div>

                <div class="pt-3 space-y-1.5">
                    <h4 class="font-bold text-slate-900 text-sm">Informasi yang kami kumpulkan</h4>
                    <ul class="list-disc list-inside space-y-1 text-slate-600 text-xs">
                        <li><strong>Data saat registrasi:</strong> nama lengkap, username, alamat email, jenis kelamin, password.</li>
                        <li><strong>Data kesehatan & aktivitas:</strong> tinggi/berat badan, BMI, kadar Hb, tracking lari GPS, log hidrasi harian.</li>
                        <li><strong>Izin perangkat:</strong> Lokasi (fitur tracking run), Kamera & Galeri (foto profil dan media kesehatan).</li>
                    </ul>
                </div>

                <div class="pt-3 space-y-1.5">
                    <h4 class="font-bold text-slate-900 text-sm">Cara kami menggunakan informasi</h4>
                    <p class="text-xs">Untuk menyediakan layanan kesehatan digital, menjaga keamanan akun, memproses pemesanan langganan, dan menyediakan fitur aplikasi berbasis AI. Kami tidak menjual data pribadi kepada pihak ketiga.</p>
                </div>

                <div class="pt-3 space-y-1.5">
                    <h4 class="font-bold text-slate-900 text-sm">Penyedia Layanan Terkait</h4>
                    <p class="text-xs">Google Play Billing, Sign in with Google, Google Maps, Cloudflare Turnstile, dan Google Gemini API.</p>
                </div>

                <!-- Akhir Dokumen -->
                <div class="pt-4 pb-2 text-center bg-emerald-50/70 p-4 rounded-xl border border-emerald-200">
                    <div class="w-8 h-8 rounded-full bg-emerald-200 text-emerald-800 flex items-center justify-center mx-auto mb-2 font-bold">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="font-bold text-slate-900 text-xs sm:text-sm">Anda telah mencapai akhir dokumen Kebijakan Privasi.</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Silakan klik tombol di bawah untuk menyetujui.</p>
                </div>
            </div>

            <!-- Footer Agreement Button -->
            <div class="p-4 sm:p-5 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span id="scrollStatusBadge" class="text-xs text-amber-800 bg-amber-100/80 px-3 py-1.5 rounded-lg border border-amber-200 flex items-center gap-1.5">
                    <i class="fas fa-arrow-down animate-bounce"></i>
                    <span>Scroll hingga akhir untuk menyetujui</span>
                </span>

                <button type="button" id="btnAgreePrivacy" disabled
                        class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-xs font-bold transition-all opacity-40 cursor-not-allowed bg-slate-400 text-white flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Saya Telah Membaca dan Menyetujui</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Script for Privacy Policy Scroll Validation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const privacyConsent = document.getElementById('privacyConsent');
            const btnOpenPrivacyModal = document.getElementById('btnOpenPrivacyModal');
            const privacyModal = document.getElementById('privacyModal');
            const btnClosePrivacyModal = document.getElementById('btnClosePrivacyModal');
            const privacyScrollBox = document.getElementById('privacyScrollBox');
            const btnAgreePrivacy = document.getElementById('btnAgreePrivacy');
            const scrollStatusBadge = document.getElementById('scrollStatusBadge');
            const scrollPercentage = document.getElementById('scrollPercentage');
            const privacyWarningText = document.getElementById('privacyWarningText');
            const registerForm = document.getElementById('registerForm');

            let hasScrolledToBottom = false;

            function openModal() {
                privacyModal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeModal() {
                privacyModal.classList.add('hidden');
                document.body.style.overflow = '';
            }

            btnOpenPrivacyModal.addEventListener('click', function(e) {
                e.preventDefault();
                openModal();
            });

            btnClosePrivacyModal.addEventListener('click', closeModal);

            privacyConsent.addEventListener('click', function(e) {
                if (!hasScrolledToBottom) {
                    e.preventDefault();
                    privacyConsent.checked = false;
                    privacyWarningText.classList.remove('hidden');
                    openModal();
                }
            });

            privacyScrollBox.addEventListener('scroll', function() {
                const scrollTop = privacyScrollBox.scrollTop;
                const scrollHeight = privacyScrollBox.scrollHeight;
                const clientHeight = privacyScrollBox.clientHeight;

                const maxScroll = scrollHeight - clientHeight;
                const pct = maxScroll > 0 ? Math.min(100, Math.round((scrollTop / maxScroll) * 100)) : 100;
                scrollPercentage.textContent = pct + '%';

                if (maxScroll - scrollTop <= 25) {
                    if (!hasScrolledToBottom) {
                        hasScrolledToBottom = true;
                        btnAgreePrivacy.removeAttribute('disabled');
                        btnAgreePrivacy.classList.remove('opacity-40', 'cursor-not-allowed', 'bg-slate-400');
                        btnAgreePrivacy.classList.add('bg-[#308a34]', 'hover:bg-[#1B5E20]', 'text-white', 'cursor-pointer', 'shadow-md', 'shadow-emerald-600/30');

                        scrollStatusBadge.className = 'text-xs text-emerald-800 bg-emerald-100 px-3 py-1.5 rounded-lg border border-emerald-300 flex items-center gap-1.5 font-semibold';
                        scrollStatusBadge.innerHTML = '<i class="fas fa-check-circle text-emerald-600"></i> Dokumen selesai dibaca';
                    }
                }
            });

            btnAgreePrivacy.addEventListener('click', function() {
                if (hasScrolledToBottom) {
                    privacyConsent.checked = true;
                    privacyWarningText.classList.add('hidden');
                    closeModal();
                }
            });

            registerForm.addEventListener('submit', function(e) {
                if (!privacyConsent.checked) {
                    e.preventDefault();
                    privacyWarningText.classList.remove('hidden');
                    openModal();
                }
            });
        });
    </script>
</body>
</html>
