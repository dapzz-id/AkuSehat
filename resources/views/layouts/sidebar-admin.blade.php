<nav class="flex-1 px-4 py-6 space-y-2">
    <a href="{{ route('dashboard') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-tachometer-alt mr-3"></i>
        Dashboard
    </a>
    
    <a href="{{ route('admin.kesehatan.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.kesehatan.*') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-stethoscope mr-3"></i>
        Data Kesehatan
    </a>
    
    <a href="{{ route('admin.hb.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.hb.*') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-tint mr-3"></i>
        Data HB
    </a>
    
    <a href="{{ route('admin.member.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.member.*') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-users mr-3"></i>
        Data Member
    </a>
    
    <a href="{{ route('admin.kelas.index') }}" 
       class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.kelas.*') ? 'bg-[#1B5E20] text-white shadow-soft' : 'text-primary-100 hover:bg-[#1B5E20] hover:text-white' }}">
        <i class="fas fa-school mr-3"></i>
        Data Divisi
    </a>
</nav>