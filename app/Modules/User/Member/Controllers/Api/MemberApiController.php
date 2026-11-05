<?php

namespace App\Modules\User\Member\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Modules\User\Member\Services\MemberService;
use App\Modules\User\Member\Repositories\MemberRepository;
use App\Modules\User\Member\Requests\GetMemberProfileRequest;
use App\Modules\User\Member\Requests\SystemRealtimeSyncRequest;
use App\Modules\User\Member\Requests\UpdateProfileRequest;
use App\Modules\User\Member\Requests\UpdatePasswordRequest;
use App\Modules\User\Member\Requests\GetNotificationRequest;
use App\Modules\User\Member\Requests\GetSummaryRequest;
use App\Modules\User\Member\Requests\GetRiwayatHaidRequest;
use App\Modules\User\Member\Requests\StoreHaidRequest;
use App\Modules\User\Member\Requests\UpdateHaidRequest;
use App\Modules\User\Member\Requests\CheckPitaStatusRequest;
use App\Modules\User\Member\Requests\GetRiwayatPitaRequest;
use App\Modules\User\Member\Requests\StorePitaRequest;
use App\Modules\User\Member\Requests\UpdateFcmTokenRequest;
use App\Modules\User\Member\Requests\DeleteFcmTokenRequest;
use App\Modules\Mens\DataHaid\Services\FemHealthService;
use App\Modules\Mens\DataHaid\Repositories\DataHaidRepository;
use App\Modules\Mens\DataHaid\Enums\HaidStatus;
use App\Modules\Mens\DataHaid\Enums\PitaStatus;
use App\Modules\User\Enums\UserRole;
use App\Modules\Kesehatan\Counseling\Services\CounselingService;
use App\Modules\Kesehatan\Counseling\Requests\BookCounselingRequest;
use App\Modules\Kesehatan\Counseling\Requests\CancelCounselingRequest;
use App\Modules\Kesehatan\Counseling\Requests\ListMemberCounselingRequest;
use App\Modules\User\Member\Requests\ListConsultantsRequest;

class MemberApiController extends Controller
{
    public function __construct(
        protected MemberService $memberService,
        protected MemberRepository $memberRepository,
        protected CounselingService $counselingService,
        protected FemHealthService $femHealthService,
        protected DataHaidRepository $dataHaidRepository
    ) {}

