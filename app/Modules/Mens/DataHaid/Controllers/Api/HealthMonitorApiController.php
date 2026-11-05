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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\Mens\DataHaid\Enums\HaidStatus;

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

    public function getHaidRecords(Request $request): JsonResponse
    {
        $user = Auth::user();
        $instansiId = $user?->instansi_id;

        if (empty($instansiId)) {
            return response()->json([
                'status' => true,
                'message' => 'Data siklus menstruasi berhasil dimuat.',
                'data' => [],
                'divisions' => [],
                'summary' => [
                    'total_wanita' => 0,
                    'sedang_haid' => 0,
                    'selesai' => 0,
                    'warning' => 0,
                ],
            ]);
        }

        $filters = [
            'divisi_id' => $request->query('divisi_id'),
            'status' => $request->query('status'),
            'search' => $request->query('search'),
            'month' => $request->query('month'),
            'year' => $request->query('year'),
        ];

        $records = $this->dataHaidRepository->getHaidRecordsWithFilters($instansiId, $filters);

        $formatted = $records->map(function ($h) {
            $startDate = $h->tanggal_mulai ? (is_string($h->tanggal_mulai) ? $h->tanggal_mulai : $h->tanggal_mulai->format('Y-m-d')) : null;
            $endDate = $h->tanggal_selesai ? (is_string($h->tanggal_selesai) ? $h->tanggal_selesai : $h->tanggal_selesai->format('Y-m-d')) : null;

            $activeDays = 0;
            if ($startDate) {
                $startCarbon = \Carbon\Carbon::parse($startDate)->startOfDay();
                $endCarbon = $endDate ? \Carbon\Carbon::parse($endDate)->startOfDay() : now()->startOfDay();
                $activeDays = max(1, (int) $startCarbon->diffInDays($endCarbon) + 1);
            }

            $effectiveDurasi = $h->durasi_hari ?: $activeDays;
            $isOverdue = $effectiveDurasi > 7;

            return [
                'id' => $h->id,
                'id_user' => $h->id_user,
                'user' => [
                    'id' => $h->user?->id,
                    'nama' => $h->user?->nama ?? 'Pengguna',
                    'nomor_induk' => $h->user?->nomor_induk ?? '-',
                    'avatar_url' => $h->user?->avatar_url,
                    'level' => $h->user?->role_label ?? ($h->user?->level ?? 'Member'),
                    'jk' => $h->user?->jk ?? 'P',
                    'divisi' => $h->user?->divisi ? [
                        'id' => $h->user->divisi->id,
                        'divisi_name' => $h->user->divisi->divisi_name,
                        'kategori' => $h->user->divisi->kategori,
                        'color_cover' => $h->user->divisi->color_cover,
                    ] : null,
                ],
                'tanggal_mulai' => $startDate,
                'tanggal_selesai' => $endDate,
                'durasi_hari' => $effectiveDurasi,
                'status' => $h->status,
                'catatan' => $h->catatan,
                'is_overdue' => $isOverdue,
                'hari_ke' => $h->status === HaidStatus::BERLANGSUNG->value ? $activeDays : null,
                'created_at' => $h->created_at?->toISOString(),
            ];
        });

        // Divisions list in institution with total female count
        $divisions = \App\Models\Divisi::where('instansi_id', $instansiId)
            ->withCount(['users as total_female' => function ($q) {
                $q->where('jk', 'P');
            }])
            ->orderBy('divisi_name')
            ->get(['id', 'divisi_name', 'kategori', 'color_cover']);

        // Summary counts
        $totalWanita = \App\Models\User::where('instansi_id', $instansiId)->where('jk', 'P')->count();
        $sedangHaid = \App\Models\DataHaid::whereHas('user', fn($q) => $q->where('instansi_id', $instansiId))
            ->where('status', HaidStatus::BERLANGSUNG->value)
            ->count();
        $selesai = \App\Models\DataHaid::whereHas('user', fn($q) => $q->where('instansi_id', $instansiId))
            ->where('status', HaidStatus::SELESAI->value)
            ->count();
        $warnings = $formatted->filter(fn($x) => $x['is_overdue'])->count();

        return response()->json([
            'status' => true,
            'message' => 'Data siklus menstruasi berhasil dimuat.',
            'data' => $formatted,
            'divisions' => $divisions,
            'summary' => [
                'total_wanita' => $totalWanita,
                'sedang_haid' => $sedangHaid,
                'selesai' => $selesai,
                'warning' => $warnings,
            ],
        ]);
    }
}
