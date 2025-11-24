@extends('layouts.app')

@section('title', 'Maintenance Mode')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Maintenance Mode</h1>
    </div>

    <!-- Current Status Cards -->
    <div class="grid grid-cols-1 gap-4 sm:gap-6 md:grid-cols-2">
        <!-- Web Application Status -->
        <div class="bg-white rounded-lg shadow-soft p-4 sm:p-6 border-l-4 {{ \App\Models\MaintenanceMode::isWebInMaintenance() ? 'border-red-500' : 'border-green-500' }}">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 {{ \App\Models\MaintenanceMode::isWebInMaintenance() ? 'bg-red-100' : 'bg-green-100' }} rounded-full mr-2 sm:mr-3">
                        <i class="fas fa-desktop text-xl sm:text-2xl {{ \App\Models\MaintenanceMode::isWebInMaintenance() ? 'text-red-600' : 'text-green-600' }}"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Web Application</h3>
                        <p class="text-xs sm:text-sm text-gray-600">Status Platform Web</p>
                    </div>
                </div>
                <span class="px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm {{ \App\Models\MaintenanceMode::isWebInMaintenance() ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' }} rounded-full font-semibold">
                    {{ \App\Models\MaintenanceMode::isWebInMaintenance() ? 'Maintenance' : 'Online' }}
                </span>
            </div>
        </div>

        <!-- Mobile Application Status -->
        <div class="bg-white rounded-lg shadow-soft p-4 sm:p-6 border-l-4 {{ \App\Models\MaintenanceMode::isMobileInMaintenance() ? 'border-red-500' : 'border-green-500' }}">
            <div class="flex items-center justify-between mb-3 sm:mb-4">
                <div class="flex items-center">
                    <div class="p-2 sm:p-3 {{ \App\Models\MaintenanceMode::isMobileInMaintenance() ? 'bg-red-100' : 'bg-green-100' }} rounded-full mr-2 sm:mr-3">
                        <i class="fas fa-mobile-alt text-xl sm:text-2xl {{ \App\Models\MaintenanceMode::isMobileInMaintenance() ? 'text-red-600' : 'text-green-600' }}"></i>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Mobile Application</h3>
                        <p class="text-xs sm:text-sm text-gray-600">Status Platform Mobile</p>
                    </div>
                </div>
                <span class="px-3 py-1 sm:px-4 sm:py-2 text-xs sm:text-sm {{ \App\Models\MaintenanceMode::isMobileInMaintenance() ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' }} rounded-full font-semibold">
                    {{ \App\Models\MaintenanceMode::isMobileInMaintenance() ? 'Maintenance' : 'Online' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Maintenance History -->
    <div class="bg-white rounded-lg shadow-soft">
        <div class="p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Riwayat Maintenance</h3>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Platform</th>
                            <th class="px-3 py-2 sm:px-4 sm:py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-3 py-2 sm:px-4 sm:py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($maintenances as $maintenance)
                        @php
                            $isActive = ($maintenance->id == 1 && \App\Models\MaintenanceMode::isMobileInMaintenance()) ||
                                        ($maintenance->id == 2 && \App\Models\MaintenanceMode::isWebInMaintenance());
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-3 py-2 sm:px-4 sm:py-3">
                                <div class="flex items-center">
                                    <i class="fas fa-{{ $maintenance->id == 2 ? 'desktop' : ($maintenance->id == 1 ? 'mobile-alt' : 'globe') }} mr-2 text-gray-600 text-sm"></i>
                                    <span class="font-medium text-gray-800 text-sm">{{ ucfirst($maintenance->id == 1 ? 'mobile' : ($maintenance->id == 2 ? 'website' : 'other')) }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-2 sm:px-4 sm:py-3">
                                <span class="px-2 py-1 {{ $isActive ? 'bg-red-200 text-red-800' : 'bg-gray-200 text-gray-800' }} text-xs rounded-full font-semibold">
                                    {{ $isActive ? 'Maintenance' : 'Aktif' }}
                                </span>
                            </td>
                            <td class="px-3 py-2 sm:px-4 sm:py-3">
                                <div class="flex items-center justify-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox"
                                            id="switch-{{ $maintenance->id }}"
                                            onclick="openToggleModal('{{ $maintenance->id }}', '{{ $maintenance->id == 1 ? 'Mobile' : ($maintenance->id == 2 ? 'Web' : 'Other') }}', {{ $isActive ? 'true' : 'false' }})"
                                            class="sr-only peer"
                                            {{ $isActive ? 'checked' : '' }}>
                                        
                                        <div class="w-12 h-7 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-green-400 rounded-full peer 
                                                    peer-checked:after:translate-x-6 
                                                    peer-checked:bg-green-500 
                                                    after:content-[''] after:absolute after:top-[3px] after:left-[3px]
                                                    after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 sm:py-8 text-center text-gray-500">
                                <i class="fas fa-tools text-2xl sm:text-4xl mb-2"></i>
                                <p class="text-sm sm:text-base">Belum ada riwayat maintenance</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Toggle Maintenance -->
<div id="toggleModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg w-full max-w-md">
        <div class="p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4" id="toggleModalTitle">Toggle Maintenance</h3>
            <form id="toggleForm" method="POST">
                @csrf
                <input type="hidden" name="platform" id="togglePlatform">
                
                <div class="mb-4">
                    <p class="text-sm text-gray-700" id="toggleMessage"></p>
                </div>

                <div class="flex items-center justify-end space-x-2 sm:space-x-3">
                    <button type="button" onclick="cancelToggleModal()" class="px-3 sm:px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200 text-sm sm:text-base">
                        Batal
                    </button>
                    <button type="button" onclick="submitToggleForm()" class="px-3 sm:px-4 py-2 bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 text-sm sm:text-base">
                        <i class="fas fa-sync-alt mr-2"></i>Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let lastSwitchId = null;
let lastSwitchChecked = false;

function openToggleModal(id, platform, isActive) {
    // Simpan toggle terakhir agar bisa dikembalikan kalau dibatalkan
    lastSwitchId = id;
    lastSwitchChecked = isActive;

    window.lastToggledCheckbox = document.getElementById(`switch-${id}`);
    window.lastToggledState = isActive;

    const modal = document.getElementById('toggleModal');
    const form = document.getElementById('toggleForm');
    const title = document.getElementById('toggleModalTitle');
    const message = document.getElementById('toggleMessage');
    const platformInput = document.getElementById('togglePlatform');

    // Gunakan JS murni untuk set action
    form.action = `/superadmin/maintenance/${id}/toggle`;

    platformInput.value = platform.toLowerCase().replace(/\s+/g, '');

    const action = isActive ? 'nonaktifkan' : 'aktifkan';
    const actionTitle = isActive ? 'Non-Aktifkan' : 'Aktifkan';

    title.textContent = `${actionTitle} Maintenance`;
    message.textContent = `Apakah Anda yakin ingin ${action} maintenance mode untuk platform ${platform}?`;

    modal.classList.remove('hidden');
}

function cancelToggleModal() {
    const modal = document.getElementById('toggleModal');
    modal.classList.add('hidden');

    // Matikan saklar kembali ke posisi semula
    if (lastSwitchId) {
        const switchEl = document.getElementById(`switch-${lastSwitchId}`);
        if (switchEl) switchEl.checked = lastSwitchChecked;
    }
}

function submitToggleForm() {
    const form = document.getElementById('toggleForm');
    const platformInput = document.getElementById('togglePlatform');
    form.submit();
}

// Tutup modal bila klik luar area
document.addEventListener('click', function(e) {
    const modal = document.getElementById('toggleModal');
    if (e.target.id === 'toggleModal') {
        cancelToggleModal();
    }
});
</script>
@endpush
