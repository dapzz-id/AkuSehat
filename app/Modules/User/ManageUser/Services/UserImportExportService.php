<?php

namespace App\Modules\User\ManageUser\Services;

use App\Models\User;
use App\Models\Divisi;
use App\Models\Instansi;
use App\Modules\User\Enums\UserGender;
use App\Modules\User\Enums\UserRole;
use App\Modules\User\Enums\UserStatus;
use App\Modules\User\ManageUser\Repositories\UserRepository;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Exception;

class UserImportExportService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function importFromExcel(UploadedFile $file, User $actor): array
    {
        $instansi = $actor->sekolah ?: Instansi::find($actor->instansi_id);
        $license = $instansi ? $instansi->getActiveLicense() : null;

        if (!$license) {
            throw new Exception('Instansi Anda belum memiliki license key aktif.');
        }

        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Buang header
        array_shift($rows);

        // Keamanan: Batasi maksimum 1,000 baris per file import untuk cegah Denial of Service (DoS)
        if (count($rows) > 1000) {
            throw new Exception('Maksimal baris import adalah 1.000 baris per file untuk menjaga stabilitas server.');
        }

        $imported = 0;
        $errors = [];
        $dataToProcess = [];

        $existingDivisions = Divisi::where('instansi_id', $actor->instansi_id)
            ->get()
            ->keyBy(fn($d) => strtolower(trim($d->divisi_name)));

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            if (empty(array_filter($row))) continue;

            $nomorInduk = trim((string) ($row[0] ?? ''));
            $nama = trim((string) ($row[1] ?? ''));
            $username = trim((string) ($row[2] ?? ''));
            $passwordInput = trim((string) ($row[3] ?? ''));
            $divisiName = trim((string) ($row[4] ?? ''));
            $jk = strtoupper(trim((string) ($row[5] ?? '')));
            $level = trim((string) ($row[6] ?? UserRole::MEMBER->value));

            // Keamanan: Validasi ketat peran/level impor agar tidak terjadi privilege escalation (misal menjadi SuperAdmin)
            $allowedImportRoles = array_merge(UserRole::values(), ['Siswa', 'Guru BK', 'Guru Olahraga', 'Admin PMR']);
            if (!in_array($level, $allowedImportRoles, true) || $level === UserRole::SUPERADMIN->value) {
                $level = UserRole::MEMBER->value;
            }

            if ($jk === 'LAKI-LAKI' || $jk === 'LAKI' || $jk === 'LAKI LAKI') $jk = UserGender::LAKI_LAKI->value;
            if ($jk === 'PEREMPUAN' || $jk === 'WANITA') $jk = UserGender::PEREMPUAN->value;

            if (empty($nomorInduk) || empty($nama) || empty($username) || empty($jk)) {
                $errors[] = "Baris $rowNumber: Data wajib tidak lengkap.";
                continue;
            }

            if (!in_array($jk, UserGender::values(), true)) {
                $errors[] = "Baris $rowNumber: Jenis kelamin harus L atau P.";
                continue;
            }

            $divisi = !empty($divisiName) ? ($existingDivisions[strtolower($divisiName)] ?? null) : null;

            if (!$divisi && !empty($divisiName)) {
                $errors[] = "Baris $rowNumber: Divisi '{$divisiName}' tidak ditemukan.";
                continue;
            }

            $dataToProcess[] = [
                'nomor_induk' => $nomorInduk,
                'nama' => $nama,
                'username' => $username,
                'password' => !empty($passwordInput) ? Hash::make($passwordInput) : Hash::make('12345678'),
                'id_divisi' => $divisi?->id,
                'instansi_id' => $actor->instansi_id,
                'jk' => $jk,
                'level' => $level,
                'tgl' => now()->format('Y-m-d'),
                'status' => UserStatus::AKTIF->value,
            ];
        }

        if (empty($dataToProcess) && !empty($errors)) {
            return ['imported' => 0, 'errors' => $errors];
        }

        DB::transaction(function () use ($dataToProcess, &$imported) {
            foreach ($dataToProcess as $item) {
                User::create($item);
                $imported++;
            }
        });

        AuditLogger::userAction(
            'user_import_excel',
            "Admin berhasil mengimpor {$imported} pengguna via Excel",
            ['imported_count' => $imported]
        );

        return ['imported' => $imported, 'errors' => $errors];
    }

    /**
     * Ekspor daftar pengguna instansi ke file Excel (.xlsx).
     */
    public function exportToExcel(User $actor)
    {
        $users = User::with('divisi')
            ->where('instansi_id', $actor->instansi_id)
            ->orderBy('nama', 'asc')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pengguna');

        $headers = ['No', 'Nomor Induk', 'Nama Lengkap', 'Username', 'Divisi / Unit', 'Jenis Kelamin', 'Peran (Role)', 'Status'];
        $sheet->fromArray($headers, null, 'A1');

        $rowNum = 2;
        foreach ($users as $index => $u) {
            $sheet->fromArray([
                $index + 1,
                $u->nomor_induk ?? '-',
                $u->nama,
                $u->username,
                $u->divisi?->divisi_name ?? 'Tanpa Divisi',
                $u->jk === UserGender::LAKI_LAKI->value ? UserGender::LAKI_LAKI->label() : UserGender::PEREMPUAN->label(),
                $u->level,
                $u->status ?? UserStatus::AKTIF->value,
            ], null, "A{$rowNum}");
            $rowNum++;
        }

        $instansiSlug = $actor->instansi?->nama_instansi 
            ? \Illuminate\Support\Str::slug($actor->instansi->nama_instansi) 
            : 'instansi';
        $fileName = "data_pengguna_{$instansiSlug}_" . date('Ymd_His') . '.xlsx';

        AuditLogger::userAction(
            'user_export_excel',
            "Admin mengekspor " . $users->count() . " data pengguna ke Excel",
            ['exported_count' => $users->count()]
        );

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
