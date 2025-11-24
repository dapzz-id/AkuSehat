<nav class="flex-1 px-4 py-6 space-y-2">
    <a href="{{ route('dashboard') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Dashboard
    </a>
    
    <a href="{{ route('guru-bk.peminjaman.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('guru-bk.peminjaman.*') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-clipboard-list mr-3"></i>
        Peminjaman Pita
    </a>
    
    <a href="{{ route('guru-bk.warning') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('guru-bk.warning') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-exclamation-triangle mr-3 text-yellow-400"></i>
        Peringatan
    </a>
</nav>
