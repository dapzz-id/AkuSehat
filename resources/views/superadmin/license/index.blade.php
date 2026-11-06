@extends('layouts.app')

@section('title', 'Manajemen License Key')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Manajemen License Key</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Daftar lisensi aktif dan kadaluarsa untuk instansi dan sekolah.</p>
        </div>
        <a href="{{ route('superadmin.license.create') }}" class="px-4 py-2 text-center bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 shadow-soft text-sm sm:text-base font-medium">
            <i class="fas fa-plus mr-2"></i>Generate License
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-lg shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-exclamation-circle text-rose-600 text-lg"></i>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

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
                                    <p class="font-semibold text-gray-800">{{ $license->sekolah->nama_sekolah ?? 'Belum Ditugaskan' }}</p>
                                    <p class="text-xs text-gray-500">{{ $license->sekolah ? ($license->sekolah->jenjang . ' - ' . $license->sekolah->kota) : '-' }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <code class="px-2 py-1 bg-gray-100 text-sm text-gray-800 rounded font-mono">{{ $license->key }}</code>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                @php
                                    $usedQuota = $license->getUsedQuota();
                                    $totalQuota = (int) $license->kuota_pengguna;
                                    $remQuota = max(0, $totalQuota - $usedQuota);
                                    $deleteInfo = isset($licenseKeyService) 
                                        ? $licenseKeyService->canDeleteLicense($license) 
                                        : app(\App\Modules\SuperAdmin\LicenseKey\Services\LicenseKeyService::class)->canDeleteLicense($license);
                                @endphp
                                <div class="font-bold text-gray-800">
                                    {{ $usedQuota }}/{{ $totalQuota }} User
                                </div>
                                <div class="text-[11px] text-gray-400">
                                    Terpakai: {{ $usedQuota }} (Sisa {{ $remQuota }})
                                </div>
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
                                    <button type="button" onclick="openRenewModal({{ $license->id }})" class="p-2 text-green-600 hover:bg-green-50 rounded transition-colors duration-150" title="Perpanjang">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                    @endif
                                    <a href="{{ route('superadmin.license.edit', $license->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded transition-colors duration-150" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('superadmin.license.destroy', $license->id) }}" 
                                          method="POST" 
                                          class="inline form-delete-license"
                                          data-key="{{ $license->key }}"
                                          data-school="{{ $license->sekolah->nama_sekolah ?? 'Belum Ditugaskan' }}"
                                          data-can-delete="{{ $deleteInfo['can_delete'] ? '1' : '0' }}"
                                          data-action-type="{{ $deleteInfo['action_type'] }}"
                                          data-reason="{{ $deleteInfo['reason'] ?? '' }}"
                                          data-used="{{ $deleteInfo['used_quota'] }}"
                                          data-total-users="{{ $deleteInfo['total_users'] }}"
                                          data-other-quota="{{ $deleteInfo['other_quota'] }}"
                                          data-target-key="{{ $deleteInfo['target_license'] ? $deleteInfo['target_license']->key : '' }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" 
                                                onclick="confirmDeleteLicense(this.closest('form'))" 
                                                class="p-2 text-red-600 hover:bg-red-50 rounded transition-colors duration-150" 
                                                title="Hapus">
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
                <h3 class="font-semibold text-gray-800 text-lg mb-1">{{ $license->sekolah->nama_sekolah ?? 'Belum Ditugaskan' }}</h3>
                <p class="text-sm text-gray-600">{{ $license->sekolah ? ($license->sekolah->jenjang . ' - ' . $license->sekolah->kota) : '-' }}</p>
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
                        $usedQuota = $license->getUsedQuota();
                        $totalQuota = (int) $license->kuota_pengguna;
                        $remQuota = max(0, $totalQuota - $usedQuota);
                        $deleteInfo = isset($licenseKeyService) 
                            ? $licenseKeyService->canDeleteLicense($license) 
                            : app(\App\Modules\SuperAdmin\LicenseKey\Services\LicenseKeyService::class)->canDeleteLicense($license);
                    @endphp
                    <p class="text-sm font-semibold text-gray-800">{{ $usedQuota }}/{{ $totalQuota }} User</p>
                    <p class="text-[11px] text-gray-500">Terpakai: {{ $usedQuota }} (Sisa {{ $remQuota }})</p>
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
                <button type="button" onclick="openRenewModal({{ $license->id }})" class="flex-1 sm:flex-none px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 text-sm font-medium">
                    <i class="fas fa-sync-alt mr-1"></i>Perpanjang
                </button>
                @endif
                <a href="{{ route('superadmin.license.edit', $license->id) }}" class="flex-1 sm:flex-none px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 text-sm font-medium text-center">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <form action="{{ route('superadmin.license.destroy', $license->id) }}" 
                      method="POST" 
                      class="flex-1 sm:flex-none inline form-delete-license"
                      data-key="{{ $license->key }}"
                      data-school="{{ $license->sekolah->nama_sekolah ?? 'Belum Ditugaskan' }}"
                      data-can-delete="{{ $deleteInfo['can_delete'] ? '1' : '0' }}"
                      data-action-type="{{ $deleteInfo['action_type'] }}"
                      data-reason="{{ $deleteInfo['reason'] ?? '' }}"
                      data-used="{{ $deleteInfo['used_quota'] }}"
                      data-total-users="{{ $deleteInfo['total_users'] }}"
                      data-other-quota="{{ $deleteInfo['other_quota'] }}"
                      data-target-key="{{ $deleteInfo['target_license'] ? $deleteInfo['target_license']->key : '' }}">
                    @csrf
                    @method('DELETE')
                    <button type="button" 
                            onclick="confirmDeleteLicense(this.closest('form'))"
                            class="w-full px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 text-sm font-medium">
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

