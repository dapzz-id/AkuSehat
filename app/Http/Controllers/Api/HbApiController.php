<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hb;

class HbApiController extends Controller
{
    public function getHbMember(Request $request, $id)
    {
        $user = $request->user();
        
        // 🔒 Cegah member melihat data orang lain
        if ($user->isMember() && $user->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses tidak diperbolehkan.'
            ], 403);
        }

        // 🩸 Ambil semua data Hb user
        $hb = Hb::where('id_user', $id)
            ->orderBy('tgl', 'desc')
            ->get();

        // 🗓️ Kelompokkan berdasarkan tahun → bulan
        $grouped = $hb->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tgl)->format('Y');
        })->map(function ($yearGroup) {
            return $yearGroup->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->tgl)->format('m');
            })->map(function ($monthGroup, $month) {
                $monthName = \Carbon\Carbon::createFromFormat('m', $month)
                    ->locale('id')
                    ->translatedFormat('F');
                return [
                    'month' => strtolower($monthName),
                    'data' => $monthGroup->values(),
                ];
            })->values();
        });

        // 🔁 Format akhir yang rapi
        $formatted = $grouped->map(function ($months, $year) {
            return [
                'year' => (int) $year,
                'months' => $months,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $formatted
        ]);
    }
}