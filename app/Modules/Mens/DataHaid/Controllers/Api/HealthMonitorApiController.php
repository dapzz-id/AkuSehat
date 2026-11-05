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
use App\Modules\Mens\DataHaid\Requests\FilterFemHealthRequest;
use App\Modules\Mens\DataHaid\Requests\StopMemberHaidRequest;
use App\Modules\Mens\DataHaid\Requests\NotifyMemberCallRequest;
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

    public function getHaidRecords(FilterFemHealthRequest $request): JsonResponse
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

    /**
     * Menghentikan siklus haid member oleh Health Consultant (kondisi khusus/medis)
     */
    public function stopMemberHaid(StopMemberHaidRequest $request, string|int $id): JsonResponse
    {
        $consultant = Auth::user();
        $instansiId = $consultant?->instansi_id;

        $haid = \App\Models\DataHaid::with('user')->find($id);
        if (!$haid) {
            return response()->json([
                'status' => false,
                'message' => 'Data siklus haid tidak ditemukan.',
            ], 404);
        }

        if ($haid->user?->instansi_id !== $instansiId && !$consultant->isSuperAdmin()) {
            return response()->json([
                'status' => false,
                'message' => 'Akses ditolak. Pengguna bukan dari institusi Anda.',
            ], 403);
        }

        if ($haid->status === HaidStatus::SELESAI->value) {
            return response()->json([
                'status' => false,
                'message' => 'Siklus haid ini sudah berstatus selesai.',
            ], 400);
        }

        $tanggalSelesai = $request->input('tanggal_selesai', date('Y-m-d'));
        $tanggalMulai = date('Y-m-d', strtotime($haid->tanggal_mulai));
        $durasiHari = max(1, (strtotime($tanggalSelesai) - strtotime($tanggalMulai)) / 86400 + 1);
        $alasan = trim((string) $request->input('alasan', 'Dihentikan oleh Petugas UKS / Health Consultant atas pertimbangan medis.'));

        $catatanExisting = $haid->catatan ? $haid->catatan . " | " : "";
        $catatanUpdated = $catatanExisting . "[Health Consultant Note: {$alasan}]";

        $haid->update([
            'tanggal_selesai' => $tanggalSelesai,
            'durasi_hari' => $durasiHari,
            'status' => HaidStatus::SELESAI->value,
            'catatan' => $catatanUpdated,
        ]);

        // Kirim notifikasi push ke perangkat siswi via FCM
        if ($haid->user) {
            try {
                app(\App\Services\FcmPushNotificationService::class)->sendToUser(
                    $haid->user,
                    'Siklus Haid Diselesaikan Petugas UKS',
                    "Siklus menstruasi Anda telah ditandai selesai oleh {$consultant->nama}. Catatan: {$alasan}",
                    [
                        'type' => 'haid_completed_by_consultant',
                        'id_haid' => (string) $haid->id,
                    ]
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('FCM send failed on stopMemberHaid: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Siklus haid berhasil diselesaikan atas rekomendasi Health Consultant.',
            'data' => $haid,
        ]);
    }

    /**
     * Memanggil siswi/member untuk konsultasi UKS dan/atau pengembalian pita via FCM
     */
    public function notifyMemberCall(NotifyMemberCallRequest $request, string|int $id): JsonResponse
    {
        $consultant = Auth::user();
        $instansiId = $consultant?->instansi_id;
        $targetType = $request->input('target_type', 'peminjaman'); // 'peminjaman' | 'haid'
        $customPesan = trim((string) $request->input('pesan'));

        $targetUser = null;
        $defaultBody = "";

        if ($targetType === 'haid') {
            $haid = \App\Models\DataHaid::with('user')->find($id);
            if (!$haid || !$haid->user) {
                return response()->json(['status' => false, 'message' => 'Data siklus haid tidak ditemukan.'], 404);
            }
            if ($haid->user->instansi_id !== $instansiId && !$consultant->isSuperAdmin()) {
                return response()->json(['status' => false, 'message' => 'Akses ditolak.'], 403);
            }
            $targetUser = $haid->user;
            $defaultBody = "Halo {$targetUser->nama}, Anda dipanggil oleh Petugas UKS / Konselor ({$consultant->nama}) untuk konsultasi pemantauan kesehatan siklus menstruasi Anda.";
        } else {
            $pita = \App\Models\PeminjamanPita::with('user')->find($id);
            if (!$pita || !$pita->user) {
                return response()->json(['status' => false, 'message' => 'Data peminjaman pita tidak ditemukan.'], 404);
            }
            if ($pita->user->instansi_id !== $instansiId && !$consultant->isSuperAdmin()) {
                return response()->json(['status' => false, 'message' => 'Akses ditolak.'], 403);
            }
            $targetUser = $pita->user;
            $defaultBody = "Halo {$targetUser->nama}, Anda dipanggil oleh Petugas UKS ({$consultant->nama}) untuk pengembalian pita UKS dan konsultasi kesehatan.";
        }

        $title = "Panggilan UKS & Konseling Kesehatan";
        $body = !empty($customPesan) ? $customPesan : $defaultBody;

        $fcmResult = app(\App\Services\FcmPushNotificationService::class)->sendToUser(
            $targetUser,
            $title,
            $body,
            [
                'type' => 'call_to_uks',
                'consultant_id' => (string) $consultant->id,
                'consultant_name' => (string) $consultant->nama,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Panggilan via notifikasi FCM berhasil dikirim ke perangkat pengguna.',
            'fcm_result' => $fcmResult,
        ]);
    }
}
