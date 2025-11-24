@extends('layouts.app')

@section('title', 'Manajemen License Key')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Manajemen License Key</h1>
        <a href="{{ route('superadmin.license.create') }}" class="px-4 py-2 text-center bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 shadow-soft text-sm sm:text-base">
            <i class="fas fa-plus mr-2"></i>Generate License
        </a>
    </div>

    <!-- Desktop Table (hidden on mobile) -->
    <div class="hidden sm:block bg-white rounded-lg shadow-soft">
        <div class="p-4 sm:p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sekolah</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">License Key</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kuota</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Periode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Sisa Hari</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($licenses as $index => $license)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $licenses->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $license->sekolah->nama_sekolah }}</p>
                                    <p class="text-xs text-gray-500">{{ $license->sekolah->jenjang }} - {{ $license->sekolah->kota }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <code class="px-2 py-1 bg-gray-100 text-sm text-gray-800 rounded font-mono">{{ $license->key }}</code>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @php
                                    $countSiswaWithLicense = $license->users()->count();
                                @endphp
                                {{ (int)$license->kuota_pengguna - ((int)$countSiswaWithLicense) }}/{{ $license->kuota_pengguna }} User
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-700">
                                    <p class="text-xs text-gray-500">{{ $license->tanggal_berakhir->format('d/m/Y') }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $daysRemaining = $license->getDaysRemaining();
                                    $colorClass = round($daysRemaining) > 30 ? 'text-green-600' : (round($daysRemaining) > 0 ? 'text-yellow-600' : 'text-red-600');
                                @endphp
                                <span class="font-semibold {{ $colorClass }}">
                                    {{ round($daysRemaining) }} hari
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($license->status == 'active')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-semibold">
                                        <i class="fas fa-check-circle mr-1"></i>Aktif
                                    </span>
                                @elseif($license->status == 'expired')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-semibold">
                                        <i class="fas fa-times-circle mr-1"></i>Expired
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full font-semibold">
                                        <i class="fas fa-ban mr-1"></i>Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center space-x-2">
                                    @if($license->status == 'expired')
                                    <button onclick="openRenewModal({{ $license->id }})" class="p-2 text-green-600 hover:bg-green-50 rounded transition-colors duration-150" title="Perpanjang">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    @endif
                                    <a href="{{ route('superadmin.license.edit', $license->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded transition-colors duration-150" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('superadmin.license.destroy', $license->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus license ini?')">
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
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-key text-4xl mb-2"></i>
                                <p>Belum ada license key</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($licenses->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $licenses->links() }}
        </div>
        @endif
    </div>

    <!-- Mobile Cards (shown only on mobile) -->
    <div class="sm:hidden space-y-4">
        @forelse($licenses as $index => $license)
        <div class="bg-white rounded-lg shadow-soft p-4 border-l-4 
            @if($license->status == 'active') border-green-500
            @elseif($license->status == 'expired') border-red-500
            @else border-gray-500 @endif">
            
            <!-- Header dengan No dan Status -->
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                    <span class="w--8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-sm font-semibold text-gray-700">
                        {{ $licenses->firstItem() + $index }}
                    </span>
                </div>
                <div>
                    @if($license->status == 'active')
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-semibold">
                            <i class="fas fa-check-circle mr-1"></i>Aktif
                        </span>
                    @elseif($license->status == 'expired')
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-semibold">
                            <i class="fas fa-times-circle mr-1"></i>Expired
                        </span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full font-semibold">
                            <i class="fas fa-ban mr-1"></i>Nonaktif
                        </span>
                    @endif
                </div>
            </div>

            <!-- Informasi Sekolah -->
            <div class="mb-3">
                <h3 class="font-semibold text-gray-800 text-lg mb-1">{{ $license->sekolah->nama_sekolah }}</h3>
                <p class="text-sm text-gray-600">{{ $license->sekolah->jenjang }} - {{ $license->sekolah->kota }}</p>
            </div>

            <!-- License Key -->
            <div class="mb-3">
                <p class="text-xs font-medium text-gray-600 mb-1">License Key</p>
                <code class="block px-3 py-2 bg-gray-100 text-sm text-gray-800 rounded font-mono break-all">
                    {{ $license->key }}
                </code>
            </div>

            <!-- Informasi Kuota dan Periode -->
            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <p class="text-xs font-medium text-gray-600 mb-1">Kuota</p>
                    @php
                        $countSiswaWithLicense = $license->users()->count();
                    @endphp
                    <p class="text-sm font-semibold text-gray-800">{{ 1000 - ((int)$countSiswaWithLicense) }}/1000 User</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-600 mb-1">Periode</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $license->tanggal_berakhir->format('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Sisa Hari -->
            <div class="mb-4">
                <p class="text-xs font-medium text-gray-600 mb-1">Sisa Hari</p>
                @php
                    $daysRemaining = $license->getDaysRemaining();
                    $colorClass = round($daysRemaining) > 30 ? 'text-green-600' : (round($daysRemaining) > 0 ? 'text-yellow-600' : 'text-red-600');
                @endphp
                <span class="font-semibold {{ $colorClass }} text-lg">
                    {{ round($daysRemaining) }} hari
                </span>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-gray-200">
                @if($license->status == 'expired')
                <button onclick="openRenewModal({{ $license->id }})" class="flex-1 sm:flex-none px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 text-sm font-medium">
                    <i class="fas fa-sync-alt mr-1"></i>Perpanjang
                </button>
                @endif
                <a href="{{ route('superadmin.license.edit', $license->id) }}" class="flex-1 sm:flex-none px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-medium text-center">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <form action="{{ route('superadmin.license.destroy', $license->id) }}" method="POST" class="flex-1 sm:flex-none inline" onsubmit="return confirm('Yakin ingin menghapus license ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-sm font-medium">
                        <i class="fas fa-trash mr-1"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-soft p-8 text-center">
            <i class="fas fa-key text-4xl text-gray-400 mb-3"></i>
            <p class="text-gray-500 text-lg">Belum ada license key</p>
        </div>
        @endforelse

        <!-- Pagination for Mobile -->
        @if($licenses->hasPages())
        <div class="bg-white rounded-lg shadow-soft p-4">
            {{ $licenses->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Renew License -->
<div id="renewModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Perpanjang License</h3>
            <form id="renewForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durasi Perpanjangan (Bulan)</label>
                    <input type="number" name="durasi_bulan" min="1" max="60" required
                        class="w-full px-3 sm:px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm sm:text-base">
                </div>
                <div class="flex items-center justify-end space-x-2 sm:space-x-3">
                    <button type="button" onclick="closeRenewModal()" class="px-3 sm:px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 text-sm sm:text-base">
                        Batal
                    </button>
                    <button type="submit" class="px-3 sm:px-4 py-2 bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 text-sm sm:text-base">
                        <i class="fas fa-sync-alt mr-2"></i>Perpanjang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openRenewModal(licenseId) {
    const modal = document.getElementById('renewModal');
    const form = document.getElementById('renewForm');
    form.action = `/superadmin/license/${licenseId}/renew`;
    modal.classList.remove('hidden');
}

function closeRenewModal() {
    const modal = document.getElementById('renewModal');
    modal.classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('renewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRenewModal();
    }
});
</script>
@endpush