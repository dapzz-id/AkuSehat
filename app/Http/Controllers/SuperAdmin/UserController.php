<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Divisi;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\LicenseKey;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $sekolah = Sekolah::all();

        $level = $request->input('level');
        $sekolahId = $request->input('sekolah_id');
        $search = $request->input('search');

        $query = User::with('sekolah')
            ->whereIn('level', ['Admin Sekolah', 'SuperAdmin']);

        if (!empty($level)) {
            $query->where('level', $level);
        }

        if (!empty($sekolahId)) {
            $query->where('sekolah_id', $sekolahId);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        $users->appends($request->all());

        return view('superadmin.users.index', compact('sekolah', 'users'));
    }

    public function create()
    {
        $sekolah = Sekolah::where('status', 'aktif')->get();
        $divisi = Divisi::all();
        return view('superadmin.users.create', compact('sekolah', 'divisi'));
    }

    /**
     * Store a newly created user in database
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:75',
            'username' => 'required|string|max:20|unique:users,username',
            'password' => 'required|string|min:6',
            'level' => 'required|in:Admin,Health Monitor,Health Consultant,Member',
            'jk' => 'required|in:L,P',
            'sekolah_id' => 'required|exists:sekolah,id',
            'nis' => 'nullable|string|max:30',
            'tgl' => 'nullable|date',
            'id_divisi' => 'nullable|exists:divisi,id',
            'license_key' => 'nullable|exists:license_keys,key',
        ],[
            'nama.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'level.required' => 'Role user wajib dipilih.',
            'level.in' => 'Role user tidak valid.',
            'jk.required' => 'Jenis kelamin wajib dipilih.',
            'jk.in' => 'Jenis kelamin tidak valid.',
            'sekolah_id.required' => 'Sekolah wajib dipilih.',
            'sekolah_id.exists' => 'Sekolah yang dipilih tidak valid.',
            'nis.max' => 'NIS maksimal 30 karakter.',
            'tgl.date' => 'Tanggal tidak valid.',
            'id_divisi.exists' => 'Divisi yang dipilih tidak valid.',
            'license_key.exists' => 'License key tidak valid.',
        ]);

        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'level' => $request->level,
            'jk' => $request->jk,
            'sekolah_id' => $request->sekolah_id,
            'nis' => $request->nis,
            'tgl' => $request->tgl,
            'id_divisi' => $request->id_divisi,
        ];

        if ($request->filled('license_key')) {
            $dataLicenseKey = LicenseKey::where('key', $request->license_key)
                ->where('sekolah_id', $request->sekolah_id)
                ->where('status', 'active')
                ->first();

            $hitungPenggunaYangPakaiLicense = User::where('license_key_id', $dataLicenseKey->id)->count();

            if ($dataLicenseKey && $dataLicenseKey->isActive()) {
                if ($hitungPenggunaYangPakaiLicense <= 1000) {
                    $data['license_key_id'] = $dataLicenseKey->id;
                } else {
                    return redirect()->back()->withInput()->with('error', 'License key sudah mencapai batas maksimum pengguna.');
                }
            } else {
                return redirect()->back()->withInput()->with('error', 'License key tidak valid atau sudah expired.');
            }
        }

        $user = User::create($data);

        return redirect()->route('superadmin.users.index')
            ->with('success', "User {$user->nama} berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $sekolah = Sekolah::all();
        $divisi = Divisi::all();
        return view('superadmin.users.edit', compact('user', 'sekolah', 'divisi'));
    }

    /**
     * Update the specified user in database
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required|string|max:75',
            'username' => 'required|string|max:20|unique:users,username,' . $user->id,
            'password' => 'nullable|string|min:6',
            'level' => 'required|in:Admin,Health Monitor,Health Consultant,Member,SuperAdmin',
            'jk' => 'required|in:L,P',
            'sekolah_id' => 'required|exists:sekolah,id',
            'nis' => 'nullable|string|max:30',
            'tgl' => 'nullable|date',
            'id_divisi' => 'nullable|exists:divisi,id',
            'license_key' => 'nullable|exists:license_keys,key',
        ],[
            'nama.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan, silakan pilih yang lain.',
            'password.min' => 'Password minimal 6 karakter.',
            'level.required' => 'Role user wajib dipilih.',
            'level.in' => 'Role user tidak valid.',
            'jk.required' => 'Jenis kelamin wajib dipilih.',
            'jk.in' => 'Jenis kelamin tidak valid.',
            'sekolah_id.required' => 'Sekolah wajib dipilih.',
            'sekolah_id.exists' => 'Sekolah yang dipilih tidak valid.',
            'nis.max' => 'NIS maksimal 30 karakter.',
            'tgl.date' => 'Tanggal tidak valid.',
            'id_divisi.exists' => 'Divisi yang dipilih tidak valid.',
            'license_key.exists' => 'License key tidak valid.',
        ]);

        $data = [
            'nama' => $request->nama,
            'username' => $request->username,
            'level' => $request->level,
            'jk' => $request->jk,
            'sekolah_id' => $request->sekolah_id,
            'nis' => $request->nis,
            'tgl' => $request->tgl,
            'id_divisi' => $request->id_divisi,
        ];
        
        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->filled('license_key')) {
            $dataLicenseKey = LicenseKey::where('key', $request->license_key)
                ->where('sekolah_id', $request->sekolah_id)
                ->where('status', 'active')
                ->first();

            $hitungPenggunaYangPakaiLicense = User::where('license_key_id', $dataLicenseKey->id)->count();

            if ($dataLicenseKey && $dataLicenseKey->isActive()) {
                if ($hitungPenggunaYangPakaiLicense <= 1000 || $user->license_key_id == $dataLicenseKey->id) {
                    $data['license_key_id'] = $dataLicenseKey->id;
                } else {
                    return redirect()->back()->withInput()->with('error', 'License key sudah mencapai batas maksimum pengguna.');
                }
            } else {
                return redirect()->back()->withInput()->with('error', 'License key tidak valid atau sudah expired.');
            }
        }

        $user->update($data);

        return redirect()->route('superadmin.users.index')
            ->with('success', "User '{$user->nama}' berhasil diupdate.");
    }

    /**
     * Remove the specified user from database
     */
    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $nama = $user->nama;
        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', "User '{$nama}' berhasil dihapus.");
    }
}
