@extends('layouts.app')

@section('title', 'Edit Pengguna: ' . $user->nama)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center shadow-md shadow-blue-700/20">
                    <i class="fas fa-user-pen text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight">Edit Pengguna: {{ $user->nama }}</h1>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Perbarui rincian akun, peran otoritas, atau perpindahan institusi pengguna.
                    </p>
                </div>
            </div>
        </div>
        <a href="{{ route('superadmin.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-xl shadow-sm">
            <div class="flex items-center gap-2 font-bold text-sm mb-1">
                <i class="fas fa-circle-exclamation text-rose-600"></i>
                <span>Terdapat kesalahan validasi formulir:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-soft border border-slate-200/80 p-6 sm:p-8">
        <form action="{{ route('superadmin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-6">
                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}" required
                           class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-blue-600 @error('nama') border-rose-500 @enderror">
                    @error('nama')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Username <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                           class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-blue-600 @error('username') border-rose-500 @enderror">
                    @error('username')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Alamat Email (Opsional)
                    </label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           placeholder="Contoh: user@domain.com"
                           class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-blue-600 @error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Baru (Opsional) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Password Baru (Kosongkan Jika Tidak Diubah)
                    </label>
                    <input type="password" name="password"
                           placeholder="Biarkan kosong jika tetap menggunakan password saat ini"
                           class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-blue-600 @error('password') border-rose-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role / Peran -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Role / Peran Pengguna <span class="text-rose-500">*</span>
                    </label>
                    @php $currLevel = old('level', $user->level); @endphp
                    <select name="level" id="level" required
                            class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-blue-600 @error('level') border-rose-500 @enderror">
                        <option value="Member" {{ in_array($currLevel, ['Member', 'Siswa']) ? 'selected' : '' }}>Member (Siswa / Mahasiswa / Karyawan)</option>
                        <option value="Admin" {{ in_array($currLevel, ['Admin', 'Admin PMR']) ? 'selected' : '' }}>Admin PMR / Koordinator Unit</option>
                        <option value="Health Monitor" {{ in_array($currLevel, ['Health Monitor', 'Guru Olahraga']) ? 'selected' : '' }}>Health Monitor (Guru Penjas / Petugas Medis / HSE)</option>
                        <option value="Health Consultant" {{ in_array($currLevel, ['Health Consultant', 'Guru BK']) ? 'selected' : '' }}>Health Consultant (Guru BK / Konselor / Psikolog)</option>
                        <option value="Admin Instansi" {{ $currLevel == 'Admin Instansi' ? 'selected' : '' }}>Admin Instansi / Institusi</option>
                        <option value="SuperAdmin" {{ $currLevel == 'SuperAdmin' ? 'selected' : '' }}>Super Administrator Pusat</option>
                    </select>
                    @error('level')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select name="jk" required
                            class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-blue-600 @error('jk') border-rose-500 @enderror">
                        <option value="L" {{ old('jk', $user->jk) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jk', $user->jk) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jk')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Asal Institusi / Instansi -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Asal Institusi / Instansi <span class="text-rose-500">*</span>
                    </label>
                    @php $currInstansiId = old('instansi_id', old('sekolah_id', $user->instansi_id)); @endphp
                    <select name="instansi_id" id="instansi_id" required
                            class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-blue-600 @if($errors->has('instansi_id') || $errors->has('sekolah_id')) border-rose-500 @endif">
                        <option value="">-- Pilih Institusi / Instansi --</option>
                        @foreach($sekolah as $s)
                            <option value="{{ $s->id }}" {{ $currInstansiId == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_instansi ?? $s->nama_sekolah }} ({{ $s->jenis_instansi ?? $s->jenjang ?? 'Umum' }})
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('instansi_id'))
                        <p class="mt-1 text-xs text-rose-500">{{ $errors->first('instansi_id') }}</p>
                    @elseif($errors->has('sekolah_id'))
                        <p class="mt-1 text-xs text-rose-500">{{ $errors->first('sekolah_id') }}</p>
                    @endif
                </div>

                <!-- Divisi / Unit Kerja -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Divisi / Kelas / Unit Kerja (Opsional)
                    </label>
                    @php $currDivisiId = old('id_divisi', $user->id_divisi); @endphp
                    <select name="id_divisi" id="id_divisi"
                            class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-blue-600 @error('id_divisi') border-rose-500 @enderror">
                        <option value="">-- Tanpa Divisi (Langsung di Instansi) --</option>
                        @foreach($divisi as $d)
                            <option value="{{ $d->id }}" data-instansi="{{ $d->instansi_id }}" {{ $currDivisiId == $d->id ? 'selected' : '' }}>
                                {{ $d->divisi_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_divisi')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Induk -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nomor Induk (NIS / NIP / NIK)
                    </label>
                    <input type="text" name="nomor_induk" value="{{ old('nomor_induk', old('nis', $user->nomor_induk)) }}"
                           placeholder="Contoh: 10293847"
                           class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-blue-600 @if($errors->has('nomor_induk') || $errors->has('nis')) border-rose-500 @endif">
                    @if($errors->has('nomor_induk'))
                        <p class="mt-1 text-xs text-rose-500">{{ $errors->first('nomor_induk') }}</p>
                    @elseif($errors->has('nis'))
                        <p class="mt-1 text-xs text-rose-500">{{ $errors->first('nis') }}</p>
                    @endif
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Tanggal Lahir (Opsional)
                    </label>
                    <input type="date" name="tgl" value="{{ old('tgl', $user->tgl ? (\Carbon\Carbon::parse($user->tgl)->format('Y-m-d')) : '') }}"
                           class="w-full px-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:border-blue-600 @error('tgl') border-rose-500 @enderror">
                    @error('tgl')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-5 border-t border-slate-100">
                <a href="{{ route('superadmin.users.index') }}" 
                   class="px-5 py-2.5 bg-slate-100 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-200 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-700/20 transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Simpan Perubahan Pengguna</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const instansiSelect = document.getElementById('instansi_id');
    const divisiSelect = document.getElementById('id_divisi');

    if (instansiSelect && divisiSelect) {
        const filterDivisi = () => {
            const selectedInstansi = instansiSelect.value;
            const currentVal = divisiSelect.value;
            let hasActiveMatch = false;

            Array.from(divisiSelect.options).forEach((opt, idx) => {
                if (idx === 0) return; // Keep "-- Tanpa Divisi --"
                const instansi = opt.getAttribute('data-instansi');
                if (!selectedInstansi || instansi === selectedInstansi) {
                    opt.style.display = '';
                    if (opt.value === currentVal) hasActiveMatch = true;
                } else {
                    opt.style.display = 'none';
                }
            });

            if (!hasActiveMatch && currentVal !== '') {
                divisiSelect.value = '';
            }
        };

        instansiSelect.addEventListener('change', filterDivisi);
        filterDivisi();
    }
});
</script>
@endsection
