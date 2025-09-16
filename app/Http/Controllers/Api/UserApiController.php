<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserApiController extends Controller
{
    public function getSiswa(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isGuruOlahraga()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $siswa = User::with('kelas')
            ->where('level', 'Siswa')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $siswa
        ]);
    }

    public function getSiswaByKelas(Request $request, $kelas_id)
    {
        $user = $request->user();
        
        if (!$user->isGuruOlahraga()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $siswa = User::with('kelas')
            ->where('level', 'Siswa')
            ->where('id_kelas', $kelas_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $siswa
        ]);
    }
}