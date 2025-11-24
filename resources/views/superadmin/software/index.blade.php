@extends('layouts.app')

@section('title', 'Update Aplikasi')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Update Aplikasi</h1>
        <a href="{{ route('superadmin.update.create') }}" class="px-4 py-2 bg-[#308a34] text-white text-center rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 shadow-soft text-sm sm:text-base">
            <i class="fas fa-plus mr-2"></i>Upload Update
        </a>
    </div>

    <!-- Desktop Table (hidden on mobile) -->
    <div class="hidden sm:block bg-white rounded-lg shadow-soft">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Versi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Judul</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Platform</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tanggal Rilis</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($updates as $update)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full font-mono font-semibold">
                                    v{{ $update->version }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $update->name }}</p>
                                    <p class="text-xs text-gray-500">{{ \Str::limit($update->deskripsi, 50) }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 
                                    @if($update->platform == 'android') bg-green-100 text-green-800
                                    @elseif($update->platform == 'ios') bg-blue-100 text-blue-800
                                    @else bg-red-100 text-red-800
                                    @endif
                                    text-xs rounded-full font-semibold uppercase">
                                    {{ $update->platform }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $update->release_date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('superadmin.update.download', ['id' => $update->id]) }}" class="p-2 text-green-600 hover:bg-green-50 rounded transition-colors duration-150" title="Download Mobile">
                                        <i class="fas fa-mobile-alt"></i>
                                    </a>
                                    <a href="{{ route('superadmin.update.edit', $update->id) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded transition-colors duration-150" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('superadmin.update.destroy', $update->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus update ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded transition-colors duration-150" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-download text-4xl mb-2"></i>
                                <p>Belum ada update aplikasi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($updates->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $updates->links() }}
        </div>
        @endif
    </div>

    <!-- Mobile Cards (shown only on mobile) -->
    <div class="sm:hidden space-y-4">
        @forelse($updates as $update)
        <div class="bg-white rounded-lg shadow-soft p-4 border-l-4 
            @if($update->platform == 'android') border-green-500
            @elseif($update->platform == 'ios') border-blue-500
            @else border-red-500 @endif">
            
            <!-- Header dengan Versi dan Platform -->
            <div class="flex items-center justify-between mb-3">
                <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm rounded-full font-mono font-semibold">
                    v{{ $update->version }}
                </span>
                <span class="px-2 py-1 
                    @if($update->platform == 'android') bg-green-100 text-green-800
                    @elseif($update->platform == 'ios') bg-blue-100 text-blue-800
                    @else bg-red-100 text-red-800
                    @endif
                    text-xs rounded-full font-semibold uppercase">
                    {{ $update->platform }}
                </span>
            </div>

            <!-- Judul Update -->
            <div class="mb-3">
                <h3 class="font-semibold text-gray-800 text-lg mb-2">{{ $update->name }}</h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $update->deskripsi }}</p>
            </div>

            <!-- Tanggal Rilis -->
            <div class="mb-4">
                <p class="text-xs font-medium text-gray-600 mb-1">Tanggal Rilis</p>
                <p class="text-sm font-semibold text-gray-800">{{ $update->release_date->format('d F Y') }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-gray-200">
                <a href="{{ route('superadmin.update.download', ['id' => $update->id]) }}" class="flex-1 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 text-sm font-medium text-center">
                    <i class="fas fa-mobile-alt"></i>
                </a>
                <a href="{{ route('superadmin.update.edit', $update->id) }}" class="flex-1 px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200 text-sm font-medium text-center">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('superadmin.update.destroy', $update->id) }}" method="POST" class="flex-1 inline" onsubmit="return confirm('Yakin ingin menghapus update ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-3 py-2 text-center bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-sm font-medium">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-soft p-8 text-center">
            <i class="fas fa-download text-4xl text-gray-400 mb-3"></i>
            <p class="text-gray-500 text-lg">Belum ada update aplikasi</p>
        </div>
        @endforelse

        <!-- Pagination for Mobile -->
        @if($updates->hasPages())
        <div class="bg-white rounded-lg shadow-soft p-4">
            {{ $updates->links() }}
        </div>
        @endif
    </div>
</div>
@endsection