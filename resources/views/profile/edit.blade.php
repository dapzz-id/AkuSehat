@extends('layouts.app')

@section('title', 'Profil & Pengaturan Akun')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-600/20">
                    <i class="fas fa-user-gear text-base"></i>
                </div>
                <span>Profil & Pengaturan Akun</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola informasi identitas pribadi, kontak email, kata sandi, dan foto profil Anda di ekosistem AkuSehat.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <i class="fas fa-shield-halved text-emerald-600"></i>
                <span>{{ $user->role_label }}</span>
            </span>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: User Summary Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden text-center">
                <!-- Top Header Banner -->
                <div class="h-28 bg-gradient-to-r from-emerald-700 via-teal-700 to-emerald-800 relative w-full">
                    <div class="absolute inset-0 bg-[radial-gradient(#ffffff_1px,transparent_1px)] [background-size:16px_16px] opacity-15"></div>
                </div>

                <!-- Profile Info & Floating Avatar -->
                <div class="px-6 pb-6 pt-0 relative flex flex-col items-center">
                    <!-- Avatar Preview Box -->
                    <div class="relative -mt-14 mb-3 inline-block" style="width: 104px; height: 104px;">
                        <div class="w-[104px] h-[104px] rounded-2xl border-4 border-white shadow-xl overflow-hidden bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white text-3xl font-black"
                             style="width: 104px; height: 104px; min-width: 104px; min-height: 104px;">
                            @if($user->avatar_url)
                                <img id="avatar-preview-display" 
                                     src="{{ $user->avatar_url }}" 
                                     alt="{{ $user->nama }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <span id="avatar-preview-initial" class="text-3xl font-black text-white select-none">{{ strtoupper(substr($user->nama, 0, 1)) }}</span>
                                <img id="avatar-preview-display" 
                                     src="" 
                                     alt="Preview" 
                                     class="w-full h-full object-cover hidden">
                            @endif
                        </div>

                        <!-- Camera Floating Icon Badge -->
                        <label for="avatar-input" 
                               class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center shadow-lg cursor-pointer border-2 border-white transition-transform hover:scale-110 active:scale-95"
                               style="position: absolute; bottom: -4px; right: -4px; width: 34px; height: 34px; z-index: 10;"
                               title="Pilih foto profil baru">
                            <i class="fas fa-camera text-xs"></i>
                        </label>
                    </div>

                    <!-- User Identity Text -->
                    <div class="text-center w-full px-2">
                        <h2 class="text-lg font-bold text-slate-800 leading-tight">{{ $user->nama }}</h2>
                        <p class="text-xs text-slate-500 font-mono mt-1">{{ '@' . $user->username }}</p>

                        <div class="flex flex-wrap items-center justify-center gap-1.5 mt-2.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                <i class="fas fa-id-badge text-[10px]"></i> {{ $user->role_label }}
                            </span>
                            @if($user->divisi)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                    <i class="fas fa-layer-group text-[10px]"></i> {{ $user->divisi->divisi_name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons for Avatar -->
                    <div class="flex items-center justify-center gap-2 mt-3 pt-3 border-t border-slate-100 w-full">
                        <label for="avatar-input" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-700 hover:text-emerald-800 bg-emerald-50/80 hover:bg-emerald-100/80 cursor-pointer border border-emerald-200 transition-colors">
                            <i class="fas fa-camera text-[11px]"></i>
                            <span>Ganti Foto</span>
                        </label>

                        @if($user->avatar)
                            <form action="{{ route('profile.avatar.delete') }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto profil?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-600 hover:text-rose-700 bg-rose-50/80 hover:bg-rose-100/80 border border-rose-200 transition-colors">
                                    <i class="fas fa-trash-can text-[11px]"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Detail Meta List -->
                    <div class="mt-4 pt-4 border-t border-slate-100 text-left space-y-3 w-full">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">Institusi</span>
                            <span class="font-semibold text-slate-700 truncate max-w-[180px] text-right" title="{{ $user->instansi->nama_instansi ?? 'Portal Pusat' }}">
                                {{ $user->instansi->nama_instansi ?? 'Pusat / Mandiri' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">Nomor Induk / ID</span>
                            <span class="font-mono font-semibold text-slate-700">{{ $user->nomor_induk ?? $user->nis ?? '-' }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">Jenis Kelamin</span>
                            <span class="font-semibold text-slate-700">
                                {{ $user->jk === 'P' ? 'Perempuan' : ($user->jk === 'L' ? 'Laki-laki' : '-') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400">Bergabung Sejak</span>
                            <span class="font-semibold text-slate-700">{{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Tip -->
            <div class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-100/80 flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 text-sm shadow-sm">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="text-xs text-emerald-900 leading-relaxed">
                    <p class="font-bold mb-0.5">Keamanan Akun Terjaga</p>
                    <p class="text-emerald-700/90">Gunakan kata sandi yang kuat dan jangan pernah membagikan kredensial login kepada pihak lain.</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Edit Forms -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Form 1: Biodata & Kontak -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Informasi Pribadi & Kontak</h2>
                        <p class="text-xs text-slate-500">Perbarui nama lengkap, alamat email aktif, dan foto profil Anda.</p>
                    </div>
                </div>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Hidden actual file input triggered by avatar camera / button -->
                    <input type="file" 
                           id="avatar-input" 
                           name="avatar" 
                           accept="image/*" 
                           class="hidden" 
                           onchange="handleAvatarSelected(event)">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div class="sm:col-span-2">
                            <label for="nama" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-user text-xs"></i>
                                </div>
                                <input type="text" 
                                       id="nama" 
                                       name="nama" 
                                       value="{{ old('nama', $user->nama) }}" 
                                       required 
                                       placeholder="Masukkan nama lengkap Anda"
                                       class="block w-full pl-10 pr-3.5 py-2.5 text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 transition-colors @error('nama') border-rose-400 bg-rose-50/30 @enderror">
                            </div>
                            @error('nama')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="sm:col-span-2">
                            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Alamat Email
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-envelope text-xs"></i>
                                </div>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       placeholder="nama@email.com (opsional)"
                                       class="block w-full pl-10 pr-3.5 py-2.5 text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 transition-colors @error('email') border-rose-400 bg-rose-50/30 @enderror">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk notifikasi sistem dan login via Google.</p>
                            @error('email')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Username (Readonly) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Username Login
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-at text-xs"></i>
                                </div>
                                <input type="text" 
                                       value="{{ $user->username }}" 
                                       readonly 
                                       disabled
                                       class="block w-full pl-10 pr-3.5 py-2.5 text-sm text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Username ditetapkan saat registrasi akun.</p>
                        </div>

                        <!-- Peran / Level (Readonly) -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tingkat Akses / Role
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-shield text-xs"></i>
                                </div>
                                <input type="text" 
                                       value="{{ $user->role_label }}" 
                                       readonly 
                                       disabled
                                       class="block w-full pl-10 pr-3.5 py-2.5 text-sm text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed">
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Dikelola oleh administrator institusi.</p>
                        </div>
                    </div>

                    <!-- File selected indicator alert -->
                    <div id="file-chosen-notice" class="hidden p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-image text-emerald-600"></i>
                            <span id="file-name-text">Foto baru dipilih</span>
                        </span>
                        <button type="button" onclick="cancelAvatarSelection()" class="text-rose-600 hover:text-rose-800 text-xs font-semibold">
                            Batal
                        </button>
                    </div>

                    <div class="flex items-center justify-end pt-3">
                        <button type="submit" class="btn-press px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                            <i class="fas fa-floppy-disk"></i>
                            <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Form 2: Ubah Password -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-sm">
                        <i class="fas fa-key"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Perbarui Kata Sandi</h2>
                        <p class="text-xs text-slate-500">Pastikan akun Anda menggunakan kata sandi yang aman dan tidak mudah ditebak.</p>
                    </div>
                </div>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Password Saat Ini -->
                    <div>
                        <label for="current_password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi Saat Ini <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-lock text-xs"></i>
                            </div>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   required 
                                   placeholder="Masukkan password saat ini"
                                   class="block w-full pl-10 pr-10 py-2.5 text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 transition-colors @error('current_password') border-rose-400 bg-rose-50/30 @enderror">
                            <button type="button" 
                                    onclick="togglePasswordVisibility('current_password', 'current_eye')" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                <i id="current_eye" class="fas fa-eye text-xs"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Password Baru -->
                        <div>
                            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kata Sandi Baru <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-shield-halved text-xs"></i>
                                </div>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       minlength="6"
                                       placeholder="Minimal 6 karakter"
                                       class="block w-full pl-10 pr-10 py-2.5 text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 transition-colors @error('password') border-rose-400 bg-rose-50/30 @enderror">
                                <button type="button" 
                                        onclick="togglePasswordVisibility('password', 'new_eye')" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <i id="new_eye" class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div>
                            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
                                Ulangi Kata Sandi Baru <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-check-double text-xs"></i>
                                </div>
                                <input type="password" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       required 
                                       minlength="6"
                                       placeholder="Ulangi kata sandi baru"
                                       class="block w-full pl-10 pr-10 py-2.5 text-sm text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 transition-colors">
                                <button type="button" 
                                        onclick="togglePasswordVisibility('password_confirmation', 'confirm_eye')" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                    <i id="confirm_eye" class="fas fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-3">
                        <button type="submit" class="btn-press px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 shadow-md shadow-slate-800/20 transition-all flex items-center gap-2">
                            <i class="fas fa-key"></i>
                            <span>Ganti Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function handleAvatarSelected(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const previewDisplay = document.getElementById('avatar-preview-display');
            const previewInitial = document.getElementById('avatar-preview-initial');
            const notice = document.getElementById('file-chosen-notice');
            const nameText = document.getElementById('file-name-text');

            if (previewDisplay) {
                previewDisplay.src = e.target.result;
                previewDisplay.classList.remove('hidden');
            }
            if (previewInitial) {
                previewInitial.classList.add('hidden');
            }
            if (notice && nameText) {
                nameText.innerText = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
                notice.classList.remove('hidden');
            }
        };
        reader.readAsDataURL(file);
    }

    function cancelAvatarSelection() {
        const input = document.getElementById('avatar-input');
        if (input) input.value = '';

        const notice = document.getElementById('file-chosen-notice');
        if (notice) notice.classList.add('hidden');

        // Reset to original avatar if available
        const originalUrl = '{{ $user->avatar_url }}';
        const previewDisplay = document.getElementById('avatar-preview-display');
        const previewInitial = document.getElementById('avatar-preview-initial');

        if (originalUrl) {
            if (previewDisplay) {
                previewDisplay.src = originalUrl;
                previewDisplay.classList.remove('hidden');
            }
            if (previewInitial) {
                previewInitial.classList.add('hidden');
            }
        } else {
            if (previewDisplay) {
                previewDisplay.src = '';
                previewDisplay.classList.add('hidden');
            }
            if (previewInitial) {
                previewInitial.classList.remove('hidden');
            }
        }
    }

    function togglePasswordVisibility(inputId, eyeIconId) {
        const input = document.getElementById(inputId);
        const eyeIcon = document.getElementById(eyeIconId);
        if (!input || !eyeIcon) return;

        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }
</script>
@endpush
