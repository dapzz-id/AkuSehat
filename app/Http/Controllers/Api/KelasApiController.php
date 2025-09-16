<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasApiController extends Controller
{
    public function getAllKelas(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isGuruOlahraga()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $kelas = Kelas::withCount(['users as jumlah_siswa' => function($query) {
            $query->where('level', 'Siswa');
        }])->get();

        return response()->json([
            'success' => true,
            'data' => $kelas
        ]);
    }
}