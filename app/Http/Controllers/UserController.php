<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DataHaid;
use App\Models\Kesehatan;
use App\Models\Hb;
use App\Models\Divisi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['divisi', 'kesehatan'])
                ->select('users.*')
                ->leftJoin('divisi', 'divisi.id', '=', 'users.id_divisi')
                ->whereNotNull('users.instansi_id')
                ->where('users.instansi_id', Auth::user()->instansi_id)
                ->where('users.level', '!=', 'Admin Instansi')
                ->orderByRaw("FIELD(level, 'Admin', 'Health Consultant', 'Health Monitor', 'Member')")
                ->orderBy('divisi.divisi_name', 'asc')
                ->orderBy('users.nama', 'asc');

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('nomor_induk', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%')
                  ->orWhere('level', 'like', '%' . $request->search . '%')
                  ->orWhereHas('divisi', function($q2) use ($request) {
                      $q2->where('divisi_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->has('divisi') && $request->divisi != '') {
            $query->where(function($q) use ($request) {
                $q->where('id_divisi', $request->divisi);
            });
        }

        $dataCount = (clone $query)->get();

        $member = $query->paginate(10)->withQueryString();

        $divisi = Divisi::where('instansi_id', Auth::user()->instansi_id)->get();

        return view('admin.users.index', compact('member', 'dataCount', 'divisi'));
    }

    public function create()
    {
        $divisi = Divisi::where('instansi_id', Auth::user()->instansi_id)->get();
        return view('admin.users.create', compact('divisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_induk' => 'required|string|max:30|unique:users,nomor_induk',
            'nama' => 'required|string|max:75',
            'username' => 'required|string|max:20|unique:users,username',
            'password' => 'required|string|min:6',
            'id_divisi' => 'nullable|exists:divisi,id',
            'level' => 'nullable|in:Admin,Health Consultant,Health Monitor,Member',
            'jk' => 'required|in:L,P',
        ]);

        $dataAdmin = User::where('id', Auth::id())->first();

        // Validasi akses admin
        if ($dataAdmin->instansi_id == null && $dataAdmin->level == 'Admin Instansi') {
            return back()->with('error', 'Anda tidak memiliki akses untuk menambah users. Silakan hubungi operator raadeveloperz.');
        } elseif ($dataAdmin->instansi_id == null && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'Anda tidak memiliki akses untuk menambah users. Silakan hubungi operator instansi.');
        }

        // Validasi license aktif
        if ($dataAdmin->license_key_id == null && $dataAdmin->level == 'Admin Instansi') {
            return back()->with('error', 'Anda belum mengaktifkan license key. Silakan beli dan aktifkan license key terlebih dahulu.');
        } elseif ($dataAdmin->license_key_id == null && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'Admin Instansi belum mengaktifkan license key. Silakan hubungi operator instansi.');
        }

        if ($dataAdmin?->instansi?->licenseKey?->isExpired() && $dataAdmin->level == 'Admin Instansi') {
            return back()->with('error', 'License key akun Anda sudah tidak aktif. Silakan perpanjang license key untuk menambah users.');
        } elseif ($dataAdmin?->licenseKey?->isExpired() && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'License key akun Anda sudah tidak aktif. Silakan hubungi operator instansi.');
        }

        if ($dataAdmin?->instansi?->license_id_active == null){
            return back()->with('error', 'License key aktif pada instansi belum ditemukan. Silakan hubungi operator instansi.');
        }

        try {
            DB::transaction(function () use ($dataAdmin, $request) {
                // Lock baris license agar tidak diakses bersamaan
                $license = DB::table('license_keys')
                    ->where('id', $dataAdmin?->instansi?->license_id_active)
                    ->lockForUpdate()
                    ->first();

                if (!$license) {
                    throw new \Exception('License key tidak ditemukan.');
                }

                $currentUserCount = User::where('instansi_id', $dataAdmin?->instansi_id)
                    ->where('license_key_id', $dataAdmin?->instansi?->license_id_active)
                    ->count();

                $remainingQuota = $license->kuota_pengguna - $currentUserCount;

                // Jika kuota sudah habis, batalkan
                if ($remainingQuota <= 0) {
                    if ($dataAdmin->level == 'Admin Instansi') {
                        throw new \Exception('Kuota pengguna sudah habis. Silakan beli dan aktifkan license key baru terlebih dahulu.');
                    } else {
                        throw new \Exception('Kuota pengguna Admin Instansi sudah habis. Silakan hubungi operator instansi.');
                    }
                }

                // Simpan user baru
                User::create([
                    'nomor_induk' => $request->nomor_induk,
                    'nama' => $request->nama,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'id_divisi' => $request->id_divisi,
                    'instansi_id' => $dataAdmin->instansi_id,
                    'license_key_id' => $dataAdmin->instansi?->license_id_active,
                    'jk' => $request->jk,
                    'level' => $request->level ?? 'Member',
                    'tgl' => now()->format('Y-m-d'),
                ]);

                // Update kuota (kurangi 1)
                DB::table('license_keys')
                    ->where('id', $license->id)
                    ->update(['kuota_pengguna' => $license->kuota_pengguna - 1]);
            });

            return redirect()->route('admin.users.index')
                ->with('success', 'Data users berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $member = User::findOrFail($id);
        $divisi = Divisi::where('instansi_id', Auth::user()->instansi_id)->get();
        return view('admin.users.edit', compact('member', 'divisi'));
    }

    public function update(Request $request, $id)
    {
        $member = User::findOrFail($id);
        $lisenceTerbaru = null;
        
        $request->validate([
            'nomor_induk' => 'required|string|max:30|unique:users,nomor_induk,' . $id,
            'nama' => 'required|string|max:75',
            'username' => 'required|string|max:20|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'id_divisi' => 'nullable|exists:divisi,id',
            'level' => 'nullable|in:Admin,Health Consultant,Health Monitor,Member',
            'jk' => 'required|in:L,P',
        ]);

        $data = [
            'nomor_induk' => $request->nomor_induk,
            'license_key_id' => $lisenceTerbaru ?? $member->license_key_id,
            'nama' => $request->nama,
            'username' => $request->username,
            'level' => $request->level ?? $member->level,
            'id_divisi' => $request->id_divisi ?? $member->id_divisi ?? null,
            'jk' => $request->jk,
        ];

        if ($data['id_divisi'] === '') {
            $data['id_divisi'] = null;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }


        $dataAdmin = User::where('id', Auth::id())->first();

        // Validasi akses admin
        if ($dataAdmin->instansi_id == null && $dataAdmin->level == 'Admin Instansi') {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah data users. Silakan hubungi operator raadeveloperz.');
        } elseif ($dataAdmin->instansi_id == null && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah data users. Silakan hubungi operator instansi.');
        }

        // Validasi license aktif
        if ($dataAdmin->license_key_id == null && $dataAdmin->level == 'Admin Instansi') {
            return back()->with('error', 'Anda belum mengaktifkan license key. Silakan beli dan aktifkan license key terlebih dahulu.');
        } elseif ($dataAdmin->license_key_id == null && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'Admin Instansi belum mengaktifkan license key. Silakan hubungi operator instansi.');
        }

        if ($dataAdmin?->licenseKey?->isExpired() && $dataAdmin->level == 'Admin Instansi') {
            return back()->with('error', 'License key akun Anda sudah tidak aktif. Silakan perpanjang license key untuk menambah users.');
        } elseif ($dataAdmin?->licenseKey?->isExpired() && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'License key akun Anda sudah tidak aktif. Silakan hubungi operator instansi.');
        }

        if ($dataAdmin?->instansi?->license_id_active == null){
            return back()->with('error', 'License key aktif pada instansi belum diaktifkan. Silakan hubungi operator instansi.');
        }

        if ($dataAdmin?->instansi?->licenseKey?->isExpired()) {
            return back()->with('error', 'License key aktif pada instansi sudah tidak aktif. Silakan hubungi operator instansi.');
        }

        if ($member->instansi_id !== $dataAdmin->instansi_id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah data users dari instansi lain.');
        }

        if ($member->license_key_id !== null){
            $isActiveLicense = $member->licenseKey?->isActive() ? true : false;
            if (!$isActiveLicense) {
                return back()->with('error', 'License key pada users ini sudah tidak aktif. Silakan hubungi operator instansi.');
            }

            if ($dataAdmin?->instansi->licenseKey?->isActive() ? true : false) {
                $lisenceTerbaru = $dataAdmin?->instansi?->license_id_active;
            } else {
                return back()->with('error', 'License key aktif pada instansi sudah tidak aktif. Silakan hubungi operator instansi.');
            }
        }else{
            if ($dataAdmin?->instansi?->licenseKey?->isActive() ? true : false) {
                $lisenceTerbaru = $dataAdmin?->instansi?->license_id_active;
            } else {
                return back()->with('error', 'License key aktif pada instansi sudah tidak aktif. Silakan hubungi operator instansi.');
            }
        }

        try {
            DB::transaction(function () use ($dataAdmin, $request, $data, $member, $lisenceTerbaru) {
                // Lock baris license agar tidak diakses bersamaan
                $license = DB::table('license_keys')
                    ->where('id', $dataAdmin?->instansi?->license_id_active)
                    ->lockForUpdate()
                    ->first();

                if (!$license) {
                    throw new \Exception('License key tidak ditemukan.');
                }

                $currentUserCount = User::where('instansi_id', $dataAdmin?->instansi_id)
                    ->where('license_key_id', $dataAdmin?->instansi?->license_id_active)
                    ->count();

                $remainingQuota = $license->kuota_pengguna - $currentUserCount;

                // Jika kuota sudah habis, batalkan
                if ($remainingQuota <= 0) {
                    if ($dataAdmin->level == 'Admin Instansi') {
                        throw new \Exception('Kuota pengguna sudah habis. Silakan beli dan aktifkan license key baru terlebih dahulu.');
                    } else {
                        throw new \Exception('Kuota pengguna Admin Instansi sudah habis. Silakan hubungi operator instansi.');
                    }
                }

                $bedaKeyLisence = $member->license_key_id !== $lisenceTerbaru;
                $member->update($data);

                // Update kuota (kurangi 1)
                if ($bedaKeyLisence) {
                    DB::table('license_keys')
                        ->where('id', $license->id)
                        ->update(['kuota_pengguna' => $license->kuota_pengguna - 1]);
                }
            });

            return redirect()->route('admin.users.index')
                ->with('success', 'Data users berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Data users berhasil diupdate.');
    }

    public function destroy($id)
    {
        $member = User::findOrFail($id);

        $dataAdmin = User::where('id', Auth::id())->first();
        if ($dataAdmin->instansi_id == null && $dataAdmin->level == 'Admin Instansi') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data users. Silakan hubungi operator raadeveloperz.');
        } else if ($dataAdmin->instansi_id == null && $dataAdmin->level != 'Admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data users. Silakan hubungi operator instansi.');
        }

        $member->dataHaid()->delete();
        $member->kesehatan()->delete();
        $member->hb()->delete();

        $member->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Data users berhasil dihapus.');
    }

    public function mass_destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $dataAdmin = User::where('id', Auth::id())->first();
        if ($dataAdmin->instansi_id == null && $dataAdmin->level == 'Admin Instansi') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data users. Silakan hubungi operator raadeveloperz.');
        } else if ($dataAdmin->instansi_id == null && $dataAdmin->level != 'Admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data users. Silakan hubungi operator instansi.');
        }

        $deletedCount = 0;

        try {
            DB::transaction(function () use ($request, $dataAdmin, &$deletedCount) {
                foreach ($request->ids as $id) {
                    $member = User::find($id);
                    if ($member && $member->instansi_id === $dataAdmin->instansi_id) {
                        $member->dataHaid()->delete();
                        $member->kesehatan()->delete();
                        $member->hb()->delete();
                        $member->delete();
                        $deletedCount++;
                    }
                }
            });

            return redirect()->route('admin.users.index')
                ->with('success', "$deletedCount data users berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saat menghapus: ' . $e->getMessage());
        }
    }

    public function mass_update_divisi(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'new_divisi' => 'required|exists:divisi,id',
        ]);

        $dataAdmin = Auth::user();

        // Validasi akses (mirip mass_destroy)
        if ($dataAdmin->instansi_id == null && $dataAdmin->level == 'Admin Instansi') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah data users. Silakan hubungi operator raadeveloperz.');
        } else if ($dataAdmin->instansi_id == null && $dataAdmin->level != 'Admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengubah data users. Silakan hubungi operator instansi.');
        }

        // Validasi license (mirip update)
        // ... (tambahkan validasi license serupa jika diperlukan)

        $updatedCount = User::whereIn('id', $request->ids)
            ->where('instansi_id', $dataAdmin->instansi_id)
            ->update(['id_divisi' => $request->new_divisi]);

        return redirect()->route('admin.users.index')
            ->with('success', "$updatedCount data users berhasil diupdate divisinya.");
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
        $sheet->setCellValue('A1', 'Nomor Induk');
        $sheet->setCellValue('B1', 'Nama Lengkap');
        $sheet->setCellValue('C1', 'Username');
        $sheet->setCellValue('D1', 'Password');
        $sheet->setCellValue('E1', 'Divisi');
        $sheet->setCellValue('F1', 'Jenis Kelamin');
        $sheet->setCellValue('G1', 'Level');

        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(25);

        // Add example data
        $sheet->setCellValue('A2', '001234');
        $sheet->setCellValue('B2', 'Raihan Kusuma Putra');
        $sheet->setCellValue('C2', 'raihan001');
        $sheet->setCellValue('D2', 'password');
        $sheet->setCellValue('E2', 'XII RPL 2');
        $sheet->setCellValue('F2', 'L');
        $sheet->setCellValue('G2', 'Member');

        $sheet->setCellValue('A3', '001235');
        $sheet->setCellValue('B3', 'Kadavi Raditya Alvino');
        $sheet->setCellValue('C3', 'kadaviradityaa');
        $sheet->setCellValue('D3', 'password');
        $sheet->setCellValue('E3', 'XII RPL 2');
        $sheet->setCellValue('F3', 'L');
        $sheet->setCellValue('G3', 'Admin');
        
        $sheet->setCellValue('A4', '001236');
        $sheet->setCellValue('B4', 'Tiara Kusuma Dewi');
        $sheet->setCellValue('C4', 'tiarakusuma');
        $sheet->setCellValue('D4', 'password');
        $sheet->setCellValue('E4', 'Guru');
        $sheet->setCellValue('F4', 'P');
        $sheet->setCellValue('G4', 'Health Consultant');

        $sheet->setCellValue('A5', '001237');
        $sheet->setCellValue('B5', 'Marissa Dewi Sartika');
        $sheet->setCellValue('C5', 'marissadewi');
        $sheet->setCellValue('D5', 'password');
        $sheet->setCellValue('E5', 'Guru');
        $sheet->setCellValue('F5', 'P');
        $sheet->setCellValue('G5', 'Health Monitor');

        // Add notes
        $sheet->setCellValue('A9', 'Catatan:');
        $sheet->setCellValue('A10', '- Nomor Induk harus unik');
        $sheet->setCellValue('A11', '- Username harus unik');
        $sheet->setCellValue('A12', '- Jenis Kelamin: L (Laki-laki) atau P (Perempuan)');
        $sheet->setCellValue('A13', '- Divisi harus sesuai dengan nama divisi yang ada di sistem');
        $sheet->setCellValue('A14', '- Password minimal 6 karakter');
        $sheet->setCellValue('A15', '- Level harus salah satu dari: Admin, Health Consultant, Health Monitor, Member');

        $sheet->getStyle('A9:A15')->getFont()->setItalic(true)->setSize(9);

        // Create writer
        $writer = new Xlsx($spreadsheet);
        
        // Set headers for download
        $filename = 'template_users_' . date('Y-m-d') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        $dataAdmin = Auth::user();

        // Validasi akses dan license (mirip store)
        if ($dataAdmin->instansi_id == null && $dataAdmin->level == 'Admin Instansi') {
            return back()->with('error', 'Anda tidak memiliki akses untuk import users. Silakan hubungi operator raadeveloperz.');
        } elseif ($dataAdmin->instansi_id == null && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'Anda tidak memiliki akses untuk import users. Silakan hubungi operator instansi.');
        }

        if ($dataAdmin->license_key_id == null && $dataAdmin->level == 'Admin Instansi') {
            return back()->with('error', 'Anda belum mengaktifkan license key. Silakan beli dan aktifkan license key terlebih dahulu.');
        } elseif ($dataAdmin->license_key_id == null && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'Admin Instansi belum mengaktifkan license key. Silakan hubungi operator instansi.');
        }

        if ($dataAdmin?->instansi?->licenseKey?->isExpired() && $dataAdmin->level == 'Admin Instansi') {
            return back()->with('error', 'License key akun Anda sudah tidak aktif. Silakan perpanjang license key untuk import users.');
        } elseif ($dataAdmin?->licenseKey?->isExpired() && $dataAdmin->level != 'Admin') {
            return back()->with('error', 'License key akun Anda sudah tidak aktif. Silakan hubungi operator instansi.');
        }

        if ($dataAdmin?->instansi?->license_id_active == null){
            return back()->with('error', 'License key aktif pada instansi belum ditemukan. Silakan hubungi operator instansi.');
        }

        try {
            $file = $request->file('file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Remove header
            array_shift($rows);

            $imported = 0;
            $errors = [];
            $newUsersCount = 0;

            // First pass: validate and count new users
            $dataToProcess = [];
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;

                if (empty(array_filter($row))) continue;

                $nomorinduk = $row[0] ?? null;
                if (!is_numeric($nomorinduk)) continue;

                $nama = $row[1] ?? null;
                $username = $row[2] ?? null;
                $passwordInput = $row[3] ?? null;
                $divisiName = $row[4] ?? null;
                $jk = strtoupper(trim($row[5] ?? ''));
                $level = $row[6] ?? 'Member';

                // Normalize jk
                if ($jk == 'LAKI' || $jk == 'LAKI-LAKI' || $jk == 'LAKI LAKI') $jk = 'L';
                if ($jk == 'PEREMPUAN' || $jk == 'WANITA') $jk = 'P';

                if (empty($nomorinduk) || empty($nama) || empty($username) || empty($jk)) {
                    $errors[] = "Baris $rowNumber: Data tidak lengkap";
                    continue;
                }

                if (!in_array($jk, ['L', 'P'])) {
                    $errors[] = "Baris $rowNumber: Jenis kelamin harus L atau P (input: {$row[5]})";
                    continue;
                }

                if (!empty($passwordInput) && strlen($passwordInput) < 6) {
                    $errors[] = "Baris $rowNumber: Password minimal 6 karakter";
                    continue;
                }

                if (User::where('nomor_induk', $nomorinduk)->where('username', '!=', $username)->exists()) {
                    $errors[] = "Baris $rowNumber: Nomor Induk $nomorinduk sudah terdaftar untuk user lain";
                    continue;
                }

                $divisi = null;
                if (!empty($divisiName)) {
                    $divisi = Divisi::where('divisi_name', $divisiName)
                        ->where('instansi_id', $dataAdmin->instansi_id)
                        ->first();
                    if (!$divisi) {
                        $errors[] = "Baris $rowNumber: Divisi $divisiName tidak ditemukan";
                        continue;
                    }
                }

                $existingUser = User::where('username', $username)->first();

                if (!$existingUser) {
                    $newUsersCount++;
                    $password = $passwordInput ?: '12345678';
                } else {
                    $password = $passwordInput;
                }

                $dataToProcess[] = [
                    'rowNumber' => $rowNumber,
                    'nomor_induk' => $nomorinduk,
                    'nama' => $nama,
                    'username' => $username,
                    'password' => $password,
                    'divisi_id' => $divisi ? $divisi->id : null,
                    'jk' => $jk,
                    'level' => $level,
                    'is_new' => !$existingUser,
                    'existing' => $existingUser,
                ];
            }

            if (!empty($errors)) {
                session()->flash('import_errors', $errors);
                return redirect()->route('admin.users.index')->with('error', 'Terdapat error validasi data.');
            }

            // Check quota for new users
            $licenseId = $dataAdmin->instansi?->license_id_active;

            try {
                DB::transaction(function () use ($dataAdmin, $dataToProcess, $newUsersCount, $licenseId, &$imported, &$errors) {
                    $license = DB::table('license_keys')
                        ->where('id', $licenseId)
                        ->lockForUpdate()
                        ->first();

                    if (!$license) {
                        throw new \Exception('License key tidak ditemukan.');
                    }

                    $currentUserCount = User::where('instansi_id', $dataAdmin->instansi_id)
                        ->where('license_key_id', $licenseId)
                        ->count();

                    $remainingQuota = $license->kuota_pengguna - $currentUserCount;

                    if ($remainingQuota < $newUsersCount) {
                        throw new \Exception('Kuota pengguna tidak cukup untuk import ' . $newUsersCount . ' user baru. Sisa kuota: ' . $remainingQuota);
                    }

                    foreach ($dataToProcess as $data) {
                        try {
                            if ($data['is_new']) {
                                User::create([
                                    'nomor_induk' => $data['nomor_induk'],
                                    'nama' => $data['nama'],
                                    'username' => $data['username'],
                                    'password' => Hash::make($data['password']),
                                    'id_divisi' => $data['divisi_id'],
                                    'jk' => $data['jk'],
                                    'level' => $data['level'],
                                    'tgl' => now()->format('Y-m-d'),
                                    'instansi_id' => $dataAdmin->instansi_id,
                                    'license_key_id' => $licenseId,
                                ]);

                                // Decrement quota
                                DB::table('license_keys')
                                    ->where('id', $licenseId)
                                    ->decrement('kuota_pengguna');
                            } else {
                                $updateData = [
                                    'nomor_induk' => $data['nomor_induk'],
                                    'nama' => $data['nama'],
                                    'id_divisi' => $data['divisi_id'],
                                    'jk' => $data['jk'],
                                    'level' => $data['level'],
                                ];

                                if (!empty($data['password'])) {
                                    if (!Hash::check($data['password'], $data['existing']->password)) {
                                        $updateData['password'] = Hash::make($data['password']);
                                    }
                                }

                                // If license different, update and decrement if new license
                                if ($data['existing']->license_key_id !== $licenseId) {
                                    $updateData['license_key_id'] = $licenseId;
                                    DB::table('license_keys')
                                        ->where('id', $licenseId)
                                        ->decrement('kuota_pengguna');
                                }

                                $data['existing']->update($updateData);
                            }

                            $imported++;
                        } catch (\Exception $e) {
                            $errors[] = "Baris {$data['rowNumber']}: Error - " . $e->getMessage();
                        }
                    }
                });

                $message = "$imported data users berhasil diimport.";
                if (!empty($errors)) {
                    $message .= " Terdapat " . count($errors) . " error.";
                    session()->flash('import_errors', $errors);
                }

                return redirect()->route('admin.users.index')
                    ->with('success', $message);
            } catch (\Exception $e) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Error saat import: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Error saat import: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $query = User::with('divisi')
            ->where('level', '!=', 'Admin Instansi')
            ->where('instansi_id', Auth::user()->instansi_id);

        if ($request->filter_type === 'divisi' && $request->divisi) {
            $query->where('id_divisi', $request->divisi);
        } elseif ($request->filter_type === 'tanggal') {
            if ($request->from) $query->whereDate('tgl', '>=', $request->from);
            if ($request->to) $query->whereDate('tgl', '<=', $request->to);
        } elseif ($request->filter_type === 'divisi_tanggal') {
            if ($request->divisi) $query->where('id_divisi', $request->divisi);
            if ($request->from) $query->whereDate('tgl', '>=', $request->from);
            if ($request->to) $query->whereDate('tgl', '<=', $request->to);
        }

        $member = $query->orderByRaw("FIELD(level, 'Admin', 'Health Consultant', 'Health Monitor', 'Member')")
            ->orderBy('nama', 'asc')
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
        $sheet->setCellValue('B1', 'Nomor Induk');
        $sheet->setCellValue('C1', 'Nama Lengkap');
        $sheet->setCellValue('D1', 'Username');
        $sheet->setCellValue('E1', 'Divisi');
        $sheet->setCellValue('F1', 'Jenis Kelamin');
        $sheet->setCellValue('G1', 'Level');
        $sheet->setCellValue('H1', 'Tanggal Daftar');

        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(20);

        // Fill data
        $row = 2;
        foreach ($member as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->nomor_induk ?? '-');
            $sheet->setCellValue('C' . $row, $item->nama);
            $sheet->setCellValue('D' . $row, $item->username);
            $sheet->setCellValue('E' . $row, $item->divisi->divisi_name ?? '-');
            $sheet->setCellValue('F' . $row, $item->jk === 'L' ? 'Laki-laki' : 'Perempuan');
            $sheet->setCellValue('G' . $row, $item->level);
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
        $filename = 'data_users_' . date('Y-m-d_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}