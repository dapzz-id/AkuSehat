<?php

namespace App\Modules\Kesehatan\Pemeriksaan\Services;

use App\Models\Kesehatan;
use App\Models\User;
use App\Modules\Kesehatan\Pemeriksaan\Enums\BmiStatus;
use App\Modules\Kesehatan\Pemeriksaan\Enums\BloodPressureStatus;
use App\Enums\AuditCategory;
use App\Modules\Kesehatan\Pemeriksaan\Repositories\KesehatanRepository;
use App\Services\AuditLogger;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;
use Exception;

class KesehatanService
{
    public function __construct(
        protected KesehatanRepository $kesehatanRepository,
        protected KesehatanCalculationService $calculationService
    ) {}

    public function getDashboardData(User $actor, array $filters): array
    {
        $instansiId = (string) $actor->instansi_id;

        $kesehatan = $this->kesehatanRepository->getPaginatedForDashboard($instansiId, $filters);
        $aggregates = $this->kesehatanRepository->getDashboardAggregates($instansiId, $filters);
        $years = $this->kesehatanRepository->getDistinctYears($instansiId);
        $divisi = $this->kesehatanRepository->getInstansiDivisions($instansiId);

        $totalKesehatan = $aggregates['total'];

        $imtChartData = [
            'labels' => [
                BmiStatus::NORMAL->label(),
                BmiStatus::UNDERWEIGHT->label(),
                BmiStatus::OVERWEIGHT->label(),
                BmiStatus::OBESITAS->label(),
            ],
            'data' => [
                $aggregates['count_normal'],
                $aggregates['count_underweight'],
                $aggregates['count_overweight'],
                $aggregates['count_obesitas']
            ],
            'colors' => ['#10b981', '#f59e0b', '#f97316', '#ef4444']
        ];

        $darahChartData = [
            'labels' => [
                BloodPressureStatus::HIPOTENSI->label(),
                BloodPressureStatus::NORMAL->label(),
                BloodPressureStatus::ELEVASI->label(),
                'Hipertensi',
                BloodPressureStatus::KRISIS->label(),
            ],
            'data' => [
                $aggregates['count_hipotensi'],
                $aggregates['count_normal_darah'],
                $aggregates['count_elevasi'],
                $aggregates['count_hipertensi'],
                $aggregates['count_krisis']
            ],
            'colors' => ['#38bdf8', '#10b981', '#f59e0b', '#f97316', '#ef4444']
        ];

        return compact(
            'kesehatan',
            'divisi',
            'years',
            'totalKesehatan',
            'imtChartData',
            'darahChartData'
        );
    }

    public function getFormData(User $actor): array
    {
        $instansiId = (string) $actor->instansi_id;
        $member = $this->kesehatanRepository->getInstansiMembersForForm($instansiId);
        $divisi = $this->kesehatanRepository->getInstansiDivisions($instansiId);

        return compact('member', 'divisi');
    }

    public function getEditFormData(int|string $id, User $actor): array
    {
        $instansiId = (string) $actor->instansi_id;
        $kesehatan = $this->kesehatanRepository->findOrFail($id);
        $member = $this->kesehatanRepository->getInstansiMembersForForm($instansiId);
        $divisi = $this->kesehatanRepository->getInstansiDivisions($instansiId);

        return compact('kesehatan', 'member', 'divisi');
    }

    public function recordExamination(array $data, User $actor): Kesehatan
    {
        $imtData = $this->calculationService->calculateImt((float) $data['tb'], (float) $data['bb']);
        $darahData = $this->calculationService->determineBloodPressureStatus(
            isset($data['sistol']) && $data['sistol'] !== '' ? (float) $data['sistol'] : null,
            isset($data['diastol']) && $data['diastol'] !== '' ? (float) $data['diastol'] : null
        );

        $payload = [
            'id_user' => $data['id_user'],
            'tgl' => !empty($data['tgl']) ? $data['tgl'] : now()->format('Y-m-d'),
            'tb' => $data['tb'],
            'bb' => $data['bb'],
            'imt' => $imtData['imt'],
            'status' => $imtData['status'],
            'pesan_imt' => $imtData['pesan'],
            'sistol' => $data['sistol'] ?? null,
            'diastol' => $data['diastol'] ?? null,
            'status_darah' => $darahData['status'],
            'pesan_tkd' => $darahData['pesan'],
            'kondisi_telinga' => $data['kondisi_telinga'] ?? null,
            'kondisi_gigi' => $data['kondisi_gigi'] ?? null,
            'perilaku_beresiko' => $data['perilaku_beresiko'] ?? null,
            'gangguan_reproduksi' => $data['gangguan_reproduksi'] ?? null,
        ];

        $kesehatan = $this->kesehatanRepository->create($payload);

        AuditLogger::log(
            'record_kesehatan',
            AuditCategory::HEALTH,
            "Pencatatan pemeriksaan kesehatan untuk user ID: {$data['id_user']} (IMT: {$imtData['imt']} - {$imtData['status']})",
            ['kesehatan_id' => $kesehatan->id_kesehatan ?? $kesehatan->id]
        );

        return $kesehatan;
    }

