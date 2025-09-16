<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hb;

class HbApiController extends Controller
{
    public function getHbSiswa(Request $request, $id)
    {
        $user = $request->user();
        
        if ($user->isSiswa() && $user->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $hb = Hb::where('id_user', $id)
            ->orderBy('tgl', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $hb
        ]);
    }
}