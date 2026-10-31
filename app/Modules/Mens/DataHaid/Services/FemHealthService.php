<?php

namespace App\Modules\Mens\DataHaid\Services;

use App\Models\DataHaid;
use App\Models\PeminjamanPita;
use App\Models\CounselingSession;
use App\Models\User;
use App\Modules\Mens\DataHaid\Repositories\DataHaidRepository;
use App\Modules\Mens\DataHaid\Enums\PitaStatus;
use App\Modules\Mens\DataHaid\Enums\HaidStatus;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Exception;

class FemHealthService
{
    public function __construct(
        protected DataHaidRepository $dataHaidRepository
    ) {}

    public function getDashboardData(User $actor, array $filters): array
    {
        $instansiId = (string) $actor->instansi_id;

        $this->refreshOverduePita($instansiId);

        $warnings = $this->dataHaidRepository->getOverdueWarnings($instansiId);
        $currentlyHaid = $this->dataHaidRepository->getActiveHaidForInstansi($instansiId);

        $selectedMonth = (int) ($filters['month'] ?? now()->month);
        $selectedYear = (int) ($filters['year'] ?? now()->year);
        $currentDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        $daysInMonth = $currentDate->daysInMonth;
        $firstDayOfWeek = $currentDate->dayOfWeek;
        $monthName = $currentDate->translatedFormat('F Y');

        $monthStart = $currentDate->copy()->startOfMonth()->toDateString();
        $monthEnd = $currentDate->copy()->endOfMonth()->toDateString();

        $monthHaidRecords = $this->dataHaidRepository->getMonthHaidRecords($instansiId, $monthStart, $monthEnd);

        $calendarEvents = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateStr = sprintf('%04d-%02d-%02d', $selectedYear, $selectedMonth, $d);
            $calendarEvents[$d] = $monthHaidRecords->filter(function ($item) use ($dateStr) {
                $mulai = $item->tanggal_mulai ? (is_string($item->tanggal_mulai) ? $item->tanggal_mulai : $item->tanggal_mulai->format('Y-m-d')) : null;
                if (!$mulai) return false;
                $selesai = $item->tanggal_selesai ? (is_string($item->tanggal_selesai) ? $item->tanggal_selesai : $item->tanggal_selesai->format('Y-m-d')) : ($item->durasi_hari ? Carbon::parse($mulai)->addDays($item->durasi_hari)->toDateString() : $mulai);
                return $dateStr >= $mulai && $dateStr <= $selesai;
            });
        }
        $calendarData = $calendarEvents;

        $femaleStudents = $this->dataHaidRepository->getFemaleMembers($instansiId);
        $femaleMembers = $femaleStudents;

        $historyPita = $this->dataHaidRepository->getPitaHistoryPaginated($instansiId, 10);
        $pitaLogs = $historyPita;

        $haidLogs = $this->dataHaidRepository->getHaidLogsPaginated($instansiId, 10);
        $totalHaidLogs = $this->dataHaidRepository->getTotalHaidLogs($instansiId);

        $pitaCounts = $this->dataHaidRepository->getPitaCounts($instansiId);
        $totalPitaAktif = $pitaCounts['aktif'];
        $totalPitaSelesai = $pitaCounts['selesai'];
        $totalPitaTerlambat = $pitaCounts['terlambat'];

        $totalMenungguVerif = $this->dataHaidRepository->countPendingVerification($instansiId);
        $avgDurasi = $this->dataHaidRepository->getAverageDuration($instansiId);

        $pitaStatusChartData = [
            'labels' => [
                PitaStatus::DIPINJAM->label(),
                PitaStatus::DIKEMBALIKAN->label(),
                PitaStatus::TERLAMBAT->label(),
                PitaStatus::MENUNGGU_VERIFIKASI->label(),
            ],
            'data' => [$totalPitaAktif, $totalPitaSelesai, $totalPitaTerlambat, $totalMenungguVerif],
            'colors' => ['#f59e0b', '#10b981', '#ef4444', '#6366f1']
        ];

        $durationDistribution = $this->dataHaidRepository->getDurationDistribution($instansiId);

        $haidDurationChartData = [
            'labels' => ['< 4 Hari', '4 - 7 Hari (Normal)', '> 7 Hari'],
            'data' => [
                $durationDistribution['< 4 Hari'] ?? 0,
                $durationDistribution['4 - 7 Hari (Normal)'] ?? 0,
                $durationDistribution['> 7 Hari'] ?? 0,
            ]
        ];

