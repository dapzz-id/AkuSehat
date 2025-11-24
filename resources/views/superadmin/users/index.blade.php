@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Manajemen User</h1>
        <a href="{{ route('superadmin.users.create') }}" class="px-3 sm:px-4 py-2 text-center bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 shadow-soft text-sm sm:text-base">
            <i class="fas fa-plus mr-2"></i>Tambah User
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-soft p-4 sm:p-6">
        <form method="GET" action="{{ route('superadmin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2">Role</label>
                <select name="level" class="w-full px-3 sm:px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                    <option value="">Semua Role</option>
                    <option value="Admin Sekolah" {{ request('level') == 'Admin Sekolah' ? 'selected' : '' }}>Admin Sekolah</option>
                    <option value="SuperAdmin" {{ request('level') == 'SuperAdmin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2">Sekolah</label>
                <select id="sekolahSelect" name="sekolah_id" 
                    class="w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                    <option value="">SEMUA SEKOLAH</option>
                    @foreach($sekolah as $s)
                        <option value="{{ $s->id }}" {{ request('sekolah_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
                
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        new TomSelect("#sekolahSelect", {
                            create: false,
                            sortField: {
                                field: "text",
                                direction: "asc"
                            },
                            placeholder: "Pilih sekolah...",
                            allowEmptyOption: true,
                            maxOptions: 1000,
                            render: {
                                option: function(data, escape) {
                                    return `<div class="py-2 px-2 text-sm">${escape(data.text)}</div>`;
                                },
                                item: function(data, escape) {
                                    return `<div class="py-1 text-sm">${escape(data.text)}</div>`;
                                }
                            }
                        });
                    });
                </script>
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama/Username..."
                    class="w-full px-3 sm:px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
            </div>
            <div class="sm:col-span-2 lg:col-span-1 flex items-end space-x-2">
                <button type="submit" class="px-3 sm:px-4 py-2 bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 flex-1 text-sm sm:text-base">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('superadmin.users.index') }}" class="px-3 sm:px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 text-sm sm:text-base">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-soft">
        <div class="p-4 sm:p-6">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                            <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Username</th>
                            <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Role</th>
                            <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sekolah</th>
                            <th class="px-3 py-2 sm:px-4 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $index => $user)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-3 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm text-gray-700">{{ $users->firstItem() + $index }}</td>
                            <td class="px-3 py-2 sm:px-4 sm:py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-[#308a34] rounded-full flex items-center justify-center mr-2 sm:mr-3">
                                        <span class="text-white font-semibold text-xs sm:text-sm">{{ strtoupper(substr($user->nama, 0, 1)) }}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-semibold text-gray-800 text-sm sm:text-base truncate">{{ $user->nama }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm text-gray-700">{{ $user->username }}</td>
                            <td class="px-3 py-2 sm:px-4 sm:py-3">
                                <span class="px-2 py-1 text-xs 
                                    @if($user->level == 'SuperAdmin') bg-red-100 text-red-800
                                    @elseif($user->level == 'Admin Sekolah') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif
                                    rounded-full font-semibold">
                                    {{ $user->level == 'SuperAdmin' ? 'Super Admin' : ($user->level == 'Admin Sekolah' ? 'Admin Sekolah' : 'Siswa') }}
                                </span>
                            </td>
                            <td class="px-3 py-2 sm:px-4 sm:py-3 text-xs sm:text-sm text-gray-700 max-w-[150px] truncate">
                                {{ $user->sekolah->nama_sekolah ?? '-' }}
                            </td>
                            <td class="px-3 py-2 sm:px-4 sm:py-3">
                                <div class="flex items-center justify-center space-x-1 sm:space-x-2">
                                    <a href="{{ route('superadmin.users.edit', $user->id) }}" class="p-1 sm:p-2 text-blue-600 hover:bg-blue-50 rounded transition-colors duration-150" title="Edit">
                                        <i class="fas fa-edit text-sm sm:text-base"></i>
                                    </a>
                                    @if($user->id != auth()->id())
                                    <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 sm:p-2 text-red-600 hover:bg-red-50 rounded transition-colors duration-150" title="Hapus">
                                            <i class="fas fa-trash text-sm sm:text-base"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 sm:py-8 text-center text-gray-500">
                                <i class="fas fa-users text-2xl sm:text-4xl mb-2"></i>
                                <p class="text-sm sm:text-base">Belum ada data user</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($users->hasPages())
        <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection