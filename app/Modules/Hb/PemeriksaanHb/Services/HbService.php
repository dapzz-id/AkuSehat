<?php

namespace App\Modules\Hb\PemeriksaanHb\Services;

use App\Models\Hb;
use App\Models\User;
use App\Modules\Hb\PemeriksaanHb\Enums\HbStatus;
use App\Enums\AuditCategory;
use App\Modules\Hb\PemeriksaanHb\Repositories\HbRepository;
use App\Services\AuditLogger;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use Exception;

class HbService
{
    public function __construct(
        protected HbRepository $hbRepository,
        protected HbCalculationService $calculationService
    ) {}

    public function getDashboardData(User $actor, array $filters): array
    {
        $instansiId = (string) $actor->instansi_id;

        $hb = $this->hbRepository->getPaginatedForDashboard($instansiId, $filters);
        $aggregates = $this->hbRepository->getStatusAggregates($instansiId, $filters);
        $divisionStats = $this->hbRepository->getDivisionAverages($instansiId);
        $years = $this->hbRepository->getDistinctYears($instansiId);
        $divisi = $this->hbRepository->getInstansiDivisions($instansiId);

        $totalHb = $aggregates['total'];
        $countNormal = $aggregates['count_normal'];
        $countAnemia = $aggregates['count_anemia'];
        $countTinggi = $aggregates['count_tinggi'];

        $hbChartData = [
            'labels' => [
                HbStatus::NORMAL->label(),
                HbStatus::ANEMIA->label(),
                HbStatus::TINGGI->label(),
            ],
            'data' => [$countNormal, $countAnemia, $countTinggi],
            'colors' => ['#10b981', '#f43f5e', '#6366f1']
        ];

        $divisionHbChartData = [
            'labels' => $divisionStats['labels'],
            'data' => $divisionStats['data'],
        ];

        return compact(
            'hb',
            'divisi',
            'years',
            'totalHb',
            'countNormal',
            'countAnemia',
            'countTinggi',
            'hbChartData',
            'divisionHbChartData'
        );
    }

    public function getFormData(User $actor): array
    {
        $instansiId = (string) $actor->instansi_id;
        $member = $this->hbRepository->getInstansiMembersForForm($instansiId);
        $divisi = $this->hbRepository->getInstansiDivisions($instansiId);

        return compact('member', 'divisi');
    }

    public function getEditFormData(int|string $id, User $actor): array
    {
        $instansiId = (string) $actor->instansi_id;
        $hb = $this->hbRepository->findOrFail($id);
        $member = $this->hbRepository->getInstansiMembersForForm($instansiId);
        $divisi = $this->hbRepository->getInstansiDivisions($instansiId);

        return compact('hb', 'member', 'divisi');
    }

    public function recordExamination(array $data, User $actor): Hb
    {
        $targetUser = User::findOrFail($data['id_user']);
        $calc = $this->calculationService->calculateStatus((float) $data['hb'], $targetUser->jk);

        $payload = [
            'id_user' => $data['id_user'],
            'id_divisi' => $targetUser->id_divisi,
            'tgl' => $data['tgl'] ?? now()->format('Y-m-d'),
            'hb' => $data['hb'],
            'status' => $calc['status'],
            'pesan' => $calc['pesan'],
        ];

        $hb = $this->hbRepository->create($payload);

        AuditLogger::log(
            'record_hb',
            AuditCategory::HEALTH,
            "Pencatatan pemeriksaan Hb untuk user ID: {$data['id_user']} (Hb: {$data['hb']} - {$calc['status']})",
            ['hb_id' => $hb->id_hb ?? $hb->id]
        );

        return $hb;
    }

    public function updateExamination(Hb $hb, array $data, User $actor): bool
    {
        $targetUser = User::findOrFail($data['id_user']);
        $calc = $this->calculationService->calculateStatus((float) $data['hb'], $targetUser->jk);

        $payload = [
            'id_user' => $data['id_user'],
            'id_divisi' => $targetUser->id_divisi,
            'tgl' => $data['tgl'] ?? $hb->tgl,
            'hb' => $data['hb'],
            'status' => $calc['status'],
            'pesan' => $calc['pesan'],
        ];

        $updated = $this->hbRepository->update($hb, $payload);

        AuditLogger::log(
            'update_hb',
            'health',
            "Pembaruan pemeriksaan Hb ID: {$hb->id_hb} untuk user ID: {$data['id_user']}",
            ['hb_id' => $hb->id_hb ?? $hb->id]
        );

        return $updated;
    }

