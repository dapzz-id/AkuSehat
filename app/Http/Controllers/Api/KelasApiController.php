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
        
        if (!$user->isHealthConsultant()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $kelas = Kelas::withCount(['users as jumlah_member' => function($query) {
            $query->where('level', 'Member');
        }])->get();

        return response()->json([
            'success' => true,
            'data' => $kelas
        ]);
    }
}