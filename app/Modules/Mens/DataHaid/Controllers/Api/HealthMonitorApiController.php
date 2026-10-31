<?php

namespace App\Modules\Mens\DataHaid\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Mens\DataHaid\Services\FemHealthService;
use App\Modules\Mens\DataHaid\Repositories\DataHaidRepository;
use App\Modules\Mens\DataHaid\Requests\RejectPitaRequest;
use App\Modules\Mens\DataHaid\Requests\GetMemberSedangHaidRequest;
use App\Modules\Mens\DataHaid\Requests\GetOverdueWarningsRequest;
use App\Modules\Mens\DataHaid\Requests\StorePitaPeminjamanRequest;
use App\Modules\Mens\DataHaid\Requests\UpdatePitaPeminjamanRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class HealthMonitorApiController extends Controller
{
    public function __construct(
        protected FemHealthService $femHealthService,
        protected DataHaidRepository $dataHaidRepository
    ) {}

    public function index(): JsonResponse
    {
        $user = Auth::user();
        $this->femHealthService->refreshOverduePita($user->instansi_id);

        $peminjaman = $this->dataHaidRepository->getAllPeminjaman($user->instansi_id);
        $menungguVerifikasi = $this->dataHaidRepository->countPendingVerification($user->instansi_id);
        $totalMember = $this->dataHaidRepository->countFemaleMembers($user->instansi_id);

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman pita berhasil dimuat.',
            'data' => $peminjaman,
            'menunggu_verifikasi' => $menungguVerifikasi,
            'total_member' => $totalMember,
        ]);
    }

    public function getActiveHaid(): JsonResponse
    {
        $activeHaid = $this->dataHaidRepository->getActiveHaidForInstansi(Auth::user()->instansi_id);

        return response()->json([
            'status' => true,
            'message' => 'Data siswi sedang menstruasi berhasil dimuat.',
            'data' => $activeHaid,
        ]);
    }

    public function verifikasi(string|int $id): JsonResponse
    {
        $this->femHealthService->verifyPita($id);

        return response()->json([
            'status' => true,
            'message' => 'Peminjaman pita berhasil disetujui.',
        ]);
    }

    public function tolak(RejectPitaRequest $request, string|int $id): JsonResponse
    {
        $this->femHealthService->rejectPita($id, $request->validated('alasan'));

        return response()->json([
            'status' => true,
            'message' => 'Peminjaman pita telah ditolak.',
        ]);
    }

    public function getMemberSedangHaid(GetMemberSedangHaidRequest $request): JsonResponse
    {
        $activeHaid = $this->dataHaidRepository->getActiveHaidForInstansi(Auth::user()->instansi_id);

        return response()->json([
            'status' => true,
            'message' => 'Data siswi yang sedang haid berhasil dimuat.',
            'data' => $activeHaid,
        ]);
    }

    public function getOverdueWarnings(GetOverdueWarningsRequest $request): JsonResponse
    {
        $warnings = $this->dataHaidRepository->getOverdueWarnings(Auth::user()->instansi_id);

        return response()->json([
            'status' => true,
            'message' => 'Data keterlambatan pengembalian pita berhasil dimuat.',
            'data' => $warnings,
        ]);
    }

    public function show(string|int $id): JsonResponse
    {
        $pita = $this->dataHaidRepository->findPeminjamanOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $pita->load('user'),
        ]);
    }

    public function store(StorePitaPeminjamanRequest $request): JsonResponse
    {
        $pita = $this->femHealthService->createPeminjamanFromApi($request->validated(), Auth::user());

        return response()->json([
            'status' => true,
            'message' => 'Peminjaman pita berhasil dicatat.',
            'data' => $pita,
        ], 201);
    }

    public function update(UpdatePitaPeminjamanRequest $request, string|int $id): JsonResponse
    {
        $pita = $this->femHealthService->updatePeminjamanFromApi($id, $request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman pita berhasil diperbarui.',
            'data' => $pita,
        ]);
    }

    public function destroy(string|int $id): JsonResponse
    {
        $this->femHealthService->deletePeminjamanFromApi($id);

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman pita berhasil dihapus.',
        ]);
    }

    public function acceptVerifikasi(string|int $id): JsonResponse
    {
        return $this->verifikasi($id);
    }

    public function rejectVerifikasi(RejectPitaRequest $request, string|int $id): JsonResponse
    {
        return $this->tolak($request, $id);
    }

    public function warningTerlambat(GetOverdueWarningsRequest $request): JsonResponse
    {
        return $this->getOverdueWarnings($request);
    }
}
