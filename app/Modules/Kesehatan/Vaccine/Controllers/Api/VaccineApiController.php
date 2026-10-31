<?php

namespace App\Modules\Kesehatan\Vaccine\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VaccineRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VaccineApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $records = VaccineRecord::where('user_id', $user->id)
            ->orderBy('administered_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $records,
            'meta' => [
                'total_records' => $records->count(),
                'latest_vaccine' => $records->first()?->vaccine_name,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vaccine_name' => 'required|string|max:150',
            'dose_number' => 'required|integer|min:1|max:10',
            'administered_date' => 'required|date',
            'batch_number' => 'nullable|string|max:50',
            'administered_by' => 'nullable|string|max:100',
            'allergy_notes' => 'nullable|string|max:300',
        ]);

        $user = $request->user();
        $record = VaccineRecord::create([
            'user_id' => $user->id,
            'instansi_id' => $user->instansi_id,
            'vaccine_name' => $validated['vaccine_name'],
            'dose_number' => (int) $validated['dose_number'],
            'administered_date' => $validated['administered_date'],
            'batch_number' => $validated['batch_number'] ?? null,
            'administered_by' => $validated['administered_by'] ?? null,
            'allergy_notes' => $validated['allergy_notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data vaksinasi berhasil ditambahkan ke Paspor Vaksin',
            'data' => $record,
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $record = VaccineRecord::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Catatan vaksinasi tidak ditemukan',
            ], 404);
        }

        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catatan vaksinasi berhasil dihapus',
        ]);
    }
}