function confirmDeleteLicense(form) {
    if (!form) return;
    const key = form.dataset.key || 'Lisensi';
    const school = form.dataset.school || 'Instansi';
    const canDelete = form.dataset.canDelete === '1';
    const actionType = form.dataset.actionType || 'direct';
    const reason = form.dataset.reason || '';
    const totalUsers = parseInt(form.dataset.totalUsers || '0');
    const otherQuota = parseInt(form.dataset.otherQuota || '0');
    const targetKey = form.dataset.targetKey || '';

    // KASUS 1: Lisensi TIDAK BISA DIHAPUS (sedang digunakan atau kuota lisensi lain tidak cukup)
    if (!canDelete) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Lisensi Tidak Dapat Dihapus',
                html: `
                    <div class="text-left text-sm text-slate-600 space-y-3">
                        <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-800">
                            <div class="flex items-center gap-2 font-bold mb-1">
                                <i class="fas fa-triangle-exclamation text-rose-600"></i>
                                <span>Penghapusan Ditolak Sistem</span>
                            </div>
                            <p class="text-xs leading-relaxed">${reason}</p>
                        </div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div><strong>License Key:</strong> <code class="px-1.5 py-0.5 bg-slate-100 rounded text-slate-800 font-mono">${key}</code></div>
                            <div><strong>Instansi:</strong> ${school}</div>
                            <div><strong>Pengguna Aktif:</strong> ${totalUsers} pengguna</div>
                            ${otherQuota > 0 ? `<div><strong>Kapasitas Lisensi Aktif Lain:</strong> ${otherQuota} kuota</div>` : ''}
                        </div>
                    </div>
                `,
                icon: 'warning',
                iconColor: '#e11d48',
                confirmButtonText: '<i class="fas fa-check mr-1.5"></i> Saya Mengerti',
                buttonsStyling: false,
                customClass: {
                    popup: 'swal2-akusehat-popup',
                    title: 'swal2-akusehat-title',
                    htmlContainer: 'swal2-akusehat-html',
                    confirmButton: 'swal2-akusehat-btn-emerald',
                    actions: 'swal2-akusehat-actions'
                }
            });
        } else {
            alert(reason || `Lisensi [${key}] tidak dapat dihapus.`);
        }
        return;
    }

    // KASUS 2: Lisensi bisa dihapus dan kuota dialihkan ke lisensi lain
    if (actionType === 'transfer') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Pengalihan & Hapus Lisensi',
                html: `
                    <div class="text-left text-sm text-slate-600 space-y-3">
                        <p>License Key <code class="px-1.5 py-0.5 bg-slate-100 rounded text-slate-800 font-mono font-bold">${key}</code> sedang digunakan oleh <strong>${totalUsers} pengguna</strong> di <strong>${school}</strong>.</p>
                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 text-xs leading-relaxed">
                            <div class="font-bold flex items-center gap-1.5 mb-1">
                                <i class="fas fa-arrow-right-arrow-left text-blue-600"></i>
                                <span>Pengalihan Kuota Otomatis</span>
                            </div>
                            Seluruh ${totalUsers} pengguna akan <strong>dialihkan ke lisensi aktif berikutnya:</strong><br>
                            <code class="px-1.5 py-0.5 bg-white border border-blue-200 rounded font-mono font-bold text-blue-800 mt-1 inline-block">${targetKey}</code>
                            <span class="block mt-1 text-[11px] text-blue-700 font-medium">(Kapasitas mencukupi: ${otherQuota} kuota).</span>
                        </div>
                        <p class="text-xs text-rose-600 font-semibold"><i class="fas fa-triangle-exclamation mr-1"></i> License Key [${key}] akan dihapus permanen.</p>
                    </div>
                `,
                icon: 'question',
                iconColor: '#0284c7',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-arrow-right-arrow-left mr-1.5"></i> Ya, Alihkan & Hapus',
                cancelButtonText: '<i class="fas fa-times mr-1.5"></i> Batal',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'swal2-akusehat-popup',
                    title: 'swal2-akusehat-title',
                    htmlContainer: 'swal2-akusehat-html',
                    confirmButton: 'swal2-akusehat-btn-danger',
                    cancelButton: 'swal2-akusehat-btn-cancel',
                    actions: 'swal2-akusehat-actions'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.dataset.swalBypassed = 'true';
                    form.submit();
                }
            });
        } else {
            if (confirm(`Lisensi [${key}] akan dihapus dan pengguna dialihkan ke lisensi [${targetKey}]. Lanjutkan?`)) {
                form.dataset.swalBypassed = 'true';
                form.submit();
            }
        }
        return;
    }

    // KASUS 3: Lisensi tidak digunakan atau belum ditugaskan (penghapusan langsung)
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus License Key?',
            html: `
                <div class="text-left text-sm text-slate-600 space-y-2">
                    <p>Apakah Anda yakin ingin menghapus License Key berikut?</p>
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs space-y-1">
                        <div><strong>Key:</strong> <code class="px-1.5 py-0.5 bg-white border rounded text-slate-800 font-mono">${key}</code></div>
                        <div><strong>Instansi:</strong> ${school}</div>
                    </div>
                    <p class="text-xs text-rose-600 font-semibold"><i class="fas fa-triangle-exclamation mr-1"></i> Tindakan ini tidak dapat dibatalkan!</p>
                </div>
            `,
            icon: 'warning',
            iconColor: '#e11d48',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-trash-can mr-1.5"></i> Ya, Hapus',
            cancelButtonText: '<i class="fas fa-times mr-1.5"></i> Batal',
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                popup: 'swal2-akusehat-popup',
                title: 'swal2-akusehat-title',
                htmlContainer: 'swal2-akusehat-html',
                confirmButton: 'swal2-akusehat-btn-danger',
                cancelButton: 'swal2-akusehat-btn-cancel',
                actions: 'swal2-akusehat-actions'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.dataset.swalBypassed = 'true';
                form.submit();
            }
        });
    } else {
        if (confirm(`Yakin ingin menghapus license [${key}]?`)) {
            form.dataset.swalBypassed = 'true';
            form.submit();
        }
    }
}
</script>
@endpush