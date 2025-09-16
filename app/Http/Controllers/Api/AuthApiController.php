<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Hanya allow Guru Olahraga dan Siswa untuk API
            if (!in_array($user->level, ['Guru Olahraga', 'Siswa'])) {
                Auth::logout();
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diperbolehkan untuk aplikasi mobile.'
                ], 403);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user->load('kelas'),
                    'token' => $token,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Username atau password salah.'
        ], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.'
        ]);
    }
}