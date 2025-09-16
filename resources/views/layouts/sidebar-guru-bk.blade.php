<nav class="space-y-1 px-4">
    <a href="{{ route('dashboard') }}" 
       class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Dashboard
    </a>
    
    <a href="{{ route('guru-bk.peminjaman.index') }}" 
       class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('guru-bk.peminjaman.*') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <i class="fas fa-clipboard-list mr-3"></i>
        Peminjaman Pita
    </a>
    
    <a href="{{ route('guru-bk.warning') }}" 
       class="flex items-center px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('guru-bk.warning') ? 'bg-blue-100 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
        <i class="fas fa-exclamation-triangle mr-3 text-yellow-500"></i>
        Warning Terlambat
    </a>
</nav>