<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DataHaid;
use App\Models\Kesehatan;
use App\Models\Hb;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['kelas', 'kesehatan'])
            ->where('level', 'Member')
            ->whereNotNull('sekolah_id')
            ->where('sekolah_id', Auth::user()->sekolah_id)
            ->orderBy('nama');

        if ($request->has('kelas') && $request->kelas != '') {
            $query->where(function($q) use ($request) {
                $q->where('id_kelas', $request->kelas);
            });
        }

        $dataCount = (clone $query)->get();

        $member = $query->paginate(5)->withQueryString();

        return view('admin.member.index', compact('member', 'dataCount'));
    }

    public function create()
    {
        $kelas = Kelas::where('sekolah_id', Auth::user()->sekolah_id)->get();
        return view('admin.member.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|max:30|unique:users,nis',
            'nama' => 'required|string|max:75',
            'username' => 'required|string|max:20|unique:users,username',
            'password' => 'required|string|min:6',
            'id_kelas' => 'nullable|exists:kelas,id',
            'jk' => 'required|in:L,P',
        ]);

        $dataAdmin = User::where('id', Auth::id())->first();

        // Validasi akses admin
        if ($dataAdmin->sekolah_id == null && $dataAdmin->level == 'Admin Sekolah') {
            return back()->with('error', 'Anda tidak memiliki akses untuk menambah member. Silakan hubungi operator raadeveloperz.');
        } elseif ($dataAdmin->sekolah_id == null && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'Anda tidak memiliki akses untuk menambah member. Silakan hubungi operator sekolah.');
        }

        // Validasi license aktif
        if ($dataAdmin->license_key_id == null && $dataAdmin->level == 'Admin Sekolah') {
            return back()->with('error', 'Anda belum mengaktifkan license key. Silakan beli dan aktifkan license key terlebih dahulu.');
        } elseif ($dataAdmin->license_key_id == null && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'Admin Sekolah belum mengaktifkan license key. Silakan hubungi operator sekolah.');
        }

        if ($dataAdmin?->sekolah?->licenseKey?->isExpired() && $dataAdmin->level == 'Admin Sekolah') {
            return back()->with('error', 'License key akun Anda sudah tidak aktif. Silakan perpanjang license key untuk menambah member.');
        } elseif ($dataAdmin?->licenseKey?->isExpired() && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'License key akun Anda sudah tidak aktif. Silakan hubungi operator sekolah.');
        }

        if ($dataAdmin?->sekolah?->license_id_active == null){
            return back()->with('error', 'License key aktif pada sekolah belum ditemukan. Silakan hubungi operator sekolah.');
        }

        try {
            DB::transaction(function () use ($dataAdmin, $request) {
                // Lock baris license agar tidak diakses bersamaan
                $license = DB::table('license_keys')
                    ->where('id', $dataAdmin?->sekolah?->license_id_active)
                    ->lockForUpdate()
                    ->first();

                if (!$license) {
                    throw new \Exception('License key tidak ditemukan.');
                }

                $currentUserCount = User::where('sekolah_id', $dataAdmin?->sekolah_id)
                    ->where('license_key_id', $dataAdmin?->sekolah?->license_id_active)
                    ->count();

                $remainingQuota = $license->kuota_pengguna - $currentUserCount;

                // Jika kuota sudah habis, batalkan
                if ($remainingQuota <= 0) {
                    if ($dataAdmin->level == 'Admin Sekolah') {
                        throw new \Exception('Kuota pengguna sudah habis. Silakan beli dan aktifkan license key baru terlebih dahulu.');
                    } else {
                        throw new \Exception('Kuota pengguna Admin Sekolah sudah habis. Silakan hubungi operator sekolah.');
                    }
                }

                // Simpan user baru
                User::create([
                    'nis' => $request->nis,
                    'nama' => $request->nama,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'id_kelas' => $request->id_kelas,
                    'sekolah_id' => $dataAdmin->sekolah_id,
                    'license_key_id' => $dataAdmin->sekolah?->license_id_active,
                    'jk' => $request->jk,
                    'level' => 'Member',
                    'tgl' => now()->format('Y-m-d'),
                ]);

                // Update kuota (kurangi 1)
                DB::table('license_keys')
                    ->where('id', $license->id)
                    ->update(['kuota_pengguna' => $license->kuota_pengguna - 1]);
            });

            return redirect()->route('admin.member.index')
                ->with('success', 'Data member berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $member = User::findOrFail($id);
        $kelas = Kelas::where('sekolah_id', Auth::user()->sekolah_id)->get();
        return view('admin.member.edit', compact('member', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $member = User::findOrFail($id);
        $lisenceTerbaru = null;
        
        $request->validate([
            'nis' => 'required|string|max:30|unique:users,nis,' . $id,
            'nama' => 'required|string|max:75',
            'username' => 'required|string|max:20|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'id_kelas' => 'nullable|exists:kelas,id',
            'jk' => 'required|in:L,P',
        ]);

        $dataAdmin = User::where('id', Auth::id())->first();

        // Validasi akses admin
        if ($dataAdmin->sekolah_id == null && $dataAdmin->level == 'Admin Sekolah') {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah data member. Silakan hubungi operator raadeveloperz.');
        } elseif ($dataAdmin->sekolah_id == null && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah data member. Silakan hubungi operator sekolah.');
        }

        // Validasi license aktif
        if ($dataAdmin->license_key_id == null && $dataAdmin->level == 'Admin Sekolah') {
            return back()->with('error', 'Anda belum mengaktifkan license key. Silakan beli dan aktifkan license key terlebih dahulu.');
        } elseif ($dataAdmin->license_key_id == null && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'Admin Sekolah belum mengaktifkan license key. Silakan hubungi operator sekolah.');
        }

        if ($dataAdmin?->licenseKey?->isExpired() && $dataAdmin->level == 'Admin Sekolah') {
            return back()->with('error', 'License key akun Anda sudah tidak aktif. Silakan perpanjang license key untuk menambah member.');
        } elseif ($dataAdmin?->licenseKey?->isExpired() && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'License key akun Anda sudah tidak aktif. Silakan hubungi operator sekolah.');
        }

        if ($dataAdmin?->sekolah?->license_id_active == null){
            return back()->with('error', 'License key aktif pada sekolah belum diaktifkan. Silakan hubungi operator sekolah.');
        }

        if ($dataAdmin?->sekolah?->licenseKey?->isExpired()) {
            return back()->with('error', 'License key aktif pada sekolah sudah tidak aktif. Silakan hubungi operator sekolah.');
        }

        if ($member->sekolah_id !== $dataAdmin->sekolah_id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah data member dari sekolah lain.');
        }

        if ($member->license_key_id !== null){
            $isActiveLicense = $member->licenseKey?->isActive() ? true : false;
            if (!$isActiveLicense) {
                return back()->with('error', 'License key pada member ini sudah tidak aktif. Silakan hubungi operator sekolah.');
            }

            if ($dataAdmin?->sekolah->licenseKey?->isActive() ? true : false) {
                $lisenceTerbaru = $dataAdmin?->sekolah?->license_id_active;
            } else {
                return back()->with('error', 'License key aktif pada sekolah sudah tidak aktif. Silakan hubungi operator sekolah.');
            }
        }else{
            if ($dataAdmin?->sekolah?->licenseKey?->isActive() ? true : false) {
                $lisenceTerbaru = $dataAdmin?->sekolah?->license_id_active;
            } else {
                return back()->with('error', 'License key aktif pada sekolah sudah tidak aktif. Silakan hubungi operator sekolah.');
            }
        }

        try {
            DB::transaction(function () use ($dataAdmin, $request, $data, $member, $lisenceTerbaru) {
                // Lock baris license agar tidak diakses bersamaan
                $license = DB::table('license_keys')
                    ->where('id', $dataAdmin?->sekolah?->license_id_active)
                    ->lockForUpdate()
                    ->first();

                if (!$license) {
                    throw new \Exception('License key tidak ditemukan.');
                }

                $currentUserCount = User::where('sekolah_id', $dataAdmin?->sekolah_id)
                    ->where('license_key_id', $dataAdmin?->sekolah?->license_id_active)
                    ->count();

                $remainingQuota = $license->kuota_pengguna - $currentUserCount;

                // Jika kuota sudah habis, batalkan
                if ($remainingQuota <= 0) {
                    if ($dataAdmin->level == 'Admin Sekolah') {
                        throw new \Exception('Kuota pengguna sudah habis. Silakan beli dan aktifkan license key baru terlebih dahulu.');
                    } else {
                        throw new \Exception('Kuota pengguna Admin Sekolah sudah habis. Silakan hubungi operator sekolah.');
                    }
                }

                $bedaKeyLisence = $member->license_key_id !== $lisenceTerbaru;

                $data = [
                    'nis' => $request->nis,
                    'license_key_id' => $lisenceTerbaru ?? $member->license_key_id,
                    'nama' => $request->nama,
                    'username' => $request->username,
                    'id_kelas' => $request->id_kelas,
                    'jk' => $request->jk,
                ];

                if ($request->filled('password')) {
                    $data['password'] = Hash::make($request->password);
                }

                $member->update($data);

                // Update kuota (kurangi 1)
                if ($bedaKeyLisence) {
                    DB::table('license_keys')
                        ->where('id', $license->id)
                        ->update(['kuota_pengguna' => $license->kuota_pengguna - 1]);
                }
            });

            return redirect()->route('admin.member.index')
                ->with('success', 'Data member berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.member.index')
            ->with('success', 'Data member berhasil diupdate.');
    }

    public function destroy($id)
    {
        $member = User::findOrFail($id);

        $dataAdmin = User::where('id', Auth::id())->first();
        if ($dataAdmin->sekolah_id == null && $dataAdmin->level == 'Admin Sekolah') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data member. Silakan hubungi operator raadeveloperz.');
        } else if ($dataAdmin->sekolah_id == null && $dataAdmin->level != 'Admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data member. Silakan hubungi operator sekolah.');
        }

        $member->dataHaid()->delete();
        $member->kesehatan()->delete();
        $member->hb()->delete();

        $member->delete();

        return redirect()->route('admin.member.index')
            ->with('success', 'Data member berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        // Set headers
        $sheet->setCellValue('A1', 'NIS');
        $sheet->setCellValue('B1', 'Nama Lengkap');
        $sheet->setCellValue('C1', 'Username');
        $sheet->setCellValue('D1', 'Password');
        $sheet->setCellValue('E1', 'Kelas');
        $sheet->setCellValue('F1', 'Jenis Kelamin');

        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(15);

        // Add example data
        $sheet->setCellValue('A2', '001234');
        $sheet->setCellValue('B2', 'Ahmad Pratama');
        $sheet->setCellValue('C2', 'ahmad001');
        $sheet->setCellValue('D2', 'password123');
        $sheet->setCellValue('E2', 'X TKJ 1');
        $sheet->setCellValue('F2', 'L');

        $sheet->setCellValue('A3', '001235');
        $sheet->setCellValue('B3', 'Siti Nurhaliza');
        $sheet->setCellValue('C3', 'siti001');
        $sheet->setCellValue('D3', 'password123');
        $sheet->setCellValue('E3', 'X RPL 1');
        $sheet->setCellValue('F3', 'P');

        // Add notes
        $sheet->setCellValue('A5', 'Catatan:');
        $sheet->setCellValue('A6', '- NIS harus unik');
        $sheet->setCellValue('A7', '- Username harus unik');
        $sheet->setCellValue('A8', '- Jenis Kelamin: L (Laki-laki) atau P (Perempuan)');
        $sheet->setCellValue('A9', '- Kelas harus sesuai dengan nama kelas yang ada di sistem');
        $sheet->setCellValue('A10', '- Password minimal 6 karakter');

        $sheet->getStyle('A5:A10')->getFont()->setItalic(true)->setSize(9);

        // Create writer
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        $filename = 'template_member_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function importMember(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header
            array_shift($rows);

            $imported = 0;
            $errors = [];

            // Helper untuk normalisasi jenis kelamin
            $normalizeGender = function ($value) {
                $val = strtoupper(trim($value));
                switch ($val) {
                    case 'L':
                    case 'LAKI':
                    case 'LAKI-LAKI':
                    case 'LAKI LAKI':
                        return 'L';

                    case 'P':
                    case 'PEREMPUAN':
                    case 'WANITA':
                        return 'P';

                    default:
                        return $val; // biar tetap dicek validasi error
                }
            };

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 karena index 0 dan header

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                // Ambil data
                $nis = $row[0] ?? null;
                $nama = $row[1] ?? null;
                $username = $row[2] ?? null;
                $passwordInput = $row[3] ?? null; // bisa kosong
                $kelasName = $row[4] ?? null;
                $jk = $normalizeGender($row[5] ?? '');

                // Validation dasar
                if (empty($nis) || empty($nama) || empty($username) || empty($jk)) {
                    $errors[] = "Baris $rowNumber: Data tidak lengkap";
                    continue;
                }

                if (!in_array($jk, ['L', 'P'])) {
                    $errors[] = "Baris $rowNumber: Jenis kelamin harus L atau P (input: {$row[5]})";
                    continue;
                }

                // Validasi panjang password hanya kalau user baru & password diisi
                if (empty($passwordInput) && !User::where('username', $username)->exists()) {
                    // password default
                    $passwordInput = "12345678";
                }
                if (!empty($passwordInput) && strlen($passwordInput) < 6) {
                    $errors[] = "Baris $rowNumber: Password minimal 6 karakter";
                    continue;
                }

                // Cek NIS duplikat
                if (User::where('nis', $nis)->where('username', '!=', $username)->exists()) {
                    $errors[] = "Baris $rowNumber: NIS $nis sudah terdaftar untuk user lain";
                    continue;
                }

                // Cari kelas
                $kelas = null;
                if (!empty($kelasName)) {
                    $kelas = Kelas::where('kelas', $kelasName)->first();
                    if (!$kelas) {
                        $errors[] = "Baris $rowNumber: Kelas $kelasName tidak ditemukan";
                        continue;
                    }
                }

                try {
                    $user = User::with('dataHaid')->find($username);

                    if ($user) {
                        // Update user yang sudah ada
                        $updateData = [
                            'nis' => $nis,
                            'nama' => $nama,
                            'id_kelas' => $kelas ? $kelas->id : null,
                            'jk' => $jk,
                        ];

                        if (!empty($passwordInput)) {
                            // cek apakah password sama
                            if (!Hash::check($passwordInput, $user->password)) {
                                $updateData['password'] = Hash::make($passwordInput);
                            }
                        }
                        $user->update($updateData);

                        DataHaid::updateOrCreate(
                            [
                                'id_user' => $user->id,
                            ],
                            [
                                'catatan' => $user->dataHaid->catatan ?? null,
                                'tanggal_selesai' => $user->dataHaid->tanggal_selesai ?? null,
                                'tanggal_mulai' => $user->dataHaid->tanggal_mulai ?? null,
                                'status' => $user->dataHaid->status ?? 'selesai',
                            ]
                        );
                    } else {
                        // Buat user baru
                        $user = User::create([
                            'nis' => $nis,
                            'nama' => $nama,
                            'username' => $username,
                            'password' => Hash::make($passwordInput ?: "12345678"),
                            'id_kelas' => $kelas ? $kelas->id : null,
                            'jk' => $jk,
                            'level' => 'Member',
                            'tgl' => now()->format('Y-m-d'),
                        ]);

                        DataHaid::create(
                            [
                                'id_user' => $user->id,
                                'catatan' => null,
                                'tanggal_selesai' => null,
                                'tanggal_mulai' => null,
                                'status' => 'selesai',
                            ]
                        );
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris $rowNumber: Error - " . $e->getMessage();
                }
            }

            $message = "$imported data member berhasil diimport.";
            if (!empty($errors)) {
                $message .= " Terdapat " . count($errors) . " error.";
                session()->flash('import_errors', $errors);
            }

            return redirect()->route('admin.member.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.member.index')
                ->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    public function exportMember()
    {
        $member = User::with('kelas')
            ->where('level', 'Member')
            ->orderBy('nama')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];

        // Set headers
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'NIS');
        $sheet->setCellValue('C1', 'Nama Lengkap');
        $sheet->setCellValue('D1', 'Username');
        $sheet->setCellValue('E1', 'Kelas');
        $sheet->setCellValue('F1', 'Jurusan');
        $sheet->setCellValue('G1', 'Jenis Kelamin');
        $sheet->setCellValue('H1', 'Tanggal Daftar');

        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);

        // Fill data
        $row = 2;
        foreach ($member as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->nis);
            $sheet->setCellValue('C' . $row, $item->nama);
            $sheet->setCellValue('D' . $row, $item->username);
            $sheet->setCellValue('E' . $row, $item->kelas->kelas ?? '-');
            $sheet->setCellValue('F' . $row, $item->kelas->jurusan ?? '-');
            $sheet->setCellValue('G' . $row, $item->jk === 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('H' . $row, $item->tgl);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create writer
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        $filename = 'data_member_' . date('Y-m-d_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}