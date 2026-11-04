<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aku Sehat — Ekosistem Platform Manajemen Kesehatan Terpadu untuk Instansi & Institusi</title>
    <meta name="description" content="Platform kesehatan digital terpadu untuk instansi, institusi, sekolah, universitas, perusahaan, dan unit kesehatan modern. Menghubungkan Web Dashboard Manajemen dengan Aplikasi Mobile Anggota untuk skrining antropometri, IMT otomatis, pemantauan kadar Hb anemia, siklus menstruasi, dan pelacak vitalitas harian.">
    <meta name="keywords" content="Aku Sehat, kesehatan digital, rekam medis instansi, kesehatan institusi, UKS, poliklinik, antropometri, skrining IMT, cek Hb anemia, siklus haid, Neura Cakrawira Asia">
    
    <!-- Open Graph / Meta Sosial -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Aku Sehat — Ekosistem Manajemen Kesehatan Instansi & Institusi Terpadu">
    <meta property="og:description" content="Integrasi cerdas Web Dashboard Instansi & Aplikasi Mobile Anggota untuk monitoring kesehatan, IMT, dan deteksi dini anemia secara akurat.">
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
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased selection:bg-emerald-500 selection:text-white flex flex-col min-h-screen">

    <!-- ============================================================== -->
    <!-- TOP NAVIGATION BAR (Clean, Modern & Professional Enterprise)    -->
    <!-- ============================================================== -->
    <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition-all duration-300" id="main-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ url('/') }}" class="flex items-center gap-3 shrink-0 group">
                <img src="{{ asset('src/aku_sehat_icon.png') }}" alt="Aku Sehat" class="w-10 h-10 object-contain group-hover:scale-105 transition-transform duration-200">
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="font-black text-slate-900 text-lg leading-tight tracking-tight">Aku Sehat</span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 leading-none">V1</span>
                    </div>
                    <span class="text-[11px] text-emerald-700 font-semibold tracking-wide leading-tight">Neura Cakrawira Asia</span>
                </div>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden lg:flex items-center gap-7 xl:gap-9 text-sm font-semibold text-slate-600">
                <a href="#tentang" class="whitespace-nowrap hover:text-emerald-700 transition-colors">Tentang</a>
                <a href="#fitur" class="whitespace-nowrap hover:text-emerald-700 transition-colors">Fitur Unggulan</a>
                <a href="#ekosistem" class="whitespace-nowrap hover:text-emerald-700 transition-colors">Peran & Akses</a>
                <a href="#paket" class="whitespace-nowrap hover:text-emerald-700 transition-colors">Paket & Lisensi</a>
                <a href="#unduh" class="whitespace-nowrap hover:text-emerald-700 transition-colors">Unduh Aplikasi</a>
            </nav>

            <!-- Action CTA Button (Login / Dashboard) -->
            <div class="hidden sm:flex items-center gap-3 shrink-0">
                @auth
                    <a href="{{ route('dashboard') }}" class="whitespace-nowrap inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm hover:shadow-md transition-all">
                        <i class="fas fa-columns"></i>
                        <span>Buka Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="whitespace-nowrap inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm hover:shadow-md transition-all">
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
            <div class="flex flex-col gap-2.5 text-sm font-semibold text-slate-700">
                <a href="#tentang" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-700">Tentang Aplikasi</a>
                <a href="#fitur" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-700">Fitur Unggulan</a>
                <a href="#ekosistem" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-700">Peran & Hak Akses</a>
                <a href="#paket" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-700">Paket & Lisensi</a>
                <a href="#unduh" onclick="closeMobileMenu()" class="py-2 border-b border-slate-100 hover:text-emerald-700">Unduh Aplikasi Mobile</a>
                <div class="pt-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full py-2.5 rounded-xl text-xs sm:text-sm font-bold text-center text-white bg-emerald-700 hover:bg-emerald-800 flex items-center justify-center gap-2 shadow-sm">
                            <i class="fas fa-columns"></i> Buka Dashboard Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full py-2.5 rounded-xl text-xs sm:text-sm font-bold text-center text-white bg-emerald-700 hover:bg-emerald-800 flex items-center justify-center gap-2 shadow-sm">
                            <i class="fas fa-arrow-right-to-bracket"></i> Masuk ke Web Portal
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
                    <span>Ekosistem Platform Kesehatan Digital Terpadu untuk Instansi & Institusi</span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.15] mb-6">
                    Satu Platform Terintegrasi untuk <br class="hidden sm:inline">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-800 via-teal-700 to-sky-700">Kesehatan Institusi, Instansi & Anggota</span> yang Lebih Terukur
                </h1>

                <p class="text-base sm:text-lg text-slate-600 max-w-3xl mx-auto mb-9 leading-relaxed font-normal">
                    <strong>Aku Sehat</strong> menjembatani <strong>Web Dashboard Manajemen Kesehatan Instansi</strong> dengan <strong>Aplikasi Mobile Anggota</strong>. Dari skrining antropometri IMT otomatis, pemantauan kadar Hemoglobin (Hb) bebas anemia, konseling kesehatan mental SafeSpace, hingga pelacakan vitalitas harian dalam satu ekosistem yang fleksibel untuk berbagai organisasi, aman, dan terenkripsi.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3.5 mb-12">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-lg shadow-emerald-700/20 hover:shadow-xl hover:shadow-emerald-700/30 transition-all transform hover:-translate-y-0.5">
                            <i class="fas fa-columns text-base"></i>
                            <span>Buka Dashboard Utama</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-lg shadow-emerald-700/20 hover:shadow-xl hover:shadow-emerald-700/30 transition-all transform hover:-translate-y-0.5">
                            <i class="fas fa-arrow-right-to-bracket text-base"></i>
                            <span>Masuk Portal Web</span>
                        </a>
                    @endauth

                    <a href="#fitur" class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-800 bg-white hover:bg-slate-50 border border-slate-300 shadow-sm hover:shadow transition-all transform hover:-translate-y-0.5">
                        <i class="fas fa-compass text-emerald-700"></i>
                        <span>Jelajahi Fitur Unggulan</span>
                    </a>

                    <a href="#unduh" class="inline-flex items-center gap-2 px-5 py-3.5 rounded-2xl text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 transition-all">
                        <i class="fas fa-mobile-screen-button text-emerald-700"></i>
                        <span>Unduh Aplikasi Mobile</span>
                    </a>
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
                        <span>Transformasi Kesehatan Instansi & Institusi</span>
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-snug">
                        Bukan Sekadar Catatan Medis Manual, Ini Adalah <span class="text-emerald-800">Sistem Deteksi Dini</span> untuk Seluruh Instansi & Anggota
                    </h2>

                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                        Di banyak instansi, kampus, maupun sekolah, pencatatan kesehatan anggota masih mengandalkan buku catatan manual yang rentan tercecer, gejala anemia tidak terpantau, dan manajemen kesulitan menghasilkan laporan rekapitulasi resmi terstandarisasi.
                    </p>

                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                        <strong>Aku Sehat</strong> hadir merombak paradigma tersebut dengan sistem terpusat berbasis instansi. Setiap organisasi mendapatkan <strong>lingkungan privat (multi-tenancy)</strong> lengkap dengan modul skrining klinis, pemantauan berkala, dan aplikasi mobile ramah pengguna.
                    </p>

                    <div class="space-y-3.5 pt-2">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Digitalisasi Skrining Antropometri & Klinis</h4>
                                <p class="text-xs text-slate-500">Pencatatan Tinggi Badan, Berat Badan, Mata, Gigi, dan Telinga dengan kalkulasi status gizi otomatis standar Kemenkes.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Program Pencegahan Anemia & Monitoring Hb</h4>
                                <p class="text-xs text-slate-500">Deteksi dini kadar Hemoglobin untuk mencegah kelelahan kronis, penurunan produktivitas kerja/belajar, dan risiko komplikasi jangka panjang.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">SafeSpace Konseling & Privasi Terjaga</h4>
                                <p class="text-xs text-slate-500">Layanan konseling privat dan pencatatan siklus kesehatan dilakukan secara aman di smartphone masing-masing anggota.</p>
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
                                <h4 class="text-sm font-extrabold text-white mb-2">Untuk Manajemen, Konselor & Medis</h4>
                                <ul class="text-xs text-slate-300 space-y-1.5 list-disc list-inside">
                                    <li>Rekap Divisi / Departemen / Kelas</li>
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
                                <h4 class="text-sm font-extrabold text-white mb-2">Untuk Seluruh Anggota / Pengguna</h4>
                                <ul class="text-xs text-slate-300 space-y-1.5 list-disc list-inside">
                                    <li>Pencatatan Mandiri IMT & Hb</li>
                                    <li>Log Siklus Haid & Keluhan</li>
                                    <li>Run Tracker & Vitalitas Tubuh</li>
                                    <li>Pinjam Perlengkapan Medis/UKS</li>
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
    <!-- SECTION 2: FITUR UNGGULAN                                      -->
    <!-- ============================================================== -->
    <section id="fitur" class="py-16 sm:py-24 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-3">
                    <i class="fas fa-cubes-stacked"></i>
                    <span>MODULAR & LENGKAP</span>
                </div>
                <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight mb-4">
                    Dirancang Khusus Sesuai Kebutuhan <span class="text-emerald-800">Seluruh Instansi & Anggota</span>
                </h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                    Seluruh modul telah dibangun dan siap beroperasi untuk mendukung kelancaran layanan kesehatan instansi, institusi, maupun organisasi dan gaya hidup sehat seluruh anggota.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-weight-scale"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Skrining Antropometri & IMT</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pemeriksaan berkala tinggi badan, berat badan, serta status gizi (Kurus, Normal, Gemuk, Obesitas) dengan algoritma otomatis standar klinis Kemenkes.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-droplet"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Pemantauan Hb & Bebas Anemia</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pencatatan kadar Hemoglobin (Hb) rutin, grafik tren anemia, dan peringatan dini bagi anggota yang memerlukan intervensi gizi atau suplemen penambah darah.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">SafeSpace Konseling & Burnout</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Fitur konseling privat antara anggota dan Health Consultant/Konselor serta skrining berkala tingkat kejenuhan, kelelahan, dan stres harian.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-person-running"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Run Tracker & Log Vitalitas</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Pelacak olahraga lari berbasis GPS di aplikasi mobile, log konsumsi air minum, pemantauan kualitas tidur, dan evaluasi postur ergonomi anggota.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-ribbon"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Peminjaman Fasilitas & Perlengkapan Medis</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Sistem sirkulasi peminjaman fasilitas kesehatan instansi terdigitalisasi dengan verifikasi kode cepat, notifikasi batas waktu, dan deteksi denda otomatis.
                    </p>
                </div>

                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200/80 hover:border-emerald-300 hover:shadow-xl hover:shadow-emerald-500/5 transition-all duration-300 group">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-xl font-bold mb-5 group-hover:scale-110 transition-transform">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-slate-900 mb-2">Keamanan Medis & Multi-Tenant</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Data antar-instansi terisolasi secara aman. Seluruh field kesehatan sensitif dienkripsi per-tenant tingkat perbankan (AES-256) serta mematuhi privasi data.
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
                            <i class="fas fa-building-columns"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-0.5">Admin Instansi</h4>
                        <p class="text-xs text-emerald-700 font-semibold mb-2">Pimpinan & Manajemen Instansi</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Pengambil kebijakan utama institusi/instansi. Mengatur struktur departemen/divisi/kelas, alokasi lisensi kuota anggota, pembayaran billing Midtrans, serta mengunduh rekapitulasi laporan resmi (PDF).
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200 text-center">Akses: Web Portal</span>
                </div>

                <!-- Role 2: Admin Unit Medis / UKS / Poliklinik -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fas fa-hand-holding-medical"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-0.5">Admin Unit Medis</h4>
                        <p class="text-xs text-rose-700 font-semibold mb-2">Koordinator UKS / Poliklinik / PMR / Satgas</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Mengelola operasional harian pos/klinik kesehatan: sirkulasi fasilitas & peminjaman perlengkapan medis, pemantauan cuti/keluhan menstruasi, inventaris obat, serta registrasi anggota.
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
                        <p class="text-xs text-sky-700 font-semibold mb-2">Tim Skrining Lapangan / Pembina Kesehatan</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Fokus pada skrining fisik klinis berkala: input cepat antropometri (TB/BB dengan kalkulasi otomatis status gizi IMT standar Kemenkes) dan input skrining rapid Hemoglobin (Hb anemia).
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
                        <p class="text-xs text-teal-700 font-semibold mb-2">Konselor / Psikolog / Dokter / Tenaga Medis</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Menangani konsultasi privat SafeSpace, evaluasi skrining burnout dan stres harian, serta memberikan rekomendasi klinis dan rujukan medis lanjutan ke fasilitas kesehatan/RS.
                        </p>
                    </div>
                    <span class="text-[11px] font-bold text-teal-800 bg-teal-50 px-2 py-1 rounded-lg border border-teal-200 text-center">Akses: Web Portal</span>
                </div>

                <!-- Role 5: Member (Anggota / Siswa / Karyawan) -->
                <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-800 flex items-center justify-center font-bold text-sm mb-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 mb-0.5">Member / Anggota</h4>
                        <p class="text-xs text-indigo-700 font-semibold mb-2">Seluruh Anggota / Siswa / Karyawan</p>
                        <p class="text-xs text-slate-600 leading-relaxed mb-4">
                            Pengguna aplikasi mobile Android untuk melihat rekam medis mandiri, pemantauan IMT & Hb, log siklus kesehatan haid, peminjaman fasilitas medis, pelacak vitalitas & lari GPS, serta chat SafeSpace.
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
                    Paket Kuota Lisensi Sesuai Kapasitas Instansi & Institusi
                </h2>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-normal">
                    Biaya resmi terstandarisasi <strong class="text-emerald-700 font-extrabold">Rp 5.000 / pengguna / tahun (5k/user/tahun)</strong> (All-In Seluruh Modul). Didukung pembayaran instan otomatis via <strong>Midtrans Snap</strong> (QRIS, Virtual Account Bank, GoPay, ShopeePay) serta Transfer Bank resmi.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 max-w-5xl mx-auto">
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-200 flex flex-col justify-between hover:shadow-lg transition-all">
                    <div>
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Paket Starter</div>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-1">+100 Pengguna</h3>
                        <div class="text-2xl font-black text-slate-900 mt-4 mb-0.5">Rp 500.000 <span class="text-xs font-normal text-slate-500">/ tahun</span></div>
                        <div class="text-xs font-bold text-emerald-700 mb-4">Rp 5.000 / pengguna / tahun (5k/user/tahun)</div>
                        <p class="text-xs text-slate-600 mb-6">Cocok untuk instansi rintisan, komunitas, atau 1 divisi/angkatan.</p>
                        <ul class="text-xs text-slate-600 space-y-2.5 mb-6">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-800"></i> Kuota 100 Akun Anggota/Pengguna</li>
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
                        <div class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">Instansi Menengah</div>
                        <h3 class="text-xl font-extrabold text-white mb-1">+250 Pengguna</h3>
                        <div class="text-3xl font-black text-white mt-4 mb-0.5">Rp 1.250.000 <span class="text-xs font-normal text-slate-400">/ tahun</span></div>
                        <div class="text-xs font-bold text-emerald-400 mb-4">Rp 5.000 / pengguna / tahun (5k/user/tahun)</div>
                        <p class="text-xs text-slate-300 mb-6">Pilihan ideal untuk sekolah, akademi, divisi kampus, atau instansi menengah.</p>
                        <ul class="text-xs text-slate-200 space-y-2.5 mb-6">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-400"></i> Kuota 250 Akun Anggota/Pengguna</li>
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
                        <div class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Institusi Besar & Korporasi</div>
                        <h3 class="text-xl font-extrabold text-slate-900 mb-1">+1.000 Pengguna</h3>
                        <div class="text-2xl font-black text-slate-900 mt-4 mb-0.5">Rp 5.000.000 <span class="text-xs font-normal text-slate-500">/ tahun</span></div>
                        <div class="text-xs font-bold text-emerald-700 mb-4">Rp 5.000 / pengguna / tahun (5k/user/tahun)</div>
                        <p class="text-xs text-slate-600 mb-6">Untuk universitas, institut, korporasi, yayasan, atau instansi berskala besar.</p>
                        <ul class="text-xs text-slate-600 space-y-2.5 mb-6">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-800"></i> Kuota 1.000+ Akun Anggota/Pengguna</li>
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
                <i class="fas fa-circle-info text-emerald-800 mr-1"></i> Biaya resmi terstandarisasi Rp 5.000 / pengguna / tahun (All-In). Admin Instansi dapat membeli tambahan kuota satuan secara fleksibel kapan saja langsung dari menu Billing.
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
                            Mulai Gunakan Aku Sehat di Instansi Anda Hari Ini
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                            Unduh aplikasi Android untuk seluruh anggota atau akses web portal instansi langsung tanpa memerlukan instalasi perangkat yang rumit.
                        </p>

                        <div class="flex flex-wrap items-center gap-3 pt-4">
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold text-slate-950 bg-emerald-400 hover:bg-emerald-300 shadow-md transition-all">
                                <i class="fas fa-desktop"></i>
                                <span>Buka Web Portal Instansi</span>
                            </a>
                            <a href="#fitur" class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl text-xs sm:text-sm font-bold text-white bg-slate-800 hover:bg-slate-700 border border-slate-700 transition-all">
                                <i class="fas fa-cubes-stacked text-emerald-400"></i>
                                <span>Lihat Semua Fitur</span>
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
                    Informasi penting seputar keamanan, fleksibilitas institusi, dan penggunaan Aku Sehat.
                </p>
            </div>

            <div class="space-y-3.5">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Apakah aplikasi ini memberatkan smartphone anggota?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sama sekali tidak. Aplikasi mobile Aku Sehat dikembangkan menggunakan React Native dan TypeScript yang dioptimasi secara mendalam. Tampilan grafik dan animasi menggunakan akselerasi GPU murni sehingga sangat ringan dan hemat baterai pada seluruh spesifikasi ponsel anggota.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Bagaimana keamanan data medis anggota dijamin?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Data klinis sensitif seperti catatan Hb, pemeriksaan fisik, dan riwayat kesehatan dienkripsi menggunakan enkripsi tingkat perbankan (AES-256) per-tenant. Setiap instansi memiliki ruang data terisolasi yang tidak dapat diakses atau diintip oleh instansi lain.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Bagaimana cara instansi mendaftar dan mengundang anggota?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Admin Instansi dapat mendaftar, memilih lisensi yang diinginkan, dan membuat struktur divisi/departemen/kelas. Setiap instansi mendapatkan <strong>Kode Undangan Instansi</strong> unik yang dapat dibagikan kepada anggota untuk registrasi mandiri di aplikasi mobile.
                    </p>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <h4 class="text-sm font-bold text-slate-900 mb-1">Apakah data bisa diekspor dalam bentuk laporan resmi PDF?</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Ya. Web Dashboard dilengkapi fitur pencetakan PDF resmi berstandar format laporan instansi/dinas untuk rekap antropometri, pemeriksaan Hb anemia massal, log konseling, dan inventaris fasilitas kesehatan.
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
                        Platform ekosistem pemantauan dan tata kelola kesehatan terpadu untuk instansi, institusi, universitas, sekolah, korporasi, dan unit kesehatan. Dikembangkan dengan standar medis dan keamanan industri.
                    </p>
                    <div class="text-[11px] text-slate-500">
                        &copy; 2026 <strong>Neura Cakrawira Asia</strong> / Neura Cakrawira Solusi. Hak Cipta Dilindungi.
                    </div>
                </div>

                <div class="space-y-2.5">
                    <div class="font-bold text-white text-xs uppercase tracking-wider">Navigasi Cepat</div>
                    <ul class="space-y-1.5">
                        <li><a href="#tentang" class="hover:text-emerald-400 transition-colors">Tentang Aplikasi</a></li>
                        <li><a href="#fitur" class="hover:text-emerald-400 transition-colors">Fitur Unggulan</a></li>
                        <li><a href="#ekosistem" class="hover:text-emerald-400 transition-colors">Peran & Akses</a></li>
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
    <!-- JAVASCRIPT LOGIC                                               -->
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

        // Add soft shadow on scroll
        window.addEventListener('scroll', () => {
            const header = document.getElementById('main-header');
            if (header) {
                if (window.scrollY > 20) {
                    header.classList.add('shadow-md');
                } else {
                    header.classList.remove('shadow-md');
                }
            }
        });
    </script>
</body>
</html>
