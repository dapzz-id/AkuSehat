<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Required Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page Title -->
    <title>
        @hasSection('title')
            @yield('title') | Aku Sehat
        @else
            Aku Sehat
        @endif
    </title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="@yield('meta_description', 'Sistem Informasi Kesehatan Member Seluruh Instansi dan Institusi di Indonesia')">
    <meta name="author" content="raadeveloperz">
    <meta name="keywords" content="sistem kesehatan, kesehatan member, smk, sma, smp, indonesia, instansi, institusi, kementerian, kesehatan pelajar, pmr, bimbingan konseling, Aku Sehat, raadeveloperz">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2e7d32">
    <meta name="msapplication-TileColor" content="#1b5e20">
    <meta name="msapplication-TileImage" content="{{ asset('src/aku_sehat_white_icon.png') }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Aku Sehat">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Aku Sehat">

    <!-- Favicons -->
    <link rel="icon" href="{{ asset('src/aku_sehat_icon.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('src/aku_sehat_icon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('src/aku_sehat_icon.png') }}">

    <!-- Tailwind CSS -->
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <!-- Google Fonts & Font Awesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <!-- CSRF Token for AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Custom Styles -->
    @stack('styles')

    <!-- Custom Styles for Tom Select -->
    <style>
        /* Wrapper & Control fix */
        .ts-wrapper,
        .ts-control {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        /* Dropdown fix */
        .ts-dropdown {
            position: absolute !important; /* paksa absolute */
            top: 100% !important;
            left: 0 !important;
            right: 0 !important;
            z-index: 9999 !important;
            background: #fff;
            border: 1px solid #d1d5db; /* tailwind gray-300 */
            border-radius: 0.375rem;
            max-height: 250px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Multi select wrap */
        .ts-wrapper.multi .ts-control {
            flex-wrap: wrap !important;
        }

        body {
            overflow-x: hidden;
        }
    </style>
</head>
<body class="h-screen font-sans antialiased bg-neutral-50">
    <div class="flex h-full">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 flex flex-col w-64 text-white transition-transform duration-300 ease-in-out transform -translate-x-full bg-[#308a34] shadow-medium lg:relative lg:translate-x-0" id="sidebar">
            <div class="flex items-center justify-between px-6 py-4 bg-[#1B5E20] shadow-soft">
                <div class="w-full">
                    {{-- Logo untuk layar besar (lg ke atas) --}}
                    <img src="{{ asset('src/aku_sehat_white_icon.png') }}" 
                        alt="Aku Sehat Icon White" 
                        class="w-auto h-12 mx-auto hidden lg:block">

                    {{-- Logo untuk layar kecil & medium (sm, md) --}}
                    <img src="{{ asset('src/aku_sehat_logo.png') }}" 
                        alt="Aku Sehat Logo" 
                        class="w-auto h-12 mx-auto block lg:hidden">
                </div>
                <button class="z-30 text-white lg:hidden hover:text-primary-200" id="btnNav">
                    <i class="text-lg fas fa-times"></i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-2">
                @if(auth()->user()->isAdmin())
                    @include('layouts.sidebar-admin')
                @elseif(auth()->user()->isGuruBK())
                    @include('layouts.sidebar-guru-bk')
                @elseif(auth()->user()->isSuperAdmin())
                    @include('layouts.sidebar-superadmin')
                @endif
            </nav>

            <!-- User info + Logout -->
            <div class="p-4 border-t border-[#1d5a3f]">
                <div class="flex items-center mb-4">
                    <div class="flex items-center justify-center w-10 h-10 mr-3 bg-[#1B5E20] rounded-full">
                        <span class="text-sm font-semibold text-white">
                            {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->nama }}</p>
                        <p class="text-xs text-primary-200">{{ auth()->user()->level }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-2 transition-all duration-200 rounded-lg text-primary-100 hover:bg-[#1B5E20] hover:text-white">
                        <i class="w-5 mr-3 text-center fas fa-sign-out-alt"></i>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Overlay for mobile -->
        <div class="fixed inset-0 hidden bg-opacity-50 bg-black/20 lg:hidden" id="sidebar-overlay"></div>

        <!-- Main Content -->
        <div class="flex flex-col flex-1 min-w-0">
            <!-- Header -->
            <header class="bg-[#308a34] shadow-soft">
                <div class="flex items-center justify-between px-6 py-4">
                    <button class="mr-4 text-white lg:hidden hover:text-secondary-100" onclick="toggleSidebar()">
                        <i class="text-lg fas fa-bars"></i>
                    </button>
                    <div class="lg:flex hidden"></div>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center px-3 py-2 rounded-full bg-[#1B5E20]">
                            <div class="flex items-center justify-center w-8 h-8 bg-white rounded-full lg:mr-2">
                                <span class="text-sm font-semibold text-[#1b5e20]">
                                    {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                                </span>
                            </div>
                            <span class="hidden text-sm font-medium text-white lg:block">{{ auth()->user()->nama }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 p-6 overflow-y-auto">
                @if(session('success'))
                    <div class="px-4 py-3 mb-6 border rounded-lg bg-green-50 border-green-200 text-green-800 shadow-soft">
                        <div class="flex items-center">
                            <i class="mr-2 fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="px-4 py-3 mb-6 border rounded-lg bg-red-50 border-red-200 text-red-800 shadow-soft">
                        <div class="flex items-center">
                            <i class="mr-2 fas fa-exclamation-circle"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

<script>
    const btnNav = document.getElementById("btnNav");
    btnNav.addEventListener('click', toggleSidebar);
    
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.querySelector('[onclick="toggleSidebar()"]');
        if (window.innerWidth < 1024 &&
            !sidebar.contains(event.target) &&
            !sidebarToggle.contains(event.target) &&
            !sidebar.classList.contains('-translate-x-full')) {
            toggleSidebar();
        }
    });
</script>

<!-- Additional Scripts -->
@stack('scripts')
</body>
</html>
