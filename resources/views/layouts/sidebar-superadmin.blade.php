<nav class="flex-1 px-4 py-6 space-y-2">
    <a href="{{ route('dashboard') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Dashboard
    </a>
    
    <a href="{{ route('superadmin.sekolah.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.sekolah.*') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-school mr-3"></i>
        Data Sekolah
    </a>
    
    <a href="{{ route('superadmin.users.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.users.*') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-user-shield mr-3"></i>
        Manajemen User
    </a>
    
    <a href="{{ route('superadmin.license.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.license.*') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-key mr-3"></i>
        License Key
    </a>
    
    <a href="{{ route('superadmin.update.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.update.*') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-download mr-3"></i>
        Update Aplikasi
    </a>
    
    <a href="{{ route('superadmin.maintenance.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('superadmin.maintenance.*') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-tools mr-3"></i>
        Maintenance Mode
    </a>
</nav>