    public function updateExaminationById(int|string $id, array $data, User $actor): bool
    {
        $hb = $this->hbRepository->findOrFail($id);
        return $this->updateExamination($hb, $data, $actor);
    }

    public function deleteExamination(int|string $id, User $actor): bool
    {
        $hb = $this->hbRepository->findOrFail($id);
        $deleted = $this->hbRepository->delete($hb);

        AuditLogger::log(
            'delete_hb',
            'health',
            "Penghapusan pemeriksaan Hb ID: {$id}",
            ['hb_id' => $id]
        );

        return $deleted;
    }

    public function massDeleteExaminations(array $ids, User $actor): int
    {
        $instansiId = (string) $actor->instansi_id;
        $count = $this->hbRepository->massDelete($instansiId, $ids);

        AuditLogger::log(
            'mass_delete_hb',
            'health',
            "Penghapusan massal {$count} pemeriksaan Hb oleh user ID: {$actor->id}",
            ['ids' => $ids, 'count' => $count]
        );

        return $count;
    }

    public function getMemberHbHistory(User $actor, int|string $targetUserId): array
    {
        $targetUser = User::find($targetUserId);
        if (!$targetUser) {
            return ['status' => false, 'code' => 404, 'message' => 'User tidak ditemukan.'];
        }

        if ($actor->isMember() && $actor->id != $targetUserId) {
            return ['status' => false, 'code' => 403, 'message' => 'Akses tidak diperbolehkan.'];
        }

        if (!$actor->isSuperAdmin() && $targetUser->instansi_id !== $actor->instansi_id) {
            return ['status' => false, 'code' => 403, 'message' => 'Akses lintas instansi ditolak.'];
        }

        $formatted = $this->hbRepository->getByUserGrouped($targetUserId);

        return [
            'status' => true,
            'code' => 200,
            'data' => $formatted
        ];
    }

    public function exportExcel(array $filters, User $actor): StreamedResponse
    {
        $instansiId = (string) $actor->instansi_id;
        $records = $this->hbRepository->getExportRecords($instansiId, $filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Hemoglobin');

        $headers = ['No', 'Nama Member', 'Nomor Induk', 'Jenis Kelamin', 'Divisi', 'Tanggal Pemeriksaan', 'HB (g/dL)', 'Status', 'Pesan'];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        $no = 1;

        foreach ($records as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->user->nama ?? '-');
            $sheet->setCellValue('C' . $row, $item->user->nomor_induk ?? '-');
            $sheet->setCellValue('D' . $row, $item->user->jk === 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('E' . $row, $item->user->divisi->divisi_name ?? '-');
            $sheet->setCellValue('F' . $row, $item->tgl ? $item->tgl->format('d/m/Y') : '-');
            $sheet->setCellValue('G' . $row, $item->hb);
            $sheet->setCellValue('H' . $row, $item->status);
            $sheet->setCellValue('I' . $row, $item->pesan);
            $row++;
            $no++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'data-hemoglobin-' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function exportPdf(array $filters, User $actor): Response
    {
        $instansiId = (string) $actor->instansi_id;
        $hb = $this->hbRepository->getExportRecords($instansiId, $filters);

        $namaInstansi = $actor->instansi->nama_instansi ?? 'Instansi Tidak Diketahui';
        $ikonPath = public_path('src/aku_sehat_icon.png');
        $raadeveloperz_cr = public_path('src/raadeveloperz_crc.png');

        $pdf = Pdf::loadView('admin.hb.export-pdf', compact('hb', 'namaInstansi', 'ikonPath', 'raadeveloperz_cr'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('data-hemoglobin-' . date('Y-m-d') . '.pdf');
    }
}
