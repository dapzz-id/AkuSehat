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
        $instansiId = $user?->instansi_id;

        if ($instansiId) {
            $this->femHealthService->refreshOverduePita($instansiId);
            $peminjaman = $this->dataHaidRepository->getAllPeminjaman($instansiId);
            $menungguVerifikasi = $this->dataHaidRepository->countPendingVerification($instansiId);
            $totalMember = $this->dataHaidRepository->countFemaleMembers($instansiId);
        } else {
            $peminjaman = collect();
            $menungguVerifikasi = 0;
            $totalMember = 0;
        }

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
        $instansiId = Auth::user()?->instansi_id;
        $activeHaid = $instansiId ? $this->dataHaidRepository->getActiveHaidForInstansi($instansiId) : collect();

        return response()->json([
            'status' => true,
            'message' => 'Data siswi sedang menstruasi berhasil dimuat.',
            'data' => $activeHaid,
        ]);
    }

    public function verifikasi(string|int $id): JsonResponse
    {
        $instansiId = Auth::user()?->instansi_id;
        if ($instansiId) {
            $this->femHealthService->verifyPita($id, $instansiId);
        }

        return response()->json([
            'status' => true,
            'message' => 'Peminjaman pita berhasil disetujui.',
        ]);
    }

    public function tolak(RejectPitaRequest $request, string|int $id): JsonResponse
    {
        $instansiId = Auth::user()?->instansi_id;
        $alasan = $request->validated('alasan') ?? $request->validated('alasan_penolakan');
        if ($instansiId) {
            $this->femHealthService->rejectPita($id, $alasan, $instansiId);
        }

        return response()->json([
            'status' => true,
            'message' => 'Peminjaman pita telah ditolak.',
        ]);
    }

    public function getMemberSedangHaid(GetMemberSedangHaidRequest $request): JsonResponse
    {
        $instansiId = Auth::user()?->instansi_id;
        $activeHaid = $instansiId ? $this->dataHaidRepository->getActiveHaidForInstansi($instansiId) : collect();

        return response()->json([
            'status' => true,
            'message' => 'Data siswi yang sedang haid berhasil dimuat.',
            'data' => $activeHaid,
        ]);
    }

    public function getHaidMembersForPita(): JsonResponse
    {
        $instansiId = Auth::user()?->instansi_id;
        $members = $instansiId ? $this->dataHaidRepository->getHaidMembersForPita($instansiId) : collect();

        return response()->json([
            'status' => true,
            'message' => 'Data siswi yang sedang haid aktif dan siap pinjam pita berhasil dimuat.',
            'data' => $members,
        ]);
    }

    public function getOverdueWarnings(GetOverdueWarningsRequest $request): JsonResponse
    {
        $instansiId = Auth::user()?->instansi_id;
        $warnings = $instansiId ? $this->dataHaidRepository->getOverdueWarnings($instansiId) : collect();

        return response()->json([
            'status' => true,
            'message' => 'Data keterlambatan pengembalian pita berhasil dimuat.',
            'data' => $warnings,
        ]);
    }

    public function show(string|int $id): JsonResponse
    {
        $pita = $this->dataHaidRepository->findPeminjamanOrFail($id, Auth::user()->instansi_id);

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
        $pita = $this->femHealthService->updatePeminjamanFromApi($id, $request->validated(), Auth::user()->instansi_id);

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman pita berhasil diperbarui.',
            'data' => $pita,
        ]);
    }

    public function destroy(string|int $id): JsonResponse
    {
        $this->femHealthService->deletePeminjamanFromApi($id, Auth::user()->instansi_id);

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
