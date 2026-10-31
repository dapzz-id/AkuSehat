@php
    $appThemeCookie = request()->cookie('app_portal_theme', 'atlas');
    $isEmeraldInitial = $appThemeCookie === 'emerald';
    $themeClassInitial = $isEmeraldInitial ? 'theme-emerald' : 'theme-atlas theme-portal';
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full {{ $themeClassInitial }}">
<head>
    <!-- Required Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page Title -->
    <title>
        @hasSection('title')
            @yield('title') | Aku Sehat
        @else
            Aku Sehat - Sistem Informasi Kesehatan
        @endif
    </title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Sistem Informasi Kesehatan Terpadu untuk Sekolah dan Instansi se-Indonesia.')">
    <meta name="author" content="Neura Cakrawira Solusi">
    <meta name="keywords" content="sistem kesehatan, kesehatan member, hemoglobin, rekam medis, pmr, sekolah, instansi, Aku Sehat, Neura Cakrawira Solusi">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#064e3b">
    <meta name="msapplication-TileColor" content="#064e3b">
    <meta name="msapplication-TileImage" content="{{ asset('src/aku_sehat_white_icon.png') }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Aku Sehat">
    <meta name="application-name" content="Aku Sehat">

    <!-- Favicons -->
    <link rel="icon" href="{{ asset('src/aku_sehat_icon.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('src/aku_sehat_icon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('src/aku_sehat_icon.png') }}">

    <!-- Google Fonts & Font Awesome & CDN Hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//code.jquery.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Vite Assets (Tailwind & App Scripts) -->
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <!-- Chart.js for Interactive Health & Reporting Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- SweetAlert2 for Elegant Confirmation Popups & Modern Alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Custom Styles -->
    @stack('styles')

    <style>
        /* ==========================================================================
           SweetAlert2 Custom Modern Styling (Aku Sehat Brand System)
           ========================================================================== */
        .swal2-container {
            backdrop-filter: blur(8px) !important;
            -webkit-backdrop-filter: blur(8px) !important;
            background-color: rgba(15, 23, 42, 0.5) !important;
            z-index: 99999 !important;
        }

        .swal2-popup.swal2-akusehat-popup {
            border-radius: 1.25rem !important;
            padding: 1.75rem 1.5rem !important;
            background: #ffffff !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25) !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
            max-width: 28rem !important;
            width: 92% !important;
        }

        .swal2-title.swal2-akusehat-title {
            font-size: 1.2rem !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            padding: 0 !important;
            margin: 0.75rem 0 0.5rem 0 !important;
            letter-spacing: -0.02em !important;
        }

        .swal2-html-container.swal2-akusehat-html {
            font-size: 0.875rem !important;
            color: #475569 !important;
            line-height: 1.55 !important;
            margin: 0 0 1.25rem 0 !important;
            padding: 0 !important;
        }

        .swal2-actions.swal2-akusehat-actions {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.75rem !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .swal2-akusehat-btn-danger {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #ffffff !important;
            background: linear-gradient(135deg, #e11d48, #be123c) !important;
            border: 1px solid #be123c !important;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.25) !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            outline: none !important;
        }

        .swal2-akusehat-btn-danger:hover {
            background: linear-gradient(135deg, #be123c, #9f1239) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 16px rgba(225, 29, 72, 0.35) !important;
        }

        .swal2-akusehat-btn-emerald {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #ffffff !important;
            background: linear-gradient(135deg, #059669, #047857) !important;
            border: 1px solid #047857 !important;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.25) !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            outline: none !important;
        }

        .swal2-akusehat-btn-emerald:hover {
            background: linear-gradient(135deg, #047857, #065f46) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 16px rgba(5, 150, 105, 0.35) !important;
        }

        .swal2-akusehat-btn-blue {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #ffffff !important;
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
            border: 1px solid #1d4ed8 !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25) !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            outline: none !important;
        }

        .swal2-akusehat-btn-blue:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35) !important;
        }

        .swal2-akusehat-btn-purple {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #ffffff !important;
            background: linear-gradient(135deg, #9333ea, #7e22ce) !important;
            border: 1px solid #7e22ce !important;
            box-shadow: 0 4px 12px rgba(147, 51, 234, 0.25) !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            outline: none !important;
        }

        .swal2-akusehat-btn-purple:hover {
            background: linear-gradient(135deg, #7e22ce, #6b21a8) !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 16px rgba(147, 51, 234, 0.35) !important;
        }

        .swal2-akusehat-btn-cancel {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.625rem 1.25rem !important;
            border-radius: 0.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #475569 !important;
            background: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            outline: none !important;
        }

        .swal2-akusehat-btn-cancel:hover {
            background: #e2e8f0 !important;
            color: #1e293b !important;
            transform: translateY(-1px) !important;
        }

        .swal2-icon {
            margin: 0.5rem auto 0.75rem !important;
            border-width: 3px !important;
        }

        /* Modern Tom Select Customization */
        .ts-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
        }

        .ts-control {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            border-radius: 0.5rem !important;
            border: 1px solid #d1d5db !important;
            font-size: 0.875rem !important;
            padding: 0 0.75rem !important;
            height: 42px !important;
            min-height: 42px !important;
            display: flex !important;
            align-items: center !important;
            background: #ffffff !important;
            box-shadow: none !important;
        }

        .ts-control:focus,
        .ts-wrapper.focus .ts-control {
            border-color: #308a34 !important;
            box-shadow: 0 0 0 2px rgba(48, 138, 52, 0.2) !important;
        }

        .ts-dropdown {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 9999 !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
            max-height: 250px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            margin-top: 4px !important;
        }

        .ts-dropdown .option {
            padding: 0.625rem 0.875rem !important;
            font-size: 0.875rem !important;
            transition: background 0.15s ease !important;
        }

        .ts-dropdown .active {
            background-color: #ecfdf5 !important;
            color: #065f46 !important;
        }

        .ts-wrapper.multi .ts-control {
            flex-wrap: wrap !important;
            gap: 4px !important;
        }

        .ts-wrapper.multi .ts-control > div {
            background: #ecfdf5 !important;
            color: #047857 !important;
            border: 1px solid #a7f3d0 !important;
            border-radius: 0.5rem !important;
            padding: 2px 8px !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
        }

        /* ==========================================================================
           Desktop Mini-Sidebar (Icon-Only) Collapse System
           - Default state: OPEN (expanded, width: 18rem / w-72)
           - Collapsed state: Mini-Sidebar (width: 5rem / 80px), showing ONLY icons
           - Transition: silky smooth width bezier curve with 0 CLS
           ========================================================================== */
        #sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-x: hidden;
        }

        @media (min-width: 1024px) {
            html.sidebar-collapsed #sidebar,
            body.sidebar-collapsed #sidebar {
                width: 5rem !important; /* 80px */
                min-width: 5rem !important;
                max-width: 5rem !important;
            }

            /* Header in Mini mode */
            html.sidebar-collapsed #sidebar > div:first-child {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
                justify-content: center !important;
            }
            html.sidebar-collapsed #sidebar > div:first-child a {
                justify-content: center !important;
                width: 100% !important;
                gap: 0 !important;
            }
            html.sidebar-collapsed #sidebar > div:first-child img {
                margin: 0 auto !important;
            }
            html.sidebar-collapsed #sidebar .sidebar-logo-text {
                display: none !important;
            }

            /* Nav in Mini mode */
            html.sidebar-collapsed #sidebar #sidebar-nav {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
                overflow-x: hidden !important;
            }
            /* Hide accordion header text buttons in mini mode, show clean divider line */
            html.sidebar-collapsed #sidebar .sidebar-group > button {
                display: none !important;
            }
            html.sidebar-collapsed #sidebar .sidebar-group {
                border-top: 1px solid rgba(16, 185, 129, 0.2) !important;
                padding-top: 0.5rem !important;
                margin-top: 0.5rem !important;
            }
            html.sidebar-collapsed #sidebar .sidebar-group:first-child {
                border-top: none !important;
                padding-top: 0 !important;
                margin-top: 0 !important;
            }
            /* Ensure group content is visible so icons are accessible */
            html.sidebar-collapsed #sidebar .sidebar-group > div[id$="-content"] {
                display: block !important;
            }
            /* Center icon boxes and hide labels */
            html.sidebar-collapsed #sidebar #sidebar-nav a {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                width: 100% !important;
                gap: 0 !important;
            }
            html.sidebar-collapsed #sidebar #sidebar-nav a > span,
            html.sidebar-collapsed #sidebar #sidebar-nav a > div:not(:first-child) {
                display: none !important;
            }

            /* Bottom Profile Card in Mini mode */
            html.sidebar-collapsed #sidebar > div:last-child {
                padding: 0.5rem !important;
            }
            html.sidebar-collapsed #sidebar > div:last-child a[href*="profile"] {
                padding: 0.5rem !important;
                justify-content: center !important;
            }
            html.sidebar-collapsed #sidebar > div:last-child a[href*="profile"] > div {
                justify-content: center !important;
                gap: 0 !important;
            }
            html.sidebar-collapsed #sidebar > div:last-child a[href*="profile"] .flex-1 {
                display: none !important;
            }
            html.sidebar-collapsed #sidebar #logout-form button {
                padding: 0.625rem 0 !important;
                justify-content: center !important;
            }
            html.sidebar-collapsed #sidebar #logout-form button span {
                display: none !important;
            }
        }

        /* ==========================================================================
           Portal Mode (Tampilan Portal) Display Rules
           - When active: sidebar is completely hidden, 100% full width
           - Portal brand logo and feature breadcrumbs are displayed
           ========================================================================== */
        /* ==========================================================================
           Theme Display Rules (Default: Atlas Theme, Alternate: Emerald Theme)
           - Atlas Theme (Default): Sidebar hidden, Atlas container shown, portal brand shown
           - Emerald Theme: Sidebar shown, Default/Emerald container shown
           ========================================================================== */
        html:not(.theme-emerald) #sidebar,
        html.theme-atlas #sidebar {
            display: none !important;
        }
        html:not(.theme-emerald) #sidebar-toggle-btn,
        html.theme-atlas #sidebar-toggle-btn {
            display: none !important;
        }
        html:not(.theme-emerald) .portal-brand-logo,
        html.theme-atlas .portal-brand-logo {
            display: inline-flex !important;
        }
        html:not(.theme-emerald) #portal-breadcrumb-bar,
        html.theme-atlas #portal-breadcrumb-bar {
            display: flex !important;
        }

        /* Emerald Theme Display Rules */
        html.theme-emerald #sidebar {
            display: flex !important;
        }
        html.theme-emerald #sidebar-toggle-btn {
            display: flex !important;
        }
        html.theme-emerald .portal-brand-logo {
            display: none !important;
        }
        html.theme-emerald #portal-breadcrumb-bar {
            display: none !important;
        }

        /* Container Rules */
        html:not(.theme-emerald) #theme-default-container,
        html.theme-atlas #theme-default-container {
            display: none !important;
        }
        html:not(.theme-emerald) #theme-atlas-container,
        html.theme-atlas #theme-atlas-container {
            display: block !important;
        }
        html.theme-emerald #theme-atlas-container {
            display: none !important;
        }
        html.theme-emerald #theme-default-container {
            display: block !important;
        }

        /* ==========================================================================
           Global Modal & Fixed Overlay Reset
           Prevents fixed fullscreen overlays from inheriting space-y or container margins
           ========================================================================== */
        .fixed.inset-0,
        div[id$="Modal"].fixed {
            margin: 0 !important;
            margin-top: 0 !important;
            top: 0 !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
        }
    </style>

    <!-- Instant Pre-Paint Sidebar Desktop State & Theme (Default: Atlas Theme) -->
    <script>
        (function() {
            try {
                if (localStorage.getItem('sidebar_desktop_collapsed') === 'true' && window.innerWidth >= 1024) {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
                const savedTheme = localStorage.getItem('app_portal_theme');
                if (savedTheme === 'emerald') {
                    document.documentElement.classList.remove('theme-portal', 'theme-atlas');
                    document.documentElement.classList.add('theme-emerald');
                } else if (savedTheme === 'atlas') {
                    document.documentElement.classList.add('theme-portal', 'theme-atlas');
                    document.documentElement.classList.remove('theme-emerald');
                }
            } catch (e) {}
        })();
    </script>
</head>

<body class="h-full bg-slate-50 font-sans antialiased text-slate-800 {{ $themeClassInitial }}">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Backdrop for Mobile -->
        <div id="sidebar-overlay" 
             onclick="toggleSidebar()" 
             class="fixed inset-0 z-40 bg-slate-950/60 backdrop-blur-sm opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden">
        </div>

        <!-- Sidebar Navigation -->
        <aside id="sidebar" 
               class="fixed inset-y-0 left-0 z-50 flex flex-col shrink-0 w-72 bg-gradient-to-b from-slate-950 via-emerald-950 to-slate-950 text-white border-r border-emerald-900/30 transform -translate-x-full lg:static lg:translate-x-0 shadow-2xl lg:shadow-none">
            
            <!-- Sidebar Header / Logo -->
            <div class="relative flex items-center justify-between px-6 py-5 border-b border-emerald-900/40 bg-black/20 transition-all duration-300">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="flex items-center justify-center mb-1 shrink-0">
                        <img src="{{ asset('src/aku_sehat_icon.png') }}" class="w-8 h-8 object-contain" alt="Aku Sehat Icon">
                    </div>
                    <div class="sidebar-logo-text whitespace-nowrap overflow-hidden transition-all duration-200">
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-base tracking-tight text-white">Aku Sehat</span>
                            <span class="text-[9px] uppercase font-bold px-1.5 py-0.5 rounded-full bg-emerald-400/20 text-emerald-300 border border-emerald-400/30">Pro</span>
                        </div>
                        <p class="text-[11px] text-emerald-200/70 font-medium">Healthcare System</p>
                    </div>
                </a>

                <!-- Mobile Close Toggle (Only on mobile, desktop uses the single navbar toggle) -->
                <button type="button" 
                        onclick="toggleSidebar()" 
                        aria-label="Tutup Menu"
                        class="p-2 -mr-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 lg:hidden transition-colors">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Sidebar Navigation Links (Scrollable) -->
            <nav id="sidebar-nav" class="flex-1 px-4 py-5 overflow-y-auto space-y-4">
                @if(auth()->user()->isAdmin() || auth()->user()->isAdminInstansi())
                    @include('layouts.sidebar-admin')
                @elseif(auth()->user()->isHealthMonitor())
                    @include('layouts.sidebar-healthmonitor')
                @elseif(auth()->user()->isSuperAdmin())
                    @include('layouts.sidebar-superadmin')
                @else
                    @include('layouts.sidebar-member')
                @endif
            </nav>
            <script>
                // Instant pre-paint scroll and group state restoration
                (function() {
                    try {
                        const nav = document.getElementById('sidebar-nav');
                        if (nav) {
                            const savedScroll = sessionStorage.getItem('sidebar_scroll_pos');
                            if (savedScroll !== null) {
                                nav.scrollTop = parseInt(savedScroll, 10);
                            }
                        }
                        document.querySelectorAll('.sidebar-group').forEach(function(group) {
                            const groupId = group.getAttribute('data-group-id');
                            const content = document.getElementById(groupId + '-content');
                            const chevron = document.getElementById(groupId + '-chevron');
                            if (!content) return;

                            const hasActive = group.getAttribute('data-has-active') === '1' || content.querySelector('a.font-semibold') !== null;
                            const savedState = localStorage.getItem('sidebar_group_' + groupId);

                            if (!hasActive && savedState === 'closed') {
                                content.classList.add('hidden');
                                if (chevron) chevron.classList.add('-rotate-90');
                            }
                        });
                    } catch (e) {}
                })();
            </script>

            <!-- Bottom User Profile Card & Logout -->
            <div class="p-4 border-t border-emerald-900/40 bg-black/30">
                <a href="{{ route('profile.edit') }}" 
                   title="Buka Pengaturan Profil"
                   class="group block p-3 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-emerald-500/40 backdrop-blur-md mb-3 transition-all duration-200">
                    <div class="flex items-center gap-3">
                        <div class="relative flex-shrink-0">
                            <div class="w-10 h-10 rounded-xl overflow-hidden bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-emerald-600/30 border border-white/20">
                                @if(auth()->user()->avatar_url)
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->nama }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                                @endif
                            </div>
                            <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-400 border-2 border-slate-900"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-white group-hover:text-emerald-300 truncate leading-tight transition-colors">{{ auth()->user()->nama }}</p>
                                <i class="fas fa-chevron-right text-[10px] text-white/40 group-hover:text-emerald-300 transition-colors"></i>
                            </div>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="text-[10px] font-medium px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                    {{ auth()->user()->role_label }}
                                </span>
                            </div>
                        </div>
                    </div>
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="button" 
                            onclick="confirmLogout(event)"
                            class="btn-press flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-xl text-xs font-semibold text-rose-300 bg-rose-500/10 hover:bg-rose-500/20 hover:text-rose-200 border border-rose-500/20 transition-all duration-200">
                        <i class="fas fa-arrow-right-from-bracket"></i>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
            <!-- Top Sticky Frosted Header -->
            <header class="sticky top-0 z-30 bg-white/85 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between px-4 sm:px-6 lg:px-8 py-3.5">
                    <!-- Left: Sidebar Trigger & Breadcrumb / App Context -->
                    <div class="flex items-center gap-3">
                        <button type="button" 
                                onclick="toggleSidebar()" 
                                id="sidebar-toggle-btn"
                                aria-label="Buka / Sembunyikan Menu Sidebar"
                                title="Buka / Sembunyikan Sidebar (Ctrl + B)"
                                class="p-2 -ml-2 rounded-xl text-slate-600 hover:text-emerald-700 hover:bg-emerald-50 transition-colors flex items-center justify-center">
                            <i id="sidebar-toggle-icon" class="fas fa-bars-staggered text-lg"></i>
                        </button>

                        <!-- Brand Logo when in Portal mode (since sidebar is hidden) -->
                        <a href="{{ route('dashboard') }}" 
                           class="portal-brand-logo hidden items-center gap-2 group hover:opacity-95 transition-opacity mr-1"
                           title="Kembali ke Beranda Portal Layanan">
                            <img src="{{ asset('src/aku_sehat_icon.png') }}" class="w-7 h-7 object-contain" alt="Aku Sehat Icon">
                            <span class="font-extrabold text-sm tracking-tight text-slate-900 group-hover:text-emerald-700 transition-colors">Aku Sehat</span>
                        </a>
                    </div>

                    <!-- Right: Live Date & Profile Pill -->
                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <!-- Theme Switcher Pill -->
                        <button type="button" 
                                onclick="toggleAppTheme()" 
                                id="theme-switch-btn"
                                title="Switch Theme (Atlas Theme / Emerald Theme)"
                                class="btn-press flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 rounded-full bg-slate-100 hover:bg-emerald-50 border border-slate-200/90 hover:border-emerald-300 text-xs font-semibold text-slate-700 hover:text-emerald-700 transition-all duration-200">
                            <i id="theme-switch-icon" class="fas fa-paint-roller text-emerald-600"></i>
                            <span id="theme-switch-label" class="hidden sm:inline">Switch to Emerald Theme</span>
                        </button>

                        <!-- Date Chip (hidden on small mobile) -->
                        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200/80 text-xs font-medium text-slate-600">
                            <i class="far fa-calendar-alt text-emerald-600"></i>
                            <span>{{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                        </div>

                        <!-- User Profile Pill -->
                        <a href="{{ route('profile.edit') }}" 
                           title="Kelola Profil Saya"
                           class="flex items-center gap-2.5 pl-2.5 pr-3 py-1 rounded-full bg-emerald-50/80 hover:bg-emerald-100/80 border border-emerald-200/60 hover:border-emerald-300 transition-all duration-200 group">
                            <div class="w-7 h-7 rounded-full overflow-hidden bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                @if(auth()->user()->avatar_url)
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->nama }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                                @endif
                            </div>
                            <div class="hidden sm:block text-left">
                                <p class="text-xs font-bold text-slate-800 group-hover:text-emerald-700 leading-tight transition-colors">{{ auth()->user()->nama }}</p>
                                <p class="text-[10px] text-emerald-700 font-medium leading-none">{{ auth()->user()->role_label }}</p>
                            </div>
                            <i class="fas fa-angle-right text-[10px] text-slate-400 group-hover:text-emerald-600 transition-colors hidden sm:inline ml-0.5"></i>
                        </a>

                        <!-- Logout Quick Action Button in Header (Atlas & Emerald Theme) -->
                        <button type="button" 
                                onclick="confirmLogout(event)"
                                title="Keluar Sistem (Logout)"
                                aria-label="Keluar dari akun sistem"
                                class="btn-press flex items-center justify-center w-8 h-8 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-600 hover:text-rose-700 border border-rose-200/80 hover:border-rose-300 transition-all duration-200">
                            <i class="fas fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Body Scrollable View -->
            <main class="flex-1 overflow-y-auto px-4 pt-3 pb-6 sm:px-6 sm:pt-4 sm:pb-8 lg:px-8">
                <div class="max-w-7xl mx-auto">
                    @php
                        $portalCategory = 'Portal Layanan';
                        if (request()->routeIs('admin.kesehatan.*') || request()->routeIs('admin.hb.*') || request()->routeIs('admin.femhealth.*') || request()->routeIs('admin.vaccine.*') || request()->routeIs('admin.pasien.*') || request()->routeIs('admin.rekam-medis.*') || request()->routeIs('kesehatan.hb.*') || request()->routeIs('kesehatan.femhealth.*') || request()->routeIs('kesehatan.vaksin.*') || request()->routeIs('kesehatan.riwayat.*')) {
                            $portalCategory = 'Data Medis & Rekam Klinis';
                        } elseif (request()->routeIs('admin.activities.*') || request()->routeIs('admin.vital-logs.*') || request()->routeIs('admin.nutrition.*') || request()->routeIs('admin.ergonomics.*') || request()->routeIs('kesehatan.activities.*') || request()->routeIs('kesehatan.nutrisi.*') || request()->routeIs('kesehatan.ergonomis.*') || request()->routeIs('kesehatan.vital.*')) {
                            $portalCategory = 'Kebugaran & Gaya Hidup';
                        } elseif (request()->routeIs('admin.counseling.*') || request()->routeIs('admin.burnout.*') || request()->routeIs('admin.skrining.*') || request()->routeIs('kesehatan.konseling.*') || request()->routeIs('kesehatan.burnout.*')) {
                            $portalCategory = 'Kesehatan Mental & Konseling';
                        } elseif (request()->routeIs('admin.users.*') || request()->routeIs('admin.divisi.*') || request()->routeIs('admin.modules.*') || request()->routeIs('admin.billing.*') || request()->routeIs('admin.langganan.*')) {
                            $portalCategory = 'Manajemen ' . (auth()->user()->instansi ? auth()->user()->instansi->tipe_sebutan : 'Instansi');
                        } elseif (request()->routeIs('monitoring.*')) {
                            $portalCategory = 'Monitoring Kesehatan';
                        } elseif (request()->routeIs('superadmin.instansi.*') || request()->routeIs('superadmin.license.*') || request()->routeIs('superadmin.modules.*')) {
                            $portalCategory = 'Manajemen Instansi & Lisensi';
                        } elseif (request()->routeIs('superadmin.update.*') || request()->routeIs('superadmin.maintenance.*') || request()->routeIs('superadmin.logs.*') || request()->routeIs('superadmin.software.*') || request()->routeIs('superadmin.terminal.*')) {
                            $portalCategory = 'Pemeliharaan & Server';
                        } elseif (request()->routeIs('profile.*')) {
                            $portalCategory = 'Pengaturan Akun';
                        } elseif (request()->routeIs('patch_notes.*')) {
                            $portalCategory = 'Catatan Pembaruan';
                        } elseif (request()->routeIs('privacy.*')) {
                            $portalCategory = 'Kebijakan & Privasi';
                        }
                    @endphp

                    <!-- Portal Mode Breadcrumb Bar (Hanya tampil saat Mode Portal di halaman fitur) -->
                    @if(!request()->routeIs('dashboard'))
                    <div id="portal-breadcrumb-bar" hidden class="mb-3 px-4 py-2.5 rounded-2xl bg-white border border-slate-200/90 shadow-sm items-center justify-between flex-wrap gap-2 transition-all">
                        <nav class="flex items-center gap-2 text-xs font-medium text-slate-500 overflow-x-auto py-0.5">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-1.5 text-emerald-700 hover:text-emerald-800 font-bold transition-colors">
                                <i class="fas fa-th-large text-emerald-600"></i>
                                <span>Dashboard</span>
                            </a>
                            <i class="fas fa-chevron-right text-[9px] text-slate-300"></i>
                            <span class="text-slate-600">{{ $portalCategory }}</span>
                            <i class="fas fa-chevron-right text-[9px] text-slate-300"></i>
                            <span class="text-slate-900 font-bold">@yield('title', 'Fitur')</span>
                        </nav>
                        
                        <a href="{{ route('dashboard') }}" 
                           title="Kembali ke Dashboard"
                           class="btn-press inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-emerald-700 bg-slate-50 hover:bg-emerald-50 px-3 py-1.5 rounded-xl border border-slate-200 hover:border-emerald-200 transition-colors">
                            <i class="fas fa-arrow-left text-[10px]"></i>
                            <span>Kembali ke Dashboard</span>
                        </a>
                    </div>
                    @endif

                    <div class="space-y-4 sm:space-y-5">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div id="flash-success" class="flex items-center justify-between p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-sm transition-all duration-300">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-500/20">
                                    <i class="fas fa-check text-sm"></i>
                                </div>
                                <span class="text-sm font-medium">{{ session('success') }}</span>
                            </div>
                            <button type="button" onclick="dismissAlert('flash-success')" class="text-emerald-700 hover:text-emerald-900 p-1">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div id="flash-error" class="flex items-center justify-between p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-sm transition-all duration-300">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-rose-500/20">
                                    <i class="fas fa-exclamation text-sm"></i>
                                </div>
                                <span class="text-sm font-medium">{{ session('error') }}</span>
                            </div>
                            <button type="button" onclick="dismissAlert('flash-error')" class="text-rose-700 hover:text-rose-900 p-1">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    @endif

                    @if(isset($errors) && $errors->any())
                        <div id="flash-validation-error" class="flex items-start justify-between p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 shadow-sm transition-all duration-300">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm shadow-rose-500/20 mt-0.5">
                                    <i class="fas fa-exclamation-triangle text-sm"></i>
                                </div>
                                <div class="text-xs">
                                    <p class="font-bold mb-1 text-rose-900">Periksa kembali data formulir:</p>
                                    <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                                        @foreach($errors->all() as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" onclick="dismissAlert('flash-validation-error')" class="text-rose-700 hover:text-rose-900 p-1">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    @endif

                    <!-- Global Module Maintenance Banner (Divisible tapi Maintenance) -->
                    @php
                        $currentRouteName = request()->route() ? request()->route()->getName() : '';
                        $routeModuleMap = [
                            'datahaid' => 'haid_pita',
                            'femhealth' => 'haid_pita',
                            'kesehatan' => 'kesehatan',
                            'hb' => 'hemoglobin',
                            'counseling' => 'konsultasi',
                            'nutrition' => 'nutrisi',
                            'burnout' => 'burnout',
                            'ergonomics' => 'ergonomi',
                            'vaccine' => 'vaksinasi',
                        ];
                        $activeRouteModule = null;
                        foreach ($routeModuleMap as $prefix => $mKey) {
                            if (str_contains($currentRouteName, $prefix)) {
                                $activeRouteModule = $mKey;
                                break;
                            }
                        }
                        $globalModuleUnderMaintenance = $activeRouteModule ? \App\Models\GlobalModuleConfig::isModuleInMaintenance($activeRouteModule) : false;
                        $maintenanceConfig = $activeRouteModule ? \App\Models\GlobalModuleConfig::getConfig($activeRouteModule) : null;
                    @endphp
                    @if($globalModuleUnderMaintenance && auth()->check() && !auth()->user()->isSuperAdmin())
                        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-300 text-amber-900 shadow-sm flex items-start gap-3.5 animate-in fade-in duration-300">
                            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm mt-0.5">
                                <i class="fas fa-wrench text-base"></i>
                            </div>
                            <div class="text-xs leading-relaxed flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <strong class="font-bold text-sm text-amber-900">
                                        Modul Sedang Dalam Pemeliharaan / Perbaikan Teknis
                                    </strong>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900">
                                        <i class="fas fa-bug text-[9px]"></i> Bug Fixing In Progress
                                    </span>
                                </div>
                                <p class="text-amber-800">
                                    {{ $maintenanceConfig && $maintenanceConfig->maintenance_message ? $maintenanceConfig->maintenance_message : 'Fitur ini sedang dalam proses perbaikan bug dan penyempurnaan sistem oleh SuperAdministrator. Anda tetap dapat melihat modul ini, namun beberapa fungsionalitas mungkin dibatasi sementara.' }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Page Content Insertion -->
                    @yield('content')

                    <!-- Subtle Footer -->
                    <footer class="pt-8 pb-4 border-t border-slate-200/80 text-center">
                        <p class="text-xs text-slate-400">
                            Aku Sehat &copy; {{ date('Y') }} &bull; <span class="text-emerald-700 font-semibold">Neura Cakrawira Solusi</span>
                        </p>
                    </footer>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Sidebar Toggle & Notification Scripts -->
    <script>
        function updateSidebarToggleState(isCollapsed) {
            const btn = document.getElementById('sidebar-toggle-btn');
            if (!btn) return;
            if (window.innerWidth >= 1024) {
                if (isCollapsed) {
                    btn.setAttribute('title', 'Perluas Sidebar (Ctrl + B)');
                    btn.classList.add('bg-emerald-50', 'text-emerald-700');
                } else {
                    btn.setAttribute('title', 'Ciutkan Sidebar (Ctrl + B)');
                    btn.classList.remove('bg-emerald-50', 'text-emerald-700');
                }
            } else {
                btn.setAttribute('title', 'Buka / Tutup Menu');
                btn.classList.remove('bg-emerald-50', 'text-emerald-700');
            }
        }

        function toggleSidebar() {
            const isDesktop = window.innerWidth >= 1024;
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (isDesktop) {
                const isCurrentlyCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
                const nextState = !isCurrentlyCollapsed;
                
                document.documentElement.classList.toggle('sidebar-collapsed', nextState);
                document.body.classList.toggle('sidebar-collapsed', nextState);
                
                try {
                    localStorage.setItem('sidebar_desktop_collapsed', nextState ? 'true' : 'false');
                } catch (e) {}

                updateSidebarToggleState(nextState);
            } else {
                if (sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('opacity-0', 'pointer-events-none');
                    overlay.classList.add('opacity-100');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('opacity-0', 'pointer-events-none');
                    overlay.classList.remove('opacity-100');
                }
            }
        }

        function dismissAlert(id) {
            const element = document.getElementById(id);
            if (element) {
                element.style.opacity = '0';
                element.style.transform = 'translateY(-8px)';
                setTimeout(() => element.remove(), 250);
            }
        }

        // Close sidebar on window resize to desktop / sync collapsed state
        window.addEventListener('resize', function() {
            const isDesktop = window.innerWidth >= 1024;
            const overlay = document.getElementById('sidebar-overlay');
            if (isDesktop) {
                if (overlay) {
                    overlay.classList.add('opacity-0', 'pointer-events-none');
                    overlay.classList.remove('opacity-100');
                }
                const isCollapsed = localStorage.getItem('sidebar_desktop_collapsed') === 'true';
                document.documentElement.classList.toggle('sidebar-collapsed', isCollapsed);
                document.body.classList.toggle('sidebar-collapsed', isCollapsed);
                updateSidebarToggleState(isCollapsed);
            } else {
                document.documentElement.classList.remove('sidebar-collapsed');
                document.body.classList.remove('sidebar-collapsed');
                updateSidebarToggleState(false);
            }
        });

        // Keyboard Shortcut: Ctrl + B or Cmd + B to toggle sidebar
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 'b' || e.key === 'B')) {
                const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
                const isEditable = document.activeElement && (
                    document.activeElement.isContentEditable || 
                    activeTag === 'textarea' || 
                    (activeTag === 'input' && !['checkbox', 'radio', 'button', 'submit'].includes(document.activeElement.type))
                );
                
                if (!isEditable) {
                    e.preventDefault();
                    toggleSidebar();
                }
            }
        });

        // Toggle Collapsible Sidebar Groups
        function toggleSidebarGroup(groupId) {
            const content = document.getElementById(groupId + '-content');
            const chevron = document.getElementById(groupId + '-chevron');
            if (!content) return;

            const isCurrentlyHidden = content.classList.contains('hidden');
            if (isCurrentlyHidden) {
                content.classList.remove('hidden');
                if (chevron) chevron.classList.remove('-rotate-90');
                try {
                    localStorage.setItem('sidebar_group_' + groupId, 'open');
                } catch (e) {}
            } else {
                content.classList.add('hidden');
                if (chevron) chevron.classList.add('-rotate-90');
                try {
                    localStorage.setItem('sidebar_group_' + groupId, 'closed');
                } catch (e) {}
            }
        }

        // ==========================================================================
        // App Theme Switcher (Atlas Theme [Default] vs Emerald Theme)
        // ==========================================================================
        function updateThemeToggleUI(theme) {
            const btn = document.getElementById('theme-switch-btn');
            const icon = document.getElementById('theme-switch-icon');
            const label = document.getElementById('theme-switch-label');
            if (!btn) return;

            if (icon) icon.className = 'fas fa-paint-roller text-emerald-600';

            if (theme === 'emerald') {
                if (label) label.textContent = 'Switch to Atlas Theme';
                btn.setAttribute('title', 'Switch to Atlas Theme');
            } else {
                if (label) label.textContent = 'Switch to Emerald Theme';
                btn.setAttribute('title', 'Switch to Emerald Theme');
            }
        }

        let isThemeSwitching = false;

        function toggleAppTheme() {
            if (isThemeSwitching) return;
            isThemeSwitching = true;

            const isEmerald = document.documentElement.classList.contains('theme-emerald');
            const nextTheme = isEmerald ? 'atlas' : 'emerald';

            const overlay = document.getElementById('theme-switch-overlay');
            const card = document.getElementById('theme-switch-card');
            const aura = document.getElementById('theme-switch-aura');
            const spinRing = document.getElementById('theme-switch-spin-ring');
            const iconBg = document.getElementById('theme-switch-icon-bg');
            const modalIcon = document.getElementById('theme-switch-modal-icon');
            const badge = document.getElementById('theme-switch-badge');
            const title = document.getElementById('theme-switch-title');
            const desc = document.getElementById('theme-switch-desc');
            const status = document.getElementById('theme-switch-status');
            const percent = document.getElementById('theme-switch-percent');
            const progressBar = document.getElementById('theme-switch-progress-bar');

            // Configure visual theme colors on the modal
            if (nextTheme === 'emerald') {
                if (aura) aura.className = 'absolute w-72 h-72 rounded-full bg-emerald-500/25 blur-3xl pointer-events-none animate-pulse';
                if (spinRing) spinRing.className = 'absolute inset-0 rounded-full border-2 border-dashed border-emerald-400/40 animate-spin';
                if (iconBg) iconBg.className = 'w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30';
                if (modalIcon) modalIcon.className = 'fas fa-paint-roller text-2xl animate-pulse';
                if (badge) badge.textContent = 'Emerald Theme';
                if (title) title.textContent = 'Beralih ke Tema Emerald...';
                if (desc) desc.textContent = 'Menyiapkan bilah navigasi sidebar dan antarmuka terintegrasi...';
                if (progressBar) progressBar.className = 'h-full rounded-full bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-300 transition-all duration-200 ease-out shadow-sm shadow-emerald-500/50';
                if (percent) percent.className = 'font-mono text-emerald-400 font-bold';
            } else {
                if (aura) aura.className = 'absolute w-72 h-72 rounded-full bg-sky-500/25 blur-3xl pointer-events-none animate-pulse';
                if (spinRing) spinRing.className = 'absolute inset-0 rounded-full border-2 border-dashed border-sky-400/40 animate-spin';
                if (iconBg) iconBg.className = 'w-16 h-16 rounded-2xl bg-gradient-to-tr from-sky-600 to-teal-500 text-white flex items-center justify-center shadow-lg shadow-sky-500/30';
                if (modalIcon) modalIcon.className = 'fas fa-paint-roller text-2xl animate-pulse';
                if (badge) badge.textContent = 'Atlas Theme';
                if (title) title.textContent = 'Beralih ke Tema Atlas...';
                if (desc) desc.textContent = 'Menyiapkan portal layanan modern 100% full-width...';
                if (progressBar) progressBar.className = 'h-full rounded-full bg-gradient-to-r from-sky-500 via-teal-400 to-emerald-400 transition-all duration-200 ease-out shadow-sm shadow-sky-500/50';
                if (percent) percent.className = 'font-mono text-sky-400 font-bold';
            }

            // Show Overlay with smooth scale-in
            if (overlay) {
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                overlay.classList.add('opacity-100');
            }
            if (card) {
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            }

            // Progress Stages Animation
            const setProgress = (val, text) => {
                if (progressBar) progressBar.style.width = val + '%';
                if (percent) percent.textContent = val + '%';
                if (status) status.textContent = text;
            };

            setProgress(15, 'Menyiapkan modul visual...');

            setTimeout(() => {
                setProgress(45, 'Menyesuaikan konfigurasi tata letak...');
            }, 200);

            setTimeout(() => {
                setProgress(80, 'Menerapkan preferensi tema...');
            }, 450);

            setTimeout(() => {
                // Apply classes to DOM
                if (nextTheme === 'emerald') {
                    document.documentElement.classList.add('theme-emerald');
                    if (document.body) document.body.classList.add('theme-emerald');
                    document.documentElement.classList.remove('theme-portal', 'theme-atlas');
                    if (document.body) document.body.classList.remove('theme-portal', 'theme-atlas');
                } else {
                    document.documentElement.classList.add('theme-portal', 'theme-atlas');
                    if (document.body) document.body.classList.add('theme-portal', 'theme-atlas');
                    document.documentElement.classList.remove('theme-emerald');
                    if (document.body) document.body.classList.remove('theme-emerald');
                }

                try {
                    localStorage.setItem('app_portal_theme', nextTheme);
                    document.cookie = "app_portal_theme=" + nextTheme + "; path=/; max-age=31536000; SameSite=Lax";
                } catch (e) {}

                updateThemeToggleUI(nextTheme);

                if (window.innerWidth >= 1024) {
                    const isCollapsed = localStorage.getItem('sidebar_desktop_collapsed') === 'true';
                    updateSidebarToggleState(isCollapsed);
                }

                setProgress(100, 'Tema berhasil dimuat!');
            }, 700);

            // Completion / Fadeout / Redirect
            setTimeout(() => {
                const isDashboard = window.location.pathname.endsWith('/dashboard') || window.location.pathname.endsWith('/dashboard/');
                if (!isDashboard) {
                    window.location.href = "{{ route('dashboard') }}";
                } else {
                    if (overlay) {
                        overlay.classList.remove('opacity-100');
                        overlay.classList.add('opacity-0', 'pointer-events-none');
                    }
                    if (card) {
                        card.classList.remove('scale-100');
                        card.classList.add('scale-95');
                    }
                    setTimeout(() => {
                        setProgress(0, 'Menyiapkan modul visual...');
                        isThemeSwitching = false;
                    }, 350);
                }
            }, 950);
        }

        // Initialize theme button UI & sidebar state on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const savedTheme = localStorage.getItem('app_portal_theme') || (document.documentElement.classList.contains('theme-emerald') ? 'emerald' : 'atlas');
                if (savedTheme === 'emerald') {
                    document.documentElement.classList.add('theme-emerald');
                    if (document.body) document.body.classList.add('theme-emerald');
                    document.documentElement.classList.remove('theme-portal', 'theme-atlas');
                    if (document.body) document.body.classList.remove('theme-portal', 'theme-atlas');
                } else {
                    document.documentElement.classList.add('theme-portal', 'theme-atlas');
                    if (document.body) document.body.classList.add('theme-portal', 'theme-atlas');
                    document.documentElement.classList.remove('theme-emerald');
                    if (document.body) document.body.classList.remove('theme-emerald');
                }
                updateThemeToggleUI(savedTheme);

                if (window.innerWidth >= 1024) {
                    const isCollapsed = localStorage.getItem('sidebar_desktop_collapsed') === 'true';
                    document.documentElement.classList.toggle('sidebar-collapsed', isCollapsed);
                    if (document.body) document.body.classList.toggle('sidebar-collapsed', isCollapsed);
                    updateSidebarToggleState(isCollapsed);
                }
            } catch (e) {}
        });

        // Global Portal Feature Search & Filter Handlers
        function filterPortalFeatures(query) {
            const clearBtn = document.getElementById('atlas-search-clear') || document.getElementById('cpanel-search-clear');
            if (clearBtn) {
                clearBtn.classList.toggle('hidden', !query || query.trim() === '');
            }

            const q = (query || '').toLowerCase().trim();
            const cards = document.querySelectorAll('.atlas-tool-card, .cpanel-tool-card');
            let matchedCount = 0;

            cards.forEach(function(card) {
                const title = (card.querySelector('h3') ? card.querySelector('h3').textContent : '').toLowerCase();
                const desc = (card.querySelector('p') ? card.querySelector('p').textContent : '').toLowerCase();
                const keywords = (card.getAttribute('data-keywords') || '').toLowerCase();

                if (q === '' || title.includes(q) || desc.includes(q) || keywords.includes(q)) {
                    card.style.display = 'flex';
                    matchedCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            const sections = document.querySelectorAll('.atlas-section, .cpanel-section');
            sections.forEach(function(section) {
                const visibleCards = section.querySelectorAll('.atlas-tool-card[style*="display: flex"], .atlas-tool-card:not([style*="display: none"]), .cpanel-tool-card[style*="display: flex"], .cpanel-tool-card:not([style*="display: none"])');
                if (q !== '' && visibleCards.length === 0) {
                    section.style.display = 'none';
                } else {
                    section.style.display = 'block';
                }
            });

            const noResults = document.getElementById('atlas-no-results') || document.getElementById('cpanel-no-results');
            if (noResults) {
                noResults.classList.toggle('hidden', matchedCount > 0 || q === '');
            }
        }
        window.filterAtlasFeatures = filterPortalFeatures;
        window.filterCpanelFeatures = filterPortalFeatures;
        window.filterPortalFeatures = filterPortalFeatures;

        function clearPortalSearch() {
            const input = document.getElementById('atlas-search-input') || document.getElementById('cpanel-search-input');
            if (input) {
                input.value = '';
                filterPortalFeatures('');
                input.focus();
            }
        }
        window.clearAtlasSearch = clearPortalSearch;
        window.clearCpanelSearch = clearPortalSearch;
        window.clearPortalSearch = clearPortalSearch;

        function togglePortalCategory(catId) {
            const content = document.getElementById(catId + '-content');
            const chevron = document.getElementById(catId + '-chevron');
            if (!content) return;

            const isHidden = content.classList.contains('hidden');
            if (isHidden) {
                content.classList.remove('hidden');
                if (chevron) chevron.classList.remove('-rotate-90');
            } else {
                content.classList.add('hidden');
                if (chevron) chevron.classList.add('-rotate-90');
            }
        }
        window.toggleAtlasCategory = togglePortalCategory;
        window.toggleCpanelCategory = togglePortalCategory;
        window.togglePortalCategory = togglePortalCategory;

        // Hotkey: press '/' to focus search when in Portal / Atlas Mode
        document.addEventListener('keydown', function(e) {
            if (e.key === '/' && (document.documentElement.classList.contains('theme-portal') || document.documentElement.classList.contains('theme-atlas') || document.documentElement.classList.contains('theme-cpanel'))) {
                const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
                if (activeTag !== 'input' && activeTag !== 'textarea') {
                    e.preventDefault();
                    const searchInput = document.getElementById('atlas-search-input') || document.getElementById('cpanel-search-input');
                    if (searchInput) searchInput.focus();
                }
            }
        });

        // Sidebar scroll preservation across reloads & navigations
        document.addEventListener('DOMContentLoaded', function() {
            // Sync initial desktop sidebar toggle button state
            if (window.innerWidth >= 1024) {
                const isCollapsed = document.documentElement.classList.contains('sidebar-collapsed');
                updateSidebarToggleState(isCollapsed);
            }

            // Sync initial theme toggle UI
            const activeTheme = localStorage.getItem('app_portal_theme') || (document.documentElement.classList.contains('theme-cpanel') ? 'cpanel' : 'default');
            updateThemeToggleUI(activeTheme);

            const nav = document.getElementById('sidebar-nav');
            if (!nav) return;

            // Re-apply scroll position after full layout calculation
            const savedScroll = sessionStorage.getItem('sidebar_scroll_pos');
            if (savedScroll !== null) {
                const targetScroll = parseInt(savedScroll, 10);
                nav.scrollTop = targetScroll;
                requestAnimationFrame(() => {
                    nav.scrollTop = targetScroll;
                });
            }

            // Continuously save scroll position
            nav.addEventListener('scroll', function() {
                sessionStorage.setItem('sidebar_scroll_pos', nav.scrollTop);
            }, { passive: true });

            // Save on clicking any link inside sidebar
            nav.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link) {
                    sessionStorage.setItem('sidebar_scroll_pos', nav.scrollTop);
                }
            });

            // Save before page unloads
            window.addEventListener('beforeunload', function() {
                if (nav) {
                    sessionStorage.setItem('sidebar_scroll_pos', nav.scrollTop);
                }
            });
        });

        // Floating Interactive Tooltip for Mini-Sidebar on Desktop
        (function() {
            let tooltip = document.getElementById('mini-sidebar-tooltip');
            if (!tooltip) {
                tooltip = document.createElement('div');
                tooltip.id = 'mini-sidebar-tooltip';
                tooltip.className = 'fixed z-[99999] pointer-events-none px-3 py-1.5 text-xs font-semibold text-white bg-slate-900/95 backdrop-blur-md border border-emerald-500/40 rounded-xl shadow-2xl shadow-emerald-950/40 opacity-0 transition-opacity duration-150 whitespace-nowrap';
                document.body.appendChild(tooltip);
            }

            document.addEventListener('mouseover', function(e) {
                if (!document.documentElement.classList.contains('sidebar-collapsed') || window.innerWidth < 1024) return;
                
                const target = e.target.closest('#sidebar #sidebar-nav a, #sidebar > div:last-child a, #sidebar #logout-form button');
                if (!target || !document.getElementById('sidebar').contains(target)) return;
                
                let labelText = target.getAttribute('data-sidebar-title') || target.getAttribute('title');

                if (!labelText) {
                    const span = target.querySelector('span');
                    if (span) {
                        labelText = span.textContent.trim();
                    }
                }

                if (!labelText) return;

                tooltip.textContent = labelText;
                const rect = target.getBoundingClientRect();
                
                tooltip.style.left = (rect.right + 12) + 'px';
                tooltip.style.top = (rect.top + (rect.height / 2) - 14) + 'px';
                tooltip.style.opacity = '1';
            });

            document.addEventListener('mouseout', function(e) {
                if (!document.documentElement.classList.contains('sidebar-collapsed') || window.innerWidth < 1024) return;
                const target = e.target.closest('#sidebar a, #sidebar button');
                if (target && tooltip) {
                    tooltip.style.opacity = '0';
                }
            });
        })();

        /* ==========================================================================
           Aku Sehat Confirmation Engine (SweetAlert2 Integration)
           Handles Logout, Deletion, Bulk Delete, and All Sensitive Operations
           ========================================================================== */

        // 1. Dedicated Logout Confirmation
        window.confirmLogout = function(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }
            if (typeof Swal === 'undefined') {
                if (confirm('Apakah Anda yakin ingin keluar dari sistem Aku Sehat?')) {
                    const form = document.getElementById('logout-form') || document.querySelector('form[action*="logout"]');
                    if (form) form.submit();
                }
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Keluar',
                html: 'Apakah Anda yakin ingin keluar dari sistem <strong>Aku Sehat</strong>?<br><span class="text-xs text-slate-500 mt-1 block">Sesi aktif Anda akan diakhiri. Pastikan pekerjaan Anda sudah tersimpan.</span>',
                icon: 'warning',
                iconColor: '#e11d48',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-arrow-right-from-bracket mr-1.5"></i> Ya, Keluar',
                cancelButtonText: '<i class="fas fa-times mr-1.5"></i> Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'swal2-akusehat-popup',
                    title: 'swal2-akusehat-title',
                    htmlContainer: 'swal2-akusehat-html',
                    confirmButton: 'swal2-akusehat-btn-danger',
                    cancelButton: 'swal2-akusehat-btn-cancel',
                    actions: 'swal2-akusehat-actions'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('logout-form') || document.querySelector('form[action*="logout"]');
                    if (form) {
                        form.dataset.swalBypassed = 'true';
                        form.submit();
                    }
                }
            });
        };

        // 2. Generic Action Helper
        window.confirmAction = function(options) {
            if (typeof Swal === 'undefined') {
                return Promise.resolve({ isConfirmed: confirm(options.text || options.title || 'Lanjutkan?') });
            }
            const isDanger = options.isDanger ?? true;
            return Swal.fire({
                title: options.title || 'Konfirmasi Tindakan',
                html: options.html || options.text || 'Apakah Anda yakin ingin melanjutkan?',
                icon: options.icon || (isDanger ? 'warning' : 'question'),
                iconColor: options.iconColor || (isDanger ? '#e11d48' : '#059669'),
                showCancelButton: true,
                confirmButtonText: options.confirmText || (isDanger ? '<i class="fas fa-trash-can mr-1.5"></i> Ya, Lanjutkan' : '<i class="fas fa-check mr-1.5"></i> Ya, Lanjutkan'),
                cancelButtonText: options.cancelText || '<i class="fas fa-times mr-1.5"></i> Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'swal2-akusehat-popup',
                    title: 'swal2-akusehat-title',
                    htmlContainer: 'swal2-akusehat-html',
                    confirmButton: isDanger ? 'swal2-akusehat-btn-danger' : 'swal2-akusehat-btn-emerald',
                    cancelButton: 'swal2-akusehat-btn-cancel',
                    actions: 'swal2-akusehat-actions'
                }
            });
        };

        // 3. Generic Delete Helper
        window.confirmDelete = function(message, onConfirm) {
            return window.confirmAction({
                title: 'Konfirmasi Penghapusan',
                html: `<span>${message || 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.'}</span>`,
                isDanger: true,
                confirmText: '<i class="fas fa-trash-can mr-1.5"></i> Ya, Hapus'
            }).then(result => {
                if (result.isConfirmed && typeof onConfirm === 'function') {
                    onConfirm();
                }
                return result;
            });
        };

        // 4. Universal Sanitizer: Neutralize native browser confirm() inline handlers so SweetAlert is always used
        function neutralizeNativeConfirms() {
            document.querySelectorAll('form[onsubmit*="confirm("]').forEach(form => {
                const attr = form.getAttribute('onsubmit') || '';
                const match = attr.match(/confirm\(\s*(['"])([\s\S]*?)\1\s*\)/);
                if (match) {
                    if (!form.dataset.confirm) {
                        form.dataset.confirm = match[2];
                    }
                    form.removeAttribute('onsubmit');
                    form.onsubmit = null;
                }
            });

            document.querySelectorAll('[onclick*="confirm("]').forEach(el => {
                const attr = el.getAttribute('onclick') || '';
                const match = attr.match(/confirm\(\s*(['"])([\s\S]*?)\1\s*\)/);
                if (match) {
                    if (!el.dataset.confirm) {
                        el.dataset.confirm = match[2];
                    }
                    el.removeAttribute('onclick');
                    el.onclick = null;
                }
            });
        }
        document.addEventListener('DOMContentLoaded', neutralizeNativeConfirms);
        setTimeout(neutralizeNativeConfirms, 100);

        // Intercept click on submit buttons inside forms with confirm/onsubmit to neutralize native confirm before submission
        document.addEventListener('click', function(e) {
            const submitBtn = e.target.closest('button[type="submit"], input[type="submit"], button:not([type])');
            if (!submitBtn) return;
            const form = submitBtn.closest('form');
            if (!form) return;
            if (form.dataset.swalBypassed === 'true') return;

            const onsubmitAttr = form.getAttribute('onsubmit') || '';
            if (onsubmitAttr.includes('confirm(')) {
                const match = onsubmitAttr.match(/confirm\(\s*(['"])([\s\S]*?)\1\s*\)/);
                if (match && !form.dataset.confirm) {
                    form.dataset.confirm = match[2];
                }
                form.removeAttribute('onsubmit');
                form.onsubmit = null;
            }
        }, true);

        // 5. Universal Form Submit Interceptor (Capture Phase)
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;
            if (typeof Swal === 'undefined') return;

            // Allow form submission if already verified by SweetAlert
            if (form.dataset.swalBypassed === 'true') {
                return;
            }

            const actionUrl = form.getAttribute('action') || '';

            // A. Intercept Logout Form
            if (form.id === 'logout-form' || actionUrl.includes('/logout') || actionUrl.endsWith('logout')) {
                e.preventDefault();
                e.stopImmediatePropagation();
                window.confirmLogout();
                return;
            }

            // B. Intercept Bulk / Mass Delete Form
            const isBulkDelete = form.id === 'bulkDeleteForm' || actionUrl.includes('mass_destroy');
            if (isBulkDelete) {
                const checkedBoxes = form.querySelectorAll('input[type="checkbox"]:checked:not(#selectAll)');
                const checkedCount = checkedBoxes.length;

                if (checkedCount === 0) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    Swal.fire({
                        title: 'Tidak Ada Data Terpilih',
                        text: 'Silakan centang setidaknya satu data yang ingin dihapus terlebih dahulu.',
                        icon: 'info',
                        confirmButtonText: '<i class="fas fa-check mr-1.5"></i> Mengerti',
                        buttonsStyling: false,
                        customClass: {
                            popup: 'swal2-akusehat-popup',
                            title: 'swal2-akusehat-title',
                            htmlContainer: 'swal2-akusehat-html',
                            confirmButton: 'swal2-akusehat-btn-emerald',
                            actions: 'swal2-akusehat-actions'
                        }
                    });
                    return;
                }

                e.preventDefault();
                e.stopImmediatePropagation();

                Swal.fire({
                    title: `Hapus ${checkedCount} Data Terpilih?`,
                    html: `Sebanyak <strong>${checkedCount} data</strong> yang dipilih akan dihapus permanen dari sistem.<br><span class="text-xs text-rose-600 font-semibold mt-1 block"><i class="fas fa-triangle-exclamation mr-1"></i> Tindakan ini tidak dapat dibatalkan!</span>`,
                    icon: 'warning',
                    iconColor: '#e11d48',
                    showCancelButton: true,
                    confirmButtonText: `<i class="fas fa-trash-can mr-1.5"></i> Ya, Hapus (${checkedCount})`,
                    cancelButtonText: '<i class="fas fa-times mr-1.5"></i> Batal',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal2-akusehat-popup',
                        title: 'swal2-akusehat-title',
                        htmlContainer: 'swal2-akusehat-html',
                        confirmButton: 'swal2-akusehat-btn-danger',
                        cancelButton: 'swal2-akusehat-btn-cancel',
                        actions: 'swal2-akusehat-actions'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.swalBypassed = 'true';
                        form.submit();
                    }
                });
                return;
            }

            // C. Exclude GET search/filter forms unless explicitly tagged with data-confirm or confirm()
            const formMethod = (form.getAttribute('method') || 'GET').toUpperCase();
            const methodInput = form.querySelector('input[name="_method"]');
            const actualMethod = (methodInput ? methodInput.value : formMethod).toUpperCase();
            const onsubmitAttr = form.getAttribute('onsubmit') || '';
            const dataConfirm = form.dataset.confirm;
            const hasExplicitConfirm = Boolean(dataConfirm || onsubmitAttr.includes('confirm('));

            if (actualMethod === 'GET' && !hasExplicitConfirm) {
                return;
            }

            // Exclude authentication forms (login / register) if not explicitly set
            if (form.id === 'loginForm' || lowerUrl.endsWith('/login') || lowerUrl.includes('/login') ||
                form.id === 'registerForm' || lowerUrl.endsWith('/register') || lowerUrl.includes('/register')) {
                if (!hasExplicitConfirm) return;
            }

            // D. HTML5 Constraint Validation Check
            // Do not prompt confirmation if required fields are empty or invalid
            if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                if (typeof form.reportValidity === 'function') {
                    form.reportValidity();
                }
                return;
            }

            // E. Analyze Context & Determine CUD Action Type
            const submitter = e.submitter || document.activeElement;
            const btnText = (submitter && (submitter.innerText || submitter.value) ? (submitter.innerText || submitter.value) : '').trim();
            const lowerBtn = btnText.toLowerCase();

            let explicitMsg = dataConfirm || form.dataset.confirmText;
            if (!explicitMsg && onsubmitAttr) {
                const match = onsubmitAttr.match(/confirm\(\s*(['"])([\s\S]*?)\1\s*\)/);
                if (match) {
                    explicitMsg = match[2];
                }
            }

            const explicitTitle = form.dataset.confirmTitle || (submitter ? submitter.dataset.confirmTitle : null);
            const explicitBtn = form.dataset.confirmBtn || (submitter ? submitter.dataset.confirmBtn : null);

            // Classification:
            const isDelete = actualMethod === 'DELETE' ||
                lowerUrl.includes('/destroy') ||
                lowerUrl.includes('/delete') ||
                lowerUrl.includes('/avatar/delete') ||
                lowerBtn.includes('hapus') ||
                lowerBtn.includes('delete') ||
                lowerBtn.includes('cabut') ||
                (explicitMsg && (explicitMsg.toLowerCase().includes('hapus') || explicitMsg.toLowerCase().includes('delete') || explicitMsg.toLowerCase().includes('cabut')));

            const isStatusToggle = lowerUrl.includes('/toggle') ||
                lowerUrl.includes('/activate') ||
                lowerUrl.includes('/suspend') ||
                lowerUrl.includes('/ban') ||
                lowerUrl.includes('/approve') ||
                lowerUrl.includes('/reject') ||
                lowerBtn.includes('jadikan tester') ||
                lowerBtn.includes('tester aktif') ||
                lowerBtn.includes('aktifkan') ||
                lowerBtn.includes('pulihkan') ||
                lowerBtn.includes('suspend') ||
                lowerBtn.includes('banned');

            const isUpdate = actualMethod === 'PUT' || actualMethod === 'PATCH' ||
                lowerUrl.includes('/update') ||
                lowerUrl.includes('/edit') ||
                lowerUrl.includes('/renew') ||
                lowerUrl.includes('/modules/update') ||
                lowerUrl.includes('/profile/password') ||
                lowerUrl.includes('/profile') ||
                lowerBtn.includes('simpan perubahan') ||
                lowerBtn.includes('perbarui') ||
                lowerBtn.includes('update') ||
                lowerBtn.includes('ubah') ||
                lowerBtn.includes('simpan pengaturan') ||
                (explicitMsg && (explicitMsg.toLowerCase().includes('perbarui') || explicitMsg.toLowerCase().includes('update') || explicitMsg.toLowerCase().includes('simpan perubahan')));

            const isCreate = !isDelete && !isUpdate && !isStatusToggle && (
                actualMethod === 'POST' ||
                lowerUrl.includes('/store') ||
                lowerUrl.includes('/create') ||
                lowerBtn.includes('tambah') ||
                lowerBtn.includes('simpan') ||
                lowerBtn.includes('buat') ||
                lowerBtn.includes('daftarkan') ||
                lowerBtn.includes('unggah') ||
                lowerBtn.includes('upload') ||
                lowerBtn.includes('rilis') ||
                lowerBtn.includes('import')
            );

            // Defaults configuration:
            let dialogTitle = explicitTitle || 'Konfirmasi Tindakan';
            let message = explicitMsg || 'Apakah Anda yakin ingin memproses tindakan ini?';
            let iconType = 'question';
            let iconColor = '#059669';
            let confirmBtnText = explicitBtn || '<i class="fas fa-check mr-1.5"></i> Ya, Lanjutkan';
            let confirmBtnClass = 'swal2-akusehat-btn-emerald';

            if (isDelete) {
                const isRevoke = lowerBtn.includes('cabut') || (explicitMsg && explicitMsg.toLowerCase().includes('cabut'));
                dialogTitle = explicitTitle || (isRevoke ? 'Konfirmasi Cabut Akses' : 'Konfirmasi Penghapusan');
                message = explicitMsg || (isRevoke ? 'Cabut hak akses dari akun pengguna ini?' : 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.');
                iconType = 'warning';
                iconColor = '#e11d48';
                confirmBtnText = explicitBtn || (isRevoke ? '<i class="fas fa-user-xmark mr-1.5"></i> Ya, Cabut Akses' : '<i class="fas fa-trash-can mr-1.5"></i> Ya, Hapus Data');
                confirmBtnClass = 'swal2-akusehat-btn-danger';
            } else if (isStatusToggle) {
                if (lowerUrl.includes('/tester') || lowerBtn.includes('tester')) {
                    const isRevoke = lowerBtn.includes('cabut');
                    dialogTitle = explicitTitle || (isRevoke ? 'Cabut Hak Akses Tester?' : 'Tetapkan Sebagai Akun Tester?');
                    message = explicitMsg || (isRevoke ? 'Cabut hak akses akun tester dari pengguna ini?' : 'Jadikan akun ini sebagai penguji resmi (Tester)? Pengguna akan dapat mencoba modul berstatus Tester Only.');
                    iconType = isRevoke ? 'warning' : 'question';
                    iconColor = isRevoke ? '#e11d48' : '#9333ea';
                    confirmBtnText = explicitBtn || (isRevoke ? '<i class="fas fa-user-xmark mr-1.5"></i> Ya, Cabut Tester' : '<i class="fas fa-flask mr-1.5"></i> Ya, Jadikan Tester');
                    confirmBtnClass = isRevoke ? 'swal2-akusehat-btn-danger' : 'swal2-akusehat-btn-purple';
                } else if (lowerUrl.includes('/suspend') || lowerUrl.includes('/ban') || lowerBtn.includes('suspend') || lowerBtn.includes('banned')) {
                    const isBan = lowerUrl.includes('/ban') || lowerBtn.includes('banned');
                    dialogTitle = explicitTitle || (isBan ? 'Konfirmasi Banned Akun' : 'Konfirmasi Penangguhan (Suspend)');
                    message = explicitMsg || (isBan ? 'Terapkan sanksi Banned permanen pada akun ini? Sesi dan token aktif akan dicabut segera.' : 'Terapkan status penangguhan (suspend) sementara pada pengguna ini?');
                    iconType = 'warning';
                    iconColor = '#e11d48';
                    confirmBtnText = explicitBtn || (isBan ? '<i class="fas fa-ban mr-1.5"></i> Ya, Banned Akun' : '<i class="fas fa-clock mr-1.5"></i> Ya, Terapkan Suspend');
                    confirmBtnClass = 'swal2-akusehat-btn-danger';
                } else if (lowerUrl.includes('/activate') || lowerBtn.includes('aktifkan') || lowerBtn.includes('pulihkan')) {
                    dialogTitle = explicitTitle || 'Konfirmasi Pengaktifan Akun';
                    message = explicitMsg || 'Pulihkan dan aktifkan kembali akun pengguna ini?';
                    iconType = 'question';
                    iconColor = '#059669';
                    confirmBtnText = explicitBtn || '<i class="fas fa-check mr-1.5"></i> Ya, Aktifkan Akun';
                    confirmBtnClass = 'swal2-akusehat-btn-emerald';
                } else if (lowerUrl.includes('/approve') || lowerBtn.includes('setujui')) {
                    dialogTitle = explicitTitle || 'Konfirmasi Persetujuan';
                    message = explicitMsg || 'Setujui permohonan atau jadwal ini?';
                    iconType = 'question';
                    iconColor = '#059669';
                    confirmBtnText = explicitBtn || '<i class="fas fa-check mr-1.5"></i> Ya, Setujui';
                    confirmBtnClass = 'swal2-akusehat-btn-emerald';
                } else {
                    dialogTitle = explicitTitle || 'Konfirmasi Perubahan Status';
                    message = explicitMsg || 'Apakah Anda yakin ingin memperbarui status ini?';
                    iconType = 'question';
                    iconColor = '#2563eb';
                    confirmBtnText = explicitBtn || '<i class="fas fa-check mr-1.5"></i> Ya, Terapkan';
                    confirmBtnClass = 'swal2-akusehat-btn-blue';
                }
            } else if (isUpdate) {
                dialogTitle = explicitTitle || (lowerUrl.includes('/modules') ? 'Konfirmasi Simpan Pengaturan Modul' : (lowerUrl.includes('/password') ? 'Konfirmasi Perbarui Password' : 'Konfirmasi Simpan Perubahan'));
                message = explicitMsg || (lowerUrl.includes('/modules') ? 'Apakah Anda yakin ingin menerapkan seluruh perubahan konfigurasi status modul dan tester?' : 'Apakah Anda yakin ingin menyimpan seluruh perubahan pada data ini?');
                iconType = 'question';
                iconColor = '#2563eb';
                confirmBtnText = explicitBtn || '<i class="fas fa-floppy-disk mr-1.5"></i> Ya, Simpan Perubahan';
                confirmBtnClass = 'swal2-akusehat-btn-blue';
            } else if (isCreate) {
                dialogTitle = explicitTitle || 'Konfirmasi Tambah Data';
                message = explicitMsg || 'Pastikan data yang diisi telah sesuai. Apakah Anda yakin ingin menyimpan data baru ini ke sistem?';
                iconType = 'question';
                iconColor = '#059669';
                confirmBtnText = explicitBtn || '<i class="fas fa-check mr-1.5"></i> Ya, Simpan Data';
                confirmBtnClass = 'swal2-akusehat-btn-emerald';
            }

            e.preventDefault();
            e.stopImmediatePropagation();

            Swal.fire({
                title: dialogTitle,
                html: `<span>${message}</span>`,
                icon: iconType,
                iconColor: iconColor,
                showCancelButton: true,
                confirmButtonText: confirmBtnText,
                cancelButtonText: '<i class="fas fa-times mr-1.5"></i> Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'swal2-akusehat-popup',
                    title: 'swal2-akusehat-title',
                    htmlContainer: 'swal2-akusehat-html',
                    confirmButton: confirmBtnClass,
                    cancelButton: 'swal2-akusehat-btn-cancel',
                    actions: 'swal2-akusehat-actions'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.swalBypassed = 'true';
                    if (submitter && submitter.name) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = submitter.name;
                        hiddenInput.value = submitter.value;
                        form.appendChild(hiddenInput);
                    }
                    form.submit();
                }
            });
        }, true);

        // 5. Universal Link & Standalone Button Click Interceptor (Capture Phase)
        document.addEventListener('click', function(e) {
            const triggerEl = e.target.closest('a, button[type="button"]');
            if (!triggerEl || typeof Swal === 'undefined') return;
            if (triggerEl.dataset.swalBypassed === 'true') return;

            const onclickAttr = triggerEl.getAttribute('onclick') || '';
            const dataConfirm = triggerEl.dataset.confirm;

            if (dataConfirm || onclickAttr.includes('confirm(')) {
                e.preventDefault();
                e.stopImmediatePropagation();

                let message = dataConfirm;
                if (!message && onclickAttr) {
                    const match = onclickAttr.match(/confirm\(\s*(['"])([\s\S]*?)\1\s*\)/);
                    if (match) message = match[2];
                }
                if (!message) message = 'Apakah Anda yakin ingin memproses tindakan ini?';

                const lowerMsg = message.toLowerCase();
                const isDanger = lowerMsg.includes('hapus') || lowerMsg.includes('delete') || lowerMsg.includes('cabut');
                const isNav = triggerEl.tagName === 'A' && triggerEl.href;

                Swal.fire({
                    title: triggerEl.dataset.confirmTitle || (isDanger ? 'Konfirmasi Tindakan' : (isNav ? 'Konfirmasi Navigasi' : 'Konfirmasi Tindakan')),
                    html: `<span>${message}</span>`,
                    icon: isDanger ? 'warning' : 'question',
                    iconColor: isDanger ? '#e11d48' : '#059669',
                    showCancelButton: true,
                    confirmButtonText: triggerEl.dataset.confirmBtn || (isDanger ? '<i class="fas fa-trash-can mr-1.5"></i> Ya, Lanjutkan' : (isNav ? '<i class="fas fa-arrow-right mr-1.5"></i> Lanjutkan' : '<i class="fas fa-check mr-1.5"></i> Ya, Lanjutkan')),
                    cancelButtonText: '<i class="fas fa-times mr-1.5"></i> Batal',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'swal2-akusehat-popup',
                        title: 'swal2-akusehat-title',
                        htmlContainer: 'swal2-akusehat-html',
                        confirmButton: isDanger ? 'swal2-akusehat-btn-danger' : 'swal2-akusehat-btn-emerald',
                        cancelButton: 'swal2-akusehat-btn-cancel',
                        actions: 'swal2-akusehat-actions'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        triggerEl.dataset.swalBypassed = 'true';
                        if (triggerEl.tagName === 'A' && triggerEl.href) {
                            window.location.href = triggerEl.href;
                        } else if (typeof triggerEl.click === 'function') {
                            triggerEl.click();
                        }
                    }
                });
            }
        }, true);

        /* ==========================================================================
           Realtime Web Version & Auto-Reload Engine (Single-Run Per Version)
           Clears client session storage & cached assets WITHOUT logging user out.
           ========================================================================== */
        (function() {
            let isCheckingUpdate = false;

            function checkWebVersionUpdate() {
                if (isCheckingUpdate) return;
                isCheckingUpdate = true;

                fetch('{{ route('version.check.web') }}', {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store'
                })
                .then(res => res.json())
                .then(data => {
                    isCheckingUpdate = false;
                    if (!data || !data.version) return;

                    const remoteVersion = data.version;
                    const versionBadge = document.getElementById('app-web-version-badge');
                    if (versionBadge) {
                        versionBadge.innerText = 'v' + remoteVersion;
                    }

                    const localVersion = localStorage.getItem('akusehat_web_version');

                    // If first visit, store current version and do nothing
                    if (!localVersion) {
                        localStorage.setItem('akusehat_web_version', remoteVersion);
                        return;
                    }

                    // If a newer version is deployed:
                    if (localVersion !== remoteVersion) {
                        // Immediately update localStorage so this runs STRICTLY ONCE!
                        localStorage.setItem('akusehat_web_version', remoteVersion);

                        // Clear client-side sessionStorage (tabs, filters, scroll positions)
                        try {
                            sessionStorage.clear();
                        } catch (e) {}

                        // Clear browser cache if Cache API is available
                        if ('caches' in window) {
                            try {
                                caches.keys().then(names => {
                                    for (let name of names) caches.delete(name);
                                });
                            } catch (e) {}
                        }

                        // Inform user with SweetAlert toast or auto-reload alert
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Pembaruan Sistem Tersedia!',
                                html: `Versi terbaru <strong>v${remoteVersion}</strong> telah dirilis.<br><span class="text-xs text-slate-500 mt-1 block">Menyegarkan halaman dan membersihkan cache aplikasi...</span>`,
                                icon: 'info',
                                iconColor: '#059669',
                                showConfirmButton: false,
                                timer: 2000,
                                timerProgressBar: true,
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                customClass: {
                                    popup: 'swal2-akusehat-popup',
                                    title: 'swal2-akusehat-title',
                                    htmlContainer: 'swal2-akusehat-html'
                                }
                            }).then(() => {
                                window.location.reload(true);
                            });
                        } else {
                            setTimeout(() => {
                                window.location.reload(true);
                            }, 800);
                        }
                    }
                })
                .catch(() => {
                    isCheckingUpdate = false;
                });
            }

            // Run check on page load without blocking initial rendering
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(checkWebVersionUpdate, 1500);

                // Periodic check every 60 seconds
                setInterval(checkWebVersionUpdate, 60000);

                // Check whenever user switches back to this browser tab
                document.addEventListener('visibilitychange', function() {
                    if (document.visibilityState === 'visible') {
                        checkWebVersionUpdate();
                    }
                });
            });
        })();
    </script>

    <!-- Theme Switch Process Overlay (Screen Process & Progress Bar) -->
    <div id="theme-switch-overlay" 
         class="fixed inset-0 z-[99999] flex items-center justify-center bg-slate-950/80 backdrop-blur-md opacity-0 pointer-events-none transition-all duration-300 ease-out"
         aria-hidden="true">
        <!-- Floating Ambient Aura -->
        <div id="theme-switch-aura" class="absolute w-72 h-72 rounded-full bg-emerald-500/25 blur-3xl pointer-events-none animate-pulse"></div>

        <!-- Glassmorphism Modal Card -->
        <div id="theme-switch-card" class="relative z-10 w-11/12 max-w-sm rounded-3xl bg-slate-900/90 border border-slate-700/80 p-7 text-center text-white shadow-2xl backdrop-blur-xl transform scale-95 transition-transform duration-300 ease-out">
            <!-- Icon with Animated Rings -->
            <div class="relative w-20 h-20 mx-auto mb-5 flex items-center justify-center">
                <!-- Spinning decorative dashed ring -->
                <div id="theme-switch-spin-ring" class="absolute inset-0 rounded-full border-2 border-dashed border-emerald-400/40 animate-spin" style="animation-duration: 8s;"></div>
                <!-- Inner pulse glow ring -->
                <div id="theme-switch-icon-bg" class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <i id="theme-switch-modal-icon" class="fas fa-paint-roller text-2xl animate-bounce"></i>
                </div>
            </div>

            <!-- Target Theme Badge -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-white/10 text-emerald-300 border border-white/15 mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                <span id="theme-switch-badge">Beralih Tema</span>
            </div>

            <!-- Main Title & Description -->
            <h3 id="theme-switch-title" class="text-lg font-extrabold text-white tracking-tight">Menyiapkan Tema Baru...</h3>
            <p id="theme-switch-desc" class="text-xs text-slate-400 mt-1 leading-relaxed">Menyesuaikan tata letak dan konfigurasi navigasi sistem...</p>

            <!-- Progress Bar Section -->
            <div class="mt-6">
                <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1.5 font-medium">
                    <span id="theme-switch-status">Menyiapkan modul visual...</span>
                    <span id="theme-switch-percent" class="font-mono text-emerald-400 font-bold">0%</span>
                </div>
                <div class="w-full h-2.5 bg-slate-950/80 rounded-full overflow-hidden p-0.5 border border-slate-800 shadow-inner">
                    <div id="theme-switch-progress-bar" 
                         class="h-full rounded-full bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-300 transition-all duration-200 ease-out shadow-sm shadow-emerald-500/50" 
                         style="width: 0%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Modal Stack (Outside relative/overflow/sticky stacking contexts) -->
    @stack('modals')

    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
