<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aku Sehat — Ekosistem Platform Manajemen Kesehatan Sekolah & Institusi Terpadu</title>
    <meta name="description" content="Platform kesehatan digital terpadu untuk UKS, sekolah, dan institusi modern. Menghubungkan Web Dashboard Manajemen dengan Aplikasi Mobile Siswa untuk skrining antropometri, IMT otomatis, pemantauan kadar Hb anemia, siklus menstruasi, dan pelacak vitalitas harian.">
    <meta name="keywords" content="Aku Sehat, UKS digital, kesehatan sekolah, rekam medis siswa, antropometri, skrining IMT, cek Hb anemia, siklus haid, Neura Cakrawira Asia">
    
    <!-- Open Graph / Meta Sosial -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Aku Sehat — Ekosistem Manajemen Kesehatan Sekolah Terpadu">
    <meta property="og:description" content="Integrasi cerdas Web Dashboard Sekolah & Aplikasi Mobile Siswa untuk monitoring kesehatan, IMT, dan deteksi anemia secara akurat.">
    <meta property="og:image" content="{{ asset('images/landing/feature_graphic.png') }}">

    <!-- Favicon & Brand Icons -->
    <link rel="icon" href="{{ asset('src/aku_sehat_icon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('images/landing/app_icon_512.png') }}">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Stylesheets via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Lightweight Animations & Custom Accents (Zero-lag, GPU-accelerated) */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        @keyframes cursorClick {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(0.85); opacity: 0.9; }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-float {
            animation: floatSlow 6s ease-in-out infinite;
        }
        .animate-pulse-glow {
            animation: pulseGlow 4s ease-in-out infinite;
        }
        .animate-click {
            animation: cursorClick 0.6s ease-in-out;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(226, 232, 240, 0.9);
        }
        .mockup-phone-shadow {
            box-shadow: 0 25px 60px -15px rgba(16, 185, 129, 0.25), 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .mockup-laptop-shadow {
            box-shadow: 0 30px 70px -20px rgba(15, 23, 42, 0.25), 0 12px 30px -10px rgba(16, 185, 129, 0.15);
        }
        /* Custom slim scrollbar for embedded web frame */
        .web-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .web-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(16, 185, 129, 0.3);
            border-radius: 9999px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-emerald-500 selection:text-white flex flex-col min-h-screen">

    <!-- ============================================================== -->
    <!-- TOP NAVIGATION BAR (Sticky with Blur & Mobile Drawer)          -->
    <!-- ============================================================== -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition-all duration-300" id="main-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('src/aku_sehat_icon.png') }}" alt="Aku Sehat" class="w-10 h-10 object-contain group-hover:scale-105 transition-transform duration-300">
                <div class="flex flex-col">
                    <span class="font-black text-slate-900 text-lg leading-tight tracking-tight flex items-center gap-1.5">
                        Aku Sehat
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">V1</span>
                    </span>
                    <span class="text-[10px] text-emerald-700 font-semibold tracking-wide">Neura Cakrawira Asia</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden lg:flex items-center gap-6 xl:gap-8 text-sm font-semibold text-slate-600">
                <a href="#tentang" class="hover:text-emerald-700 transition-colors">Tentang</a>
                <a href="#fitur" class="hover:text-emerald-700 transition-colors">Fitur Unggulan</a>
                <a href="#demo-section" class="inline-flex items-center gap-1.5 text-emerald-700 hover:text-emerald-800 transition-colors font-bold">
                    <span>Demo & Walkthrough</span>
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">LIVE</span>
                </a>
                <a href="#ekosistem" class="hover:text-emerald-700 transition-colors">Peran & Akses</a>
                <a href="#paket" class="hover:text-emerald-700 transition-colors">Paket & Lisensi</a>
                <a href="#unduh" class="hover:text-emerald-700 transition-colors">Unduh App</a>
            </nav>

            <!-- Action Buttons (Login / Dashboard / Demo) -->
            <div class="hidden lg:flex items-center gap-3">
                <button type="button" onclick="openDemoModal('admin')" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-emerald-800 bg-slate-100 hover:bg-emerald-50 border border-slate-200 shadow-2xs transition-all">
                    <i class="fas fa-play text-emerald-700 text-[10px]"></i>
                    <span>Walkthrough Web</span>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm hover:shadow-md transition-all">
                        <i class="fas fa-columns"></i>
                        <span>Buka Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm hover:shadow-md transition-all">
                        <i class="fas fa-arrow-right-to-bracket"></i>
                        <span>Masuk Portal</span>
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger Toggle -->
            <div class="flex items-center gap-2 lg:hidden">
                <button type="button" onclick="toggleMobileMenu()" class="p-2.5 rounded-xl text-slate-700 hover:bg-slate-100 focus:outline-none" aria-label="Toggle Menu">
                    <i class="fas fa-bars text-xl" id="menu-icon"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-200 bg-white/95 backdrop-blur-md px-5 py-4 transition-all">
            <div class="flex flex-col gap-3 text-sm font-semibold text-slate-700">
                <a href="#tentang" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-800">Tentang Aplikasi</a>
                <a href="#fitur" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-800">Fitur Unggulan</a>
                <a href="#demo-section" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 text-emerald-800 font-bold flex items-center justify-between">
                    <span>Demo Interaktif (Web & Mobile)</span>
                    <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-100 text-emerald-800 font-extrabold">LIVE</span>
                </a>
                <a href="#ekosistem" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-800">Peran & Akses Web</a>
                <a href="#paket" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-800">Paket & Lisensi</a>
                <a href="#unduh" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-800">Unduh Aplikasi Mobile</a>
                <div class="pt-2 flex flex-col gap-2">
                    <button type="button" onclick="closeMobileMenu(); openDemoModal('admin');" class="w-full py-2.5 rounded-xl text-xs font-bold text-center text-slate-700 bg-slate-100 border border-slate-200">
                        <i class="fas fa-play text-emerald-800 mr-1.5"></i> Tonton Video Walkthrough Web
                    </button>
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full py-2.5 rounded-xl text-xs font-bold text-center text-white bg-emerald-800">
                            Buka Dashboard Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full py-2.5 rounded-xl text-xs font-bold text-center text-white bg-emerald-800">
                            Masuk ke Web Portal
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- ============================================================== -->
    <!-- HERO SECTION (High-Impact Title, Badges & Dual-Action CTAs)    -->
    <!-- ============================================================== -->
    <section class="relative overflow-hidden pt-12 pb-16 lg:pt-20 lg:pb-24 bg-gradient-to-b from-emerald-50/60 via-slate-50 to-white">
        <div class="absolute top-10 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-emerald-400/15 rounded-full blur-3xl pointer-events-none -z-10 animate-pulse-glow"></div>
        <div class="absolute top-40 right-10 w-[350px] h-[350px] bg-sky-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100/80 text-emerald-800 border border-emerald-200 mb-6 shadow-xs animate-float">
                    <i class="fas fa-heart-pulse text-emerald-800"></i>
                    <span>Ekosistem Digitalisasi UKS & Kesehatan Sekolah Terpadu</span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.15] mb-6">
                    Satu Platform Terintegrasi untuk <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-800 via-teal-700 to-sky-700">Kesehatan Siswa & Sekolah</span> yang Lebih Terukur
                </h1>

                <p class="text-base sm:text-lg text-slate-600 max-w-3xl mx-auto mb-9 leading-relaxed font-normal">
                    <strong>Aku Sehat</strong> menjembatani <strong>Web Dashboard Manajemen UKS</strong> dengan <strong>Aplikasi Mobile Siswa</strong>. Dari skrining antropometri IMT otomatis, pemantauan kadar Hemoglobin (Hb) bebas anemia, konseling kesehatan mental SafeSpace, hingga pelacakan vitalitas harian dalam satu ekosistem yang aman dan terenkripsi.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3.5 mb-12">
                    <a href="#demo-section" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl text-sm font-bold text-white bg-emerald-800 hover:bg-emerald-900 shadow-lg shadow-emerald-700/20 hover:shadow-xl hover:shadow-emerald-700/30 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-desktop text-base"></i>
                        <span>Coba Demo Web & Mobile</span>
                    </a>

                    <button type="button" onclick="openDemoModal('admin')" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-800 bg-white hover:bg-slate-50 border border-slate-300 shadow-sm hover:shadow transition-all transform hover:-translate-y-0.5">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">
                            <i class="fas fa-play ml-0.5"></i>
                        </span>
                        <span>Walkthrough Web Asli</span>
                    </button>

                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-3.5 rounded-2xl text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all">
                            <i class="fas fa-user-check text-emerald-800"></i>
                            <span>Dashboard Saya</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-3.5 rounded-2xl text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all">
                            <i class="fas fa-arrow-right-to-bracket text-emerald-800"></i>
                            <span>Masuk Portal</span>
                        </a>
                    @endauth
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 max-w-4xl mx-auto pt-6 border-t border-slate-200/80">
                    <div class="p-3.5 rounded-2xl bg-white/70 border border-slate-200/70 text-center shadow-xs">
                        <div class="text-emerald-800 text-lg mb-1"><i class="fas fa-shield-halved"></i></div>
                        <div class="font-extrabold text-slate-800 text-sm">Enkripsi Medis</div>
                        <div class="text-[11px] text-slate-500">AES-256 Per-Tenant</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/70 border border-slate-200/70 text-center shadow-xs">
                        <div class="text-teal-700 text-lg mb-1"><i class="fas fa-calculator"></i></div>
                        <div class="font-extrabold text-slate-800 text-sm">IMT Otomatis</div>
                        <div class="text-[11px] text-slate-500">Standar Baku Kemenkes</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/70 border border-slate-200/70 text-center shadow-xs">
                        <div class="text-sky-700 text-lg mb-1"><i class="fas fa-mobile-screen-button"></i></div>
                        <div class="font-extrabold text-slate-800 text-sm">Mobile & Web</div>
                        <div class="text-[11px] text-slate-500">Sinkronisasi Realtime</div>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-white/70 border border-slate-200/70 text-center shadow-xs">
                        <div class="text-indigo-700 text-lg mb-1"><i class="fas fa-palette"></i></div>
                        <div class="font-extrabold text-slate-800 text-sm">Dual UI Theme</div>
                        <div class="text-[11px] text-slate-500">Emerald & Atlas Neo</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 1: APA ITU AKU SEHAT?                                  -->
    <!-- ============================================================== -->
    <section id="tentang" class="py-16 sm:py-20 bg-white border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                
                <div class="lg:col-span-6 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        <i class="fas fa-circle-question"></i>
                        <span>Transformasi Kesehatan Sekolah</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-snug">
                        Bukan Sekadar Catatan UKS Biasa, Ini Adalah <span class="text-emerald-800">Sistem Deteksi Dini</span> untuk Generasi Sehat
                    </h2>

                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                        Di banyak sekolah, pencatatan kesehatan siswa masih mengandalkan buku catatan manual yang rentan hilang, data anemia siswi tidak terdeteksi, dan pihak sekolah kesulitan menghasilkan laporan resmi untuk Dinas Pendidikan maupun Puskesmas.
                    </p>

                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                        <strong>Aku Sehat</strong> hadir merombak paradigma tersebut dengan sistem terpusat berbasis instansi. Setiap sekolah mendapatkan <strong>lingkungan privat (multi-tenancy)</strong> lengkap dengan modul skrining klinis, pemantauan berkala, dan akses mobile ramah siswa.
                    </p>

                    <div class="space-y-3.5 pt-2">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Digitalisasi Skrining Antropometri & Klinis</h4>
                                <p class="text-xs text-slate-500">Pencatatan Tinggi Badan, Berat Badan, Mata, Gigi, dan Telinga dengan kalkulasi status gizi otomatis.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Program Pencegahan Anemia & Monitoring Hb</h4>
                                <p class="text-xs text-slate-500">Deteksi dini kadar Hemoglobin siswi putri untuk mencegah penurunan konsentrasi dan risiko stunting di masa depan.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">SafeSpace Konseling & Privasi Terjaga</h4>
                                <p class="text-xs text-slate-500">Layanan konseling mental siswa dan pencatatan siklus haid dilakukan secara aman di smartphone masing-masing siswa.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-slate-900 to-slate-950 text-white shadow-2xl border border-slate-800 relative overflow-hidden">
                        <div class="absolute -top-20 -right-20 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl pointer-events-none"></div>

                        <div class="flex items-center justify-between pb-6 border-b border-slate-800 mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-base border border-emerald-500/30">
                                    <i class="fas fa-hospital-user"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-white text-base">Arsitektur Dua Arah</h3>
                                    <p class="text-xs text-slate-400">Sinkronisasi Web Portal & Android Mobile</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                REALTIME SYNC
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl bg-slate-800/70 border border-slate-700/80">
                                <div class="flex items-center gap-2 mb-2 text-emerald-400 font-bold text-xs uppercase tracking-wider">
                                    <i class="fas fa-desktop"></i>
                                    <span>Web Dashboard</span>
                                </div>
                                <h4 class="text-sm font-extrabold text-white mb-2">Untuk Admin Sekolah, Konselor & UKS</h4>
                                <ul class="text-xs text-slate-300 space-y-1.5 list-disc list-inside">
                                    <li>Rekap Divisi/Kelas Terpadu</li>
                                    <li>Skrining Klinis Massal</li>
                                    <li>Konseling SafeSpace & Burnout</li>
                                    <li>Penerbitan Laporan PDF Resmi</li>
                                </ul>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-800/70 border border-slate-700/80">
                                <div class="flex items-center gap-2 mb-2 text-sky-400 font-bold text-xs uppercase tracking-wider">
                                    <i class="fas fa-mobile-screen"></i>
                                    <span>Mobile Android</span>
                                </div>
                                <h4 class="text-sm font-extrabold text-white mb-2">Untuk Siswa & Anggota</h4>
                                <ul class="text-xs text-slate-300 space-y-1.5 list-disc list-inside">
                                    <li>Pencatatan Mandiri IMT & Hb</li>
                                    <li>Log Siklus Haid & Keluhan</li>
                                    <li>Run Tracker & Vitalitas Tubuh</li>
                                    <li>Pinjam Perlengkapan UKS</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-6 p-3.5 rounded-2xl bg-emerald-950/60 border border-emerald-800/60 flex items-center gap-3 text-xs text-emerald-200">
                            <i class="fas fa-shield-cat text-lg text-emerald-400"></i>
                            <span>Seluruh data medis sensitif dienkripsi deterministik dengan standar keamanan tinggi untuk mematuhi regulasi privasi data.</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 2: INTERACTIVE DEMO & VIDEO WALKTHROUGH (CORE FEATURE) -->
    <!-- 100% Desain Asli Web Project (Admin, Konselor & Monitor)       -->
    <!-- ============================================================== -->
    <section id="demo-section" class="py-16 sm:py-24 bg-slate-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#34d399_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>
        <div class="absolute top-1/4 left-10 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-sky-600/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-10">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-3">
                    <i class="fas fa-display"></i>
                    <span>DESAIN 100% ASLI DARI PROYEK WEBPAGE INI</span>
                </div>
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight mb-4">
                    Eksplorasi Fitur Web & Mobile <span class="text-emerald-400">Berdasarkan Peran</span>
                </h2>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-normal">
                    Lihat persis apa yang bisa dilakukan oleh <strong>Admin Instansi</strong>, <strong>Health Consultant</strong>, dan <strong>Health Monitor</strong> di antarmuka web asli aplikasi Aku Sehat. Tanpa rekayasa desain, tanpa membebani memori ponsel Anda.
                </p>
            </div>

            <!-- Main Demo Mode Switcher (Web Dashboard vs Mobile App) -->
            <div class="flex flex-wrap items-center justify-center gap-3 mb-8">
                <div class="inline-flex p-1 rounded-2xl bg-slate-800/90 border border-slate-700/80 shadow-inner">
                    <button type="button" id="tab-btn-web" onclick="switchDemoTab('web')" class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 bg-emerald-700 text-white shadow">
                        <i class="fas fa-desktop"></i>
                        <span>Web Dashboard (3 Peran Asli)</span>
                    </button>
                    <button type="button" id="tab-btn-mobile" onclick="switchDemoTab('mobile')" class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 text-slate-300 hover:text-white">
                        <i class="fas fa-mobile-screen"></i>
                        <span>Aplikasi Mobile (Siswa)</span>
                    </button>
                </div>

                <button type="button" onclick="openDemoModal('admin')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-teal-700 to-emerald-700 hover:from-teal-800 hover:to-emerald-800 border border-emerald-600/40 shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px]">
                        <i class="fas fa-play"></i>
                    </span>
                    <span>Tonton Video Walkthrough Web</span>
                </button>
            </div>

            <!-- ======================================================== -->
            <!-- TAB 1: WEB DASHBOARD SHOWCASE (100% ASLI PROYEK)          -->
            <!-- ======================================================== -->
            <div id="demo-content-web" class="transition-all duration-300">
                
                <!-- Role Selector Sub-Pills (4 Peran Web Terpadu: Admin Instansi, Admin PMR/UKS, Health Monitor, Health Consultant) -->
                <div class="flex flex-wrap items-center justify-center gap-2 mb-6">
                    <span class="text-xs font-bold text-slate-400 mr-2 flex items-center gap-1.5">
                        <i class="fas fa-user-tag text-emerald-400"></i> Pilih Peran Web:
                    </span>
                    <button type="button" onclick="setRoleWebDemo('admin')" id="role-pill-admin" class="role-pill px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 flex items-center gap-2 bg-emerald-600 text-white shadow-sm border border-emerald-500">
                        <i class="fas fa-school"></i>
                        <span>Admin Instansi (Kepala Sekolah)</span>
                    </button>
                    <button type="button" onclick="setRoleWebDemo('adminpmr')" id="role-pill-adminpmr" class="role-pill px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 flex items-center gap-2 bg-slate-800 text-slate-300 hover:text-white border border-slate-700">
                        <i class="fas fa-hand-holding-medical text-rose-400"></i>
                        <span>Admin PMR (Pengurus UKS)</span>
                    </button>
                    <button type="button" onclick="setRoleWebDemo('monitor')" id="role-pill-monitor" class="role-pill px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 flex items-center gap-2 bg-slate-800 text-slate-300 hover:text-white border border-slate-700">
                        <i class="fas fa-heart-pulse text-sky-400"></i>
                        <span>Health Monitor (Guru Penjas / Medis)</span>
                    </button>
                    <button type="button" onclick="setRoleWebDemo('consultant')" id="role-pill-consultant" class="role-pill px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 flex items-center gap-2 bg-slate-800 text-slate-300 hover:text-white border border-slate-700">
                        <i class="fas fa-user-doctor text-teal-400"></i>
                        <span>Health Consultant (Konselor / BK)</span>
                    </button>
                </div>

                <!-- Web Browser Window Mockup -->
                <div class="rounded-3xl bg-slate-900 border border-slate-700/90 shadow-2xl mockup-laptop-shadow overflow-hidden max-w-6xl mx-auto">
                    
                    <!-- Chrome Browser Top Bar -->
                    <div class="px-4 py-2.5 bg-slate-950 border-b border-slate-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                        </div>
                        <!-- URL Bar -->
                        <div class="flex items-center gap-2 px-4 py-1 rounded-xl bg-slate-900 text-slate-400 text-xs font-mono w-72 sm:w-96 truncate border border-slate-800">
                            <i class="fas fa-lock text-emerald-400 text-[10px]"></i>
                            <span id="web-url-bar" class="truncate">https://akusehat.neuracakrawira.asia/dashboard</span>
                        </div>
                        <div class="text-xs text-slate-400 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span id="web-active-role-badge" class="font-bold text-emerald-400 text-[11px]">Admin Instansi</span>
                        </div>
                    </div>

                    <!-- Inner App Workspace: Actual Sidebar + Content Layout (100% Design Asli) -->
                    <div class="flex flex-col lg:flex-row min-h-[560px] bg-slate-100 text-slate-800 select-none">
                        
                        <!-- ==================================================== -->
                        <!-- SIDEBAR ASLI PROYEK (Emerald Gradient Theme)        -->
                        <!-- ==================================================== -->
                        <aside class="w-full lg:w-64 bg-gradient-to-b from-[#092e20] to-[#041a12] text-slate-300 p-4 border-r border-emerald-950/40 flex flex-col justify-between shrink-0">
                            <div class="space-y-4">
                                <div class="p-3 rounded-2xl bg-white/5 border border-white/10 flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-sm shrink-0 border border-emerald-500/30">
                                        <i class="fas fa-school"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="text-[11px] font-black text-white truncate leading-tight">SMKS TELELESANDI</div>
                                        <div class="text-[10px] text-emerald-400/90 font-medium truncate" id="sidebar-role-label">Admin Instansi</div>
                                    </div>
                                </div>

                                <div id="sidebar-links-container" class="space-y-3 text-xs web-scrollbar overflow-y-auto max-h-[380px] pr-1">
                                    <!-- Rendered dynamically via JS for 100% accuracy with project sidebars -->
                                </div>
                            </div>

                            <div class="pt-3 border-t border-white/10 flex items-center justify-between text-[11px]">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">
                                        AS
                                    </div>
                                    <span class="text-white font-semibold truncate max-w-[100px]" id="sidebar-username">Admin Sekolah</span>
                                </div>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Aktif</span>
                            </div>
                        </aside>

                        <!-- ==================================================== -->
                        <!-- MAIN CONTENT AREA (100% Desain Asli Blade View)     -->
                        <!-- ==================================================== -->
                        <main class="flex-1 p-4 sm:p-6 overflow-y-auto web-scrollbar bg-slate-50 flex flex-col justify-between">
                            
                            <div id="web-page-content" class="space-y-5">
                                <!-- Rendered dynamically based on selected role & sub-page -->
                            </div>

                            <!-- Bottom Walkthrough Navigation Toolbar -->
                            <div class="mt-6 pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3 text-xs bg-white/70 p-3 rounded-2xl border border-slate-200/60 shadow-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-700"><i class="fas fa-layer-group text-emerald-700 mr-1"></i> Halaman yang Sedang Ditinjau:</span>
                                    <span id="current-screen-title" class="font-extrabold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                        Dashboard Utama
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="prevWebScreen()" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition-colors">
                                        <i class="fas fa-chevron-left mr-1"></i> Sebelumnya
                                    </button>
                                    <button type="button" onclick="nextWebScreen()" class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold transition-colors shadow-xs">
                                        Selanjutnya <i class="fas fa-chevron-right ml-1"></i>
                                    </button>
                                </div>
                            </div>

                        </main>

                    </div>
                </div>

            </div>

            <!-- ======================================================== -->
            <!-- TAB 2: MOBILE APP SHOWCASE (Siswa / Member di Android)    -->
            <!-- ======================================================== -->
            <div id="demo-content-mobile" class="hidden transition-all duration-300">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    
                    <div class="lg:col-span-5 flex justify-center">
                        <div class="relative w-[290px] sm:w-[320px] rounded-[48px] p-3.5 bg-slate-800 border-4 border-slate-700 shadow-2xl mockup-phone-shadow overflow-hidden">
                            <div class="absolute top-5 left-1/2 -translate-x-1/2 w-28 h-5 bg-slate-900 rounded-full z-30 flex items-center justify-center">
                                <div class="w-2.5 h-2.5 rounded-full bg-slate-800 mr-2"></div>
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                            </div>

                            <div class="relative w-full aspect-[9/19] rounded-[38px] bg-slate-950 overflow-hidden text-slate-900 flex flex-col justify-between border border-slate-800 select-none">
                                
                                <!-- Screen 1: Dashboard Member & IMT -->
                                <div id="mobile-screen-1" class="mobile-screen-view w-full h-full flex flex-col bg-slate-900 text-white p-4 pt-10">
                                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-3 px-1">
                                        <span>08:45</span>
                                        <div class="flex items-center gap-1.5 text-[10px]">
                                            <i class="fas fa-wifi"></i>
                                            <i class="fas fa-battery-full text-emerald-400"></i>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-10 h-10 rounded-2xl bg-emerald-600/30 text-emerald-400 flex items-center justify-center font-bold text-sm border border-emerald-500/30">
                                                AD
                                            </div>
                                            <div>
                                                <div class="text-[10px] text-slate-400">Halo Siswa,</div>
                                                <div class="text-sm font-bold text-white leading-tight">Aditya Pratama</div>
                                            </div>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                            Kelas XII-RPL
                                        </span>
                                    </div>

                                    <div class="p-3.5 rounded-2xl bg-gradient-to-br from-emerald-900/60 to-slate-800 border border-emerald-700/50 mb-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-[11px] font-semibold text-emerald-300">Indeks Massa Tubuh (IMT)</span>
                                            <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-emerald-400 text-slate-950">NORMAL</span>
                                        </div>
                                        <div class="flex items-baseline gap-2 mb-2">
                                            <span class="text-3xl font-black text-white">20.8</span>
                                            <span class="text-[11px] text-slate-400">kg/m² (Ideal)</span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-[10px] text-slate-300 pt-2 border-t border-emerald-800/40">
                                            <div>Tinggi: <strong class="text-white">168 cm</strong></div>
                                            <div>Berat: <strong class="text-white">58.8 kg</strong></div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-2.5 mb-3">
                                        <div class="p-3 rounded-xl bg-slate-800/80 border border-slate-700/80">
                                            <div class="text-[10px] text-slate-400 mb-1 flex items-center justify-between">
                                                <span>Hemoglobin</span>
                                                <i class="fas fa-droplet text-rose-400"></i>
                                            </div>
                                            <div class="text-base font-extrabold text-white">14.2 <span class="text-[9px] font-normal text-slate-400">g/dL</span></div>
                                            <div class="text-[9px] text-emerald-400 font-semibold mt-0.5">Bebas Anemia</div>
                                        </div>
                                        <div class="p-3 rounded-xl bg-slate-800/80 border border-slate-700/80">
                                            <div class="text-[10px] text-slate-400 mb-1 flex items-center justify-between">
                                                <span>Pita UKS</span>
                                                <i class="fas fa-ribbon text-teal-400"></i>
                                            </div>
                                            <div class="text-sm font-extrabold text-teal-300">Aktif Dipinjam</div>
                                            <div class="text-[9px] text-slate-400 mt-0.5">Jatuh Tempo: Besok</div>
                                        </div>
                                    </div>

                                    <div class="mt-auto p-2.5 rounded-xl bg-emerald-600 text-center font-bold text-xs text-white shadow-sm">
                                        <i class="fas fa-heart-pulse mr-1"></i> Skrining Mandiri
                                    </div>
                                </div>

                                <!-- Screen 2: Run & Vitality Tracker -->
                                <div id="mobile-screen-2" class="mobile-screen-view hidden w-full h-full flex flex-col bg-slate-900 text-white p-4 pt-10">
                                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-3 px-1">
                                        <span>09:15</span>
                                        <div class="flex items-center gap-1.5 text-[10px]">
                                            <i class="fas fa-location-arrow text-emerald-400"></i>
                                            <i class="fas fa-battery-three-quarters text-emerald-400"></i>
                                        </div>
                                    </div>
                                    <div class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-1">GPS Run Tracker</div>
                                    <h4 class="text-lg font-black text-white mb-3">Aktivitas Olahraga</h4>
                                    
                                    <div class="p-4 rounded-2xl bg-slate-800/90 border border-slate-700 mb-3 relative overflow-hidden">
                                        <div class="text-[11px] text-slate-400 mb-1">Jarak Tempuh</div>
                                        <div class="text-3xl font-black text-emerald-400">3.42 <span class="text-sm text-white font-bold">KM</span></div>
                                        <div class="grid grid-cols-2 gap-2 mt-3 pt-2 border-t border-slate-700 text-xs">
                                            <div>Durasi: <strong class="text-white">24m 12s</strong></div>
                                            <div>Kalori: <strong class="text-white">215 kkal</strong></div>
                                        </div>
                                    </div>

                                    <div class="p-3 rounded-xl bg-slate-800/80 border border-slate-700 mb-2 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-sky-500/20 text-sky-400 flex items-center justify-center text-xs">
                                                <i class="fas fa-glass-water"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-white">Hidrasi Air Harian</div>
                                                <div class="text-[10px] text-slate-400">Target: 2.000 ml</div>
                                            </div>
                                        </div>
                                        <span class="text-xs font-bold text-sky-300">1.600 ml</span>
                                    </div>

                                    <div class="mt-auto p-2.5 rounded-xl bg-slate-700 text-center font-bold text-xs text-white">
                                        <i class="fas fa-play mr-1 text-emerald-400"></i> Lanjutkan Sesi Lari
                                    </div>
                                </div>

                                <!-- Screen 3: Hemoglobin & Siklus Menstruasi -->
                                <div id="mobile-screen-3" class="mobile-screen-view hidden w-full h-full flex flex-col bg-slate-900 text-white p-4 pt-10">
                                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-3 px-1">
                                        <span>10:30</span>
                                        <div class="flex items-center gap-1.5 text-[10px]">
                                            <i class="fas fa-shield-halved text-rose-400"></i>
                                            <i class="fas fa-battery-full text-emerald-400"></i>
                                        </div>
                                    </div>
                                    <div class="text-xs font-bold text-rose-400 uppercase tracking-wider mb-1">Kesehatan Siswi & Hb</div>
                                    <h4 class="text-lg font-black text-white mb-3">Monitoring Anemia</h4>

                                    <div class="p-3.5 rounded-2xl bg-rose-950/40 border border-rose-800/50 mb-3">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-[10px] text-rose-300 font-semibold">Skrining Hb Terakhir</span>
                                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-rose-500 text-white">TERCATAT</span>
                                        </div>
                                        <div class="text-2xl font-black text-white mb-1">13.5 <span class="text-xs text-slate-400 font-normal">g/dL</span></div>
                                        <p class="text-[10px] text-slate-300">Status kadar hemoglobin berada dalam batas normal sehat.</p>
                                    </div>

                                    <div class="mt-auto p-2.5 rounded-xl bg-rose-700 text-center font-bold text-xs text-white">
                                        <i class="fas fa-plus mr-1"></i> Catat Gejala Hari Ini
                                    </div>
                                </div>

                                <!-- Screen 4: Atlas Neo Modern Dual Theme -->
                                <div id="mobile-screen-4" class="mobile-screen-view hidden w-full h-full flex flex-col bg-[#070c14] text-white p-4 pt-10">
                                    <div class="flex items-center justify-between text-[11px] text-slate-500 mb-3 px-1">
                                        <span>20:00</span>
                                        <span class="text-[9px] font-mono text-cyan-400">ATLAS_NEO_V1</span>
                                    </div>
                                    <div class="text-[10px] font-mono text-cyan-400 uppercase tracking-widest mb-1">TELEMETRY_ENGINE</div>
                                    <h4 class="text-base font-black text-white mb-3">Atlas Neo Modern UI</h4>

                                    <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-cyan-500/30 mb-3 relative overflow-hidden">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-[10px] text-cyan-300 font-mono">SYSTEM_STATUS</span>
                                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                                        </div>
                                        <div class="text-xl font-mono font-bold text-white mb-1">OPTIMAL_HEALTH</div>
                                        <p class="text-[10px] text-slate-400 font-mono">Active capsule dynamic navigation with light sweep sheen.</p>
                                    </div>

                                    <div class="mt-auto p-2.5 rounded-xl bg-cyan-600/30 border border-cyan-500/50 text-center font-mono text-xs text-cyan-300">
                                        Theme: Atlas Neo Cyber
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-7 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">
                                <i class="fas fa-hand-pointer mr-1"></i> Klik untuk melihat layar siswa:
                            </span>
                            <span class="text-xs text-slate-400" id="mobile-step-indicator">Layar 1 dari 4</span>
                        </div>

                        <!-- Step Card 1 -->
                        <div onclick="setMobileScreen(1)" id="step-mobile-1" class="step-card-mobile p-4 rounded-2xl bg-slate-800/90 border-2 border-emerald-500 cursor-pointer transition-all duration-200 hover:bg-slate-800">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">01</div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-extrabold text-white">Dashboard Siswa & Kalkulasi IMT Instan</h4>
                                        <span class="text-[10px] font-bold text-emerald-400">Aktif</span>
                                    </div>
                                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                                        Siswa melihat tinggi badan, berat badan, serta klasifikasi Indeks Massa Tubuh (Normal, Kurus, Gemuk, Obesitas) berdasarkan standar baku Kemenkes RI.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step Card 2 -->
                        <div onclick="setMobileScreen(2)" id="step-mobile-2" class="step-card-mobile p-4 rounded-2xl bg-slate-800/40 border border-slate-700/80 cursor-pointer transition-all duration-200 hover:bg-slate-800/70">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">02</div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-extrabold text-white">Pelacak Lari GPS & Vitalitas Harian</h4>
                                        <span class="text-[10px] font-bold text-slate-500">Klik Tayangkan</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                        Sensor GPS smartphone menghitung jarak tempuh lari/jalan, durasi, estimasi kalori terbakar, asupan air harian, serta jam tidur berkualitas.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step Card 3 -->
                        <div onclick="setMobileScreen(3)" id="step-mobile-3" class="step-card-mobile p-4 rounded-2xl bg-slate-800/40 border border-slate-700/80 cursor-pointer transition-all duration-200 hover:bg-slate-800/70">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">03</div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-extrabold text-white">Pemantauan Hemoglobin & Siklus Menstruasi</h4>
                                        <span class="text-[10px] font-bold text-slate-500">Klik Tayangkan</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                        Riwayat pemeriksaan Hb untuk deteksi dini anemia siswi, kalender siklus menstruasi otomatis, dan catatan gejala bulanan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step Card 4 -->
                        <div onclick="setMobileScreen(4)" id="step-mobile-4" class="step-card-mobile p-4 rounded-2xl bg-slate-800/40 border border-slate-700/80 cursor-pointer transition-all duration-200 hover:bg-slate-800/70">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">04</div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-extrabold text-white">Dual-Theme: Emerald Classic & Atlas Neo Modern</h4>
                                        <span class="text-[10px] font-bold text-slate-500">Klik Tayangkan</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                        Dua tema adaptif: nuansa hijau klinis bersih atau mode gelap futuristik bergaya telemetry cyber dengan expanding pill navigation.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 3: FITUR UNGGULAN                                      -->
    <!-- ============================================================== -->
    <section id="fitur" class="py-16 sm:py-24 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-3">
                    <i class="fas fa-cubes-stacked"></i>
                    <span>MODULAR & LENGKAP</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                    Dirancang Khusus Sesuai Kebutuhan <span class="text-emerald-800">Sekolah & Siswa</span>
                </h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                    Seluruh modul telah dibangun dan aktif beroperasi dalam sistem Aku Sehat untuk mendukung kelancaran operasional UKS dan gaya hidup sehat siswa.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-weight-scale"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Skrining Antropometri & IMT</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pemeriksaan berkala tinggi badan, berat badan, serta status gizi (Kurus, Normal, Gemuk, Obesitas) dengan algoritma otomatis standar Kemenkes.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-droplet"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Pemantauan Hb & Bebas Anemia</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pencatatan kadar Hemoglobin (Hb) rutin, grafik tren anemia, dan peringatan dini bagi siswi yang memerlukan intervensi gizi atau suplemen darah.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">SafeSpace Konseling & Burnout</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Fitur konseling privat antara siswa dan Health Consultant/Guru BK serta skrining berkala tingkat kejenuhan belajar dan stres akademik.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-person-running"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Run Tracker & Log Vitalitas</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pelacak olahraga lari berbasis GPS di aplikasi mobile, log konsumsi air minum, pemantauan kualitas tidur, dan evaluasi postur ergonomi siswa.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-ribbon"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Peminjaman Pita & Fasilitas UKS</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Sistem sirkulasi peminjaman pita kesehatan UKS terdigitalisasi dengan verifikasi kode cepat, notifikasi batas waktu, dan deteksi denda otomatis.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Keamanan Medis & Multi-Tenant</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Data antar-sekolah terisolasi secara aman. Field kesehatan sensitif dienkripsi tingkat perbankan (AES-256) serta kepatuhan standar Google Play Store.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 4: EKOSISTEM BERBASIS PERAN (RBAC)                     -->
    <!-- ============================================================== -->
    <section id="ekosistem" class="py-16 sm:py-20 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-3">
                    <i class="fas fa-users-gear"></i>
                    <span>KOLABORASI SELURUH PIHAK</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                    Hak Akses Terstruktur untuk Setiap Peran
                </h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                    Pemisahan peran (*Role-Based Access Control*) memastikan privasi tetap terjaga sembari memberikan wewenang yang tepat kepada masing-masing pengguna.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                <!-- Role 1: Admin Instansi -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fas fa-school"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-0.5">Admin Instansi</h4>
                        <p class="text-xs text-emerald-700 font-semibold mb-2">Kepala Sekolah / Yayasan</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Pengambil kebijakan utama. Mengatur struktur kelas/divisi, lisensi kuota siswa, pembayaran billing Midtrans, serta mengunduh rekapitulasi laporan resmi (PDF).
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200 text-center">Akses: Web Portal</span>
                </div>

                <!-- Role 2: Admin PMR / Pengurus UKS -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fas fa-hand-holding-medical"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-0.5">Admin PMR / UKS</h4>
                        <p class="text-xs text-rose-700 font-semibold mb-2">Pengurus Lapangan UKS / PMI</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Mengelola operasional harian ruang UKS: sirkulasi peminjaman pita haid & cuti menstruasi siswi, sistem deteksi keterlambatan > 7 hari, dan registrasi anggota.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-rose-800 bg-rose-50 px-2 py-1 rounded-lg border border-rose-200 text-center">Akses: Web Portal</span>
                </div>

                <!-- Role 3: Health Monitor -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fas fa-heart-pulse"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-0.5">Health Monitor</h4>
                        <p class="text-xs text-sky-700 font-semibold mb-2">Guru Penjas / Tim Skrining</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Fokus pada skrining fisik klinis: input cepat timbangan antropometri (TB/BB dengan kalkulasi otomatis IMT Kemenkes) dan tes rapid Hemoglobin (Hb anemia siswi).
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-sky-800 bg-sky-50 px-2 py-1 rounded-lg border border-sky-200 text-center">Akses: Web Portal</span>
                </div>

                <!-- Role 4: Health Consultant -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fas fa-user-doctor"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-0.5">Health Consultant</h4>
                        <p class="text-xs text-teal-700 font-semibold mb-2">Guru BK / Konselor / Dokter</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Menangani konsultasi privat SafeSpace, evaluasi skrining burnout dan stres akademik, serta memberikan rekomendasi klinis dan rujukan lanjutan ke Puskesmas.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-teal-800 bg-teal-50 px-2 py-1 rounded-lg border border-teal-200 text-center">Akses: Web Portal</span>
                </div>

                <!-- Role 5: Member (Siswa) -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-0.5">Siswa / Siswi</h4>
                        <p class="text-xs text-indigo-700 font-semibold mb-2">Peserta Didik / Anggota</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Pengguna aplikasi mobile Android untuk melihat rekam medis mandiri, log siklus haid, pengajuan pinjam pita UKS, pelacak lari GPS, dan chat SafeSpace dengan BK.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-indigo-800 bg-indigo-50 px-2 py-1 rounded-lg border border-indigo-200 text-center">Akses: Mobile Android</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 5: PAKET & LISENSI FLEKSIBEL (Billing Midtrans)        -->
    <!-- ============================================================== -->
    <section id="paket" class="py-16 sm:py-24 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-3">
                    <i class="fas fa-credit-card"></i>
                    <span>TARIF RESMI & TRANSPARAN</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                    Paket Kuota Lisensi Sesuai Kapasitas Sekolah
                </h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                    Biaya resmi terstandarisasi <strong class="text-emerald-700 font-extrabold">Rp 5.000 / siswa / tahun (5k/siswa)</strong> (All-In Seluruh Modul). Didukung pembayaran instan otomatis via <strong>Midtrans Snap</strong> (QRIS, VA Bank, GoPay, ShopeePay) serta Transfer BCA resmi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 max-w-5xl mx-auto">
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 flex flex-col justify-between hover:shadow-lg transition-all">
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Paket Angkatan</div>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-1">+100 Siswa</h3>
                        <div class="text-2xl font-black text-slate-900 mt-4 mb-0.5">Rp 500.000 <span class="text-xs font-normal text-slate-500">/ tahun</span></div>
                        <div class="text-xs font-bold text-emerald-700 mb-4">Rp 5.000 / siswa / tahun (5k/siswa)</div>
                        <p class="text-xs text-slate-600 mb-6">Cocok untuk 1 tingkatan angkatan atau sekolah rintisan.</p>
                        <ul class="text-xs text-slate-600 space-y-2.5 mb-6">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-800"></i> Kuota 100 Akun Siswa</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-800"></i> Akses Seluruh Modul Klinis</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-800"></i> Laporan Rekapitulasi PDF</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-800"></i> Integrasi Aplikasi Mobile</li>
                        </ul>
                    </div>
                    <a href="{{ route('login') }}" class="w-full py-3 rounded-xl text-xs font-bold text-center text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 transition-all">
                        Pilih di Portal Admin
                    </a>
                </div>

                <div class="p-6 rounded-3xl bg-gradient-to-b from-emerald-950 to-slate-950 text-white border-2 border-emerald-500 flex flex-col justify-between shadow-xl relative transform lg:-translate-y-2">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full text-[10px] font-black bg-emerald-700 text-white uppercase tracking-wider shadow">
                        PALING POPULER
                    </div>
                    <div>
                        <div class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">Sekolah Menengah</div>
                        <h3 class="text-xl font-extrabold text-white mb-1">+250 Siswa</h3>
                        <div class="text-3xl font-black text-white mt-4 mb-0.5">Rp 1.250.000 <span class="text-xs font-normal text-slate-400">/ tahun</span></div>
                        <div class="text-xs font-bold text-emerald-400 mb-4">Rp 5.000 / siswa / tahun (5k/siswa)</div>
                        <p class="text-xs text-slate-300 mb-6">Pilihan ideal untuk SMP / SMA / SMK skala menengah.</p>
                        <ul class="text-xs text-slate-200 space-y-2.5 mb-6">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-400"></i> Kuota 250 Akun Siswa</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-400"></i> Modul Hb & Antropometri Lengkap</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-400"></i> Multi-Divisi Tanpa Batas</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-400"></i> Dukungan Teknis Prioritas</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-400"></i> Struk & Faktur Pajak Resmi</li>
                        </ul>
                    </div>
                    <a href="{{ route('login') }}" class="w-full py-3 rounded-xl text-xs font-bold text-center text-white bg-emerald-800 hover:bg-emerald-900 shadow-md transition-all">
                        Pilih di Portal Admin
                    </a>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 flex flex-col justify-between hover:shadow-lg transition-all">
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Yayasan / Besar</div>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-1">+1.000 Siswa</h3>
                        <div class="text-2xl font-black text-slate-900 mt-4 mb-0.5">Rp 5.000.000 <span class="text-xs font-normal text-slate-500">/ tahun</span></div>
                        <div class="text-xs font-bold text-emerald-700 mb-4">Rp 5.000 / siswa / tahun (5k/siswa)</div>
                        <p class="text-xs text-slate-600 mb-6">Untuk sekolah besar, yayasan pendidikan, atau pesantren.</p>
                        <ul class="text-xs text-slate-600 space-y-2.5 mb-6">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-800"></i> Kuota 1.000+ Akun Siswa</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-800"></i> Audit Logging Append-Only</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-800"></i> Konsultasi Implementasi Khusus</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-800"></i> Layanan Onboarding Khusus</li>
                        </ul>
                    </div>
                    <a href="{{ route('login') }}" class="w-full py-3 rounded-xl text-xs font-bold text-center text-slate-700 bg-white hover:bg-slate-100 border border-slate-300 transition-all">
                        Pilih di Portal Admin
                    </a>
                </div>
            </div>

            <div class="mt-8 text-center text-xs text-slate-500">
                <i class="fas fa-circle-info text-emerald-800 mr-1"></i> Biaya resmi terstandarisasi Rp 5.000 / siswa / tahun (All-In). Admin Instansi dapat membeli tambahan kuota satuan secara fleksibel kapan saja langsung dari menu Billing.
            </div>
        </div>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 6: UNDUH APLIKASI MOBILE                               -->
    <!-- ============================================================== -->
    <section id="unduh" class="py-16 sm:py-20 bg-slate-900 text-white relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="p-8 sm:p-12 rounded-3xl bg-gradient-to-r from-emerald-950 via-slate-900 to-slate-900 border border-emerald-800/40 relative overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    
                    <div class="lg:col-span-8 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            <i class="fab fa-android"></i>
                            <span>APK ANDROID & WEB BROWSER</span>
                        </div>
                        <h2 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                            Mulai Gunakan Aku Sehat di Sekolah Anda Hari Ini
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                            Unduh aplikasi Android untuk siswa atau login via peramban web tanpa memerlukan instalasi tambahan untuk komputer UKS sekolah.
                        </p>

                        <div class="flex flex-wrap items-center gap-3 pt-4">
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold text-slate-950 bg-emerald-400 hover:bg-emerald-300 shadow-md transition-all">
                                <i class="fas fa-desktop"></i>
                                <span>Buka Web Portal UKS</span>
                            </a>
                            <a href="#demo-section" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition-all">
                                <i class="fas fa-mobile-screen"></i>
                                <span>Simulasi Mobile di Browser</span>
                            </a>
                        </div>
                    </div>

                    <div class="lg:col-span-4 flex justify-center lg:justify-end">
                        <div class="p-5 rounded-2xl bg-slate-800/90 border border-slate-700 text-center">
                            <img src="{{ asset('src/aku_sehat_icon.png') }}" alt="Aku Sehat" class="w-16 h-16 mx-auto mb-3 object-contain">
                            <div class="text-sm font-bold text-white">Aku Sehat Mobile</div>
                            <div class="text-[11px] text-emerald-400 font-semibold mb-3">Versi 1.0.0 Stabil (Android)</div>
                            <div class="text-[10px] text-slate-400 max-w-[200px] mx-auto">
                                Membutuhkan Android 8.0+ • Ukuran ringan • Dioptimasi untuk ponsel ramah kuota.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 7: FAQ                                                 -->
    <!-- ============================================================== -->
    <section class="py-16 sm:py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Pertanyaan yang Sering Diajukan (FAQ)
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-2">
                    Informasi penting seputar keamanan, kompatibilitas, dan penggunaan Aku Sehat.
                </p>
            </div>

            <div class="space-y-3.5">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Apakah aplikasi ini memberatkan smartphone siswa?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sama sekali tidak. Aplikasi mobile Aku Sehat dikembangkan menggunakan React Native dan TypeScript yang dioptimasi secara mendalam. Tampilan grafik dan animasi menggunakan akselerasi GPU murni sehingga sangat ringan dan hemat baterai, bahkan pada ponsel spesifikasi standar.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Bagaimana keamanan data medis siswa dijamin?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Data klinis sensitif seperti catatan Hb, pemeriksaan fisik, dan riwayat kesehatan haid dienkripsi menggunakan enkripsi tingkat perbankan (AES-256) per-tenant. Setiap sekolah memiliki ruang data terisolasi yang tidak dapat diintip oleh sekolah lain.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Bagaimana cara sekolah mendaftar dan mengundang siswa?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Admin Sekolah dapat mendaftar, memilih lisensi yang diinginkan, dan membuat kelas/divisi. Setiap sekolah mendapatkan <strong>Kode Undangan Instansi</strong> unik yang dapat dibagikan kepada siswa untuk registrasi mandiri di aplikasi mobile.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Apakah data bisa diekspor dalam bentuk PDF untuk dinas?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Ya. Web Dashboard dilengkapi fitur pencetakan PDF resmi berstandar format laporan dinas/sekolah untuk rekap antropometri, pemeriksaan Hb anemia massal, dan inventaris fasilitas UKS.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================== -->
    <!-- FOOTER                                                         -->
    <!-- ============================================================== -->
    <footer class="bg-slate-950 text-slate-400 text-xs py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8 pb-8 border-b border-slate-800">
                <div class="md:col-span-2 space-y-3">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('src/aku_sehat_icon.png') }}" alt="Aku Sehat" class="w-8 h-8 object-contain">
                        <span class="font-extrabold text-white text-base">Aku Sehat</span>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed max-w-sm">
                        Platform ekosistem pemantauan dan tata kelola kesehatan sekolah, UKS, dan institusi terpadu. Dikembangkan dengan standar medis dan keamanan industri.
                    </p>
                    <div class="text-[11px] text-slate-500">
                        &copy; 2026 <strong>Neura Cakrawira Asia</strong> / Neura Cakrawira Solusi. Hak Cipta Dilindungi.
                    </div>
                </div>

                <div class="space-y-2.5">
                    <div class="font-bold text-white text-xs uppercase tracking-wider">Navigasi Cepat</div>
                    <ul class="space-y-1.5">
                        <li><a href="#tentang" class="hover:text-emerald-400 transition-colors">Tentang Aplikasi</a></li>
                        <li><a href="#demo-section" class="hover:text-emerald-400 transition-colors">Demo & Walkthrough</a></li>
                        <li><a href="#fitur" class="hover:text-emerald-400 transition-colors">Fitur Unggulan</a></li>
                        <li><a href="#paket" class="hover:text-emerald-400 transition-colors">Paket & Lisensi</a></li>
                        <li><a href="{{ route('patch_notes.index') }}" class="hover:text-emerald-400 transition-colors">Catatan Rilis (Patch Notes)</a></li>
                    </ul>
                </div>

                <div class="space-y-2.5">
                    <div class="font-bold text-white text-xs uppercase tracking-wider">Kepatuhan & Privasi</div>
                    <ul class="space-y-1.5">
                        <li><a href="{{ route('privacy.policy') }}" class="hover:text-emerald-400 transition-colors">Kebijakan Privasi (Privacy Policy)</a></li>
                        <li><a href="{{ route('account.delete') }}" class="hover:text-emerald-400 transition-colors">Hapus Akun / Data Mandiri</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-emerald-400 transition-colors">Portal Masuk Sistem</a></li>
                    </ul>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-2">
                <div>Dirancang dengan arsitektur modular Domain-Driven di bawah naungan PT Neura Cakrawira Solusi.</div>
                <div class="flex items-center gap-3">
                    <span>Enkripsi Medis Tenant</span>
                    <span>•</span>
                    <span>Google Play Compliance</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- ============================================================== -->
    <!-- MODAL: VIDEO WALKTHROUGH ON-DEMAND (100% Desain Asli Web)      -->
    <!-- Simulasi Realistis Berdasarkan 3 Peran Web                     -->
    <!-- ============================================================== -->
    <div id="demo-video-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-2 sm:p-4 bg-slate-950/90 backdrop-blur-md transition-all duration-300">
        <div class="relative w-full max-w-5xl bg-slate-900 border border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden flex flex-col text-white" onclick="event.stopPropagation()">
            
            <!-- Modal Header with Role Selector -->
            <div class="px-5 py-3.5 bg-slate-800/90 border-b border-slate-700 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm font-bold border border-emerald-500/30">
                        <i class="fas fa-play"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white flex items-center gap-2">
                            <span>Video Walkthrough Web (Desain Asli)</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">60 FPS</span>
                        </div>
                        <div class="text-[10px] text-slate-400">Pilih peran untuk melihat alur kerja lengkap di dashboard web:</div>
                    </div>
                </div>

                <!-- Video Role Tabs (4 Peran Web Lengkap) -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-900 rounded-xl border border-slate-700/80 text-[11px] font-bold">
                    <button type="button" onclick="changeVideoRole('admin')" id="v-role-admin" class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white transition-colors">
                        Admin Instansi
                    </button>
                    <button type="button" onclick="changeVideoRole('adminpmr')" id="v-role-adminpmr" class="px-2.5 py-1 rounded-lg text-slate-400 hover:text-white transition-colors">
                        Admin PMR / UKS
                    </button>
                    <button type="button" onclick="changeVideoRole('monitor')" id="v-role-monitor" class="px-2.5 py-1 rounded-lg text-slate-400 hover:text-white transition-colors">
                        Health Monitor
                    </button>
                    <button type="button" onclick="changeVideoRole('consultant')" id="v-role-consultant" class="px-2.5 py-1 rounded-lg text-slate-400 hover:text-white transition-colors">
                        Health Consultant
                    </button>
                </div>

                <button type="button" onclick="closeDemoModal()" class="w-8 h-8 rounded-full bg-slate-700 hover:bg-slate-600 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-colors">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Video Player Stage (Actual Web Page Simulation) -->
            <div class="relative w-full aspect-[16/10] sm:aspect-[16/9] bg-slate-950 flex flex-col justify-between overflow-hidden select-none" id="video-stage">
                
                <!-- Stage Top Banner -->
                <div class="px-4 py-2 bg-slate-900/90 border-b border-slate-800 flex items-center justify-between text-xs z-20">
                    <div class="flex items-center gap-2">
                        <span id="v-chapter-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            LANGKAH 1
                        </span>
                        <span id="v-chapter-title" class="font-bold text-white text-xs truncate max-w-md">
                            Dashboard Manajemen Sekolah
                        </span>
                    </div>
                    <span id="video-timer" class="text-xs font-mono text-emerald-400">00:00 / 01:00</span>
                </div>

                <!-- Video Viewport Content -->
                <div class="flex-1 overflow-hidden relative flex flex-col justify-center items-center p-3 sm:p-5" id="video-viewport">
                    
                    <!-- Simulated Click Cursor Element -->
                    <div id="simulated-cursor" class="absolute z-50 pointer-events-none transition-all duration-700 text-emerald-400 drop-shadow-md hidden">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-arrow-pointer text-xl text-white drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)] filter"></i>
                            <span id="cursor-tooltip" class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-700 text-white shadow-md">
                                Klik Catat
                            </span>
                        </div>
                    </div>

                    <!-- Screen Container for Video Scenes -->
                    <div class="w-full h-full max-w-4xl bg-slate-100 text-slate-800 rounded-2xl border border-slate-700/60 shadow-xl overflow-hidden flex flex-col" id="video-inner-card">
                        <!-- Simulated Top Navigation -->
                        <div class="px-4 py-2 bg-gradient-to-r from-[#092e20] to-[#041a12] text-white flex items-center justify-between text-xs border-b border-emerald-900/40 shrink-0">
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-emerald-300">AKU SEHAT</span>
                                <span class="text-slate-400">|</span>
                                <span id="v-header-instansi" class="text-slate-200 font-semibold truncate max-w-[200px]">SMKS TELEKOMUNIKASI TELESANDI BEKASI</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span id="v-header-role" class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Admin Instansi</span>
                                <div class="w-6 h-6 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-[10px]">
                                    <i class="fas fa-user text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Simulated Body Content -->
                        <div id="v-simulated-body" class="flex-1 p-4 overflow-y-auto web-scrollbar bg-slate-50 flex flex-col justify-between">
                            <!-- Dynamic Content Rendered Here -->
                        </div>
                    </div>

                </div>

                <!-- Video Controls & Chapter Navigation Bar -->
                <div class="z-20 bg-slate-900/95 border-t border-slate-800 p-3 space-y-2">
                    <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden cursor-pointer" onclick="seekVideo(event)">
                        <div id="video-progress" class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full w-0 transition-all duration-150"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="togglePlayVideo()" id="play-btn" class="w-8 h-8 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white flex items-center justify-center font-bold">
                                <i class="fas fa-play" id="play-btn-icon"></i>
                            </button>
                            <button type="button" onclick="restartVideo()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center">
                                <i class="fas fa-rotate-left"></i>
                            </button>
                            <span class="text-slate-400 text-[11px] ml-1" id="video-status-text">Putar Walkthrough</span>
                        </div>

                        <div class="hidden sm:flex items-center gap-1 text-[10px]" id="video-chapter-pills">
                            <!-- Injected dynamically -->
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3 bg-slate-800/90 border-t border-slate-700/80 flex flex-wrap items-center justify-between text-xs text-slate-400 gap-2">
                <div><i class="fas fa-feather text-emerald-400 mr-1.5"></i> Menggunakan komponen tampilan 100% identik dari kode Blade Laravel aplikasi Aku Sehat.</div>
                <button type="button" onclick="closeDemoModal()" class="px-4 py-1.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-bold transition-colors">
                    Tutup Walkthrough
                </button>
            </div>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- JAVASCRIPT LOGIC: REALISTIC WALKTHROUGH ENGINE (100% ASLI)     -->
    <!-- ============================================================== -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('menu-icon');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                menu.classList.add('hidden');
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        }
        function closeMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const icon = document.getElementById('menu-icon');
            menu.classList.add('hidden');
            if (icon) {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        }

        function switchDemoTab(tab) {
            const btnWeb = document.getElementById('tab-btn-web');
            const btnMobile = document.getElementById('tab-btn-mobile');
            const contentWeb = document.getElementById('demo-content-web');
            const contentMobile = document.getElementById('demo-content-mobile');

            if (tab === 'web') {
                btnWeb.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 bg-emerald-700 text-white shadow';
                btnMobile.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 text-slate-300 hover:text-white';
                contentWeb.classList.remove('hidden');
                contentMobile.classList.add('hidden');
            } else {
                btnMobile.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 bg-emerald-700 text-white shadow';
                btnWeb.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 text-slate-300 hover:text-white';
                contentMobile.classList.remove('hidden');
                contentWeb.classList.add('hidden');
            }
        }

        const rolesData = {
            admin: {
                name: 'Admin Instansi',
                badgeText: 'Admin Instansi (Kepala Sekolah / Yayasan)',
                username: 'Kepala Sekolah / Yayasan',
                instansi: 'SMKS TELEKOMUNIKASI TELESANDI BEKASI',
                sidebarLinks: [
                    { id: 'dashboard', icon: 'fa-chart-pie', label: 'Dashboard Utama', active: true },
                    { id: 'kesehatan', icon: 'fa-notes-medical', label: 'Data Kesehatan Fisik (IMT)' },
                    { id: 'hb', icon: 'fa-droplet', label: 'Data Hemoglobin (Anemia)' },
                    { id: 'femhealth', icon: 'fa-calendar-alt', label: 'Siklus & Pita Haid' },
                    { id: 'divisi', icon: 'fa-sitemap', label: 'Data Divisi & Kelas' },
                    { id: 'users', icon: 'fa-users', label: 'Manajemen Akun Siswa' },
                    { id: 'billing', icon: 'fa-credit-card', label: 'Langganan & Billing Midtrans' }
                ],
                screens: [
                    {
                        id: 'dashboard',
                        title: 'Dashboard Analitik & Rekapitulasi Sekolah',
                        url: 'https://akusehat.neuracakrawira.asia/dashboard',
                        html: `
                            <div class="rounded-2xl bg-gradient-to-r from-emerald-950 via-emerald-900 to-teal-950 p-4 sm:p-5 text-white shadow-md border border-emerald-800/40 relative overflow-hidden mb-4">
                                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div>
                                        <h3 class="text-base sm:text-lg font-extrabold text-white">Selamat Datang, Admin SMKS TELEKOMUNIKASI TELESANDI!</h3>
                                        <p class="text-xs text-emerald-100/80">Pemantauan kesehatan berkala & analitik terpadu multi-divisi aktif.</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="px-3 py-1.5 rounded-xl text-[11px] font-bold bg-white text-emerald-900 shadow-sm flex items-center gap-1.5">
                                            <i class="fas fa-plus text-emerald-700"></i> Catat Kesehatan
                                        </span>
                                        <span class="px-3 py-1.5 rounded-xl text-[11px] font-bold bg-emerald-500/20 text-white border border-emerald-400/30 flex items-center gap-1.5">
                                            <i class="fas fa-droplet text-rose-400"></i> Input Hb
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                                <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Total Anggota</div>
                                    <div class="text-xl font-black text-slate-900 mt-1">450 Siswa</div>
                                    <div class="text-[9px] text-emerald-700 font-bold mt-0.5">12 Divisi Terdaftar</div>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Gizi Normal (Ideal)</div>
                                    <div class="text-xl font-black text-emerald-700 mt-1">382 Siswa</div>
                                    <div class="text-[9px] text-slate-400 mt-0.5">84.8% Standar Kemenkes</div>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Pemeriksaan Hb</div>
                                    <div class="text-xl font-black text-rose-600 mt-1">8 Anemia</div>
                                    <div class="text-[9px] text-rose-500 font-bold mt-0.5">Perlu Tindak Lanjut</div>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-xs">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Lisensi Aktif</div>
                                    <div class="text-xl font-black text-indigo-700 mt-1">500 Kuota</div>
                                    <div class="text-[9px] text-emerald-700 font-bold mt-0.5">Berlaku s/d Des 2026</div>
                                </div>
                            </div>
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 text-xs">
                                <div class="flex justify-between items-center mb-2 font-bold text-slate-700">
                                    <span>Distribusi IMT Siswa (Standar Baku Kemenkes RI)</span>
                                    <span class="text-[10px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Real-Time Sync</span>
                                </div>
                                <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden flex">
                                    <div class="bg-sky-400 h-full" style="width: 8%" title="Kurus (8%)"></div>
                                    <div class="bg-emerald-500 h-full" style="width: 77%" title="Normal (77%)"></div>
                                    <div class="bg-amber-400 h-full" style="width: 11%" title="Gemuk (11%)"></div>
                                    <div class="bg-rose-500 h-full" style="width: 4%" title="Obesitas (4%)"></div>
                                </div>
                                <div class="flex justify-between text-[10px] text-slate-500 mt-2">
                                    <span>🔵 Gizi Kurang: 36 (8%)</span>
                                    <span>🟢 Gizi Normal: 346 (77%)</span>
                                    <span>🟡 Kelebihan BB: 50 (11%)</span>
                                    <span>🔴 Obesitas: 18 (4%)</span>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'kesehatan',
                        title: 'Data Pemeriksaan Fisik & Skrining Antropometri (IMT)',
                        url: 'https://akusehat.neuracakrawira.asia/admin/kesehatan',
                        html: `
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h4 class="font-extrabold text-sm text-slate-900">Rekapitulasi Skrining Fisik Siswa</h4>
                                    <p class="text-[11px] text-slate-500">Pencatatan antropometri (TB, BB, IMT) & fisik (Telinga, Mata, Gigi)</p>
                                </div>
                                <div class="flex gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-700 text-white flex items-center gap-1 shadow-xs">
                                        <i class="fas fa-file-pdf"></i> Ekspor PDF Resmi
                                    </span>
                                </div>
                            </div>
                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden text-xs">
                                <div class="grid grid-cols-6 font-bold text-slate-500 p-2.5 bg-slate-50 border-b border-slate-200">
                                    <span>Nama / NIS</span>
                                    <span>Kelas</span>
                                    <span>TB / BB</span>
                                    <span>Nilai IMT</span>
                                    <span>Status Gizi</span>
                                    <span class="text-right">Aksi</span>
                                </div>
                                <div class="grid grid-cols-6 p-2.5 border-b border-slate-100 items-center">
                                    <div><strong class="text-slate-900">Aditya Pratama</strong><div class="text-[10px] text-slate-400">NIS: 2026101</div></div>
                                    <span class="text-slate-600">XII-RPL 1</span>
                                    <span class="font-mono text-slate-700">168 cm / 58 kg</span>
                                    <span class="font-mono font-bold text-emerald-800">20.55</span>
                                    <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Gizi Baik</span></span>
                                    <span class="text-right text-emerald-700 font-bold"><i class="fas fa-eye"></i> Detail</span>
                                </div>
                                <div class="grid grid-cols-6 p-2.5 border-b border-slate-100 items-center">
                                    <div><strong class="text-slate-900">Bagas Mahardika</strong><div class="text-[10px] text-slate-400">NIS: 2026102</div></div>
                                    <span class="text-slate-600">XII-RPL 1</span>
                                    <span class="font-mono text-slate-700">165 cm / 74 kg</span>
                                    <span class="font-mono font-bold text-amber-700">27.18</span>
                                    <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Kelebihan BB</span></span>
                                    <span class="text-right text-emerald-700 font-bold"><i class="fas fa-eye"></i> Detail</span>
                                </div>
                                <div class="grid grid-cols-6 p-2.5 items-center">
                                    <div><strong class="text-slate-900">Citra Kirana</strong><div class="text-[10px] text-slate-400">NIS: 2026103</div></div>
                                    <span class="text-slate-600">XI-TKJ 2</span>
                                    <span class="font-mono text-slate-700">155 cm / 39 kg</span>
                                    <span class="font-mono font-bold text-sky-700">16.23</span>
                                    <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">Gizi Kurang</span></span>
                                    <span class="text-right text-emerald-700 font-bold"><i class="fas fa-eye"></i> Detail</span>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'hb',
                        title: 'Data Hemoglobin & Pencegahan Anemia Siswi',
                        url: 'https://akusehat.neuracakrawira.asia/admin/hb',
                        html: `
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h4 class="font-extrabold text-sm text-slate-900">Monitoring Hemoglobin (Hb)</h4>
                                    <p class="text-[11px] text-slate-500">Skrining berkala siswi putri untuk program bebas anemia</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    Target 100% Bebas Anemia
                                </span>
                            </div>
                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden text-xs">
                                <div class="grid grid-cols-5 font-bold text-slate-500 p-2.5 bg-slate-50 border-b border-slate-200">
                                    <span>Nama Siswi</span>
                                    <span>Kelas</span>
                                    <span>Kadar Hb</span>
                                    <span>Kategori Klinis</span>
                                    <span>Tindak Lanjut</span>
                                </div>
                                <div class="grid grid-cols-5 p-2.5 border-b border-slate-100 items-center">
                                    <strong class="text-slate-900">Dewi Sartika</strong>
                                    <span class="text-slate-600">X-RPL 1</span>
                                    <span class="font-mono font-bold text-emerald-800">13.4 g/dL</span>
                                    <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Normal</span></span>
                                    <span class="text-slate-500">Konsumsi TTD Rutin</span>
                                </div>
                                <div class="grid grid-cols-5 p-2.5 items-center">
                                    <strong class="text-slate-900">Intan Permata</strong>
                                    <span class="text-slate-600">XI-MM 2</span>
                                    <span class="font-mono font-bold text-rose-600">10.4 g/dL</span>
                                    <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Anemia Ringan</span></span>
                                    <span class="text-rose-600 font-bold">Rujuk Konseling & Puskesmas</span>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'femhealth',
                        title: 'Siklus Menstruasi & Peminjaman Pita UKS / Cuti Haid',
                        url: 'https://akusehat.neuracakrawira.asia/admin/femhealth',
                        html: `
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center font-bold text-sm">
                                        <i class="fas fa-venus"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-sm text-slate-900">Siklus Menstruasi & Peminjaman Pita UKS</h4>
                                        <p class="text-[11px] text-slate-500">Pemantauan kesehatan reproduksi siswi & sirkulasi pita UKS</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-pink-50 text-pink-700 border border-pink-200">
                                    <i class="fas fa-chart-pie mr-1"></i> Monitoring Mode
                                </span>
                            </div>

                            <!-- Warning Banner Keterlambatan Pita -->
                            <div class="p-3 bg-gradient-to-r from-rose-50 to-pink-50 rounded-xl border border-rose-200 mb-3 flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs shrink-0 mt-0.5">
                                    <i class="fas fa-triangle-exclamation"></i>
                                </div>
                                <div class="text-xs">
                                    <div class="font-bold text-rose-900">Peringatan: 1 Siswi Melebihi Batas Waktu 7 Hari</div>
                                    <div class="text-[11px] text-rose-700 mt-0.5">Masa pinjam pita maksimal 7 hari. Siswi terlambat terdeteksi otomatis untuk evaluasi medis dan kedisiplinan.</div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden text-xs">
                                <div class="grid grid-cols-6 font-bold text-slate-500 p-2.5 bg-slate-50 border-b border-slate-200">
                                    <span>Siswi</span>
                                    <span>Kelas / Divisi</span>
                                    <span>Tgl Pinjam</span>
                                    <span>Batas Tempo</span>
                                    <span>Status Medis</span>
                                    <span class="text-right">Tindakan</span>
                                </div>
                                <div class="grid grid-cols-6 p-2.5 border-b border-slate-100 items-center">
                                    <div><strong class="text-slate-900">Maya Putri</strong><div class="text-[10px] text-slate-400">NIS: 2026115</div></div>
                                    <span class="text-slate-600">XI-MM 2</span>
                                    <span class="font-mono text-slate-700">28 Okt 2026</span>
                                    <span class="font-mono text-rose-600 font-bold">04 Nov 2026</span>
                                    <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 animate-pulse">Telat 1 Hari (Warning)</span></span>
                                    <span class="text-right"><button class="px-2 py-1 rounded bg-rose-600 text-white text-[10px] font-bold">Rujuk BK</button></span>
                                </div>
                                <div class="grid grid-cols-6 p-2.5 items-center">
                                    <div><strong class="text-slate-900">Annisa Larasati</strong><div class="text-[10px] text-slate-400">NIS: 2026118</div></div>
                                    <span class="text-slate-600">X-RPL 1</span>
                                    <span class="font-mono text-slate-700">02 Nov 2026</span>
                                    <span class="font-mono text-slate-700">09 Nov 2026</span>
                                    <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Normal (Sisa 5 Hari)</span></span>
                                    <span class="text-right text-emerald-700 font-bold"><i class="fas fa-check-circle"></i> Terpantau</span>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'divisi',
                        title: 'Data Divisi & Kelas Terdaftar SMKS TELKOMUNIKASI TELESANDI',
                        url: 'https://akusehat.neuracakrawira.asia/admin/divisi',
                        html: `
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">
                                        <i class="fas fa-school"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-sm text-slate-900">Data Divisi & Kelas</h4>
                                        <p class="text-[11px] text-slate-500">Struktur kelas dan pengelompokan anggota aktif sekolah</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-700 text-white flex items-center gap-1 shadow-xs">
                                    <i class="fas fa-plus"></i> Tambah Divisi Baru
                                </span>
                            </div>

                            <div class="grid grid-cols-3 gap-3 mb-3">
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Total Divisi / Kelas</div>
                                    <div class="text-xl font-black text-slate-900 mt-0.5">12 Kelas</div>
                                    <div class="text-[9px] text-emerald-700 font-bold mt-0.5">3 Tingkatan (X, XI, XII)</div>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Total Siswa Terdaftar</div>
                                    <div class="text-xl font-black text-slate-900 mt-0.5">450 Siswa</div>
                                    <div class="text-[9px] text-slate-400 mt-0.5">Terhubung ke Aplikasi Mobile</div>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Sinkronisasi Lisensi</div>
                                    <div class="text-xl font-black text-emerald-700 mt-0.5">100% Valid</div>
                                    <div class="text-[9px] text-emerald-600 font-bold mt-0.5">Kuota Multi-Paket Aktif</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-3 text-xs">
                                <div class="p-3 bg-white rounded-xl border border-slate-200 border-t-4 border-t-emerald-600">
                                    <div class="flex justify-between items-center">
                                        <span class="font-extrabold text-slate-900">Kelas X-RPL 1</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold">36 Siswa</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-1">Wali Kelas: Budi Santoso, S.Kom</p>
                                    <div class="mt-2 pt-2 border-t border-slate-100 flex justify-between text-[10px] text-emerald-700 font-bold">
                                        <span>Skrining Lengkap</span>
                                        <span class="cursor-pointer hover:underline">Kelola Siswa →</span>
                                    </div>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200 border-t-4 border-t-sky-600">
                                    <div class="flex justify-between items-center">
                                        <span class="font-extrabold text-slate-900">Kelas XI-TKJ 2</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-sky-50 text-sky-700 font-bold">34 Siswa</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-1">Wali Kelas: Siti Rahma, M.Pd</p>
                                    <div class="mt-2 pt-2 border-t border-slate-100 flex justify-between text-[10px] text-sky-700 font-bold">
                                        <span>Skrining Lengkap</span>
                                        <span class="cursor-pointer hover:underline">Kelola Siswa →</span>
                                    </div>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200 border-t-4 border-t-rose-600">
                                    <div class="flex justify-between items-center">
                                        <span class="font-extrabold text-slate-900">Kelas XII-MM 1</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-rose-50 text-rose-700 font-bold">32 Siswa</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-1">Wali Kelas: Hendra Kurniawan</p>
                                    <div class="mt-2 pt-2 border-t border-slate-100 flex justify-between text-[10px] text-rose-700 font-bold">
                                        <span>Skrining Lengkap</span>
                                        <span class="cursor-pointer hover:underline">Kelola Siswa →</span>
                                    </div>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'users',
                        title: 'Manajemen Data Pengguna & Akun Siswa Terpadu',
                        url: 'https://akusehat.neuracakrawira.asia/admin/users',
                        html: `
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-sm text-slate-900">Manajemen Akun Siswa & Anggota</h4>
                                        <p class="text-[11px] text-slate-500">Pendaftaran akun, alokasi lisensi, dan hak akses mobile</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-600 text-white flex items-center gap-1 shadow-xs">
                                        <i class="fas fa-file-import"></i> Import Excel
                                    </span>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-700 text-white flex items-center gap-1 shadow-xs">
                                        <i class="fas fa-plus"></i> Tambah User
                                    </span>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden text-xs">
                                <div class="grid grid-cols-6 font-bold text-slate-500 p-2.5 bg-slate-50 border-b border-slate-200">
                                    <span>Nama Lengkap</span>
                                    <span>NIS / Identitas</span>
                                    <span>Divisi / Kelas</span>
                                    <span>Peran Akun</span>
                                    <span>Lisensi Terkait</span>
                                    <span class="text-right">Status</span>
                                </div>
                                <div class="grid grid-cols-6 p-2.5 border-b border-slate-100 items-center">
                                    <div><strong class="text-slate-900">Aditya Pratama</strong><div class="text-[10px] text-slate-400">aditya@student.sch.id</div></div>
                                    <span class="font-mono text-slate-700">2026101</span>
                                    <span class="text-slate-600">XII-RPL 1</span>
                                    <span class="text-emerald-700 font-bold">Siswa</span>
                                    <span class="font-mono text-[10px] text-slate-500">RDVZ-CS0T-7ARC</span>
                                    <span class="text-right"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span></span>
                                </div>
                                <div class="grid grid-cols-6 p-2.5 border-b border-slate-100 items-center">
                                    <div><strong class="text-slate-900">Bagas Mahardika</strong><div class="text-[10px] text-slate-400">bagas@student.sch.id</div></div>
                                    <span class="font-mono text-slate-700">2026102</span>
                                    <span class="text-slate-600">XII-RPL 1</span>
                                    <span class="text-emerald-700 font-bold">Siswa</span>
                                    <span class="font-mono text-[10px] text-slate-500">RDVZ-CS0T-7ARC</span>
                                    <span class="text-right"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span></span>
                                </div>
                                <div class="grid grid-cols-6 p-2.5 items-center">
                                    <div><strong class="text-slate-900">Citra Kirana</strong><div class="text-[10px] text-slate-400">citra@student.sch.id</div></div>
                                    <span class="font-mono text-slate-700">2026103</span>
                                    <span class="text-slate-600">XI-TKJ 2</span>
                                    <span class="text-emerald-700 font-bold">Siswi</span>
                                    <span class="font-mono text-[10px] text-slate-500">RDVZ-CS0T-7ARC</span>
                                    <span class="text-right"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span></span>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'billing',
                        title: 'Langganan Kuota & Billing Midtrans Snap (Rp 5.000 / Siswa / Tahun)',
                        url: 'https://akusehat.neuracakrawira.asia/admin/billing',
                        html: `
                            <!-- Top Overview Cards -->
                            <div class="grid grid-cols-3 gap-3 mb-3 text-xs">
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 uppercase">
                                        <span>Status Layanan</span>
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold">Aktif</span>
                                    </div>
                                    <div class="text-base font-black text-slate-900 mt-1">1 Paket Aktif</div>
                                    <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">335 Hari Sisa Terdekat</div>
                                    <div class="text-[9px] font-mono text-slate-400 truncate mt-1">Key: RDVZ-CS0T-7ARC-ISNQ</div>
                                </div>

                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="flex justify-between items-center text-[10px] font-bold text-slate-400 uppercase">
                                        <span>Total Kuota Siswa</span>
                                        <span class="text-[10px] font-bold text-emerald-700">90%</span>
                                    </div>
                                    <div class="text-base font-black text-slate-900 mt-1">450 <span class="text-xs font-normal text-slate-400">/ 500 Siswa</span></div>
                                    <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden my-1">
                                        <div class="h-1.5 bg-emerald-600 rounded-full" style="width: 90%"></div>
                                    </div>
                                    <div class="text-[9px] text-slate-500">Sisa Slot: <strong class="text-slate-800">50 Siswa Tersedia</strong></div>
                                </div>

                                <div class="p-3 bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-xl">
                                    <div class="text-[10px] font-bold text-emerald-400 uppercase">Tarif Lisensi Resmi</div>
                                    <div class="text-sm font-black text-white mt-1">Rp 5.000 / siswa / tahun</div>
                                    <div class="text-[10px] text-slate-300 mt-0.5">5k / siswa / tahun (All-In)</div>
                                    <div class="text-[9px] text-emerald-300 mt-1 flex items-center gap-1"><i class="fas fa-shield-check"></i> Multi-Bucket Midtrans</div>
                                </div>
                            </div>

                            <!-- Checkout & Billing Box -->
                            <div class="p-4 bg-white rounded-xl border border-slate-200 text-xs space-y-3">
                                <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                    <div>
                                        <h4 class="font-bold text-sm text-slate-900">Formulir Billing & Transaksi Lisensi Sekolah</h4>
                                        <p class="text-[11px] text-slate-500">Biaya langganan resmi <strong class="text-emerald-700">Rp 5.000 / siswa / tahun (5k/siswa)</strong>. Akun siswa didaftarkan langsung oleh Admin.</p>
                                    </div>
                                    <span class="px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 font-bold text-[10px] border border-emerald-200">
                                        <i class="fas fa-bolt text-emerald-600 mr-1"></i> VERIFIKASI REALTIME
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <!-- Paket Duration & Size Selection -->
                                    <div class="p-3 rounded-lg border-2 border-emerald-600 bg-emerald-50/40">
                                        <div class="flex justify-between items-center text-[10px] font-bold text-emerald-800">
                                            <span>PAKET TERPILIH</span>
                                            <i class="fas fa-check-circle text-emerald-600"></i>
                                        </div>
                                        <div class="text-sm font-black text-slate-900 mt-1">+100 Siswa (1 Tahun)</div>
                                        <div class="text-xs font-bold text-emerald-700 mt-0.5">Rp 500.000 / tahun <span class="text-[10px] font-normal text-slate-500">(5k/siswa/thn)</span></div>
                                        <div class="text-[10px] text-slate-500 mt-1">Termasuk PPN 11%: Total <strong class="text-slate-800">Rp 555.000</strong></div>
                                    </div>

                                    <!-- Payment Method (Midtrans Snap) -->
                                    <div class="p-3 rounded-lg border border-slate-200 bg-slate-900 text-white flex flex-col justify-between">
                                        <div>
                                            <div class="flex justify-between items-center text-[10px] text-emerald-400 font-bold">
                                                <span>⚡ MIDTRANS SNAP GATEWAY</span>
                                                <span class="px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 text-[9px]">OTOMATIS</span>
                                            </div>
                                            <div class="text-[11px] text-slate-300 mt-1">QRIS (BCA, GoPay, OVO), Virtual Account, ShopeePay</div>
                                        </div>
                                        <div class="mt-2 pt-2 border-t border-slate-800 flex justify-between items-center">
                                            <span class="text-[10px] text-slate-400">Total Tagihan:</span>
                                            <span class="font-mono font-bold text-emerald-400 text-xs">Rp 555.000</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center justify-between pt-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="text-[10px] text-slate-500">Order ID: <strong class="font-mono text-slate-700">MID-AKU-1793717539</strong></span>
                                    </div>
                                    <div class="flex gap-2">
                                        <button class="px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 font-bold border border-emerald-200 hover:bg-emerald-100 transition-colors text-[11px] flex items-center gap-1">
                                            <i class="fas fa-file-pdf"></i> Unduh Struk Resmi (PDF)
                                        </button>
                                        <button class="px-4 py-1.5 rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-[11px] shadow-sm flex items-center gap-1.5">
                                            <i class="fas fa-bolt"></i> Bayar Instan Otomatis
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `
                    }
                ]
            },

            consultant: {
                name: 'Health Consultant',
                badgeText: 'Health Consultant (Konselor / BK)',
                username: 'Dr. Rina Handayani',
                instansi: 'SMKS TELEKOMUNIKASI TELESANDI BEKASI',
                sidebarLinks: [
                    { id: 'dashboard', icon: 'fa-chart-pie', label: 'Dashboard Konselor', active: true },
                    { id: 'counseling', icon: 'fa-comments', label: 'Konseling SafeSpace' },
                    { id: 'burnout', icon: 'fa-brain', label: 'Skrining Burnout Siswa' },
                    { id: 'kesehatan', icon: 'fa-notes-medical', label: 'Monitoring Siswa Anomali' }
                ],
                screens: [
                    {
                        id: 'dashboard',
                        title: 'Dashboard Konselor Kesehatan & Divisi Binaan',
                        url: 'https://akusehat.neuracakrawira.asia/dashboard',
                        html: `
                            <div class="rounded-2xl bg-gradient-to-r from-teal-950 via-slate-900 to-slate-950 p-4 sm:p-5 text-white shadow-md border border-teal-800/40 mb-4">
                                <h3 class="text-base sm:text-lg font-extrabold text-white">Selamat Datang, Dr. Rina Handayani!</h3>
                                <p class="text-xs text-teal-100/80">Pantau tiket konseling masuk dan evaluasi kesehatan mental siswa binaan.</p>
                            </div>
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Konseling Menunggu</div>
                                    <div class="text-xl font-black text-amber-600 mt-1">3 Tiket Masuk</div>
                                    <div class="text-[9px] text-slate-400 mt-0.5">Permintaan Sesi Siswa</div>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Sesi Selesai</div>
                                    <div class="text-xl font-black text-emerald-700 mt-1">24 Siswa</div>
                                    <div class="text-[9px] text-emerald-600 font-bold mt-0.5">Bulan Ini</div>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Indikasi Burnout</div>
                                    <div class="text-xl font-black text-rose-600 mt-1">5 Siswa</div>
                                    <div class="text-[9px] text-rose-500 font-bold mt-0.5">Perlu Penanganan Dini</div>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'counseling',
                        title: 'Pusat Konseling SafeSpace (Privat & Terenkripsi)',
                        url: 'https://akusehat.neuracakrawira.asia/admin/counseling',
                        html: `
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h4 class="font-extrabold text-sm text-slate-900">Daftar Pengajuan Konseling Siswa</h4>
                                    <p class="text-[11px] text-slate-500">Kanal aman konsultasi medis dan kesehatan psikologis</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200">SafeSpace Aktif</span>
                            </div>
                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden text-xs">
                                <div class="grid grid-cols-5 font-bold text-slate-500 p-2.5 bg-slate-50 border-b border-slate-200">
                                    <span>Nama Siswa</span>
                                    <span>Topik Konseling</span>
                                    <span>Keluhan</span>
                                    <span>Status Sesi</span>
                                    <span class="text-right">Tindakan Konselor</span>
                                </div>
                                <div class="grid grid-cols-5 p-2.5 border-b border-slate-100 items-center">
                                    <div><strong class="text-slate-900">Rizky Ramadhan</strong><div class="text-[10px] text-slate-400">Kelas XI-RPL 2</div></div>
                                    <span class="text-slate-700">Gangguan Pola Tidur & Stres</span>
                                    <span class="text-slate-500 italic truncate pr-2">"Sulit tidur menjelang ujian..."</span>
                                    <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Menunggu Review</span></span>
                                    <span class="text-right"><button class="px-2 py-1 rounded bg-teal-700 text-white text-[10px] font-bold">Jadwalkan</button></span>
                                </div>
                                <div class="grid grid-cols-5 p-2.5 items-center">
                                    <div><strong class="text-slate-900">Siti Aisyah</strong><div class="text-[10px] text-slate-400">Kelas X-TKJ 1</div></div>
                                    <span class="text-slate-700">Nyeri Haid & Pusing (Hb Rendah)</span>
                                    <span class="text-slate-500 italic truncate pr-2">"Lemas saat pelajaran olahraga..."</span>
                                    <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Terjadwal Hari Ini</span></span>
                                    <span class="text-right"><button class="px-2 py-1 rounded bg-slate-100 text-slate-700 text-[10px] font-bold border border-slate-300">Beri Catatan</button></span>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'burnout',
                        title: 'Evaluasi Tingkat Burnout & Beban Belajar Siswa',
                        url: 'https://akusehat.neuracakrawira.asia/admin/burnout',
                        html: `
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 mb-3">
                                <h4 class="font-bold text-sm text-slate-900 mb-1">Skrining Kejenuhan Akademik (Burnout Assessment)</h4>
                                <p class="text-xs text-slate-500">Mendeteksi tingkat keletihan emosional, depersonalisasi, dan penurunan efikasi belajar.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="font-bold text-slate-800 mb-1">Kelas Butuh Pendampingan</div>
                                    <div class="text-lg font-black text-rose-600">XII-RPL 1 (Skor 68/100)</div>
                                    <p class="text-[10px] text-slate-500 mt-1">Tingkat kejenuhan menjelang ujian sertifikasi kompetensi kejuruan.</p>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="font-bold text-slate-800 mb-1">Rekomendasi Bimbingan</div>
                                    <div class="text-emerald-700 font-bold">Workshop Relaksasi & Ergonomi</div>
                                    <p class="text-[10px] text-slate-500 mt-1">Jadwalkan sesi relaksasi peregangan dan manajemen waktu belajar.</p>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'kesehatan',
                        title: 'Monitoring Siswa Indikasi Anomali & Rekomendasi Medis BK',
                        url: 'https://akusehat.neuracakrawira.asia/admin/kesehatan',
                        html: `
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h4 class="font-extrabold text-sm text-slate-900">Daftar Rujukan & Siswa Anomali Medis</h4>
                                    <p class="text-[11px] text-slate-500">Siswa dengan status gizi kurang/obesitas berat atau anemia yang membutuhkan konseling</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    3 Siswa Perlu Intervensi
                                </span>
                            </div>
                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden text-xs">
                                <div class="grid grid-cols-5 font-bold text-slate-500 p-2.5 bg-slate-50 border-b border-slate-200">
                                    <span>Nama Siswa</span>
                                    <span>Kelas</span>
                                    <span>Kondisi Klinis</span>
                                    <span>Rekomendasi UKS</span>
                                    <span class="text-right">Tindakan Konselor</span>
                                </div>
                                <div class="grid grid-cols-5 p-2.5 border-b border-slate-100 items-center">
                                    <div><strong class="text-slate-900">Intan Permata</strong><div class="text-[10px] text-slate-400">NIS: 2026105</div></div>
                                    <span class="text-slate-600">XI-MM 2</span>
                                    <span class="text-rose-600 font-bold">Hb 10.4 g/dL (Anemia)</span>
                                    <span class="text-slate-500">Konsumsi TTD + Konseling Pola Makan</span>
                                    <span class="text-right"><button class="px-2 py-1 rounded bg-teal-700 text-white text-[10px] font-bold">Mulai Sesi</button></span>
                                </div>
                                <div class="grid grid-cols-5 p-2.5 items-center">
                                    <div><strong class="text-slate-900">Citra Kirana</strong><div class="text-[10px] text-slate-400">NIS: 2026103</div></div>
                                    <span class="text-slate-600">XI-TKJ 2</span>
                                    <span class="text-sky-700 font-bold">IMT 16.23 (Gizi Kurang)</span>
                                    <span class="text-slate-500">Edukasi Gizi Seimbang & Sarapan</span>
                                    <span class="text-right"><button class="px-2 py-1 rounded bg-teal-700 text-white text-[10px] font-bold">Mulai Sesi</button></span>
                                </div>
                            </div>
                        `
                    }
                ]
            },

            adminpmr: {
                name: 'Admin PMR / Pengurus UKS',
                badgeText: 'Admin PMR (Pengurus Operasional UKS)',
                username: 'Koordinator PMR & UKS',
                instansi: 'SMKS TELEKOMUNIKASI TELESANDI BEKASI',
                sidebarLinks: [
                    { id: 'femhealth', icon: 'fa-ribbon', label: 'Sirkulasi Pita Haid & Cuti', active: true },
                    { id: 'users', icon: 'fa-users', label: 'Pendaftaran Siswa / Anggota' },
                    { id: 'kesehatan', icon: 'fa-notes-medical', label: 'Buku Tamu Ruang UKS' },
                    { id: 'hb', icon: 'fa-droplet', label: 'Monitoring Siswi Anemia (TTD)' }
                ],
                screens: [
                    {
                        id: 'femhealth',
                        title: 'Sirkulasi Pita UKS & Deteksi Keterlambatan > 7 Hari',
                        url: 'https://akusehat.neuracakrawira.asia/admin/femhealth',
                        html: `
                            <div class="flex justify-between items-center mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center font-bold text-sm">
                                        <i class="fas fa-venus"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-extrabold text-sm text-slate-900">Manajemen Sirkulasi Pita UKS & Cuti Haid</h4>
                                        <p class="text-[11px] text-slate-500">Operasional UKS harian: validasi pengembalian pita & penanganan siswi haid</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-pink-600 text-white flex items-center gap-1 shadow-xs">
                                    <i class="fas fa-ribbon"></i> Catat Peminjaman Baru
                                </span>
                            </div>

                            <!-- Warning Banner Keterlambatan Pita -->
                            <div class="p-3 bg-gradient-to-r from-rose-50 to-pink-50 rounded-xl border border-rose-200 mb-3 flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-rose-600 text-white flex items-center justify-center text-xs shrink-0 mt-0.5">
                                    <i class="fas fa-triangle-exclamation"></i>
                                </div>
                                <div class="text-xs">
                                    <div class="font-bold text-rose-900">Peringatan Pengurus UKS: 1 Siswi Melebihi Batas Waktu 7 Hari</div>
                                    <div class="text-[11px] text-rose-700 mt-0.5">Siswa berikut belum mengembalikan pita UKS atau mengalami siklus haid berkepanjangan (indikasi menorrhagia/anemia). Admin PMR berwenang mengenakan denda kedisiplinan dan merujuk siswi ke Guru BK.</div>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden text-xs">
                                <div class="grid grid-cols-6 font-bold text-slate-500 p-2.5 bg-slate-50 border-b border-slate-200">
                                    <span>Nama Siswi</span>
                                    <span>Kelas / Divisi</span>
                                    <span>Tgl Pinjam</span>
                                    <span>Batas Waktu</span>
                                    <span>Status Medis</span>
                                    <span class="text-right">Tindakan Admin PMR</span>
                                </div>
                                <div class="grid grid-cols-6 p-2.5 border-b border-slate-100 items-center">
                                    <div><strong class="text-slate-900">Maya Putri</strong><div class="text-[10px] text-slate-400">NIS: 2026115</div></div>
                                    <span class="text-slate-600">XI-MM 2</span>
                                    <span class="font-mono text-slate-700">28 Okt 2026</span>
                                    <span class="font-mono text-rose-600 font-bold">04 Nov 2026</span>
                                    <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 animate-pulse">Telat 1 Hari (Warning)</span></span>
                                    <span class="text-right"><button class="px-2 py-1 rounded bg-rose-600 text-white text-[10px] font-bold">Rujuk ke Guru BK</button></span>
                                </div>
                                <div class="grid grid-cols-6 p-2.5 items-center">
                                    <div><strong class="text-slate-900">Annisa Larasati</strong><div class="text-[10px] text-slate-400">NIS: 2026118</div></div>
                                    <span class="text-slate-600">X-RPL 1</span>
                                    <span class="font-mono text-slate-700">02 Nov 2026</span>
                                    <span class="font-mono text-slate-700">09 Nov 2026</span>
                                    <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Normal (Sisa 5 Hari)</span></span>
                                    <span class="text-right text-emerald-700 font-bold"><i class="fas fa-check-circle"></i> Selesai Dipinjam</span>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'users',
                        title: 'Pendaftaran Akun Siswa & Anggota Lapangan UKS',
                        url: 'https://akusehat.neuracakrawira.asia/admin/users',
                        html: `
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h4 class="font-extrabold text-sm text-slate-900">Daftar Akun Anggota & Siswa</h4>
                                    <p class="text-[11px] text-slate-500">Pendaftaran mandiri & aktivasi akun mobile siswa di ruang UKS</p>
                                </div>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-700 text-white flex items-center gap-1 shadow-xs">
                                    <i class="fas fa-user-plus"></i> + Tambah Siswa Baru
                                </span>
                            </div>
                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden text-xs">
                                <div class="grid grid-cols-5 font-bold text-slate-500 p-2.5 bg-slate-50 border-b border-slate-200">
                                    <span>Nama Siswa</span>
                                    <span>NIS</span>
                                    <span>Kelas</span>
                                    <span>Status Akun</span>
                                    <span class="text-right">Aksi</span>
                                </div>
                                <div class="grid grid-cols-5 p-2.5 border-b border-slate-100 items-center">
                                    <div><strong class="text-slate-900">Fajar Nugraha</strong></div>
                                    <span class="font-mono text-slate-700">2026110</span>
                                    <span class="text-slate-600">X-RPL 1</span>
                                    <span class="text-emerald-700 font-bold"><i class="fas fa-circle-check"></i> Aktif</span>
                                    <span class="text-right"><button class="px-2 py-1 rounded bg-slate-100 text-slate-700 font-bold text-[10px] border border-slate-300">Edit</button></span>
                                </div>
                                <div class="grid grid-cols-5 p-2.5 items-center">
                                    <div><strong class="text-slate-900">Dewi Sartika</strong></div>
                                    <span class="font-mono text-slate-700">2026111</span>
                                    <span class="text-slate-600">X-RPL 1</span>
                                    <span class="text-emerald-700 font-bold"><i class="fas fa-circle-check"></i> Aktif</span>
                                    <span class="text-right"><button class="px-2 py-1 rounded bg-slate-100 text-slate-700 font-bold text-[10px] border border-slate-300">Edit</button></span>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'kesehatan',
                        title: 'Buku Tamu & Kunjungan Ruang UKS Harian',
                        url: 'https://akusehat.neuracakrawira.asia/admin/kesehatan',
                        html: `
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 mb-3">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-bold text-sm text-slate-900">Log Siswa Berobat / Istirahat di UKS Hari Ini</h4>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">5 Kunjungan</span>
                                </div>
                                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden text-xs">
                                    <div class="grid grid-cols-5 font-bold text-slate-500 p-2.5 bg-slate-50 border-b border-slate-200">
                                        <span>Nama Siswa</span>
                                        <span>Kelas</span>
                                        <span>Keluhan Fisik</span>
                                        <span>Penanganan PMR</span>
                                        <span class="text-right">Status</span>
                                    </div>
                                    <div class="grid grid-cols-5 p-2.5 border-b border-slate-100 items-center">
                                        <strong class="text-slate-900">Rendi Kurnia</strong>
                                        <span class="text-slate-600">XI-TKJ 2</span>
                                        <span class="text-slate-700">Luka Lecet Olahraga</span>
                                        <span class="text-slate-500">Pembersihan Antiseptik & Perban</span>
                                        <span class="text-right"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Kembali ke Kelas</span></span>
                                    </div>
                                    <div class="grid grid-cols-5 p-2.5 items-center">
                                        <strong class="text-slate-900">Nabila Zahra</strong>
                                        <span class="text-slate-600">X-MM 1</span>
                                        <span class="text-slate-700">Demam & Pusing</span>
                                        <span class="text-slate-500">Kompres Hangat & Paracetamol</span>
                                        <span class="text-right"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Istirahat di Bed</span></span>
                                    </div>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'hb',
                        title: 'Monitoring Siswi Anemia untuk Tablet Tambah Darah (TTD)',
                        url: 'https://akusehat.neuracakrawira.asia/admin/hb',
                        html: `
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 mb-3">
                                <h4 class="font-bold text-sm text-slate-900 mb-1">Daftar Distribusi Tablet Tambah Darah (TTD) UKS</h4>
                                <p class="text-xs text-slate-500">Memastikan siswi yang terindikasi anemia mendapatkan suplemen zat besi rutin setiap minggu.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-xs">
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="font-bold text-slate-800 mb-1">Siswi Terindikasi Anemia</div>
                                    <div class="text-lg font-black text-rose-600">8 Siswi (Hb &lt; 12.0)</div>
                                    <p class="text-[10px] text-slate-500 mt-1">Diprioritaskan untuk pemantauan nutrisi dan konsumsi TTD mingguan.</p>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="font-bold text-slate-800 mb-1">Jadwal Minum Bersama</div>
                                    <div class="text-emerald-700 font-bold">Setiap Jumat Pagi di UKS</div>
                                    <p class="text-[10px] text-slate-500 mt-1">Dikoordinasikan oleh pengurus PMR bersama pembina UKS.</p>
                                </div>
                            </div>
                        `
                    }
                ]
            },

            monitor: {
                name: 'Health Monitor',
                badgeText: 'Health Monitor (Guru Penjas / Tim Skrining Fisik)',
                username: 'Guru Penjaskes / Tim Skrining',
                instansi: 'SMKS TELEKOMUNIKASI TELESANDI BEKASI',
                sidebarLinks: [
                    { id: 'dashboard', icon: 'fa-chart-pie', label: 'Dashboard Skrining Fisik', active: true },
                    { id: 'kesehatan', icon: 'fa-notes-medical', label: 'Input Antropometri (TB/BB)' },
                    { id: 'hb', icon: 'fa-droplet', label: 'Input Tes Rapid Hemoglobin (Hb)' }
                ],
                screens: [
                    {
                        id: 'dashboard',
                        title: 'Dashboard Skrining Antropometri & Hb Berkala',
                        url: 'https://akusehat.neuracakrawira.asia/dashboard',
                        html: `
                            <div class="rounded-2xl bg-gradient-to-r from-sky-950 via-slate-900 to-slate-950 p-4 sm:p-5 text-white shadow-md border border-sky-800/40 mb-4">
                                <h3 class="text-base sm:text-lg font-extrabold text-white">Dashboard Guru Penjas & Tim Skrining Fisik</h3>
                                <p class="text-xs text-sky-100/80">Entri massal pengukuran tinggi & berat badan siswa serta skrining hemoglobin berkala.</p>
                            </div>
                            <div class="grid grid-cols-3 gap-3 mb-4">
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Siswa Diperiksa Hari Ini</div>
                                    <div class="text-xl font-black text-slate-900 mt-1">28 Siswa</div>
                                    <div class="text-[9px] text-emerald-700 font-bold mt-0.5">Kelas X-TKJ (Praktek Penjas)</div>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Rata-Rata IMT Normal</div>
                                    <div class="text-xl font-black text-emerald-700 mt-1">77% Normal</div>
                                    <div class="text-[9px] text-slate-400 mt-0.5">Standar Baku Kemenkes RI</div>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-200">
                                    <div class="text-[10px] text-slate-500 uppercase font-semibold">Skrining Hb Berjalan</div>
                                    <div class="text-xl font-black text-rose-600 mt-1">100% Siswi</div>
                                    <div class="text-[9px] text-rose-500 font-bold mt-0.5">Target Bebas Anemia</div>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'kesehatan',
                        title: 'Form Entri Cepat Penimbangan Antropometri (TB / BB -> IMT Otomatis)',
                        url: 'https://akusehat.neuracakrawira.asia/admin/kesehatan/create',
                        html: `
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 mb-3">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-bold text-sm text-slate-900">Input Cepat Antropometri Fisik Siswa</h4>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Kalkulasi Otomatis IMT Aktif</span>
                                </div>
                                <div class="grid grid-cols-4 gap-2.5 text-xs">
                                    <div>
                                        <label class="text-[10px] text-slate-500 font-semibold">Pilih Siswa</label>
                                        <div class="p-1.5 bg-slate-100 rounded border border-slate-300 font-bold">Fajar Nugraha (X-RPL)</div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-500 font-semibold">Tinggi Badan (cm)</label>
                                        <div class="p-1.5 bg-white rounded border border-emerald-500 font-mono font-bold text-emerald-800">172 cm</div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-500 font-semibold">Berat Badan (kg)</label>
                                        <div class="p-1.5 bg-white rounded border border-emerald-500 font-mono font-bold text-emerald-800">62 kg</div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-500 font-semibold">Kalkulasi IMT Langsung</label>
                                        <div class="p-1.5 bg-emerald-100 rounded border border-emerald-300 font-bold text-emerald-900">20.95 (Normal)</div>
                                    </div>
                                </div>
                                <div class="mt-3 text-right">
                                    <button class="px-3 py-1.5 rounded-lg bg-emerald-700 text-white font-bold text-xs shadow-xs"><i class="fas fa-save mr-1"></i> Simpan Hasil Pengukuran</button>
                                </div>
                            </div>
                        `
                    },
                    {
                        id: 'hb',
                        title: 'Input Hasil Rapid Test Hemoglobin (Hb Bebas Anemia)',
                        url: 'https://akusehat.neuracakrawira.asia/admin/hb/create',
                        html: `
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200 mb-3">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-bold text-sm text-slate-900">Entri Hasil Rapid Test Hb Siswi</h4>
                                    <span class="text-[10px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200">Skrining Anemia Cepat</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2.5 text-xs">
                                    <div>
                                        <label class="text-[10px] text-slate-500 font-semibold">Pilih Siswi</label>
                                        <div class="p-1.5 bg-slate-100 rounded border border-slate-300 font-bold">Dewi Sartika (X-RPL 1)</div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-500 font-semibold">Kadar Hb Terukur (g/dL)</label>
                                        <div class="p-1.5 bg-white rounded border border-emerald-500 font-mono font-bold text-emerald-800">13.4 g/dL</div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] text-slate-500 font-semibold">Klasifikasi Medis Langsung</label>
                                        <div class="p-1.5 bg-emerald-100 rounded border border-emerald-300 font-bold text-emerald-900">Normal (Aman)</div>
                                    </div>
                                </div>
                                <div class="mt-3 text-right">
                                    <button class="px-3 py-1.5 rounded-lg bg-emerald-700 text-white font-bold text-xs shadow-xs"><i class="fas fa-save mr-1"></i> Simpan Catatan Hb</button>
                                </div>
                            </div>
                        `
                    }
                ]
            }
        };

        let currentActiveRole = 'admin';
        let currentActiveScreenId = 'dashboard';

        function setRoleWebDemo(roleKey) {
            currentActiveRole = roleKey;
            const data = rolesData[roleKey];
            currentActiveScreenId = data.screens[0].id;

            ['admin', 'adminpmr', 'monitor', 'consultant'].forEach(r => {
                const pill = document.getElementById('role-pill-' + r);
                if (pill) {
                    if (r === roleKey) {
                        pill.className = 'role-pill px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 flex items-center gap-2 bg-emerald-600 text-white shadow-sm border border-emerald-500';
                    } else {
                        pill.className = 'role-pill px-3.5 py-2 rounded-xl text-xs font-extrabold transition-all duration-200 flex items-center gap-2 bg-slate-800 text-slate-300 hover:text-white border border-slate-700';
                    }
                }
            });

            document.getElementById('web-active-role-badge').innerText = data.name;
            document.getElementById('sidebar-role-label').innerText = data.name;
            document.getElementById('sidebar-username').innerText = data.username;

            renderSidebarLinks();
            renderActiveScreen();
        }

        function setWebScreenById(screenId) {
            const data = rolesData[currentActiveRole];
            const found = data.screens.find(s => s.id === screenId);
            if (found) {
                currentActiveScreenId = screenId;
                renderSidebarLinks();
                renderActiveScreen();
            }
        }

        function renderSidebarLinks() {
            const data = rolesData[currentActiveRole];
            const container = document.getElementById('sidebar-links-container');
            container.innerHTML = '';

            data.sidebarLinks.forEach((link) => {
                const isActive = (link.id === currentActiveScreenId);
                const a = document.createElement('div');
                a.className = `group flex items-center gap-2.5 px-3 py-2 rounded-xl cursor-pointer transition-all duration-200 ${
                    isActive 
                    ? 'bg-gradient-to-r from-emerald-600 to-emerald-700 text-white font-bold shadow-md shadow-emerald-950/40' 
                    : 'text-slate-300 hover:bg-white/10 hover:text-white'
                }`;
                a.onclick = () => {
                    setWebScreenById(link.id);
                };
                a.innerHTML = `
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-xs ${isActive ? 'bg-white/20 text-white' : 'bg-white/5 text-emerald-400'}">
                        <i class="fas ${link.icon}"></i>
                    </div>
                    <span class="truncate">${link.label}</span>
                `;
                container.appendChild(a);
            });
        }

        function renderActiveScreen() {
            const data = rolesData[currentActiveRole];
            const screen = data.screens.find(s => s.id === currentActiveScreenId) || data.screens[0];
            currentActiveScreenId = screen.id;

            document.getElementById('web-url-bar').innerText = screen.url;
            document.getElementById('current-screen-title').innerText = screen.title;
            document.getElementById('web-page-content').innerHTML = screen.html;
        }

        function nextWebScreen() {
            const data = rolesData[currentActiveRole];
            const currentIndex = data.screens.findIndex(s => s.id === currentActiveScreenId);
            const nextIndex = (currentIndex + 1) % data.screens.length;
            currentActiveScreenId = data.screens[nextIndex].id;
            renderSidebarLinks();
            renderActiveScreen();
        }

        function prevWebScreen() {
            const data = rolesData[currentActiveRole];
            const currentIndex = data.screens.findIndex(s => s.id === currentActiveScreenId);
            const prevIndex = (currentIndex - 1 + data.screens.length) % data.screens.length;
            currentActiveScreenId = data.screens[prevIndex].id;
            renderSidebarLinks();
            renderActiveScreen();
        }

        document.addEventListener('DOMContentLoaded', () => {
            setRoleWebDemo('admin');
        });

        function setMobileScreen(step) {
            for (let i = 1; i <= 4; i++) {
                const screen = document.getElementById('mobile-screen-' + i);
                const stepCard = document.getElementById('step-mobile-' + i);
                if (screen) screen.classList.add('hidden');
                if (stepCard) {
                    stepCard.className = 'step-card-mobile p-4 rounded-2xl bg-slate-800/40 border border-slate-700/80 cursor-pointer transition-all duration-200 hover:bg-slate-800/70';
                    const badge = stepCard.querySelector('span.text-\\[10px\\]');
                    if (badge) {
                        badge.className = 'text-[10px] font-bold text-slate-500';
                        badge.innerText = 'Klik Tayangkan';
                    }
                }
            }

            const activeScreen = document.getElementById('mobile-screen-' + step);
            const activeCard = document.getElementById('step-mobile-' + step);
            if (activeScreen) activeScreen.classList.remove('hidden');
            if (activeCard) {
                activeCard.className = 'step-card-mobile p-4 rounded-2xl bg-slate-800/90 border-2 border-emerald-500 cursor-pointer transition-all duration-200 hover:bg-slate-800';
                const badge = activeCard.querySelector('span.text-\\[10px\\]');
                if (badge) {
                    badge.className = 'text-[10px] font-bold text-emerald-400';
                    badge.innerText = 'Aktif';
                }
            }

            const indicator = document.getElementById('mobile-step-indicator');
            if (indicator) indicator.innerText = `Layar ${step} dari 4`;
        }

        let videoRole = 'admin';
        let isVideoPlaying = false;
        let videoSeconds = 0;
        const totalVideoSeconds = 60;
        let videoTimerInterval = null;

        const videoRoleScenarios = {
            admin: [
                {
                    start: 0, end: 15,
                    title: 'Dashboard Admin Instansi: Analitik & Status Gizi Sekolah',
                    cursorTarget: { x: '82%', y: '16%' },
                    cursorText: 'Catat Skrining',
                    render: () => (rolesData.admin.screens.find(s => s.id === 'dashboard') || rolesData.admin.screens[0]).html
                },
                {
                    start: 15, end: 30,
                    title: 'Skrining Antropometri: Input TB/BB & Kalkulasi Otomatis IMT',
                    cursorTarget: { x: '75%', y: '65%' },
                    cursorText: 'Simpan Data',
                    render: () => (rolesData.admin.screens.find(s => s.id === 'kesehatan') || rolesData.admin.screens[1]).html
                },
                {
                    start: 30, end: 45,
                    title: 'Pusat Hemoglobin: Klasifikasi Anemia & Intervensi Medis',
                    cursorTarget: { x: '80%', y: '60%' },
                    cursorText: 'Rujuk Anemia',
                    render: () => (rolesData.admin.screens.find(s => s.id === 'hb') || rolesData.admin.screens[2]).html
                },
                {
                    start: 45, end: 60,
                    title: 'Langganan & Kuota: Aktivasi Midtrans Snap & Struk PDF Resmi (5k/siswa/thn)',
                    cursorTarget: { x: '80%', y: '80%' },
                    cursorText: 'Unduh Struk PDF',
                    render: () => (rolesData.admin.screens.find(s => s.id === 'billing') || rolesData.admin.screens[6]).html
                }
            ],
            adminpmr: [
                {
                    start: 0, end: 20,
                    title: 'Sirkulasi Pita UKS: Validasi Peminjaman & Deteksi Keterlambatan',
                    cursorTarget: { x: '82%', y: '60%' },
                    cursorText: 'Peringatan Terlambat',
                    render: () => (rolesData.adminpmr.screens.find(s => s.id === 'femhealth') || rolesData.adminpmr.screens[0]).html
                },
                {
                    start: 20, end: 40,
                    title: 'Pendaftaran Anggota: Registrasi Siswa & Aktivasi Akun Mobile',
                    cursorTarget: { x: '85%', y: '25%' },
                    cursorText: 'Tambah Siswa',
                    render: () => (rolesData.adminpmr.screens.find(s => s.id === 'users') || rolesData.adminpmr.screens[1]).html
                },
                {
                    start: 40, end: 60,
                    title: 'Log Harian UKS: Catatan Pasien Berobat & Distribusi Obat',
                    cursorTarget: { x: '70%', y: '50%' },
                    cursorText: 'Buku Tamu UKS',
                    render: () => (rolesData.adminpmr.screens.find(s => s.id === 'kesehatan') || rolesData.adminpmr.screens[2]).html
                }
            ],
            monitor: [
                {
                    start: 0, end: 20,
                    title: 'Dashboard Health Monitor: Skrining Antropometri & Hb Berkala',
                    cursorTarget: { x: '25%', y: '45%' },
                    cursorText: 'Skrining Fisik',
                    render: () => (rolesData.monitor.screens.find(s => s.id === 'dashboard') || rolesData.monitor.screens[0]).html
                },
                {
                    start: 20, end: 40,
                    title: 'Entri Cepat Antropometri: Input Hasil Timbangan & IMT Baku Kemenkes',
                    cursorTarget: { x: '78%', y: '75%' },
                    cursorText: 'Simpan Hasil',
                    render: () => (rolesData.monitor.screens.find(s => s.id === 'kesehatan') || rolesData.monitor.screens[1]).html
                },
                {
                    start: 40, end: 60,
                    title: 'Input Hasil Rapid Test Hemoglobin: Skrining Bebas Anemia',
                    cursorTarget: { x: '82%', y: '60%' },
                    cursorText: 'Catat Rapid Hb',
                    render: () => (rolesData.monitor.screens.find(s => s.id === 'hb') || rolesData.monitor.screens[2]).html
                }
            ],
            consultant: [
                {
                    start: 0, end: 20,
                    title: 'Dashboard Health Consultant: Pemantauan Siswa Binaan',
                    cursorTarget: { x: '35%', y: '45%' },
                    cursorText: 'Lihat Tiket Masuk',
                    render: () => (rolesData.consultant.screens.find(s => s.id === 'dashboard') || rolesData.consultant.screens[0]).html
                },
                {
                    start: 20, end: 40,
                    title: 'Konseling SafeSpace: Merespons Tiket & Jadwalkan Sesi Konseling',
                    cursorTarget: { x: '85%', y: '50%' },
                    cursorText: 'Jadwalkan Konseling',
                    render: () => (rolesData.consultant.screens.find(s => s.id === 'counseling') || rolesData.consultant.screens[1]).html
                },
                {
                    start: 40, end: 60,
                    title: 'Skrining Burnout: Analisis Tingkat Stres & Rekomendasi Bimbingan',
                    cursorTarget: { x: '60%', y: '65%' },
                    cursorText: 'Beri Bimbingan',
                    render: () => (rolesData.consultant.screens.find(s => s.id === 'burnout') || rolesData.consultant.screens[2]).html
                }
            ]
        };

        function openDemoModal(role = 'admin') {
            document.getElementById('demo-video-modal').classList.remove('hidden');
            changeVideoRole(role);
            startVideo();
        }

        function closeDemoModal() {
            document.getElementById('demo-video-modal').classList.add('hidden');
            pauseVideo();
        }

        function changeVideoRole(role) {
            videoRole = role;
            videoSeconds = 0;
            
            ['admin', 'adminpmr', 'monitor', 'consultant'].forEach(r => {
                const btn = document.getElementById('v-role-' + r);
                if (btn) {
                    if (r === role) {
                        btn.className = 'px-2.5 py-1 rounded-lg bg-emerald-600 text-white font-bold transition-colors';
                    } else {
                        btn.className = 'px-2.5 py-1 rounded-lg text-slate-400 hover:text-white transition-colors';
                    }
                }
            });

            const roleObj = rolesData[role];
            document.getElementById('v-header-role').innerText = roleObj.name;
            renderVideoPills();
            updateVideoFrame();
        }

        function renderVideoPills() {
            const container = document.getElementById('video-chapter-pills');
            container.innerHTML = '';
            const scenes = videoRoleScenarios[videoRole];

            scenes.forEach((sc, i) => {
                const btn = document.createElement('button');
                btn.id = 'v-scene-pill-' + i;
                btn.className = 'px-2 py-1 rounded bg-slate-800 text-slate-300 hover:text-white transition-colors';
                btn.innerText = (i + 1) + '. ' + sc.title.split(':')[0];
                btn.onclick = () => {
                    videoSeconds = sc.start;
                    updateVideoFrame();
                };
                container.appendChild(btn);
            });
        }

        function togglePlayVideo() {
            if (isVideoPlaying) {
                pauseVideo();
            } else {
                startVideo();
            }
        }

        function startVideo() {
            isVideoPlaying = true;
            const playIcon = document.getElementById('play-btn-icon');
            const statusText = document.getElementById('video-status-text');
            if (playIcon) {
                playIcon.classList.remove('fa-play');
                playIcon.classList.add('fa-pause');
            }
            if (statusText) statusText.innerText = 'Memutar Walkthrough...';

            clearInterval(videoTimerInterval);
            videoTimerInterval = setInterval(() => {
                videoSeconds++;
                if (videoSeconds >= totalVideoSeconds) {
                    videoSeconds = 0;
                }
                updateVideoFrame();
            }, 1000);
        }

        function pauseVideo() {
            isVideoPlaying = false;
            clearInterval(videoTimerInterval);
            const playIcon = document.getElementById('play-btn-icon');
            const statusText = document.getElementById('video-status-text');
            if (playIcon) {
                playIcon.classList.remove('fa-pause');
                playIcon.classList.add('fa-play');
            }
            if (statusText) statusText.innerText = 'Dijeda (Klik Play)';
        }

        function restartVideo() {
            videoSeconds = 0;
            updateVideoFrame();
            startVideo();
        }

        function seekVideo(event) {
            const bar = event.currentTarget;
            const rect = bar.getBoundingClientRect();
            const clickX = event.clientX - rect.left;
            const width = rect.width;
            const percent = Math.max(0, Math.min(1, clickX / width));
            videoSeconds = Math.floor(percent * totalVideoSeconds);
            updateVideoFrame();
        }

        function updateVideoFrame() {
            const m = Math.floor(videoSeconds / 60).toString().padStart(2, '0');
            const s = (videoSeconds % 60).toString().padStart(2, '0');
            const timerEl = document.getElementById('video-timer');
            if (timerEl) timerEl.innerText = `${m}:${s} / 01:00`;

            const progressEl = document.getElementById('video-progress');
            if (progressEl) progressEl.style.width = ((videoSeconds / totalVideoSeconds) * 100) + '%';

            const scenes = videoRoleScenarios[videoRole];
            const currentSceneIndex = scenes.findIndex(sc => videoSeconds >= sc.start && videoSeconds < sc.end);
            const activeScene = (currentSceneIndex !== -1) ? scenes[currentSceneIndex] : scenes[0];

            document.getElementById('v-chapter-badge').innerText = `LANGKAH ${currentSceneIndex + 1}`;
            document.getElementById('v-chapter-title').innerText = activeScene.title;

            scenes.forEach((_, idx) => {
                const pill = document.getElementById('v-scene-pill-' + idx);
                if (pill) {
                    if (idx === currentSceneIndex) {
                        pill.className = 'px-2 py-1 rounded bg-emerald-600 text-white font-bold';
                    } else {
                        pill.className = 'px-2 py-1 rounded bg-slate-800 text-slate-300 hover:text-white';
                    }
                }
            });

            const bodyEl = document.getElementById('v-simulated-body');
            if (bodyEl) bodyEl.innerHTML = activeScene.render();

            const cursor = document.getElementById('simulated-cursor');
            const tooltip = document.getElementById('cursor-tooltip');
            if (cursor && activeScene.cursorTarget) {
                cursor.classList.remove('hidden');
                cursor.style.left = activeScene.cursorTarget.x;
                cursor.style.top = activeScene.cursorTarget.y;
                if (tooltip) tooltip.innerText = activeScene.cursorText;

                cursor.classList.remove('animate-click');
                void cursor.offsetWidth;
                cursor.classList.add('animate-click');
            }
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeDemoModal();
        });
    </script>
</body>
</html>
