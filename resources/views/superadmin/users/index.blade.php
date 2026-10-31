@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Manajemen User</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola seluruh pengguna dari semua instansi, kontrol hak akses, serta suspend atau ban akun.</p>
        </div>
        <a href="{{ route('superadmin.users.create') }}" class="px-3 sm:px-4 py-2 text-center bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 shadow-soft text-sm sm:text-base">
            <i class="fas fa-plus mr-2"></i>Tambah User
        </a>
    </div>

    {{-- ALERT NOTIFIKASI --}}
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

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-soft p-4 sm:p-5 border border-gray-100">
        <form method="GET" action="{{ route('superadmin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 lg:grid-cols-12 gap-3.5 sm:gap-4 items-end">
            {{-- Filter Role --}}
            <div class="col-span-1 sm:col-span-1 md:col-span-3 lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5 truncate">Role / Peran</label>
                <select name="level" class="w-full h-[42px] px-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm bg-white text-gray-800 transition-colors">
                    <option value="">Semua Role</option>
                    <option value="SuperAdmin" {{ request('level') == 'SuperAdmin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="Admin Instansi" {{ request('level') == 'Admin Instansi' ? 'selected' : '' }}>Admin Instansi</option>
                    <option value="Admin" {{ request('level') == 'Admin' ? 'selected' : '' }}>Admin PMR / Unit</option>
                    <option value="Health Monitor" {{ request('level') == 'Health Monitor' ? 'selected' : '' }}>Health Monitor</option>
                    <option value="Health Consultant" {{ request('level') == 'Health Consultant' ? 'selected' : '' }}>Health Consultant</option>
                    <option value="Member" {{ request('level') == 'Member' ? 'selected' : '' }}>Member (Anggota/Siswa)</option>
                </select>
            </div>

            {{-- Filter Status --}}
            <div class="col-span-1 sm:col-span-1 md:col-span-3 lg:col-span-2">
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5 truncate">Status Akun</label>
                <select name="status" class="w-full h-[42px] px-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm bg-white text-gray-800 transition-colors">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                </select>
            </div>

            {{-- Filter Instansi --}}
            <div class="col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5 truncate">Institusi / Instansi</label>
                <select id="sekolahSelect" name="sekolah_id" class="w-full">
                    <option value="">SEMUA INSTITUSI / INSTANSI</option>
                    @foreach($sekolah as $s)
                        <option value="{{ $s->id }}" {{ request('sekolah_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->nama_instansi ?? $s->nama_sekolah }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Search --}}
            <div class="col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-3">
                <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5 truncate">Cari User</label>
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Username..."
                        class="w-full h-[42px] pl-9 pr-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#308a34] focus:border-transparent text-sm bg-white text-gray-800 transition-colors">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- Submit & Reset Buttons --}}
            <div class="col-span-1 sm:col-span-2 md:col-span-6 lg:col-span-2">
                <label class="block text-xs font-semibold text-transparent select-none mb-1.5 hidden lg:block">Aksi</label>
                <div class="flex items-center gap-2 w-full h-[42px]">
                    <button type="submit" class="h-[42px] flex-1 px-3 bg-[#308a34] text-white rounded-lg hover:bg-[#1B5E20] transition-colors duration-200 text-sm font-semibold flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fas fa-filter text-xs"></i><span>Filter</span>
                    </button>
                    <a href="{{ route('superadmin.users.index') }}" class="h-[42px] w-[42px] bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition-colors duration-200 flex items-center justify-center text-sm border border-gray-200 flex-shrink-0" title="Reset Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-soft overflow-hidden">
        <div class="p-4 sm:p-6">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[700px]">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50/50">
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Pengguna</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Username</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Instansi / Lembaga</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status Akun</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi & Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($users as $index => $user)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $user->status === 'banned' ? 'bg-red-50/40' : ($user->status === 'suspended' ? 'bg-amber-50/40' : '') }}">
                            <td class="px-3 py-3.5 text-xs sm:text-sm text-gray-700">{{ $users->firstItem() + $index }}</td>
                            
                            {{-- Nama & Avatar --}}
                            <td class="px-3 py-3.5">
                                <div class="flex items-center">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center mr-3 font-semibold text-xs sm:text-sm text-white shadow-sm
                                        @if($user->level == 'SuperAdmin') bg-rose-600
                                        @elseif($user->level == 'Admin Instansi') bg-emerald-600
                                        @elseif($user->level == 'Admin') bg-blue-600
                                        @elseif($user->level == 'Health Monitor') bg-teal-600
                                        @elseif($user->level == 'Health Consultant') bg-indigo-600
                                        @else bg-slate-600 @endif">
                                        {{ strtoupper(substr($user->nama, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 text-sm truncate">{{ $user->nama }}</p>
                                        <p class="text-xs text-gray-400">ID: #{{ $user->id }} &bull; Gender: {{ $user->jk }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Username --}}
                            <td class="px-3 py-3.5 text-xs sm:text-sm text-gray-700 font-mono">{{ $user->username }}</td>

                            {{-- Role --}}
                            <td class="px-3 py-3.5">
                                @if($user->level === 'SuperAdmin')
                                    <span class="px-2.5 py-1 text-xs rounded-full font-semibold bg-rose-100 text-rose-800 border border-rose-200/60">Super Admin</span>
                                @elseif($user->level === 'Admin Instansi')
                                    <span class="px-2.5 py-1 text-xs rounded-full font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200/60">Admin Instansi</span>
                                @elseif($user->level === 'Admin')
                                    <span class="px-2.5 py-1 text-xs rounded-full font-semibold bg-blue-100 text-blue-800 border border-blue-200/60">Admin Unit</span>
                                @elseif($user->level === 'Health Monitor')
                                    <span class="px-2.5 py-1 text-xs rounded-full font-semibold bg-teal-100 text-teal-800 border border-teal-200/60">Health Monitor</span>
                                @elseif($user->level === 'Health Consultant')
                                    <span class="px-2.5 py-1 text-xs rounded-full font-semibold bg-purple-100 text-purple-800 border border-purple-200/60">Health Consultant</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs rounded-full font-semibold bg-slate-100 text-slate-700 border border-slate-200">Member</span>
                                @endif
                            </td>

                            {{-- Instansi --}}
                            <td class="px-3 py-3.5 text-xs sm:text-sm text-gray-700 max-w-[170px] truncate">
                                @if($user->sekolah)
                                    <span class="font-medium text-gray-800">{{ $user->sekolah->nama_instansi ?? $user->sekolah->nama_sekolah }}</span>
                                    <span class="block text-[11px] text-gray-400">{{ $user->sekolah->jenis_instansi ?? 'Instansi' }}</span>
                                @else
                                    <span class="text-gray-400 italic">Pusat / Global</span>
                                @endif
                            </td>

                            {{-- Status Akun --}}
                            <td class="px-3 py-3.5">
                                @if($user->isBanned())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-full font-semibold bg-red-100 text-red-800 border border-red-200">
                                        <i class="fas fa-ban text-[11px]"></i> Banned
                                    </span>
                                    @if($user->ban_reason)
                                        <p class="text-[11px] text-red-600 mt-1 max-w-[150px] truncate" title="{{ $user->ban_reason }}">
                                            {{ $user->ban_reason }}
                                        </p>
                                    @endif
                                @elseif($user->isSuspended())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-full font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fas fa-clock text-[11px]"></i> Suspended
                                    </span>
                                    <p class="text-[11px] text-amber-700 font-medium mt-1">
                                        s/d {{ $user->suspended_until ? $user->suspended_until->format('d/m/Y H:i') : '-' }}
                                    </p>
                                    @if($user->ban_reason)
                                        <p class="text-[10px] text-amber-600 truncate max-w-[140px]" title="{{ $user->ban_reason }}">
                                            {{ $user->ban_reason }}
                                        </p>
                                    @endif
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-full font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fas fa-check-circle text-[11px]"></i> Aktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-3 py-3.5 text-center">
                                <div class="flex items-center justify-center space-x-1 sm:space-x-1.5">
                                    {{-- Edit --}}
                                    <a href="{{ route('superadmin.users.edit', $user->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-150" title="Edit Akun">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>

                                    @if($user->id !== auth()->id() && !$user->isSuperAdmin())
                                        {{-- Jika Banned / Suspended -> Tombol Aktifkan --}}
                                        @if($user->isBanned() || $user->isSuspended())
                                            <form action="{{ route('superadmin.users.activate', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Pulihkan dan aktifkan kembali akun {{ $user->nama }}?')">
                                                @csrf
                                                <button type="submit" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors duration-150" title="Aktifkan Kembali Akun (Unban/Unsuspend)">
                                                    <i class="fas fa-rotate-left text-sm"></i>
                                                </button>
                                            </form>
                                        @else
                                            {{-- Tombol Suspend (Buka Modal) --}}
                                            <button type="button" 
                                                onclick="openSuspendModal('{{ $user->id }}', this.getAttribute('data-name'), this.getAttribute('data-username'))" 
                                                data-name="{{ $user->nama }}"
                                                data-username="{{ $user->username }}"
                                                class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors duration-150" 
                                                title="Suspend Waktu">
                                                <i class="fas fa-clock text-sm"></i>
                                            </button>

                                            {{-- Tombol Ban (Buka Modal) --}}
                                            <button type="button" 
                                                onclick="openBanModal('{{ $user->id }}', this.getAttribute('data-name'), this.getAttribute('data-username'))" 
                                                data-name="{{ $user->nama }}"
                                                data-username="{{ $user->username }}"
                                                class="p-1.5 text-orange-600 hover:bg-orange-50 rounded-lg transition-colors duration-150" 
                                                title="Banned Permanen">
                                                <i class="fas fa-ban text-sm"></i>
                                            </button>
                                        @endif

                                        {{-- Tombol Hapus --}}
                                        <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus permanen akun {{ $user->nama }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150" title="Hapus Akun">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-users-slash text-3xl mb-2 text-gray-400"></i>
                                <p class="text-sm font-medium">Tidak ada data user yang sesuai dengan filter.</p>
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

{{-- MODAL SUSPEND WAKTU --}}
<div id="suspendModal" class="fixed inset-0 z-[99999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-200" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4 text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-clock text-xl"></i>
                <h3 class="font-bold text-lg">Suspend Akun Pengguna</h3>
            </div>
            <button type="button" onclick="closeSuspendModal()" class="text-white/80 hover:text-white text-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="suspendForm" method="POST" data-confirm="Terapkan status penangguhan (suspend) sementara pada pengguna ini?" class="p-6 space-y-4">
            @csrf
            <div>
                <p class="text-sm text-gray-600">Anda akan menangguhkan akses untuk akun:</p>
                <p id="suspendTargetUser" class="text-base font-bold text-gray-800 mt-0.5"></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Durasi Penangguhan <span class="text-red-500">*</span></label>
                <select name="duration" id="suspendDuration" required onchange="toggleCustomDate(this.value)"
                    class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    <option value="1">1 Hari (24 Jam)</option>
                    <option value="3">3 Hari</option>
                    <option value="7" selected>7 Hari (1 Minggu)</option>
                    <option value="14">14 Hari (2 Minggu)</option>
                    <option value="30">30 Hari (1 Bulan)</option>
                    <option value="custom">Pilih Tanggal & Waktu Kustom...</option>
                </select>
            </div>

            <div id="customDateWrapper" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Waktu Berakhir Penangguhan <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="custom_date" id="customDateInput"
                    class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alasan Suspend</label>
                <textarea name="reason" rows="2" placeholder="cth: Pelanggaran etika komunitas / investigasi data"
                    class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-transparent"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeSuspendModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 text-sm font-semibold shadow-md shadow-amber-600/20">
                    <i class="fas fa-clock mr-1.5"></i>Terapkan Suspend
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL BANNED PERMANEN --}}
<div id="banModal" class="fixed inset-0 z-[99999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-200" style="display: none;">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-gradient-to-r from-red-600 to-rose-700 px-6 py-4 text-white flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-ban text-xl"></i>
                <h3 class="font-bold text-lg">Banned Akun Permanen</h3>
            </div>
            <button type="button" onclick="closeBanModal()" class="text-white/80 hover:text-white text-lg">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="banForm" method="POST" data-confirm="Terapkan sanksi Banned permanen pada akun ini? Seluruh akses sesi aktif dan token mobile akan dicabut segera." class="p-6 space-y-4">
            @csrf
            <div>
                <p class="text-sm text-gray-600">Pengguna berikut tidak akan dapat login kembali hingga dibuka:</p>
                <p id="banTargetUser" class="text-base font-bold text-rose-700 mt-0.5"></p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alasan Banned Permanen</label>
                <textarea name="reason" rows="3" placeholder="cth: Penyalahgunaan akses / akun terindikasi kecurangan berulang"
                    class="w-full px-3.5 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
            </div>

            <div class="bg-red-50 p-3 rounded-lg border border-red-100 text-xs text-red-700">
                <i class="fas fa-info-circle mr-1"></i> Sesi mobile token aktif akan segera dicabut secara otomatis.
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2">
                <button type="button" onclick="closeBanModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-semibold shadow-md shadow-red-600/20">
                    <i class="fas fa-ban mr-1.5"></i>Banned Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (document.getElementById("sekolahSelect")) {
            new TomSelect("#sekolahSelect", {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "Pilih institusi / instansi...",
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
        }
    });

    // Suspend Modal Logic
    window.openSuspendModal = function(userId, userName, username) {
        const modal = document.getElementById('suspendModal');
        const form = document.getElementById('suspendForm');
        const targetText = document.getElementById('suspendTargetUser');

        if (form) form.action = `/superadmin/users/${userId}/suspend`;
        if (targetText) targetText.textContent = `${userName} (@${username})`;
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
        }
    };

    window.closeSuspendModal = function() {
        const modal = document.getElementById('suspendModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
        }
    };

    window.toggleCustomDate = function(val) {
        const wrapper = document.getElementById('customDateWrapper');
        const input = document.getElementById('customDateInput');
        if (val === 'custom') {
            if (wrapper) wrapper.classList.remove('hidden');
            if (input) input.required = true;
        } else {
            if (wrapper) wrapper.classList.add('hidden');
            if (input) input.required = false;
        }
    };

    // Ban Modal Logic
    window.openBanModal = function(userId, userName, username) {
        const modal = document.getElementById('banModal');
        const form = document.getElementById('banForm');
        const targetText = document.getElementById('banTargetUser');

        if (form) form.action = `/superadmin/users/${userId}/ban`;
        if (targetText) targetText.textContent = `${userName} (@${username})`;
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.remove('hidden');
        }
    };

    window.closeBanModal = function() {
        const modal = document.getElementById('banModal');
        if (modal) {
            modal.style.display = 'none';
            modal.classList.add('hidden');
        }
    };

    // Click outside backdrop to close
    window.addEventListener('click', function(e) {
        const suspendModal = document.getElementById('suspendModal');
        const banModal = document.getElementById('banModal');
        if (e.target === suspendModal) window.closeSuspendModal();
        if (e.target === banModal) window.closeBanModal();
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeSuspendModal();
            window.closeBanModal();
        }
    });
</script>
@endpush