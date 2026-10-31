<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Masuk | Aku Sehat - Sistem Informasi Kesehatan</title>

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
        <!-- Left Side: Brand Visual & Feature Highlights (Visible on lg+) -->
        <div class="hidden lg:flex lg:w-1/2 xl:w-7/12 relative overflow-hidden bg-gradient-to-br from-emerald-950 via-emerald-900 to-slate-950 p-12 lg:p-16 flex-col justify-between text-white">
            <!-- Background Subtle Glow & Grid Pattern -->
            <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:24px_24px]"></div>
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Brand Header -->
            <div class="relative z-10">
                <h1 class="text-2xl font-bold tracking-tight text-white">
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('src/aku_sehat_icon.png') }}" class="w-10 h-10 object-contain" alt="Aku Sehat Icon">
                        Aku Sehat
                    </div>
                </h1>
                <p class="text-xs text-emerald-200/80 mt-1">Sistem Informasi Kesehatan Terpadu</p>
            </div>

            <!-- Central Hero & Value Propositions -->
            <div class="relative z-10 my-auto py-12 max-w-xl">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-400/20 text-emerald-300 text-xs font-medium mb-6 backdrop-blur-md">
                    <i class="fas fa-shield-heart text-emerald-400"></i>
                    <span>Solusi Pemantauan Kesehatan Digital Masa Kini</span>
                </div>
                
                <h2 class="text-3xl xl:text-4xl font-extrabold text-white leading-tight mb-4">
                    Pantau & Kelola Kesehatan Generasi Bangsa Lebih Cerdas.
                </h2>
                <p class="text-base text-emerald-100/80 mb-8 leading-relaxed">
                    Satu portal terintegrasi untuk pencatatan rekam medis, monitoring kadar hemoglobin (HB), 
                    serta analitik kesehatan seluruh civitas instansi dan institusi.
                </p>

                <!-- Feature Highlights -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition duration-200">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-300 mb-3">
                            <i class="fas fa-notes-medical text-lg"></i>
                        </div>
                        <h3 class="font-semibold text-white text-sm">Rekam Medis Digital</h3>
                        <p class="text-xs text-emerald-200/70 mt-1">Pencatatan data kesehatan fisik, berat/tinggi badan, dan riwayat klinis berkala.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-white/5 border border-white/10 backdrop-blur-md hover:bg-white/10 transition duration-200">
                        <div class="w-10 h-10 rounded-lg bg-teal-500/20 border border-teal-400/30 flex items-center justify-center text-teal-300 mb-3">
                            <i class="fas fa-droplet text-lg"></i>
                        </div>
                        <h3 class="font-semibold text-white text-sm">Monitoring Hemoglobin</h3>
                        <p class="text-xs text-teal-200/70 mt-1">Skrining anemia dan pemantauan level HB secara terstruktur dan terukur.</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Company Signature -->
            <div class="relative z-10 pt-6 border-t border-emerald-800/60 flex items-center justify-between text-xs text-emerald-200/70">
                <p>Dikembangkan oleh <span class="text-white font-semibold ml-0.5">Neura Cakrawira Solusi</span></p>
            </div>
        </div>

        <!-- Right Side: Clean Modern Login Card -->
        <div class="w-full lg:w-1/2 xl:w-5/12 flex flex-col justify-center items-center px-6 py-12 sm:px-12 lg:px-16 bg-slate-50 relative min-h-screen lg:min-h-0">
            <!-- Subtle backdrop gradient on mobile -->
            <div class="absolute inset-0 bg-gradient-to-b from-emerald-50/50 via-slate-50 to-slate-100 lg:hidden pointer-events-none"></div>

            <div class="w-full max-w-md relative z-10">
                <!-- Header Logo & Title -->
                <div class="flex flex-col items-center mb-8 text-center">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
                    <p class="text-sm text-slate-500 mt-1.5">Masukkan kredensial akun Anda untuk mengakses sistem</p>
                </div>

                <!-- Card Form -->
                <div class="bg-white rounded-2xl shadow-[0_4px_25px_-5px_rgba(0,0,0,0.06),0_10px_20px_-5px_rgba(0,0,0,0.03)] border border-slate-200/80 p-7 sm:p-9">
                    <!-- Error Notification -->
                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm animate-fade-in">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-circle-exclamation text-rose-500 mt-0.5 text-base flex-shrink-0"></i>
                                <div class="flex-1 space-y-1">
                                    <p class="font-semibold">Gagal masuk ke sistem:</p>
                                    <ul class="list-disc list-inside text-xs space-y-0.5 text-rose-700">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm animate-fade-in flex items-start gap-3">
                            <i class="fas fa-circle-xmark text-rose-500 mt-0.5 text-base flex-shrink-0"></i>
                            <div class="text-xs leading-relaxed font-semibold">
                                {{ session('error') }}
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm animate-fade-in flex items-start gap-3">
                            <i class="fas fa-circle-check text-emerald-500 mt-0.5 text-base flex-shrink-0"></i>
                            <div class="text-xs leading-relaxed font-semibold">
                                {{ session('success') }}
                            </div>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm animate-fade-in flex items-start gap-3">
                            <i class="fas fa-circle-info text-amber-600 mt-0.5 text-base flex-shrink-0"></i>
                            <div class="text-xs leading-relaxed font-medium">
                                {{ session('warning') }}
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- Username Field -->
                        <div>
                            <label for="username" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                                Username
                            </label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-user-circle text-base"></i>
                                </div>
                                <input type="text" 
                                       id="username" 
                                       name="username" 
                                       required 
                                       autocomplete="username"
                                       value="{{ old('username') }}"
                                       placeholder="Contoh: admin atau username Anda"
                                       class="block w-full pl-10 pr-4 py-3 text-sm text-slate-800 bg-slate-50/60 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder:text-slate-400">
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                                    Password
                                </label>
                            </div>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-lock text-base"></i>
                                </div>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="block w-full pl-10 pr-11 py-3 text-sm text-slate-800 bg-slate-50/60 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-500/10 transition-all placeholder:text-slate-400">
                                <button type="button" 
                                        id="togglePassword" 
                                        aria-label="Tampilkan password"
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-emerald-700 transition-colors">
                                    <i class="far fa-eye text-base"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember & Privacy Policy Agreement -->
                        <div class="space-y-3 pt-1">
                            <!-- <div class="flex items-center justify-between">
                                <label class="flex items-center cursor-pointer select-none">
                                    <input type="checkbox" 
                                           id="remember" 
                                           name="remember" 
                                           {{ old('remember') ? 'checked' : '' }}
                                           class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0">
                                    <span class="ml-2 text-xs font-medium text-slate-600">Ingat saya</span>
                                </label>
                            </div> -->

                            <div class="p-3 bg-emerald-50/60 border border-emerald-200/80 rounded-xl">
                                <div class="flex items-start gap-2.5">
                                    <input type="checkbox" 
                                           id="privacyConsent" 
                                           name="kebijakan_privasi" 
                                           value="1"
                                           class="w-4 h-4 mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 cursor-pointer">
                                    <label for="privacyConsent" class="text-xs text-slate-700 leading-snug cursor-pointer select-none">
                                        Saya telah membaca dan menyetujui 
                                        <button type="button" id="btnOpenPrivacyModal" class="text-emerald-800 font-bold underline hover:text-emerald-900 inline-block">
                                            Kebijakan Privasi
                                        </button>
                                        <span class="text-rose-500 font-bold">*</span>
                                    </label>
                                </div>
                                <p id="privacyWarningText" class="text-[11px] text-amber-700 mt-1 pl-6 hidden flex items-center gap-1">
                                    <i class="fas fa-circle-info"></i> Wajib scroll hingga bagian paling bawah untuk menyetujui.
                                </p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" 
                                    id="btnSubmitLogin"
                                    class="btn-press w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 hover:from-emerald-700 hover:via-emerald-800 hover:to-teal-800 shadow-lg shadow-emerald-700/25 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 transition-all duration-200">
                                <span>Masuk ke Dashboard</span>
                                <i class="fas fa-arrow-right text-xs"></i>
                            </button>
                        </div>

                        <!-- Divider -->
                        <div class="relative flex py-1 items-center">
                            <div class="flex-grow border-t border-slate-200"></div>
                            <span class="flex-shrink mx-4 text-xs font-medium text-slate-400 uppercase tracking-wider">atau masuk dengan</span>
                            <div class="flex-grow border-t border-slate-200"></div>
                        </div>

                        <!-- Google Sign-In Button -->
                        <div>
                            <a href="{{ route('auth.google') }}" 
                               class="w-full flex items-center justify-center gap-3 py-3 px-4 rounded-xl text-sm font-semibold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:border-slate-400 transition-all shadow-sm">
                                <svg class="w-5 h-5" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                <span>Lanjutkan dengan Google</span>
                            </a>
                        </div>

                        <!-- Institutional Account Note (Replacing self-register) -->
                        <div class="pt-4 text-center text-xs text-slate-500 border-t border-slate-100 flex items-center justify-center gap-1.5">
                            <i class="fas fa-building-columns text-emerald-700"></i>
                            <span>Akun anggota dibuat &amp; didaftarkan langsung oleh <strong>Admin Institusi / Instansi</strong> terkait.</span>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL KEBIJAKAN PRIVASI (WAJIB SCROLL SAMPAI BAWAH) -->
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
                <button type="button" id="btnClosePrivacyModal" class="text-slate-400 hover:text-slate-600 p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Notice Bar -->
            <div id="scrollBanner" class="bg-amber-50 border-b border-amber-200 px-5 py-2.5 flex items-center justify-between text-xs text-amber-800">
                <span class="flex items-center gap-2">
                    <i class="fas fa-arrow-down-long animate-bounce text-amber-600"></i>
                    <span>Harap baca dan <strong>scroll hingga ke bagian paling bawah</strong> untuk menyetujui.</span>
                </span>
                <span id="scrollPercentage" class="font-bold font-mono">0%</span>
            </div>

            <!-- Scrollable Content Box -->
            <div id="privacyScrollBox" class="p-5 sm:p-6 overflow-y-auto space-y-5 text-xs sm:text-sm text-slate-700 leading-relaxed max-h-[50vh] divide-y divide-slate-100">
                <div class="space-y-2">
                    <p>
                        Kebijakan Privasi ini menjelaskan bagaimana <strong>Neura Cakrawira Asia</strong> (“kami”) mengumpulkan, menggunakan, dan membagikan data pribadi saat Anda mengunjungi <a href="https://akusehat.neuracakrawira.asia/" target="_blank" class="text-emerald-800 underline font-semibold">akusehat.neuracakrawira.asia</a> atau menggunakan aplikasi <strong>Aku Sehat</strong> (bersama-sama disebut “Layanan”), serta pilihan dan hak yang Anda miliki.
                    </p>
                </div>

                <div class="pt-4 space-y-2">
                    <h4 class="font-bold text-slate-900 text-sm">Siapa kami</h4>
                    <p>
                        Layanan ini dikelola oleh <strong>Neura Cakrawira Asia</strong>, seorang individu. Kami berdomisili di Indonesia. Kami bertanggung jawab atas data pribadi Anda. Undang-undang pelindungan data menyebut peran ini sebagai “pengendali data pribadi”.
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-slate-600">
                        <li><strong>Email:</strong> neuracakrawira@yahoo.com</li>
                        <li><strong>Alamat surat:</strong> neuracakrawira@yahoo.com</li>
                        <li><strong>Telepon:</strong> +62 895-3831-07479</li>
                    </ul>
                </div>

                <div class="pt-4 space-y-2">
                    <h4 class="font-bold text-slate-900 text-sm">Informasi yang kami kumpulkan</h4>
                    <p class="font-semibold text-slate-800">Informasi yang Anda berikan kepada kami:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-600">
                        <li><strong>Saat Anda menghubungi kami:</strong> nama, alamat email, isi pesan Anda</li>
                        <li><strong>Saat Anda membuat akun:</strong> nama, alamat email, nama pengguna, kata sandi, nomor telepon, tanggal lahir atau usia</li>
                        <li><strong>Saat Anda melakukan pemesanan:</strong> nama, alamat email, detail pembayaran, detail dan riwayat pesanan</li>
                    </ul>
                    <p class="font-semibold text-slate-800 pt-2">Informasi yang dikumpulkan secara otomatis:</p>
                    <p><strong>Log server</strong>, disimpan untuk menjalankan Layanan dan menjaga keamanannya: alamat IP, jenis perangkat dan browser, tanggal dan waktu akses</p>
                    <p class="font-semibold text-slate-800 pt-2">Informasi yang dapat diakses aplikasi dengan izin Anda:</p>
                    <p>Aplikasi hanya meminta izin berikut saat suatu fitur membutuhkannya: Kamera, Foto dan media, Notifikasi, Perkiraan lokasi, Lokasi presisi.</p>
                    <p class="bg-emerald-50 p-2.5 rounded-lg border border-emerald-200 text-emerald-900">
                        <strong>Kegunaannya bagi kami:</strong> Lokasi untuk fitur tracking run. Kamera, Foto, dan Media untuk mengunggah dengan yang berkaitan dengan media, seperti foto profile, dll.
                    </p>
                    <p class="font-semibold text-slate-800 pt-2">Informasi dari sumber lain:</p>
                    <p>Jika Anda masuk dengan akun dari penyedia lain (Sign in with Google), penyedia tersebut mengirimkan detail profil dasar kepada kami, seperti nama dan alamat email Anda. Penyedia pembayaran kami memberi tahu kami apakah suatu pembayaran berhasil. Kami tidak menerima atau menyimpan nomor kartu lengkap Anda.</p>
                </div>

                <div class="pt-4 space-y-2">
                    <h4 class="font-bold text-slate-900 text-sm">Cara kami menggunakan informasi Anda</h4>
                    <ul class="list-disc list-inside space-y-1 text-slate-600">
                        <li>Menjawab pertanyaan dan permintaan Anda (Dasar hukum: kepentingan sah kami).</li>
                        <li>Membuat dan mengelola akun Anda (Dasar hukum: pelaksanaan kontrak kami dengan Anda).</li>
                        <li>Memproses pesanan, pembayaran, pengiriman, dan pengembalian dana (Dasar hukum: pelaksanaan kontrak kami).</li>
                        <li>Menyimpan catatan yang diwajibkan oleh undang-undang perpajakan dan akuntansi (Dasar hukum: kewajiban hukum).</li>
                        <li>Menjaga keamanan Layanan, mencegah penyalahgunaan, dan memperbaiki kesalahan (Dasar hukum: kepentingan sah).</li>
                        <li>Menyediakan fitur aplikasi yang memerlukan izin perangkat (Dasar hukum: persetujuan Anda).</li>
                        <li>Menyediakan fitur berbasis AI (Dasar hukum: pelaksanaan kontrak kami dengan Anda).</li>
                    </ul>
                    <p>Kami tidak menjual data pribadi Anda kepada pihak mana pun.</p>
                </div>

                <div class="pt-4 space-y-2">
                    <h4 class="font-bold text-slate-900 text-sm">Cookie dan teknologi serupa</h4>
                    <p>Cookie adalah berkas kecil yang disimpan di perangkat Anda. Digunakan untuk esensial agar layanan berfungsi, misalnya menjaga Anda tetap masuk.</p>
                </div>

                <div class="pt-4 space-y-2">
                    <h4 class="font-bold text-slate-900 text-sm">Kepada siapa kami membagikan informasi</h4>
                    <ul class="list-disc list-inside space-y-1 text-slate-600">
                        <li><strong>Pemrosesan pembayaran:</strong> Google Play Billing (Google LLC)</li>
                        <li><strong>Masuk:</strong> Sign in with Google (Google LLC)</li>
                        <li><strong>Konten tersemat & peta:</strong> Google Maps (Google LLC)</li>
                        <li><strong>Keamanan:</strong> Cloudflare Turnstile (Cloudflare, Inc.)</li>
                        <li><strong>Fitur AI:</strong> Google Gemini API (Google LLC)</li>
                    </ul>
                </div>

                <div class="pt-4 space-y-2">
                    <h4 class="font-bold text-slate-900 text-sm">Hak Anda & Perlindungan Data</h4>
                    <p>Anda dapat menanyakan kepada kami kapan saja data pribadi apa yang kami simpan tentang Anda, dan meminta kami memperbaiki atau menghapusnya. Untuk itu, tulis ke <a href="mailto:neuracakrawira@yahoo.com" class="text-emerald-800 font-bold underline">neuracakrawira@yahoo.com</a>.</p>
                    <p>Kami mematuhi GDPR Eropa/UK, PIPEDA Kanada, LGPD Brasil, dan Australian Privacy Principles.</p>
                </div>

                <div class="pt-4 space-y-2">
                    <h4 class="font-bold text-slate-900 text-sm">Anak-anak & Penghapusan Akun</h4>
                    <p>Layanan ini tidak ditujukan bagi anak di bawah usia 13 tahun. Untuk menghapus akun Anda beserta informasi yang terkait, tulis ke <strong>neuracakrawira@yahoo.com</strong>.</p>
                </div>

                <!-- Akhir Dokumen -->
                <div class="pt-4 pb-2 text-center bg-emerald-50/70 p-4 rounded-xl border border-emerald-200">
                    <div class="w-8 h-8 rounded-full bg-emerald-200 text-emerald-800 flex items-center justify-center mx-auto mb-2 font-bold">
                        <i class="fas fa-check"></i>
                    </div>
                    <p class="font-bold text-slate-900 text-xs sm:text-sm">Anda telah mencapai akhir dokumen Kebijakan Privasi.</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Silakan klik tombol di bawah untuk menyetujui dan melanjutkan proses login.</p>
                </div>
            </div>

            <!-- Modal Footer with Agreement Button -->
            <div class="p-4 sm:p-5 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <span id="scrollStatusBadge" class="text-xs text-amber-800 bg-amber-100/80 px-3 py-1.5 rounded-lg border border-amber-200 flex items-center gap-1.5">
                    <i class="fas fa-arrow-down animate-bounce"></i>
                    <span>Scroll hingga akhir untuk mengaktifkan tombol</span>
                </span>

                <button type="button" 
                        id="btnAgreePrivacy" 
                        disabled
                        class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 opacity-40 cursor-not-allowed bg-slate-400 text-white flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Saya Telah Membaca dan Menyetujui</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Password toggle & Privacy Policy Scroll Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password toggle
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');

            if (togglePassword && password) {
                togglePassword.addEventListener('click', function() {
                    const isPassword = password.getAttribute('type') === 'password';
                    password.setAttribute('type', isPassword ? 'text' : 'password');
                    
                    const icon = this.querySelector('i');
                    if (isPassword) {
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            }

            // Privacy Policy Modal & Scroll to Bottom Logic
            const privacyConsent = document.getElementById('privacyConsent');
            const btnOpenPrivacyModal = document.getElementById('btnOpenPrivacyModal');
            const privacyModal = document.getElementById('privacyModal');
            const btnClosePrivacyModal = document.getElementById('btnClosePrivacyModal');
            const privacyScrollBox = document.getElementById('privacyScrollBox');
            const btnAgreePrivacy = document.getElementById('btnAgreePrivacy');
            const scrollStatusBadge = document.getElementById('scrollStatusBadge');
            const scrollPercentage = document.getElementById('scrollPercentage');
            const privacyWarningText = document.getElementById('privacyWarningText');
            const loginForm = document.querySelector('form');

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

            btnClosePrivacyModal.addEventListener('click', function() {
                closeModal();
            });

            // If user clicks checkbox directly before scrolling to bottom
            privacyConsent.addEventListener('click', function(e) {
                if (!hasScrolledToBottom) {
                    e.preventDefault();
                    privacyConsent.checked = false;
                    privacyWarningText.classList.remove('hidden');
                    openModal();
                }
            });

            // Scroll detector
            privacyScrollBox.addEventListener('scroll', function() {
                const scrollTop = privacyScrollBox.scrollTop;
                const scrollHeight = privacyScrollBox.scrollHeight;
                const clientHeight = privacyScrollBox.clientHeight;

                const maxScroll = scrollHeight - clientHeight;
                const pct = maxScroll > 0 ? Math.min(100, Math.round((scrollTop / maxScroll) * 100)) : 100;
                scrollPercentage.textContent = pct + '%';

                // Toleransi 20px
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

            // Agree button click
            btnAgreePrivacy.addEventListener('click', function() {
                if (hasScrolledToBottom) {
                    privacyConsent.checked = true;
                    privacyWarningText.classList.add('hidden');
                    closeModal();
                }
            });

            // Prevent submit if not agreed
            loginForm.addEventListener('submit', function(e) {
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