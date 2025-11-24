@extends('layouts.app')

@section('title', 'Data Sekolah')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Data Sekolah</h1>
            <a href="{{ route('superadmin.sekolah.create') }}"
                class="px-3 text-center sm:px-4 py-2 bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 shadow-soft text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>Tambah Sekolah
            </a>
        </div>

        <!-- Desktop Table -->
        <div class="bg-white rounded-lg shadow-soft hidden sm:block">
            <div class="p-4 sm:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    No</th>
                                <th
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Nama Sekolah</th>
                                <th
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    NPSN</th>
                                <th
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Jenjang</th>
                                <th
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Kota</th>
                                <th
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Status License</th>
                                <th
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    License in Use</th>
                                <th
                                    class="px-3 sm:px-4 py-2 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($sekolah as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-3 sm:px-4 py-2 sm:py-3 text-sm text-gray-700">
                                        {{ $sekolah->firstItem() + $index }}</td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3">
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm sm:text-base">
                                                {{ $item->nama_sekolah }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ \Str::limit($item->alamat, 50) }}</p>
                                        </div>
                                    </td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3 text-sm text-gray-700">{{ $item->npsn }}</td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3">
                                        <span
                                            class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-semibold">{{ $item->jenjang }}</span>
                                    </td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3 text-sm text-gray-700">{{ $item->kota }}</td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3">
                                        @php
                                            $license = $item->getActiveLicense();
                                        @endphp
                                        @if ($license)
                                            <span
                                                class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-semibold">
                                                <i class="fas fa-check-circle mr-1"></i>Aktif
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">Expired:
                                                {{ $license->tanggal_berakhir->format('d/m/Y') }}</p>
                                        @else
                                            <span
                                                class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-semibold">
                                                <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3 text-sm text-gray-700">
                                        <code class="px-2 py-1 bg-gray-100 text-sm text-gray-800 rounded font-mono">{{ $license->key; }}</code>
                                    </td>
                                    <td class="px-3 sm:px-4 py-2 sm:py-3">
                                        <div class="flex items-center justify-center space-x-1 sm:space-x-2">
                                            <a href="{{ route('superadmin.sekolah.edit', $item->id) }}"
                                                class="p-1 sm:p-2 text-blue-600 hover:bg-blue-50 rounded transition-colors duration-150"
                                                title="Edit">
                                                <i class="fas fa-edit text-sm sm:text-base"></i>
                                            </a>
                                            <form action="{{ route('superadmin.sekolah.destroy', $item->id) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-1 sm:p-2 text-red-600 hover:bg-red-50 rounded transition-colors duration-150"
                                                    title="Hapus">
                                                    <i class="fas fa-trash text-sm sm:text-base"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 sm:py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-2xl sm:text-4xl mb-2"></i>
                                        <p class="text-sm sm:text-base">Belum ada data sekolah</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($sekolah->hasPages())
                <div class="px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-200">
                    {{ $sekolah->links() }}
                </div>
            @endif
        </div>

        <!-- Mobile Cards -->
        <div class="space-y-4 sm:hidden">
            @forelse($sekolah as $index => $item)
                @php
                    $license = $item->getActiveLicense();
                @endphp
                <div class="bg-white rounded-lg shadow-soft p-4 border-l-4 border-blue-500">
                    <!-- Header dengan nomor dan status license -->
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-gray-500">#{{ $sekolah->firstItem() + $index }}</span>
                        @if ($license)
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-semibold">
                                <i class="fas fa-check-circle mr-1"></i>Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-semibold">
                                <i class="fas fa-times-circle mr-1"></i>Tidak Aktif
                            </span>
                        @endif
                    </div>

                    <!-- Informasi Sekolah -->
                    <div class="space-y-2">
                        <div>
                            <h3 class="font-semibold text-gray-800 text-base">{{ $item->nama_sekolah }}</h3>
                            <p class="text-xs text-gray-500 mt-1">{{ \Str::limit($item->alamat, 60) }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-500">NPSN:</span>
                                <p class="font-medium text-gray-800">{{ $item->npsn }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Jenjang:</span>
                                <span
                                    class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full font-semibold">{{ $item->jenjang }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Kota:</span>
                                <p class="font-medium text-gray-800">{{ $item->kota }}</p>
                            </div>
                            @if ($license)
                                <div>
                                    <span class="text-gray-500">Expired:</span>
                                    <p class="font-medium text-gray-800 text-xs">
                                        {{ $license->tanggal_berakhir->format('d/m/Y') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-2 mt-4 pt-3 border-t border-gray-100">
                        <a href="{{ route('superadmin.sekolah.edit', $item->id) }}"
                            class="flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <form action="{{ route('superadmin.sekolah.destroy', $item->id) }}" method="POST" class="inline"
                            onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-sm">
                                <i class="fas fa-trash mr-2"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-lg shadow-soft p-6 text-center">
                    <i class="fas fa-inbox text-3xl text-gray-400 mb-3"></i>
                    <p class="text-gray-500">Belum ada data sekolah</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination untuk mobile -->
        @if ($sekolah->hasPages() && $sekolah->count() > 0)
            <div class="sm:hidden bg-white rounded-lg shadow-soft p-4">
                {{ $sekolah->links() }}
            </div>
        @endif
    </div>
@endsection
