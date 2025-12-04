<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthApiController extends Controller
{
    /**
     * Register API untuk member.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:75',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
            'jenis_kelamin' => 'required|in:L,P',
            'kelas_id' => 'required|exists:kelas,id',
            'nomor_induk' => 'required|string|max:10|unique:users,nomor_induk',
        ],[
            'nama.required' => 'Nama wajib diisi.',
            'username.required' => 'Username wajib diisi.', 
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas tidak ditemukan.',
            'nomor_induk.required' => 'Nomor Induk wajib diisi.',
            'nomor_induk.unique' => 'Nomor Induk sudah terdaftar.',
            'nomor_induk.max' => 'Nomor Induk maksimal 10 karakter.',
        ]);

        $user = User::create([
            'nama' => $validated['nama'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'jk' => $validated['jenis_kelamin'],
            'tgl' => now()->format('Y-m-d'),
            'id_kelas' => $validated['kelas_id'],
            'nis' => $validated['nis'],
        ]);

        $token = $user->createToken('mobile_token-' . $user->id)->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Register berhasil!',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login API.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|exists:users,username',
            'password' => 'required|string'
        ],[
            'username.exists' => 'Username tidak ditemukan.',
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Username atau password salah'
            ], 200);
        }

        $checkLicense = $user->license_key_id ?? null;
        if ($checkLicense) {
            if ($user->licenseKey->isExpired()) {
                return response()->json([
                    'status' => false,
                    'message' => 'License key tidak aktif atau telah kedaluwarsa. Silakan hubungi administrator untuk memperbarui license key.'
                ], 200);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada license key yang terhubung dengan akun ini. Silakan hubungi administrator untuk mendapatkan license key.'
            ], 200);
        }

        // Hapus semua token lama agar 1 sesi per user
        $user->tokens()->delete();

        $token = $user->createToken('mobile_token-' . $user->id)->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login berhasil!',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Mendapatkan data user saat ini (cek sesi).
     */
    public function me(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user(),
        ]);
    }

    /**
     * Logout dan hapus token aktif.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Cek otentikasi token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkAuth(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Token valid',
        ]);
    }
}