        return compact(
            'warnings', 'currentlyHaid', 'calendarData', 'calendarEvents',
            'daysInMonth', 'firstDayOfWeek', 'selectedMonth', 'selectedYear', 'monthName',
            'femaleStudents', 'femaleMembers', 'historyPita', 'pitaLogs', 'haidLogs',
            'totalHaidLogs', 'totalPitaAktif', 'totalPitaSelesai', 'totalPitaTerlambat',
            'totalMenungguVerif', 'avgDurasi', 'pitaStatusChartData', 'haidDurationChartData'
        );
    }

    public function verifyPita(int|string $id): bool
    {
        $pita = $this->dataHaidRepository->findPeminjamanOrFail($id);
        $updated = $this->dataHaidRepository->updatePeminjaman($pita, [
            'verified' => 1,
            'status' => PitaStatus::DIPINJAM->value,
        ]);

        AuditLogger::log(
            'verify_pita',
            'health',
            "Verifikasi peminjaman pita ID: {$id}",
            ['pita_id' => $id]
        );

        return $updated;
    }

    public function rejectPita(int|string $id, ?string $alasan): bool
    {
        $pita = $this->dataHaidRepository->findPeminjamanOrFail($id);
        $updated = $this->dataHaidRepository->updatePeminjaman($pita, [
            'verified' => 2,
            'status' => 'ditolak',
            'keterangan' => $alasan ?? 'Ditolak oleh admin/petugas kesehatan.',
        ]);

        AuditLogger::log(
            'reject_pita',
            'health',
            "Penolakan peminjaman pita ID: {$id} dengan alasan: {$alasan}",
            ['pita_id' => $id, 'alasan' => $alasan]
        );

        return $updated;
    }

    public function recordHaidManual(array $validated): DataHaid
    {
        $existing = DataHaid::where('id_user', $validated['id_user'])
            ->where('tanggal_mulai', $validated['tanggal_mulai'])
            ->first();

        if ($existing) {
            throw new Exception('Data haid untuk tanggal tersebut sudah tercatat.');
        }

        $estimasiSelesai = $validated['estimasi_selesai'] 
            ?? Carbon::parse($validated['tanggal_mulai'])->addDays(7)->toDateString();

        $status = $validated['status'] ?? HaidStatus::BERLANGSUNG->value;

        $haid = $this->dataHaidRepository->create([
            'id_user' => $validated['id_user'],
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'estimasi_selesai' => $estimasiSelesai,
            'keluhan' => $validated['keluhan'] ?? ($validated['catatan'] ?? null),
            'status' => $status,
        ]);

        AuditLogger::log(
            'record_haid',
            'health',
            "Pencatatan siklus menstruasi manual untuk user ID: {$validated['id_user']}",
            ['haid_id' => $haid->id]
        );

        return $haid;
    }

    public function recordDirectPeminjaman(array $validated): PeminjamanPita
    {
        $estimasi = isset($validated['estimasi_selesai_haid']) 
            ? $validated['estimasi_selesai_haid'] 
            : Carbon::parse($validated['tanggal_pinjam'])->addDays(7)->toDateString();

        return $this->dataHaidRepository->createPeminjaman([
            'id_user' => $validated['id_user'],
            'jumlah_pita' => 1,
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'estimasi_selesai_haid' => $estimasi,
            'status' => PitaStatus::DIPINJAM->value,
            'verified' => 1,
            'keterangan' => $validated['keterangan'] ?? 'Peminjaman langsung via Admin UKS',
        ]);
    }

    public function createPeminjamanFromApi(array $validated, User $actor): PeminjamanPita
    {
        return $this->dataHaidRepository->createPeminjaman([
            'id_user' => $validated['id_user'],
            'instansi_id' => $actor->instansi_id,
            'jumlah_pita' => 1,
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'estimasi_selesai_haid' => $validated['estimasi_selesai_haid'],
            'status' => PitaStatus::DIPINJAM->value,
            'verified' => 1,
            'keterangan' => $validated['keterangan'] ?? 'Peminjaman via Mobile App',
        ]);
    }

    public function updatePeminjamanFromApi(int|string $id, array $validated): PeminjamanPita
    {
        $pita = $this->dataHaidRepository->findPeminjamanOrFail($id);
        $this->dataHaidRepository->updatePeminjaman($pita, $validated);
        return $pita;
    }

    public function deletePeminjamanFromApi(int|string $id): bool
    {
        $pita = $this->dataHaidRepository->findPeminjamanOrFail($id);
        return $this->dataHaidRepository->deletePeminjaman($pita);
    }

    public function returnPita(int|string $id): bool
    {
        $pita = $this->dataHaidRepository->findPeminjamanOrFail($id);
        return $this->dataHaidRepository->updatePeminjaman($pita, [
            'status' => PitaStatus::DIKEMBALIKAN->value,
            'tanggal_kembali' => now()->toDateString(),
        ]);
    }

    public function updateSanksiDenda(int|string $id, string $statusDenda, ?string $catatanSanksi): bool
    {
        $pita = $this->dataHaidRepository->findPeminjamanOrFail($id);
        return $this->dataHaidRepository->updatePeminjaman($pita, [
            'status_denda' => $statusDenda,
            'catatan_sanksi' => $catatanSanksi,
        ]);
    }

    public function stopHaid(int|string $id): bool
    {
        $haid = DataHaid::findOrFail($id);
        $tglMulai = Carbon::parse($haid->tanggal_mulai);
        $tglSelesai = now();
        $durasi = max(1, $tglMulai->diffInDays($tglSelesai) + 1);

        return $haid->update([
            'tanggal_selesai' => $tglSelesai->toDateString(),
            'durasi_hari' => $durasi,
            'status' => HaidStatus::SELESAI->value,
        ]);
    }

    public function recordDirectCounseling(array $validated, User $consultant): CounselingSession
    {
        return CounselingSession::create([
            'instansi_id' => $consultant->instansi_id,
            'user_id' => $validated['user_id'],
            'consultant_id' => $consultant->id,
            'session_date' => $validated['session_date'],
            'topic' => $validated['topic'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'selesai',
            'is_confidential' => true,
        ]);
    }

    public function refreshOverduePita(string $instansiId): void
    {
        PeminjamanPita::whereHas('user', function ($q) use ($instansiId) {
            $q->where('instansi_id', $instansiId);
        })->where('status', PitaStatus::DIPINJAM->value)
          ->whereDate('estimasi_selesai_haid', '<', now()->toDateString())
          ->update(['status' => PitaStatus::TERLAMBAT->value]);
    }
}
