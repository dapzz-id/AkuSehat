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
        .animate-float {
            animation: floatSlow 6s ease-in-out infinite;
        }
        .animate-pulse-glow {
            animation: pulseGlow 4s ease-in-out infinite;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(226, 232, 240, 0.9);
        }
        .glass-panel-dark {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(51, 65, 85, 0.8);
        }
        .mockup-phone-shadow {
            box-shadow: 0 25px 60px -15px rgba(16, 185, 129, 0.25), 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .mockup-laptop-shadow {
            box-shadow: 0 30px 70px -20px rgba(15, 23, 42, 0.25), 0 12px 30px -10px rgba(16, 185, 129, 0.15);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-emerald-500 selection:text-white flex flex-col min-h-screen">

    <!-- ============================================================== -->
    <!-- TOP NAVIGATION BAR (Sticky with Blur & Mobile Drawer)          -->
    <!-- ============================================================== -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all duration-300" id="main-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-18 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('src/aku_sehat_icon.png') }}" alt="Aku Sehat" class="w-10 h-10 object-contain group-hover:scale-105 transition-transform duration-300">
                <div class="flex flex-col">
                    <span class="font-extrabold text-slate-900 text-lg leading-tight tracking-tight flex items-center gap-1.5">
                        Aku Sehat
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">V1</span>
                    </span>
                    <span class="text-[10px] text-emerald-700 font-medium tracking-wide">Neura Cakrawira Asia</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden lg:flex items-center gap-7 text-sm font-semibold text-slate-600">
                <a href="#tentang" class="hover:text-emerald-700 transition-colors">Tentang</a>
                <a href="#fitur" class="hover:text-emerald-700 transition-colors">Fitur Unggulan</a>
                <a href="#demo-section" class="flex items-center gap-1.5 text-emerald-700 hover:text-emerald-800 transition-colors font-bold">
                    <span>Demo Interaktif</span>
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                </a>
                <a href="#ekosistem" class="hover:text-emerald-700 transition-colors">Peran & Akses</a>
                <a href="#paket" class="hover:text-emerald-700 transition-colors">Paket & Lisensi</a>
                <a href="#unduh" class="hover:text-emerald-700 transition-colors">Unduh App</a>
            </nav>

            <!-- Action Buttons (Login / Dashboard / Demo) -->
            <div class="hidden sm:flex items-center gap-3">
                <button type="button" onclick="openDemoModal()" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-emerald-800 bg-slate-100 hover:bg-emerald-50 border border-slate-200 transition-all">
                    <i class="fas fa-play text-emerald-800 text-[10px]"></i>
                    <span>Tonton Video</span>
                </button>

                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-800 hover:bg-emerald-900 shadow-sm hover:shadow-md transition-all">
                        <i class="fas fa-columns"></i>
                        <span>Buka Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-800 hover:bg-emerald-900 shadow-sm hover:shadow-md transition-all">
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
                <a href="#ekosistem" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-800">Peran & Akses</a>
                <a href="#paket" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-800">Paket & Lisensi</a>
                <a href="#unduh" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-800">Unduh Aplikasi Mobile</a>
                <div class="pt-2 flex flex-col gap-2">
                    <button type="button" onclick="closeMobileMenu(); openDemoModal();" class="w-full py-2.5 rounded-xl text-xs font-bold text-center text-slate-700 bg-slate-100 border border-slate-200">
                        <i class="fas fa-play text-emerald-800 mr-1.5"></i> Tonton Video Walkthrough
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
        <!-- Ambient Glow Circles -->
        <div class="absolute top-10 left-1/2 -translate-x-1/2 w-[700px] h-[350px] bg-emerald-400/15 rounded-full blur-3xl pointer-events-none -z-10 animate-pulse-glow"></div>
        <div class="absolute top-40 right-10 w-[350px] h-[350px] bg-sky-400/15 rounded-full blur-3xl pointer-events-none -z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">
                <!-- Pill Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100/80 text-emerald-800 border border-emerald-200 mb-6 shadow-xs animate-float">
                    <i class="fas fa-heart-pulse text-emerald-800"></i>
                    <span>Ekosistem Digitalisasi UKS & Kesehatan Sekolah Terpadu</span>
                </div>

                <!-- Main Hero Headline -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.15] mb-6">
                    Satu Platform Terintegrasi untuk <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-800 via-teal-700 to-sky-700">Kesehatan Siswa & Sekolah</span> yang Lebih Terukur
                </h1>

                <!-- Subheadline -->
                <p class="text-base sm:text-lg text-slate-600 max-w-3xl mx-auto mb-9 leading-relaxed font-normal">
                    <strong>Aku Sehat</strong> menjembatani <strong>Web Dashboard Manajemen UKS</strong> dengan <strong>Aplikasi Mobile Siswa</strong>. Dari skrining antropometri IMT otomatis, pemantauan kadar Hemoglobin (Hb) bebas anemia, rekap siklus menstruasi siswi, hingga pelacakan vitalitas harian dalam satu ekosistem yang aman dan terenkripsi.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center justify-center gap-3.5 mb-12">
                    <a href="#demo-section" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl text-sm font-bold text-white bg-emerald-800 hover:bg-emerald-900 shadow-lg shadow-emerald-700/20 hover:shadow-xl hover:shadow-emerald-700/30 transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-gamepad text-base"></i>
                        <span>Coba Demo Interaktif</span>
                    </a>

                    <button type="button" onclick="openDemoModal()" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-800 bg-white hover:bg-slate-50 border border-slate-300 shadow-sm hover:shadow transition-all transform hover:-translate-y-0.5">
                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">
                            <i class="fas fa-play ml-0.5"></i>
                        </span>
                        <span>Tonton Video Walkthrough</span>
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

                <!-- Trust Badges Bar -->
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
    <!-- SECTION 1: APA ITU AKU SEHAT? (Masalah, Solusi & Nilai Utama)   -->
    <!-- ============================================================== -->
    <section id="tentang" class="py-16 sm:py-20 bg-white border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                
                <!-- Left Column: Explanation -->
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
                                <h4 class="text-sm font-bold text-slate-900">Privasi Terjaga & Bebas Canggung</h4>
                                <p class="text-xs text-slate-500">Pencatatan siklus haid dan konsultasi kesehatan dilakukan secara aman di smartphone masing-masing siswa.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Visual Diagram / Realistic Card -->
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

                        <!-- Two Pillars -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Pillar 1: Web Portal -->
                            <div class="p-4 rounded-2xl bg-slate-800/70 border border-slate-700/80">
                                <div class="flex items-center gap-2 mb-2 text-emerald-400 font-bold text-xs uppercase tracking-wider">
                                    <i class="fas fa-desktop"></i>
                                    <span>Web Dashboard</span>
                                </div>
                                <h4 class="text-sm font-extrabold text-white mb-2">Untuk Admin Sekolah & UKS</h4>
                                <ul class="text-xs text-slate-300 space-y-1.5 list-disc list-inside">
                                    <li>Rekap Divisi/Kelas Terpadu</li>
                                    <li>Skrining Klinis Massal</li>
                                    <li>Penerbitan Laporan PDF Resmi</li>
                                    <li>Manajemen Lisensi & Billing</li>
                                </ul>
                            </div>

                            <!-- Pillar 2: Mobile App -->
                            <div class="p-4 rounded-2xl bg-slate-800/70 border border-slate-700/80">
                                <div class="flex items-center gap-2 mb-2 text-sky-400 font-bold text-xs uppercase tracking-wider">
                                    <i class="fas fa-mobile-screen"></i>
                                    <span>Mobile Android</span>
                                </div>
                                <h4 class="text-sm font-extrabold text-white mb-2">Untuk Siswa & Konselor</h4>
                                <ul class="text-xs text-slate-300 space-y-1.5 list-disc list-inside">
                                    <li>Pencatatan Mandiri IMT & Hb</li>
                                    <li>Log Siklus Haid & Keluhan</li>
                                    <li>Run Tracker & Vitalitas Tubuh</li>
                                    <li>Pinjam Perlengkapan UKS</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Bottom Pill -->
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
    <!-- ============================================================== -->
    <section id="demo-section" class="py-16 sm:py-24 bg-slate-900 text-white relative overflow-hidden">
        <!-- Background Grids & Ambient Light -->
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#34d399_1px,transparent_1px)] [background-size:24px_24px] pointer-events-none"></div>
        <div class="absolute top-1/4 left-10 w-96 h-96 bg-emerald-600/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-sky-600/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Section Header -->
            <div class="text-center max-w-3xl mx-auto mb-10">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-3">
                    <i class="fas fa-clapperboard"></i>
                    <span>SIMULASI REALISTIS TANPA MEMBERATKAN PERANGKAT</span>
                </div>
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight mb-4">
                    Lihat Cara Kerja <span class="text-emerald-400">Aplikasi Mobile & Web</span>
                </h2>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-normal">
                    Jelajahi alur kerja nyata berdasarkan antarmuka dan fitur aplikasi Aku Sehat saat ini. Sangat ringan, menggunakan simulasi interaktif murni 60 FPS tanpa menguras kuota atau memori smartphone Anda.
                </p>
            </div>

            <!-- Demo Mode Selector (Mobile vs Web Switcher + Video Button) -->
            <div class="flex flex-wrap items-center justify-center gap-3 mb-10">
                <div class="inline-flex p-1 rounded-2xl bg-slate-800/90 border border-slate-700/80 shadow-inner">
                    <button type="button" id="tab-btn-mobile" onclick="switchDemoTab('mobile')" class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 bg-emerald-700 text-white shadow">
                        <i class="fas fa-mobile-screen"></i>
                        <span>Aplikasi Mobile (Android)</span>
                    </button>
                    <button type="button" id="tab-btn-web" onclick="switchDemoTab('web')" class="px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 text-slate-300 hover:text-white">
                        <i class="fas fa-laptop"></i>
                        <span>Web Dashboard (Portal UKS)</span>
                    </button>
                </div>

                <button type="button" onclick="openDemoModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-teal-700 to-emerald-700 hover:from-teal-800 hover:to-emerald-800 border border-emerald-600/40 shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <span class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-[10px]">
                        <i class="fas fa-play"></i>
                    </span>
                    <span>Tonton Video Walkthrough</span>
                </button>
            </div>

            <!-- ======================================================== -->
            <!-- TAB 1: MOBILE APP SHOWCASE (Phone Frame + Real Screens)   -->
            <!-- ======================================================== -->
            <div id="demo-content-mobile" class="transition-all duration-300">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    
                    <!-- Left: Interactive Smartphone Mockup -->
                    <div class="lg:col-span-5 flex justify-center">
                        <div class="relative w-[290px] sm:w-[320px] rounded-[48px] p-3.5 bg-slate-800 border-4 border-slate-700 shadow-2xl mockup-phone-shadow overflow-hidden">
                            <!-- Phone Top Notch / Dynamic Island -->
                            <div class="absolute top-5 left-1/2 -translate-x-1/2 w-28 h-5 bg-slate-900 rounded-full z-30 flex items-center justify-center">
                                <div class="w-2.5 h-2.5 rounded-full bg-slate-800 mr-2"></div>
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                            </div>

                            <!-- Screen Viewport -->
                            <div class="relative w-full aspect-[9/19] rounded-[38px] bg-slate-950 overflow-hidden text-slate-900 flex flex-col justify-between border border-slate-800 select-none">
                                
                                <!-- Screen 1: Dashboard Member & IMT -->
                                <div id="mobile-screen-1" class="mobile-screen-view w-full h-full flex flex-col bg-slate-900 text-white p-4 pt-10">
                                    <!-- Status Bar -->
                                    <div class="flex items-center justify-between text-[11px] text-slate-400 mb-3 px-1">
                                        <span>08:45</span>
                                        <div class="flex items-center gap-1.5 text-[10px]">
                                            <i class="fas fa-wifi"></i>
                                            <i class="fas fa-battery-full text-emerald-400"></i>
                                        </div>
                                    </div>
                                    <!-- User Header -->
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

                                    <!-- BMI Card -->
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

                                    <!-- Mini Quick Grid -->
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

                                    <!-- Bottom Action Mock -->
                                    <div class="mt-auto p-2.5 rounded-xl bg-emerald-600 text-center font-bold text-xs text-white shadow-sm">
                                        <i class="fas fa-heart-pulse mr-1"></i> Skrining Mandiri
                                    </div>
                                </div>

                                <!-- Screen 2: Run & Vitality Tracker (Hidden by default, shown via step) -->
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
                                    
                                    <!-- Simulated Route Canvas / Card -->
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

                                    <div class="p-3 rounded-xl bg-slate-800/80 border border-slate-700 mb-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs">
                                                <i class="fas fa-moon"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-bold text-white">Waktu Tidur</div>
                                                <div class="text-[10px] text-slate-400">Kualitas Optimal</div>
                                            </div>
                                        </div>
                                        <span class="text-xs font-bold text-indigo-300">7 Jam 45m</span>
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

                                    <div class="p-3 rounded-xl bg-slate-800/80 border border-slate-700 mb-2">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-bold text-white">Siklus Menstruasi</span>
                                            <span class="text-[10px] text-rose-300">Fase Folikuler</span>
                                        </div>
                                        <div class="w-full bg-slate-700 h-2 rounded-full overflow-hidden mt-1.5">
                                            <div class="bg-rose-500 h-full w-2/3 rounded-full"></div>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-1.5 flex justify-between">
                                            <span>Hari ke-14 siklus</span>
                                            <span>Prediksi Haid: 14 hari lagi</span>
                                        </div>
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

                                    <div class="p-3 rounded-xl bg-slate-900/70 border border-slate-800 mb-2">
                                        <div class="text-[10px] text-slate-400 font-mono mb-1">REMOTE_THEME_HUB</div>
                                        <div class="text-xs font-bold text-white flex items-center justify-between">
                                            <span>Theme Switcher</span>
                                            <span class="text-cyan-400 text-[10px]">On-Demand</span>
                                        </div>
                                    </div>

                                    <div class="mt-auto p-2.5 rounded-xl bg-cyan-600/30 border border-cyan-500/50 text-center font-mono text-xs text-cyan-300">
                                        Theme: Atlas Neo Cyber
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Right: Interactive Feature Highlights & Screen Selector -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">
                                <i class="fas fa-hand-pointer mr-1"></i> Klik untuk melihat layar:
                            </span>
                            <span class="text-xs text-slate-400" id="mobile-step-indicator">Layar 1 dari 4</span>
                        </div>

                        <!-- Step Card 1 -->
                        <div onclick="setMobileScreen(1)" id="step-mobile-1" class="step-card-mobile p-4 rounded-2xl bg-slate-800/90 border-2 border-emerald-500 cursor-pointer transition-all duration-200 hover:bg-slate-800">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                    01
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-extrabold text-white">Dashboard Siswa & Kalkulasi IMT Instan</h4>
                                        <span class="text-[10px] font-bold text-emerald-400">Aktif</span>
                                    </div>
                                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                                        Siswa melihat tinggi badan, berat badan, serta klasifikasi Indeks Massa Tubuh (Normal, Kurus, Gemuk, Obesitas) berdasarkan standar baku Kementerian Kesehatan RI.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step Card 2 -->
                        <div onclick="setMobileScreen(2)" id="step-mobile-2" class="step-card-mobile p-4 rounded-2xl bg-slate-800/40 border border-slate-700/80 cursor-pointer transition-all duration-200 hover:bg-slate-800/70">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                    02
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-extrabold text-white">Pelacak Lari GPS & Vitalitas Harian</h4>
                                        <span class="text-[10px] font-bold text-slate-500">Klik Tayangkan</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                        Sensor GPS bawaan smartphone menghitung jarak tempuh lari/jalan, durasi, estimasi kalori terbakar, asupan air harian, serta jam tidur berkualitas.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step Card 3 -->
                        <div onclick="setMobileScreen(3)" id="step-mobile-3" class="step-card-mobile p-4 rounded-2xl bg-slate-800/40 border border-slate-700/80 cursor-pointer transition-all duration-200 hover:bg-slate-800/70">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                    03
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-extrabold text-white">Pemantauan Hemoglobin & Siklus Menstruasi</h4>
                                        <span class="text-[10px] font-bold text-slate-500">Klik Tayangkan</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                        Riwayat pemeriksaan Hb untuk deteksi dini anemia siswi, kalender siklus menstruasi otomatis, dan pencatatan gejala bulanan dengan privasi terenkripsi.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step Card 4 -->
                        <div onclick="setMobileScreen(4)" id="step-mobile-4" class="step-card-mobile p-4 rounded-2xl bg-slate-800/40 border border-slate-700/80 cursor-pointer transition-all duration-200 hover:bg-slate-800/70">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                    04
                                </div>
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

            <!-- ======================================================== -->
            <!-- TAB 2: WEB DASHBOARD SHOWCASE (Laptop Frame + Real UI)    -->
            <!-- ======================================================== -->
            <div id="demo-content-web" class="hidden transition-all duration-300">
                <div class="max-w-5xl mx-auto">
                    <!-- Browser / Laptop Mockup Window -->
                    <div class="rounded-3xl bg-slate-800 border border-slate-700/90 shadow-2xl mockup-laptop-shadow overflow-hidden">
                        <!-- Browser Window Chrome -->
                        <div class="px-4 py-3 bg-slate-900 border-b border-slate-700/80 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-rose-500/80"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-500/80"></div>
                                <div class="w-3 h-3 rounded-full bg-emerald-500/80"></div>
                            </div>
                            <!-- Address Bar -->
                            <div class="flex items-center gap-2 px-4 py-1 rounded-xl bg-slate-800 text-slate-400 text-xs font-mono w-72 sm:w-96 truncate border border-slate-700/60">
                                <i class="fas fa-lock text-emerald-400 text-[10px]"></i>
                                <span class="truncate">https://akusehat.neuracakrawira.asia/dashboard</span>
                            </div>
                            <div class="text-xs text-slate-500 flex items-center gap-2">
                                <span class="hidden sm:inline text-[11px] text-emerald-400 font-semibold">Web Portal Instansi</span>
                            </div>
                        </div>

                        <!-- Browser Body Content (Real Dashboard Simulator) -->
                        <div class="bg-slate-100 text-slate-800 p-4 sm:p-6 select-none min-h-[460px]">
                            
                            <!-- Subnav Tabs for Web Demo -->
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 pb-3 mb-4">
                                <div class="flex items-center gap-1.5 overflow-x-auto text-xs font-bold">
                                    <button type="button" onclick="setWebScreen(1)" id="web-subtab-1" class="web-subtab px-3 py-1.5 rounded-lg bg-emerald-700 text-white">
                                        <i class="fas fa-chart-pie mr-1"></i> Rekap Divisi/Kelas
                                    </button>
                                    <button type="button" onclick="setWebScreen(2)" id="web-subtab-2" class="web-subtab px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-200">
                                        <i class="fas fa-weight-scale mr-1"></i> Skrining IMT Massal
                                    </button>
                                    <button type="button" onclick="setWebScreen(3)" id="web-subtab-3" class="web-subtab px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-200">
                                        <i class="fas fa-droplet mr-1"></i> Skrining Hemoglobin
                                    </button>
                                    <button type="button" onclick="setWebScreen(4)" id="web-subtab-4" class="web-subtab px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-200">
                                        <i class="fas fa-receipt mr-1"></i> Billing & Lisensi Midtrans
                                    </button>
                                </div>
                                <span class="text-[11px] text-slate-500 hidden sm:inline">Role: <strong>Admin SMKS Telekomunikasi Telesandi</strong></span>
                            </div>

                            <!-- Screen Web 1: Rekap Divisi & Status Gizi -->
                            <div id="web-screen-1" class="web-screen-view space-y-4">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-xs">
                                        <div class="text-[11px] text-slate-500">Total Anggota Siswa</div>
                                        <div class="text-xl font-black text-slate-900 mt-1">450 Siswa</div>
                                        <div class="text-[10px] text-emerald-800 font-bold mt-0.5">12 Divisi/Kelas Aktif</div>
                                    </div>
                                    <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-xs">
                                        <div class="text-[11px] text-slate-500">Gizi Normal (Ideal)</div>
                                        <div class="text-xl font-black text-emerald-800 mt-1">382 Siswa</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">84.8% dari total siswa</div>
                                    </div>
                                    <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-xs">
                                        <div class="text-[11px] text-slate-500">Perlu Perhatian (Anemia)</div>
                                        <div class="text-xl font-black text-rose-600 mt-1">8 Siswi</div>
                                        <div class="text-[10px] text-rose-500 font-bold mt-0.5">Hb &lt; 12.0 g/dL</div>
                                    </div>
                                    <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-xs">
                                        <div class="text-[11px] text-slate-500">Lisensi Aktif</div>
                                        <div class="text-xl font-black text-indigo-700 mt-1">500 Kuota</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">Aktif s/d Des 2026</div>
                                    </div>
                                </div>

                                <!-- Mini Table Mock -->
                                <div class="rounded-xl bg-white border border-slate-200 overflow-hidden shadow-xs">
                                    <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-200 font-bold text-xs text-slate-700 flex justify-between items-center">
                                        <span>Daftar Divisi / Kelas Binaan UKS</span>
                                        <button class="px-2.5 py-1 rounded text-[10px] font-bold bg-emerald-700 text-white"><i class="fas fa-file-pdf mr-1"></i> Cetak Laporan PDF</button>
                                    </div>
                                    <div class="p-3 text-xs">
                                        <div class="grid grid-cols-4 font-bold text-slate-400 pb-2 border-b border-slate-100">
                                            <span>Nama Kelas</span>
                                            <span>Jumlah Siswa</span>
                                            <span>Rata-Rata IMT</span>
                                            <span>Status Divisi</span>
                                        </div>
                                        <div class="grid grid-cols-4 py-2 border-b border-slate-100 items-center">
                                            <span class="font-bold text-slate-800">X-RPL 1</span>
                                            <span class="text-slate-600">36 Siswa</span>
                                            <span class="text-emerald-800 font-bold">21.2 (Normal)</span>
                                            <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Sehat</span></span>
                                        </div>
                                        <div class="grid grid-cols-4 py-2 border-b border-slate-100 items-center">
                                            <span class="font-bold text-slate-800">XI-TKJ 2</span>
                                            <span class="text-slate-600">35 Siswa</span>
                                            <span class="text-amber-600 font-bold">25.1 (Kelebihan)</span>
                                            <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Monitoring</span></span>
                                        </div>
                                        <div class="grid grid-cols-4 py-2 items-center">
                                            <span class="font-bold text-slate-800">XII-MM 1</span>
                                            <span class="text-slate-600">34 Siswa</span>
                                            <span class="text-emerald-800 font-bold">20.5 (Normal)</span>
                                            <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Sehat</span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Screen Web 2: Skrining Antropometri Massal -->
                            <div id="web-screen-2" class="web-screen-view hidden space-y-4">
                                <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm">Formulir Skrining Antropometri & Klinis</h4>
                                        <p class="text-xs text-slate-500">Pencatatan cepat per kelas dengan kalkulator IMT otomatis</p>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-300">Pilih Kelas: X-RPL 1</span>
                                        <span class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-700 text-white"><i class="fas fa-check mr-1"></i> Simpan Massal</span>
                                    </div>
                                </div>
                                <div class="rounded-xl bg-white border border-slate-200 p-4 text-xs">
                                    <div class="grid grid-cols-5 font-bold text-slate-500 pb-2 border-b border-slate-200">
                                        <span>Nama Siswa</span>
                                        <span>Tinggi (cm)</span>
                                        <span>Berat (kg)</span>
                                        <span>Kalkulasi IMT</span>
                                        <span>Kategori Gizi</span>
                                    </div>
                                    <div class="grid grid-cols-5 py-2.5 border-b border-slate-100 items-center font-medium">
                                        <span class="font-bold text-slate-800">Ahmad Fauzi</span>
                                        <span class="text-slate-700 font-mono">170 cm</span>
                                        <span class="text-slate-700 font-mono">60 kg</span>
                                        <span class="font-bold text-emerald-800 font-mono">20.76</span>
                                        <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Gizi Baik (Normal)</span></span>
                                    </div>
                                    <div class="grid grid-cols-5 py-2.5 border-b border-slate-100 items-center font-medium">
                                        <span class="font-bold text-slate-800">Budi Setiawan</span>
                                        <span class="text-slate-700 font-mono">165 cm</span>
                                        <span class="text-slate-700 font-mono">75 kg</span>
                                        <span class="font-bold text-amber-700 font-mono">27.55</span>
                                        <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Kelebihan BB</span></span>
                                    </div>
                                    <div class="grid grid-cols-5 py-2.5 items-center font-medium">
                                        <span class="font-bold text-slate-800">Citra Kirana</span>
                                        <span class="text-slate-700 font-mono">158 cm</span>
                                        <span class="text-slate-700 font-mono">42 kg</span>
                                        <span class="font-bold text-sky-700 font-mono">16.82</span>
                                        <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-100 text-sky-800">Gizi Kurang (Kurus)</span></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Screen Web 3: Skrining Hemoglobin (Hb) -->
                            <div id="web-screen-3" class="web-screen-view hidden space-y-4">
                                <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-xs flex justify-between items-center">
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-sm">Monitoring Hemoglobin & Anemia Siswi</h4>
                                        <p class="text-xs text-slate-500">Integrasi program TTD (Tablet Tambah Darah) sekolah</p>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                        Target Rematri Bebas Anemia
                                    </span>
                                </div>
                                <div class="rounded-xl bg-white border border-slate-200 p-4 text-xs">
                                    <div class="grid grid-cols-4 font-bold text-slate-500 pb-2 border-b border-slate-200">
                                        <span>Nama Siswi</span>
                                        <span>Kadar Hb (g/dL)</span>
                                        <span>Klasifikasi Medis</span>
                                        <span>Tindakan UKS</span>
                                    </div>
                                    <div class="grid grid-cols-4 py-2.5 border-b border-slate-100 items-center">
                                        <span class="font-bold text-slate-800">Dewi Sartika</span>
                                        <span class="font-bold font-mono text-emerald-800">13.2 g/dL</span>
                                        <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Normal (Sehat)</span></span>
                                        <span class="text-slate-500">Pemberian TTD Rutin</span>
                                    </div>
                                    <div class="grid grid-cols-4 py-2.5 border-b border-slate-100 items-center">
                                        <span class="font-bold text-slate-800">Intan Permata</span>
                                        <span class="font-bold font-mono text-rose-600">10.8 g/dL</span>
                                        <span><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700">Anemia Ringan</span></span>
                                        <span class="text-rose-600 font-bold">Rujuk Konseling & Puskesmas</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Screen Web 4: Billing & Midtrans -->
                            <div id="web-screen-4" class="web-screen-view hidden space-y-4">
                                <div class="p-4 rounded-xl bg-gradient-to-r from-emerald-900 to-slate-900 text-white shadow-xs flex justify-between items-center">
                                    <div>
                                        <h4 class="font-bold text-white text-sm">Billing Langganan & Multi-Paket Kuota</h4>
                                        <p class="text-xs text-emerald-200">Pembayaran instan Midtrans Snap (QRIS, VA Bank) & Transfer Manual BCA</p>
                                    </div>
                                    <span class="px-2.5 py-1 rounded text-xs font-bold bg-emerald-500 text-slate-950">
                                        AKTIF & TERVERIFIKASI
                                    </span>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div class="p-3 rounded-xl bg-white border border-slate-200">
                                        <div class="text-[10px] text-slate-400">Metode Tersedia</div>
                                        <div class="text-xs font-bold text-slate-800 mt-1">QRIS, BCA VA, Mandiri, GoPay</div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-200">
                                        <div class="text-[10px] text-slate-400">Verifikasi Webhook</div>
                                        <div class="text-xs font-bold text-emerald-800 mt-1">Otomatis Aktif &lt; 5 Detik</div>
                                    </div>
                                    <div class="p-3 rounded-xl bg-white border border-slate-200">
                                        <div class="text-[10px] text-slate-400">Dokumen Resmi</div>
                                        <div class="text-xs font-bold text-indigo-700 mt-1">Struk & Invoice PDF Otomatis</div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 3: FITUR UNGGULAN (Realistis by Design & Function)    -->
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
                <!-- Card 1 -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-weight-scale"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Skrining Antropometri & IMT</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pemeriksaan berkala tinggi badan, berat badan, serta status gizi (Kurus, Normal, Gemuk, Obesitas) dengan algoritma otomatis standar Kemenkes.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-droplet"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Pemantauan Hb & Bebas Anemia</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pencatatan kadar Hemoglobin (Hb) rutin, grafik tren anemia, dan peringatan dini bagi siswi yang memerlukan intervensi gizi atau suplemen darah.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Siklus Menstruasi Siswi</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pencatatan siklus haid, durasi, dan keluhan nyeri di ponsel pribadi dengan tingkat privasi tinggi sehingga siswi merasa aman dan nyaman.
                    </p>
                </div>

                <!-- Card 4 -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-person-running"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Run Tracker & Log Vitalitas</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pelacak olahraga lari berbasis GPS di aplikasi mobile, log konsumsi air minum, pemantauan kualitas tidur, dan evaluasi postur ergonomi siswa.
                    </p>
                </div>

                <!-- Card 5 -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-ribbon"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Peminjaman Pita & Fasilitas UKS</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Sistem sirkulasi peminjaman pita kesehatan UKS terdigitalisasi dengan verifikasi barcode/kode cepat, notifikasi batas waktu, dan deteksi denda otomatis.
                    </p>
                </div>

                <!-- Card 6 -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
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

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Role 1: Admin Instansi -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fas fa-school"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-1">Admin Instansi</h4>
                        <p class="text-xs text-slate-500 mb-3">Kepala Sekolah / Pengelola UKS</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Mengatur divisi/kelas, kuota akun siswa, billing paket lisensi, dan mengunduh laporan rekapitulasi kesehatan resmi (PDF) untuk dinas/instansi terkait.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-800">Akses: Web Portal</span>
                </div>

                <!-- Role 2: Health Consultant -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fas fa-user-doctor"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-1">Health Consultant</h4>
                        <p class="text-xs text-slate-500 mb-3">Guru BK / Konselor Medis</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Menerima pengajuan konseling dari siswa, menganalisis riwayat klinis yang memerlukan tindak lanjut khusus, serta memberikan rekomendasi kesehatan.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-teal-800">Akses: Web & Mobile</span>
                </div>

                <!-- Role 3: Health Monitor -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fas fa-kit-medical"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-1">Health Monitor</h4>
                        <p class="text-xs text-slate-500 mb-3">Petugas Lapangan UKS / PMR</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Melakukan verifikasi permohonan pinjam pita UKS, validasi stok perlengkapan medis, dan meninjau keterlambatan pengembalian fasilitas.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-sky-800">Akses: Web & Mobile</span>
                </div>

                <!-- Role 4: Member (Siswa) -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-1">Siswa / Member</h4>
                        <p class="text-xs text-slate-500 mb-3">Peserta Didik / Anggota</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Melihat rekam medis pribadi, IMT, riwayat Hb, siklus haid, pelacakan lari GPS, serta mengajukan peminjaman fasilitas UKS dengan mudah via smartphone.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-indigo-800">Akses: Mobile Android</span>
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
                    <span>TRANSPARAN & MUDAH</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                    Paket Kuota Lisensi Sesuai Kapasitas Sekolah
                </h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                    Fleksibel disesuaikan dengan jumlah siswa Anda. Didukung pembayaran instan otomatis via <strong>Midtrans Snap</strong> (QRIS, VA Bank, GoPay, ShopeePay) serta Transfer Manual BCA resmi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 max-w-5xl mx-auto">
                <!-- Paket 1 -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 flex flex-col justify-between hover:shadow-lg transition-all">
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Paket Angkatan</div>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-1">+100 Siswa</h3>
                        <div class="text-2xl font-black text-slate-900 my-4">Rp 500.000 <span class="text-xs font-normal text-slate-500">/ periode</span></div>
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

                <!-- Paket 2 (Featured) -->
                <div class="p-6 rounded-3xl bg-gradient-to-b from-emerald-950 to-slate-950 text-white border-2 border-emerald-500 flex flex-col justify-between shadow-xl relative transform lg:-translate-y-2">
                    <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full text-[10px] font-black bg-emerald-700 text-white uppercase tracking-wider shadow">
                        PALING POPULER
                    </div>
                    <div>
                        <div class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">Sekolah Menengah</div>
                        <h3 class="text-xl font-extrabold text-white mb-1">+250 Siswa</h3>
                        <div class="text-3xl font-black text-white my-4">Rp 1.250.000 <span class="text-xs font-normal text-slate-400">/ periode</span></div>
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

                <!-- Paket 3 -->
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 flex flex-col justify-between hover:shadow-lg transition-all">
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Yayasan / Besar</div>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-1">+1.000 Siswa</h3>
                        <div class="text-2xl font-black text-slate-900 my-4">Rp 5.000.000 <span class="text-xs font-normal text-slate-500">/ periode</span></div>
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

            <!-- Custom Info -->
            <div class="mt-8 text-center text-xs text-slate-500">
                <i class="fas fa-circle-info text-emerald-800 mr-1"></i> Admin Instansi dapat membeli tambahan kuota satuan secara fleksibel kapan saja langsung dari menu Billing.
            </div>
        </div>
    </section>

    <!-- ============================================================== -->
    <!-- SECTION 6: UNDUH APLIKASI MOBILE (Download & Access)           -->
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
    <!-- SECTION 7: FAQ (Tanya Jawab Seputar Aplikasi)                  -->
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
                <!-- FAQ 1 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Apakah aplikasi ini memberatkan smartphone siswa?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sama sekali tidak. Aplikasi mobile Aku Sehat dikembangkan menggunakan React Native dan TypeScript yang dioptimasi secara mendalam. Tampilan grafik dan animasi menggunakan akselerasi GPU murni sehingga sangat ringan dan hemat baterai, bahkan pada ponsel spesifikasi standar.
                    </p>
                </div>

                <!-- FAQ 2 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Bagaimana keamanan data medis siswa dijamin?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Data klinis sensitif seperti catatan Hb, pemeriksaan fisik, dan riwayat kesehatan haid dienkripsi menggunakan enkripsi tingkat perbankan (AES-256) per-tenant. Setiap sekolah memiliki ruang data terisolasi yang tidak dapat diintip oleh sekolah lain.
                    </p>
                </div>

                <!-- FAQ 3 -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Bagaimana cara sekolah mendaftar dan mengundang siswa?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Admin Sekolah dapat mendaftar, memilih lisensi yang diinginkan, dan membuat kelas/divisi. Setiap sekolah mendapatkan <strong>Kode Undangan Instansi</strong> unik yang dapat dibagikan kepada siswa untuk registrasi mandiri di aplikasi mobile.
                    </p>
                </div>

                <!-- FAQ 4 -->
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
    <!-- FOOTER (Legal, Compliance & Info)                             -->
    <!-- ============================================================== -->
    <footer class="bg-slate-950 text-slate-400 text-xs py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8 pb-8 border-b border-slate-800">
                <!-- Col 1 -->
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

                <!-- Col 2 -->
                <div class="space-y-2.5">
                    <div class="font-bold text-white text-xs uppercase tracking-wider">Navigasi Cepat</div>
                    <ul class="space-y-1.5">
                        <li><a href="#tentang" class="hover:text-emerald-400 transition-colors">Tentang Aplikasi</a></li>
                        <li><a href="#demo-section" class="hover:text-emerald-400 transition-colors">Demo Interaktif</a></li>
                        <li><a href="#fitur" class="hover:text-emerald-400 transition-colors">Fitur Unggulan</a></li>
                        <li><a href="#paket" class="hover:text-emerald-400 transition-colors">Paket & Lisensi</a></li>
                        <li><a href="{{ route('patch_notes.index') }}" class="hover:text-emerald-400 transition-colors">Catatan Rilis (Patch Notes)</a></li>
                    </ul>
                </div>

                <!-- Col 3: Compliance & Legal -->
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
    <!-- MODAL: VIDEO WALKTHROUGH ON-DEMAND (Ultra-Lightweight & Fluid)  -->
    <!-- ============================================================== -->
    <div id="demo-video-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-3 sm:p-6 bg-slate-950/85 backdrop-blur-md transition-all duration-300">
        <div class="relative w-full max-w-4xl bg-slate-900 border border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden flex flex-col text-white" onclick="event.stopPropagation()">
            
            <!-- Modal Header -->
            <div class="px-5 py-3.5 bg-slate-800/90 border-b border-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">
                        <i class="fas fa-play"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-white">Video Walkthrough & Simulasi Alur Kerja</div>
                        <div class="text-[10px] text-slate-400">Aku Sehat Ekosistem — Demo Realistis Mobile & Web</div>
                    </div>
                </div>
                <button type="button" onclick="closeDemoModal()" class="w-8 h-8 rounded-full bg-slate-700 hover:bg-slate-600 text-slate-300 hover:text-white flex items-center justify-center text-xs transition-colors">
                    <i class="fas fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Video Player Stage (Lightweight Canvas / Simulated Walkthrough 60 FPS) -->
            <div class="relative aspect-video w-full bg-slate-950 flex flex-col justify-between p-4 sm:p-6 select-none overflow-hidden" id="video-stage">
                
                <!-- Scene Narrator Header -->
                <div class="flex items-center justify-between z-10">
                    <span id="video-chapter-badge" class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                        BABAK 1: PENDAHULUAN & LOGIN
                    </span>
                    <span id="video-timer" class="text-xs font-mono text-slate-400">00:00 / 01:20</span>
                </div>

                <!-- Animated Interactive Screen Canvas Container -->
                <div class="my-auto flex items-center justify-center relative py-4" id="video-screen-container">
                    
                    <!-- Scene 1: Welcome & Login Multi-Role -->
                    <div id="v-scene-1" class="v-scene transition-all duration-500 flex flex-col items-center text-center max-w-md">
                        <div class="w-16 h-16 rounded-3xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-3xl mb-4 border border-emerald-500/30 animate-float">
                            <i class="fas fa-heart-pulse"></i>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-black text-white mb-2">Selamat Datang di Aku Sehat</h3>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed mb-4">
                            Platform terintegrasi yang menghubungkan sekolah, tim medis UKS, dan seluruh siswa dalam satu ekosistem kesehatan digital.
                        </p>
                        <div class="flex gap-2 text-xs font-bold">
                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-emerald-300">Portal Web UKS</span>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-sky-300">Aplikasi Mobile Siswa</span>
                        </div>
                    </div>

                    <!-- Scene 2: Skrining Antropometri & IMT -->
                    <div id="v-scene-2" class="v-scene hidden transition-all duration-500 w-full max-w-lg bg-slate-900/90 p-5 rounded-2xl border border-slate-700">
                        <div class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">Skrining Fisik & Antropometri</div>
                        <div class="grid grid-cols-3 gap-2.5 text-center mb-3">
                            <div class="p-2.5 rounded-xl bg-slate-800 border border-slate-700">
                                <div class="text-[10px] text-slate-400">Tinggi Badan</div>
                                <div class="text-base font-bold text-white">168 cm</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-800 border border-slate-700">
                                <div class="text-[10px] text-slate-400">Berat Badan</div>
                                <div class="text-base font-bold text-white">58 kg</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-emerald-950/80 border border-emerald-700 text-emerald-300">
                                <div class="text-[10px]">IMT Otomatis</div>
                                <div class="text-base font-black">20.5 (Ideal)</div>
                            </div>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Algoritma otomatis mengklasifikasikan status gizi Kemenkes secara instan tanpa perlu tabel manual.
                        </p>
                    </div>

                    <!-- Scene 3: Hemoglobin & Pencegahan Anemia -->
                    <div id="v-scene-3" class="v-scene hidden transition-all duration-500 w-full max-w-lg bg-slate-900/90 p-5 rounded-2xl border border-slate-700">
                        <div class="text-xs font-bold text-rose-400 uppercase tracking-wider mb-2">Deteksi Dini Anemia (Kadar Hb)</div>
                        <div class="p-3 rounded-xl bg-rose-950/50 border border-rose-800/60 mb-3 flex items-center justify-between">
                            <div>
                                <div class="text-xs text-slate-300">Pemeriksaan Siswi Putri</div>
                                <div class="text-lg font-black text-white">Kadar Hb: 13.5 g/dL</div>
                            </div>
                            <span class="px-2.5 py-1 rounded text-xs font-bold bg-emerald-500 text-slate-950">Normal Sehat</span>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Mencegah penurunan konsentrasi belajar dengan pemantauan rutin dan edukasi tablet tambah darah.
                        </p>
                    </div>

                    <!-- Scene 4: Mobile Run Tracker & Peminjaman UKS -->
                    <div id="v-scene-4" class="v-scene hidden transition-all duration-500 w-full max-w-lg bg-slate-900/90 p-5 rounded-2xl border border-slate-700">
                        <div class="text-xs font-bold text-sky-400 uppercase tracking-wider mb-2">Fitur Siswa di Smartphone</div>
                        <div class="grid grid-cols-2 gap-2.5 mb-3">
                            <div class="p-2.5 rounded-xl bg-slate-800 border border-slate-700">
                                <div class="text-[10px] text-slate-400">Run Tracker GPS</div>
                                <div class="text-sm font-bold text-emerald-400">3.2 KM • 210 kkal</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-800 border border-slate-700">
                                <div class="text-[10px] text-slate-400">Pinjam Pita UKS</div>
                                <div class="text-sm font-bold text-teal-300">Approval Digital</div>
                            </div>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Mendorong gaya hidup aktif dan mempermudah siswa meminjam perlengkapan UKS tanpa birokrasi manual.
                        </p>
                    </div>

                    <!-- Scene 5: Billing & Laporan PDF -->
                    <div id="v-scene-5" class="v-scene hidden transition-all duration-500 w-full max-w-lg bg-slate-900/90 p-5 rounded-2xl border border-slate-700">
                        <div class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-2">Manajemen Billing & Ekspor PDF</div>
                        <div class="p-3 rounded-xl bg-slate-800 border border-slate-700 mb-3 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-white">Pembayaran Instan Otomatis</div>
                                <div class="text-[10px] text-slate-400">QRIS, VA Bank, GoPay & Transfer BCA</div>
                            </div>
                            <span class="px-2.5 py-1 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Laporan PDF Siap</span>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Kemudahan aktivasi kuota instansi dan unduhan rekapitulasi formal siap serah ke dinas terkait.
                        </p>
                    </div>

                </div>

                <!-- Video Controls & Chapter Navigation Bar -->
                <div class="z-10 bg-slate-900/90 backdrop-blur-md p-3 rounded-2xl border border-slate-800 space-y-2">
                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden cursor-pointer" onclick="seekVideo(event)">
                        <div id="video-progress" class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full w-0 transition-all duration-150"></div>
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <!-- Play/Pause & Nav -->
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="togglePlayVideo()" id="play-btn" class="w-8 h-8 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white flex items-center justify-center font-bold">
                                <i class="fas fa-play" id="play-btn-icon"></i>
                            </button>
                            <button type="button" onclick="restartVideo()" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 flex items-center justify-center">
                                <i class="fas fa-rotate-left"></i>
                            </button>
                            <span class="text-slate-400 text-[11px] ml-1" id="video-status-text">Putar Walkthrough</span>
                        </div>

                        <!-- Chapters Navigation Pills -->
                        <div class="hidden sm:flex items-center gap-1.5 text-[10px]">
                            <button onclick="setVideoScene(1)" class="v-pill px-2 py-1 rounded bg-slate-800 text-slate-300 hover:text-white" id="v-pill-1">1. Intro</button>
                            <button onclick="setVideoScene(2)" class="v-pill px-2 py-1 rounded bg-slate-800 text-slate-300 hover:text-white" id="v-pill-2">2. Skrining</button>
                            <button onclick="setVideoScene(3)" class="v-pill px-2 py-1 rounded bg-slate-800 text-slate-300 hover:text-white" id="v-pill-3">3. Anemia</button>
                            <button onclick="setVideoScene(4)" class="v-pill px-2 py-1 rounded bg-slate-800 text-slate-300 hover:text-white" id="v-pill-4">4. Mobile</button>
                            <button onclick="setVideoScene(5)" class="v-pill px-2 py-1 rounded bg-slate-800 text-slate-300 hover:text-white" id="v-pill-5">5. Billing</button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-5 py-3 bg-slate-800/90 border-t border-slate-700/80 flex flex-wrap items-center justify-between text-xs text-slate-400 gap-2">
                <div><i class="fas fa-feather text-emerald-400 mr-1.5"></i> Ringan & ramah smartphone: berjalan tanpa mengunduh berkas video berukuran besar.</div>
                <button type="button" onclick="closeDemoModal()" class="px-4 py-1.5 rounded-xl bg-slate-700 hover:bg-slate-600 text-white font-bold transition-colors">
                    Tutup Demo
                </button>
            </div>

        </div>
    </div>

    <!-- ============================================================== -->
    <!-- INTERACTIVE JAVASCRIPT LOGIC (Zero-Lag, Lightweight Vanilla)    -->
    <!-- ============================================================== -->
    <script>
        // -------------------------------------------------------------
        // 1. Mobile Menu Drawer
        // -------------------------------------------------------------
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

        // -------------------------------------------------------------
        // 2. Demo Tab Switcher (Mobile App vs Web Dashboard)
        // -------------------------------------------------------------
        function switchDemoTab(tab) {
            const btnMobile = document.getElementById('tab-btn-mobile');
            const btnWeb = document.getElementById('tab-btn-web');
            const contentMobile = document.getElementById('demo-content-mobile');
            const contentWeb = document.getElementById('demo-content-web');

            if (tab === 'mobile') {
                btnMobile.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 bg-emerald-600 text-white shadow';
                btnWeb.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 text-slate-300 hover:text-white';
                contentMobile.classList.remove('hidden');
                contentWeb.classList.add('hidden');
            } else {
                btnWeb.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 bg-emerald-600 text-white shadow';
                btnMobile.className = 'px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold transition-all duration-200 flex items-center gap-2 text-slate-300 hover:text-white';
                contentWeb.classList.remove('hidden');
                contentMobile.classList.add('hidden');
            }
        }

        // -------------------------------------------------------------
        // 3. Mobile Phone Screen Switcher (Steps 1 to 4)
        // -------------------------------------------------------------
        let currentMobileScreen = 1;
        function setMobileScreen(step) {
            currentMobileScreen = step;
            // Hide all screens
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

            // Show chosen screen
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

        // -------------------------------------------------------------
        // 4. Web Dashboard Subtabs Switcher (Tabs 1 to 4)
        // -------------------------------------------------------------
        function setWebScreen(tab) {
            for (let i = 1; i <= 4; i++) {
                const screen = document.getElementById('web-screen-' + i);
                const btn = document.getElementById('web-subtab-' + i);
                if (screen) screen.classList.add('hidden');
                if (btn) {
                    btn.className = 'web-subtab px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-200';
                }
            }
            const activeScreen = document.getElementById('web-screen-' + tab);
            const activeBtn = document.getElementById('web-subtab-' + tab);
            if (activeScreen) activeScreen.classList.remove('hidden');
            if (activeBtn) {
                activeBtn.className = 'web-subtab px-3 py-1.5 rounded-lg bg-emerald-600 text-white';
            }
        }

        // -------------------------------------------------------------
        // 5. Video Walkthrough Modal & Player Engine (Ultra-Lightweight)
        // -------------------------------------------------------------
        let isVideoPlaying = false;
        let videoCurrentSeconds = 0;
        const totalVideoSeconds = 80; // 1m 20s
        let videoInterval = null;
        let currentScene = 1;

        const scenesConfig = [
            { id: 1, title: 'BABAK 1: PENDAHULUAN & LOGIN MULTI-ROLE', start: 0, end: 16 },
            { id: 2, title: 'BABAK 2: SKRINING FISIK & IMT OTOMATIS', start: 16, end: 32 },
            { id: 3, title: 'BABAK 3: DETEKSI DINI ANEMIA (HEMOGLOBIN)', start: 32, end: 48 },
            { id: 4, title: 'BABAK 4: FITUR MOBILE SISWA & RUN TRACKER', start: 48, end: 64 },
            { id: 5, title: 'BABAK 5: BILLING LISENSI & EKSPOR PDF RESMI', start: 64, end: 80 }
        ];

        function openDemoModal() {
            const modal = document.getElementById('demo-video-modal');
            modal.classList.remove('hidden');
            startVideoPlayback();
        }

        function closeDemoModal() {
            const modal = document.getElementById('demo-video-modal');
            modal.classList.add('hidden');
            pauseVideoPlayback();
        }

        function togglePlayVideo() {
            if (isVideoPlaying) {
                pauseVideoPlayback();
            } else {
                startVideoPlayback();
            }
        }

        function startVideoPlayback() {
            isVideoPlaying = true;
            const playIcon = document.getElementById('play-btn-icon');
            const statusText = document.getElementById('video-status-text');
            if (playIcon) {
                playIcon.classList.remove('fa-play');
                playIcon.classList.add('fa-pause');
            }
            if (statusText) statusText.innerText = 'Sedang Berputar...';

            clearInterval(videoInterval);
            videoInterval = setInterval(() => {
                videoCurrentSeconds++;
                if (videoCurrentSeconds > totalVideoSeconds) {
                    videoCurrentSeconds = 0;
                }
                updateVideoUI();
            }, 1000);
        }

        function pauseVideoPlayback() {
            isVideoPlaying = false;
            clearInterval(videoInterval);
            const playIcon = document.getElementById('play-btn-icon');
            const statusText = document.getElementById('video-status-text');
            if (playIcon) {
                playIcon.classList.remove('fa-pause');
                playIcon.classList.add('fa-play');
            }
            if (statusText) statusText.innerText = 'Dijeda (Klik Play)';
        }

        function restartVideo() {
            videoCurrentSeconds = 0;
            updateVideoUI();
            startVideoPlayback();
        }

        function seekVideo(event) {
            const bar = event.currentTarget;
            const rect = bar.getBoundingClientRect();
            const clickX = event.clientX - rect.left;
            const width = rect.width;
            const percent = Math.max(0, Math.min(1, clickX / width));
            videoCurrentSeconds = Math.floor(percent * totalVideoSeconds);
            updateVideoUI();
        }

        function setVideoScene(sceneNum) {
            const conf = scenesConfig.find(s => s.id === sceneNum);
            if (conf) {
                videoCurrentSeconds = conf.start;
                updateVideoUI();
            }
        }

        function updateVideoUI() {
            // Update Timer
            const m = Math.floor(videoCurrentSeconds / 60).toString().padStart(2, '0');
            const s = (videoCurrentSeconds % 60).toString().padStart(2, '0');
            const timerEl = document.getElementById('video-timer');
            if (timerEl) timerEl.innerText = `${m}:${s} / 01:20`;

            // Update Progress Bar
            const progressEl = document.getElementById('video-progress');
            const percent = (videoCurrentSeconds / totalVideoSeconds) * 100;
            if (progressEl) progressEl.style.width = percent + '%';

            // Determine Scene
            const currentConf = scenesConfig.find(sc => videoCurrentSeconds >= sc.start && videoCurrentSeconds < sc.end) || scenesConfig[0];
            if (currentConf.id !== currentScene) {
                currentScene = currentConf.id;
            }

            // Update Chapter Badge
            const badgeEl = document.getElementById('video-chapter-badge');
            if (badgeEl) badgeEl.innerText = currentConf.title;

            // Update Scene Views
            for (let i = 1; i <= 5; i++) {
                const sc = document.getElementById('v-scene-' + i);
                const pill = document.getElementById('v-pill-' + i);
                if (sc) {
                    if (i === currentScene) {
                        sc.classList.remove('hidden');
                    } else {
                        sc.classList.add('hidden');
                    }
                }
                if (pill) {
                    if (i === currentScene) {
                        pill.className = 'v-pill px-2 py-1 rounded bg-emerald-600 text-white font-bold';
                    } else {
                        pill.className = 'v-pill px-2 py-1 rounded bg-slate-800 text-slate-300 hover:text-white';
                    }
                }
            }
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDemoModal();
            }
        });
    </script>
</body>
</html>
