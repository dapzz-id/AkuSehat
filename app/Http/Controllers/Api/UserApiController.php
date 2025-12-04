<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserApiController extends Controller
{
    public function getMember(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isHealthConsultant()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $member = User::with('divisi')
            ->where('level', 'Member')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $member
        ]);
    }

    public function getMemberByDivisi(Request $request, $divisi_id)
    {
        $user = $request->user();
        
        if (!$user->isHealthConsultant()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        $member = User::with('divisi')
            ->where('level', 'Member')
            ->where('id_divisi', $divisi_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $member
        ]);
    }
}