    public function updateExamination(Kesehatan $kesehatan, array $data, User $actor): bool
    {
        $imtData = $this->calculationService->calculateImt((float) $data['tb'], (float) $data['bb']);
        $darahData = $this->calculationService->determineBloodPressureStatus(
            isset($data['sistol']) && $data['sistol'] !== '' ? (float) $data['sistol'] : null,
            isset($data['diastol']) && $data['diastol'] !== '' ? (float) $data['diastol'] : null
        );

        $payload = [
            'id_user' => $data['id_user'] ?? $kesehatan->id_user,
            'tgl' => !empty($data['tgl']) ? $data['tgl'] : $kesehatan->tgl,
            'tb' => $data['tb'],
            'bb' => $data['bb'],
            'imt' => $imtData['imt'],
            'status' => $imtData['status'],
            'pesan_imt' => $imtData['pesan'],
            'sistol' => $data['sistol'] ?? null,
            'diastol' => $data['diastol'] ?? null,
            'status_darah' => $darahData['status'],
            'pesan_tkd' => $darahData['pesan'],
            'kondisi_telinga' => $data['kondisi_telinga'] ?? null,
            'kondisi_gigi' => $data['kondisi_gigi'] ?? null,
            'perilaku_beresiko' => $data['perilaku_beresiko'] ?? null,
            'gangguan_reproduksi' => $data['gangguan_reproduksi'] ?? null,
        ];

        $updated = $this->kesehatanRepository->update($kesehatan, $payload);

        AuditLogger::log(
            'update_kesehatan',
            'health',
            "Pembaruan pemeriksaan kesehatan ID: {$kesehatan->id_kesehatan}",
            ['kesehatan_id' => $kesehatan->id_kesehatan ?? $kesehatan->id]
        );

        return $updated;
    }

    public function updateExaminationById(int|string $id, array $data, User $actor): bool
    {
        $kesehatan = $this->kesehatanRepository->findOrFail($id);
        return $this->updateExamination($kesehatan, $data, $actor);
    }

    public function deleteExamination(int|string $id, User $actor): bool
    {
        $kesehatan = $this->kesehatanRepository->findOrFail($id);
        $deleted = $this->kesehatanRepository->delete($kesehatan);

        AuditLogger::log(
            'delete_kesehatan',
            'health',
            "Penghapusan pemeriksaan fisik ID: {$id}",
            ['kesehatan_id' => $id]
        );

        return $deleted;
    }

    public function massDeleteExaminations(array $ids, User $actor): int
    {
        $instansiId = (string) $actor->instansi_id;
        $count = $this->kesehatanRepository->massDelete($instansiId, $ids);

        AuditLogger::log(
            'mass_delete_kesehatan',
            'health',
            "Penghapusan massal {$count} pemeriksaan kesehatan oleh user ID: {$actor->id}",
            ['ids' => $ids, 'count' => $count]
        );

        return $count;
    }

    public function getMemberKesehatanHistory(User $actor, int|string $targetUserId): array
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

        $formatted = $this->kesehatanRepository->getByUserGrouped($targetUserId);

        return [
            'status' => true,
            'code' => 200,
            'data' => $formatted
        ];
    }

    public function exportExcel(array $filters, User $actor): StreamedResponse
    {
        $records = $this->kesehatanRepository->getExportRecords((string) $actor->instansi_id, $filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Kesehatan');

        $headers = ['No', 'Nama Member', 'Nomor Induk', 'Divisi', 'Tanggal Pemeriksaan', 'Berat Badan (kg)', 'Tinggi Badan (cm)', 'IMT', 'Status BMI', 'Tekanan Darah Sistol', 'Tekanan Darah Diastol', 'Status Tekanan Darah', 'Kondisi Telinga', 'Kondisi Gigi', 'Perilaku Berisiko', 'Gangguan Reproduksi'];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        $no = 1;

        foreach ($records as $item) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $item->user->nama ?? '-');
            $sheet->setCellValue('C' . $row, $item->user->nomor_induk ?? '-');
            $sheet->setCellValue('D' . $row, $item->user->divisi->divisi_name ?? '-');
            $sheet->setCellValue('E' . $row, $item->tgl ? $item->tgl->format('d/m/Y') : '-');
            $sheet->setCellValue('F' . $row, $item->bb);
            $sheet->setCellValue('G' . $row, $item->tb);
            $sheet->setCellValue('H' . $row, $item->imt);
            $sheet->setCellValue('I' . $row, $item->status);
            $sheet->setCellValue('J' . $row, $item->sistol ?? '-');
            $sheet->setCellValue('K' . $row, $item->diastol ?? '-');
            $sheet->setCellValue('L' . $row, $item->status_darah ?? '-');
            $sheet->setCellValue('M' . $row, $item->kondisi_telinga ?? '-');
            $sheet->setCellValue('N' . $row, $item->kondisi_gigi ?? '-');
            $sheet->setCellValue('O' . $row, $item->perilaku_beresiko ?? '-');
            $sheet->setCellValue('P' . $row, $item->gangguan_reproduksi ?? '-');
            $row++;
            $no++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'data-kesehatan-' . date('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    public function exportPdf(array $filters, User $actor): Response
    {
        $kesehatan = $this->kesehatanRepository->getExportRecords((string) $actor->instansi_id, $filters);

        $namaInstansi = $actor->instansi->nama_instansi ?? 'Instansi Tidak Diketahui';
        $ikonPath = public_path('src/aku_sehat_icon.png');
        $raadeveloperz_cr = public_path('src/raadeveloperz_crc.png');

        $pdf = Pdf::loadView('admin.kesehatan.export-pdf', compact('kesehatan', 'namaInstansi', 'ikonPath', 'raadeveloperz_cr'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('data-kesehatan-' . date('Y-m-d') . '.pdf');
    }
}