    public function me(GetMemberProfileRequest $req): JsonResponse
    {
        $u = $req->user();
        $divisi = $this->memberRepository->getUserDivisi($u->id_divisi);
        $instansi = $u->instansi;

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $u->id,
                'nama' => $u->nama,
                'email' => $u->email,
                'username' => $u->username,
                'avatar' => $u->avatar_url,
                'nomor_induk' => $u->nomor_induk,
                'id_divisi' => $u->id_divisi,
                'divisi' => $divisi->divisi_name ?? null,
                'color_cover' => $divisi?->color_cover,
                'tertiary_color' => $divisi?->color_cover,
                'jk' => $u->jk,
                'level' => $u->level,
                'role_label' => $u->role_label,
                'active_modules' => $u->getActiveModules(),
                'module_permissions' => $u->getCrudPermissions(),
                'can_add_user' => $u->canAddUser(),
                'instansi' => $instansi ? [
                    'id' => $instansi->id,
                    'nama_instansi' => $instansi->nama_instansi,
                    'jenis_instansi' => $instansi->jenis_instansi,
                    'active_modules' => $instansi->active_modules ?? \App\Models\Instansi::getDefaultModulePresets($instansi->jenis_instansi),
                    'custom_role_labels' => [
                        UserRole::MEMBER->value => $instansi->getRoleLabel(UserRole::MEMBER->value),
                        UserRole::HEALTH_MONITOR->value => $instansi->getRoleLabel(UserRole::HEALTH_MONITOR->value),
                        UserRole::HEALTH_CONSULTANT->value => $instansi->getRoleLabel(UserRole::HEALTH_CONSULTANT->value),
                    ],
                ] : null,
            ]
        ]);
    }

    public function systemRealtimeSync(SystemRealtimeSyncRequest $req): JsonResponse
    {
        $platform = $req->input('platform', 'android');
        $currentVersion = $req->input('current_version') ?? $req->header('X-App-Version', '1.0.0');

        $u = $req->user() ?? $req->user('sanctum');
        $instansiId = $u?->instansi_id ?? 'global';

        // 1. Read ultra-lightweight version tokens from Cache/Memory (0 SQL queries)
        $globalSysVer = \Illuminate\Support\Facades\Cache::get('global_system_version', 'g_sys_1');
        $globalModVer = \Illuminate\Support\Facades\Cache::get('global_modules_version', 'g_mod_1');
        $instansiVer = $u ? \Illuminate\Support\Facades\Cache::get("instansi_modules_version_{$instansiId}", 'i1') : 'none';
        $userCustomVer = $u ? md5(json_encode([$u->custom_modules, $u->custom_permissions])) : 'none';
        $userUpdatedVer = $u?->updated_at ? $u->updated_at->timestamp : 'u0';

        // 2. Generate composite version ETag
        $currentEtag = '"' . md5("{$globalSysVer}_{$globalModVer}_{$instansiVer}_{$userCustomVer}_{$userUpdatedVer}_{$platform}_{$currentVersion}") . '"';

        // 3. Conditional Check: Webhook-style zero-load response
        $clientEtag = $req->header('If-None-Match');
        if ($clientEtag && trim($clientEtag, '" ') === trim($currentEtag, '" ')) {
            // 304 Not Modified -> 0 DB query, 0 body transfer, server processing <0.3ms
            return response()->json([], 304)->withHeaders([
                'ETag' => $currentEtag,
                'Cache-Control' => 'private, no-cache, must-revalidate',
            ]);
        }

        // 4. Compute all realtime states simultaneously when version changes
        $isMaintenance = in_array($platform, ['android', 'ios'])
            ? \App\Models\MaintenanceMode::isMobileInMaintenance()
            : \App\Models\MaintenanceMode::isWebInMaintenance();

        /** @var \App\Modules\SuperAdmin\SystemConfig\Services\SoftwareAppService $softwareService */
        $softwareService = app(\App\Modules\SuperAdmin\SystemConfig\Services\SoftwareAppService::class);
        $updateCheck = $softwareService->checkUpdate($platform, $currentVersion);

        $payload = [
            'status' => true,
            'version' => $currentEtag,
            'maintenance' => [
                'is_maintenance' => $isMaintenance,
                'message' => $isMaintenance
                    ? 'Sistem sedang dalam proses pemeliharaan berkala untuk peningkatan performa dan keamanan. Silakan tunggu beberapa saat.'
                    : null,
            ],
            'app_update' => [
                'has_update' => $updateCheck['has_update'] ?? false,
                'is_mandatory' => $updateCheck['is_mandatory'] ?? false,
                'data' => $updateCheck['data'] ?? null,
            ],
        ];

        if ($u) {
            $instansi = $u->instansi;
            $payload['active_modules'] = $u->getActiveModules();
            $payload['module_permissions'] = $u->getCrudPermissions();
            $payload['role_label'] = $u->role_label;
            $payload['can_add_user'] = $u->canAddUser();
            $payload['instansi'] = $instansi ? [
                'id' => $instansi->id,
                'nama_instansi' => $instansi->nama_instansi,
                'active_modules' => $instansi->active_modules ?? \App\Models\Instansi::getDefaultModulePresets($instansi->jenis_instansi),
                'custom_role_labels' => [
                    \App\Modules\User\Enums\UserRole::MEMBER->value => $instansi->getRoleLabel(\App\Modules\User\Enums\UserRole::MEMBER->value),
                    \App\Modules\User\Enums\UserRole::HEALTH_MONITOR->value => $instansi->getRoleLabel(\App\Modules\User\Enums\UserRole::HEALTH_MONITOR->value),
                    \App\Modules\User\Enums\UserRole::HEALTH_CONSULTANT->value => $instansi->getRoleLabel(\App\Modules\User\Enums\UserRole::HEALTH_CONSULTANT->value),
                ],
            ] : null;
        }

        return response()->json($payload)->withHeaders([
            'ETag' => $currentEtag,
            'Cache-Control' => 'private, no-cache, must-revalidate',
        ]);
    }

    public function myModules(SystemRealtimeSyncRequest $req): JsonResponse
    {
        return $this->systemRealtimeSync($req);
    }

    public function updateProfile(UpdateProfileRequest $req): JsonResponse
    {
        $u = $req->user();
        $validated = $req->validated();

        $avatarFile = $req->hasFile('avatar') ? $req->file('avatar') : null;
        $avatarInput = $req->filled('avatar') ? $req->input('avatar') : null;

        $result = $this->memberService->updateProfileInfo($u, $validated, $avatarFile, $avatarInput);

        if (!$result['status']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
            ], 422);
        }

        $user = $result['user'];

        return response()->json([
            'status' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'username' => $user->username,
                'avatar' => $user->avatar_url,
                'level' => $user->level,
                'role_label' => $user->role_label,
            ]
        ]);
    }

    public function changePassword(UpdatePasswordRequest $req): JsonResponse
    {
        $u = $req->user();
        $validated = $req->validated();

        $result = $this->memberService->updatePassword(
            $u,
            $validated['current_password'],
            $validated['password']
        );

        if (!$result['status']) {
            return response()->json([
                'status' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'status' => true,
            'message' => 'Password berhasil diubah.'
        ]);
    }

    public function updateFcmToken(UpdateFcmTokenRequest $req): JsonResponse
    {
        $u = $req->user();
        $token = $req->input('fcm_token');
        $platform = $req->input('platform', 'android');
        $deviceModel = $req->input('device_model');

        try {
            // Update token utama di users table jika kolom tersedia
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'fcm_token')) {
                $u->fcm_token = $token;
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'fcm_platform')) {
                    $u->fcm_platform = $platform;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'fcm_updated_at')) {
                    $u->fcm_updated_at = now();
                }
                $u->save();
            }

            // Daftarkan ke user_device_tokens untuk multi-device push jika tabel tersedia
            if (\Illuminate\Support\Facades\Schema::hasTable('user_device_tokens')) {
                \Illuminate\Support\Facades\DB::table('user_device_tokens')->updateOrInsert(
                    ['user_id' => $u->id, 'token' => $token],
                    [
                        'id' => (string) \Illuminate\Support\Str::uuid(),
                        'platform' => $platform,
                        'device_model' => $deviceModel,
                        'last_active_at' => now(),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('FCM token registration fallback: ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'FCM Device Token berhasil didaftarkan.',
        ]);
    }

    public function deleteFcmToken(DeleteFcmTokenRequest $req): JsonResponse
    {
        $u = $req->user();
        $token = $req->input('fcm_token');

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('user_device_tokens')) {
                if ($token) {
                    \Illuminate\Support\Facades\DB::table('user_device_tokens')
                        ->where('user_id', $u->id)
                        ->where('token', $token)
                        ->delete();
                } else {
                    \Illuminate\Support\Facades\DB::table('user_device_tokens')
                        ->where('user_id', $u->id)
                        ->delete();
                }
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'fcm_token')) {
                if ($token) {
                    if ($u->fcm_token === $token) {
                        $u->fcm_token = null;
                        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'fcm_platform')) {
                            $u->fcm_platform = null;
                        }
                        $u->save();
                    }
                } else {
                    $u->fcm_token = null;
                    if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'fcm_platform')) {
                        $u->fcm_platform = null;
                    }
                    $u->save();
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('FCM token deletion fallback: ' . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'FCM Device Token berhasil dihapus.',
        ]);
    }

    public function notifications(GetNotificationRequest $req): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data' => [
                ['id' => 1, 'title' => 'Pemeriksaan berkala', 'message' => 'Jangan lupa cek kesehatan berkala.', 'created_at' => now()->toDateTimeString()],
                ['id' => 2, 'title' => 'Status Aktif', 'message' => 'Akun Anda aktif dan terverifikasi.', 'created_at' => now()->toDateTimeString()],
            ]
        ]);
    }

    public function summary(GetSummaryRequest $req): JsonResponse
    {
        $data = $this->memberService->getSummary($req->user());

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function summaryGrafik(GetSummaryRequest $req): JsonResponse
    {
        $result = $this->memberService->getSummaryGrafik($req->user());

        return response()->json([
            'status' => true,
            'data' => $result,
        ]);
    }

    public function riwayatHaid(GetRiwayatHaidRequest $req, string|int $userId): JsonResponse
    {
        $u = $req->user();
        if ($u->isMember() && (string) $u->id !== (string) $userId) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if (!$u->isSuperAdmin() && $u->instansi_id) {
            $targetUser = $this->memberRepository->findById($userId);
            if (!$targetUser || $targetUser->instansi_id !== $u->instansi_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan.'
                ], 404);
            }
        }

        $result = $this->dataHaidRepository->getUserHaidGrouped($userId);

        return response()->json(['status' => true, 'data' => $result]);
    }

    public function riwayatHaidMember(GetRiwayatHaidRequest $req, string|int $id): JsonResponse
    {
        return $this->riwayatHaid($req, $id);
    }

    public function statusHaid(GetRiwayatHaidRequest $req): JsonResponse
    {
        $u = $req->user();
        $haid = $this->dataHaidRepository->getActiveUserHaid($u->id);

        if (!$haid) {
            return response()->json([
                'status' => true,
                'data' => null
            ]);
        }

        $tglMulai = Carbon::parse($haid->tanggal_mulai);
        $diff = Carbon::today()->diffInDays($tglMulai) + 1;

        return response()->json([
            'status' => true,
            'data' => [
                'haid' => $haid,
                'hari_ke' => $diff
            ]
        ]);
    }

    public function listHaid(GetRiwayatHaidRequest $req): JsonResponse
    {
        $u = $req->user();
        $today = Carbon::today()->toDateString();

        $rows = $this->dataHaidRepository->getUserActiveHaidWindow($u->id, $today);

        return response()->json([
            'status' => true,
            'message' => 'Data member perempuan berhasil dimuat.',
            'data' => $rows
        ]);
    }

    public function storeHaid(StoreHaidRequest $req): JsonResponse
    {
        $u = $req->user();

        $jumlahBulanIni = $this->dataHaidRepository->countHaidInCurrentMonth($u->id);

        if ($jumlahBulanIni >= 2) {
            return response()->json([
                'status' => false,
                'message' => 'Anda hanya dapat membuat maksimal 2 data siklus haid dalam 1 bulan.'
            ], 400);
        }

        $validated = $req->validated();

        // Verifikasi konfirmasi password akun
        if (!Hash::check($validated['password'], $u->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password akun yang Anda masukkan tidak sesuai. Konfirmasi mulai haid gagal.'
            ], 422);
        }

        if ($this->dataHaidRepository->getActiveUserHaid($u->id)) {
            return response()->json([
                'status' => false,
                'message' => 'Anda masih memiliki data haid yang berlangsung. Selesaikan terlebih dahulu.'
            ], 400);
        }

        $this->dataHaidRepository->create([
            'id_user' => $u->id,
            'instansi_id' => $u->instansi_id,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => null,
            'durasi_hari' => 0,
            'status' => HaidStatus::BERLANGSUNG->value,
            'catatan' => $validated['catatan'] ?? 'Data haid dari aplikasi',
        ]);

        return response()->json(['status' => true, 'message' => 'Data haid berhasil dimulai']);
    }

    public function updateHaid(UpdateHaidRequest $req, string|int $id): JsonResponse
    {
        $u = $req->user();
        
        $haid = $this->dataHaidRepository->findUserHaidRecord($id, $u->id);

        if (!$haid) {
            return response()->json(['status' => false, 'message' => 'Data haid tidak ditemukan'], 404);
        }

        if ($haid->status === HaidStatus::SELESAI->value) {
            return response()->json(['status' => false, 'message' => 'Data haid sudah selesai'], 400);
        }

        $tanggalSelesai = $req->validated('tanggal_selesai') ?? date('Y-m-d');
        $tanggalMulai = date('Y-m-d', strtotime($haid->tanggal_mulai));
        $durasiHari = max(1, (strtotime($tanggalSelesai) - strtotime($tanggalMulai)) / 86400 + 1);

        // Aturan ketat: Siklus haid tidak dapat dihentikan mandiri sebelum minimal 7 hari
        if ($durasiHari < 7 && !$u->isHealthConsultant() && !$u->isHealthMonitor() && !$u->isAdmin() && !$u->isSuperAdmin()) {
            return response()->json([
                'status' => false,
                'message' => "Siklus haid tidak dapat dihentikan mandiri sebelum minimal 7 hari (saat ini baru berjalan hari ke-{$durasiHari}). Jika Anda mengalami keluhan kesehatan atau kondisi darurat, silakan hubungi Health Consultant / Petugas UKS instansi Anda untuk melakukan penghentian khusus."
            ], 422);
        }

        $this->dataHaidRepository->completeUserHaidRecord($id, $u->id, $tanggalSelesai, $durasiHari);
        $this->dataHaidRepository->updateUserPitaEstimasi($u->id, date('Y-m-d', strtotime($tanggalSelesai . ' +1 day')));

        return response()->json(['status' => true, 'message' => 'Data haid berhasil diselesaikan']);
    }

    public function checkPeminjamanStatus(CheckPitaStatusRequest $req): JsonResponse
    {
        $u = $req->user();
        $today = Carbon::today();
        
        $this->dataHaidRepository->markOverduePitaForUser($u->id, $today->toDateString());
        
        $activePinjaman = $this->dataHaidRepository->getActiveUserPita($u->id);
        
        if (!$activePinjaman) {
            return response()->json([
                'status' => true,
                'data' => null
            ]);
        }
        
        $estimasi = Carbon::parse($activePinjaman->estimasi_selesai_haid);
        $diff = $today->diffInDays($estimasi, false);
        
        $notification = null;
        if ($diff === 0) {
            $notification = [
                'type' => 'warning',
                'title' => 'Pengembalian Pita Hari Ini!',
                'message' => 'Hari ini adalah estimasi terakhir pengembalian pita. Segera kembalikan pita Anda.',
                'days_remaining' => 0
            ];
        } elseif ($diff > 0 && $diff <= 3) {
            $notification = [
                'type' => 'info',
                'title' => 'Pengingat Pengembalian Pita',
                'message' => "Estimasi pengembalian pita Anda adalah {$diff} hari lagi. Harap kembalikan tepat waktu.",
                'days_remaining' => $diff
            ];
        } elseif ($diff < 0) {
            $lateDays = abs($diff);
            $notification = [
                'type' => 'danger',
                'title' => 'Terlambat Mengembalikan Pita!',
                'message' => "Anda sudah terlambat {$lateDays} hari mengembalikan pita. Segera kembalikan!",
                'days_late' => $lateDays
            ];
        }
        
        return response()->json([
            'status' => true,
            'data' => [
                'peminjaman' => $activePinjaman,
                'notification' => $notification,
                'estimasi_selesai' => $estimasi->format('Y-m-d'),
                'days_diff' => $diff
            ]
        ]);
    }

    public function getStatusPita(CheckPitaStatusRequest $req): JsonResponse
    {
        return $this->checkPeminjamanStatus($req);
    }

    public function getRiwayatPita(GetRiwayatPitaRequest $req, string|int $userId): JsonResponse
    {
        $u = $req->user();
        if ($u->isMember() && (string) $u->id !== (string) $userId) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if (!$u->isSuperAdmin() && $u->instansi_id) {
            $targetUser = $this->memberRepository->findById($userId);
            if (!$targetUser || $targetUser->instansi_id !== $u->instansi_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data pengguna tidak ditemukan di institusi Anda.'
                ], 404);
            }
        }

        $result = $this->dataHaidRepository->getUserPitaGrouped($userId);

        return response()->json(['status' => true, 'data' => $result]);
    }

    public function storePita(StorePitaRequest $req): JsonResponse
    {
        $u = $req->user();
        
        $activePinjaman = $this->dataHaidRepository->getActiveUserPita($u->id);

        if ($activePinjaman) {
            return response()->json([
                'status' => false,
                'message' => 'Anda masih memiliki peminjaman yang belum dikembalikan'
            ], 400);
        }

        $activeHaid = $this->dataHaidRepository->getActiveUserHaid($u->id);

        if (!$activeHaid) {
            return response()->json([
                'status' => false,
                'message' => 'Tidak ada data haid yang aktif. Mulai jadwal haid terlebih dahulu.'
            ], 400);
        }

        $validated = $req->validated();

        // Verifikasi konfirmasi password akun
        if (!Hash::check($validated['password'], $u->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Password akun yang Anda masukkan tidak sesuai. Konfirmasi pengajuan pinjam pita gagal.'
            ], 422);
        }

        $estimasiSelesai = Carbon::parse($activeHaid->tanggal_mulai)->addDays(7)->format('Y-m-d');

        $this->dataHaidRepository->createPeminjaman([
            'id_user' => $u->id,
            'instansi_id' => $u->instansi_id,
            'jumlah_pita' => $validated['jumlah_pita'] ?? 1,
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_kembali' => null,
            'status' => 'menunggu',
            'verified' => 0,
            'keterangan' => 'Peminjaman pita: 1 buah',
            'estimasi_selesai_haid' => $estimasiSelesai,
        ]);

        return response()->json(['status' => true, 'message' => 'Pengajuan peminjaman pita berhasil']);
    }

    public function listCounseling(ListMemberCounselingRequest $req): JsonResponse
    {
        $sessions = $this->counselingService->getMemberSessions($req->user());

        return response()->json([
            'status' => true,
            'data' => $sessions
        ]);
    }

    public function bookCounseling(BookCounselingRequest $req): JsonResponse
    {
        $u = $req->user();
        if (!$u->instansi_id) {
            return response()->json([
                'status' => false,
                'message' => 'Anda harus terdaftar di sebuah instansi untuk booking konsultasi.'
            ], 422);
        }

        $validated = $req->validated();

        if (!empty($validated['consultant_id'])) {
            $consultant = $this->memberRepository->findById($validated['consultant_id']);

            if (!$consultant || $consultant->instansi_id !== $u->instansi_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'Konselor yang dipilih tidak berada di instansi Anda.'
                ], 422);
            }
        }

        $session = $this->counselingService->bookSession($u, $validated);

        return response()->json([
            'status' => true,
            'message' => 'Jadwal konsultasi berhasil diajukan! Menunggu persetujuan dari konselor/petugas kesehatan.',
            'data' => $session->load('consultant:id,nama,level')
        ], 201);
    }

    public function cancelCounseling(CancelCounselingRequest $req, string|int $id): JsonResponse
    {
        $res = $this->counselingService->cancelSession($req->user(), $id);

        return response()->json([
            'status' => $res['status'],
            'message' => $res['message'],
        ], $res['code']);
    }

    public function listConsultants(ListConsultantsRequest $req): JsonResponse
    {
        $u = $req->user();
        if (!$u->instansi_id) {
            return response()->json(['status' => false, 'data' => []]);
        }

        $consultants = $this->counselingService->getInstansiConsultants($u->instansi_id);

        return response()->json([
            'status' => true,
            'data' => $consultants->map(fn($c) => [
                'id' => $c->id,
                'nama' => $c->nama,
                'level' => $c->level,
                'role_label' => match($c->level) {
                    UserRole::HEALTH_CONSULTANT->value => 'Guru BK / Konselor',
                    UserRole::HEALTH_MONITOR->value => 'Guru PJOK / Health Monitor',
                    UserRole::ADMIN->value => 'Admin PMR / Unit Medis',
                    UserRole::ADMIN_INSTANSI->value => 'Penanggung Jawab Instansi',
                    default => $c->level,
                },
                'nomor_induk' => $c->nomor_induk,
                'jk' => $c->jk,
                'avatar' => $c->avatar,
            ])
        ]);
    }
}
