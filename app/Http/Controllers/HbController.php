<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Hb;
use App\Models\User;
use App\Models\Divisi;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;
use PDF;

class HbController extends Controller
{
    public function index(Request $request)
    {
        $query = Hb::with(['user', 'user.divisi'])
            ->whereHas('user', function($query) {
                $query->where('level', 'Member')->whereNotNull('instansi_id')->where('instansi_id', Auth::user()->instansi_id);
            });

        if (!empty($request->search)) {
            $search = $request->search;

            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('nama', 'like', "%$search%")
                    ->orWhere('nomor_induk', 'like', "%$search%")
                    ->orWhereHas('divisi', function($d) use ($search) {
                        $d->where('divisi_name', 'like', "%$search%");
                    });
                })
                ->orWhere('hb', 'like', "%$search%")
                ->orWhere('status', 'like', "%$search%");
            });
        }

        if (!empty($request->year)) {
            if ($request->year !== 'all') {
                $query->whereYear('tgl', $request->year);
            }
        }else{
            $query->whereYear('tgl', Carbon::now()->year);
        }

        $hb = $query->where('tgl', '!=', null)
                ->orderBy('tgl', 'desc')
                ->paginate(15)->withQueryString();

        $years = Hb::whereHas('user', function($query) {
                    $query->where('level', 'Member')
                        ->whereNotNull('instansi_id')
                        ->where('instansi_id', Auth::user()->instansi_id);
                })
                ->selectRaw('YEAR(tgl) as year')
                ->whereNotNull('tgl')
                ->groupBy('year')
                ->pluck('year')
                ->toArray();

        $currentYear = now()->year;
        if (!in_array($currentYear, $years)) {
            $years[] = $currentYear;
        }

        rsort($years);

        $divisi = Divisi::where('instansi_id', Auth::user()->instansi_id)->get();

        return view('admin.hb.index', compact('hb', 'divisi', 'years'));
    }

    public function create()
    {
        $member = User::with('divisi')
                    ->select('users.*')
                    ->leftJoin('divisi', 'divisi.id', '=', 'users.id_divisi')
                    ->where('users.level', 'Member')
                    ->whereNotNull('users.instansi_id')
                    ->whereNotNull('users.id_divisi')
                    ->where('users.instansi_id', Auth::user()->instansi_id)
                    ->orderBy('divisi.divisi_name', 'asc')
                    ->orderBy('users.nama', 'asc')
                    ->get();

        $divisi = Divisi::where('instansi_id', Auth::user()->instansi_id)->get();
        return view('admin.hb.create', compact('member', 'divisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'hb' => 'required|numeric',
        ],[
            'hb.numeric' => 'Kadar HB harus berupa angka.',
            'hb.required' => 'Kadar HB wajib diisi.',
            'hb.min' => 'Kadar HB minimal 0.',
            'hb.max' => 'Kadar HB maksimal 25.',
            'id_user.required' => 'Member wajib dipilih.',
            'id_user.exists' => 'Member tidak ditemukan.',
        ]);

        $user = User::find($request->id_user);
        $hb_value = floatval($request->hb);
        
        $status = 'Normal';
        $pesan = 'Kadar hemoglobin normal.';
        
        if ($user->jk === 'P') {
            if ($hb_value < 12) {
                $status = 'Anemia';
                $pesan = 'Kadar hemoglobin rendah (anemia). Disarankan untuk mengonsumsi makanan kaya zat besi.';
            } elseif ($hb_value > 15) {
                $status = 'Tinggi';
                $pesan = 'Kadar hemoglobin tinggi. Perlu pemeriksaan lebih lanjut.';
            }
        } else {
            if ($hb_value < 13) {
                $status = 'Anemia';
                $pesan = 'Kadar hemoglobin rendah (anemia). Disarankan untuk mengonsumsi makanan kaya zat besi.';
            } elseif ($hb_value > 17) {
                $status = 'Tinggi';
                $pesan = 'Kadar hemoglobin tinggi. Perlu pemeriksaan lebih lanjut.';
            }
        }

        Hb::create([
            'id_user' => $request->id_user,
            'id_divisi' => User::find($request->id_user)->id_divisi,
            'tgl' => now()->format('Y-m-d'),
            'hb' => $request->hb,
            'status' => $status,
            'pesan' => $pesan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.hb.index')
            ->with('success', 'Data HB berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $hb = Hb::findOrFail($id);
        $member = User::with('divisi')
                ->select('users.*')
                ->leftJoin('divisi', 'divisi.id', '=', 'users.id_divisi')
                ->where('users.level', 'Member')
                ->whereNotNull('users.instansi_id')
                ->whereNotNull('users.id_divisi')
                ->where('users.instansi_id', Auth::user()->instansi_id)
                ->orderBy('divisi.divisi_name', 'asc')
                ->orderBy('users.nama', 'asc')
                ->get();

        $divisi = Divisi::where('instansi_id', Auth::user()->instansi_id)->get();
        
        return view('admin.hb.edit', compact('hb', 'member', 'divisi'));
    }

    public function update(Request $request, $id)
    {
        $hb = Hb::findOrFail($id);
        
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'hb' => 'required|numeric',
        ]);

        $user = User::find($request->id_user);
        $hb_value = floatval($request->hb);
        
        $status = 'Normal';
        $pesan = 'Kadar hemoglobin normal.';
        
        if ($user->jk === 'P') {
            if ($hb_value < 12) {
                $status = 'Anemia';
                $pesan = 'Kadar hemoglobin rendah (anemia). Disarankan untuk mengonsumsi makanan kaya zat besi.';
            } elseif ($hb_value > 15) {
                $status = 'Tinggi';
                $pesan = 'Kadar hemoglobin tinggi. Perlu pemeriksaan lebih lanjut.';
            }
        } else {
            if ($hb_value < 13) {
                $status = 'Anemia';
                $pesan = 'Kadar hemoglobin rendah (anemia). Disarankan untuk mengonsumsi makanan kaya zat besi.';
            } elseif ($hb_value > 17) {
                $status = 'Tinggi';
                $pesan = 'Kadar hemoglobin tinggi. Perlu pemeriksaan lebih lanjut.';
            }
        }

        $hb->update([
            'id_user' => $request->id_user,
            'id_divisi' => $hb->id_divisi ?? User::find($request->id_user)->id_divisi,
            'tgl' => $hb->tgl,
            'hb' => $request->hb,
            'status' => $status,
            'pesan' => $pesan,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.hb.index')
            ->with('success', 'Data HB berhasil diupdate.');
    }

    public function destroy($id)
    {
        $hb = Hb::findOrFail($id);
        $hb->delete();

        return redirect()->route('admin.hb.index')
            ->with('success', 'Data HB berhasil dihapus.');
    }

    public function mass_destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:hb,id_hb',
        ]);

        $deletedCount = 0;

        try {
            foreach ($request->ids as $id) {
                $hb = Hb::find($id);
                if ($hb && $hb->user->instansi_id === Auth::user()->instansi_id) {
                    $hb->delete();
                    $deletedCount++;
                }
            }

            return redirect()->route('admin.hb.index')
                ->with('success', "$deletedCount data HB berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saat menghapus: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $filterType = $request->input('filter_type', 'all'); // Default 'all' jika kosong

            $query = Hb::with(['user', 'user.divisi'])
                ->select('hb.*')
                ->join('users', 'hb.id_user', '=', 'users.id')
                ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
                ->where('users.level', 'Member')
                ->whereNotNull('users.instansi_id')
                ->where('users.instansi_id', Auth::user()->instansi_id)
                ->where('hb.tgl', '!=', null)
                ->orderBy('divisi.divisi_name', 'asc')
                ->orderBy('users.nama', 'asc');

            // Filter divisi hanya jika relevan
            if (in_array($filterType, ['divisi', 'divisi_tanggal']) && $request->filled('divisi')) {
                $query->where('users.id_divisi', $request->divisi);
            }

            // Filter tanggal hanya jika relevan
            if (in_array($filterType, ['tanggal', 'divisi_tanggal'])) {
                if ($request->filled('from')) {
                    $query->whereDate('hb.tgl', '>=', $request->from);
                }
                if ($request->filled('to')) {
                    $query->whereDate('hb.tgl', '<=', $request->to);
                }
                
                if (!$request->filled('from') && !$request->filled('to')) {
                    $query->whereYear('hb.tgl', now()->year);
                }
            }

            // Untuk 'all' atau 'divisi' tanpa tanggal spesifik, tambah default tahun
            if (in_array($filterType, ['all', 'divisi'])) {
                $query->whereYear('hb.tgl', now()->year);
            }

            $hb = $query->orderBy('hb.tgl', 'desc')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set judul
            $sheet->setTitle('Data Hemoglobin');
            
            // Header
            $headers = [
                'No',
                'Nama Member',
                'Nomor Induk', 
                'Jenis Kelamin',
                'Divisi',
                'Tanggal Pemeriksaan',
                'HB (g/dL)',
                'Status',
                'Pesan'
            ];

            // Set header
            $sheet->fromArray($headers, null, 'A1');

            // Data rows
            $row = 2;
            $no = 1;
            
            foreach ($hb as $item) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item->user->nama ?? '-');
                $sheet->setCellValue('C' . $row, $item->user->nomor_induk ?? '-');
                $sheet->setCellValue('D' . $row, $item->user->jk === 'L' ? 'Laki-laki' : 'Perempuan');
                $sheet->setCellValue('E' . $row, $item->user->divisi->divisi_name ?? '-');
                $sheet->setCellValue('F' . $row, $item->tgl->format('d/m/Y'));
                $sheet->setCellValue('G' . $row, $item->hb);
                $sheet->setCellValue('H' . $row, $item->status);
                $sheet->setCellValue('I' . $row, $item->pesan);
                
                $row++;
                $no++;
            }

            // Styling
            $this->applyExcelStyles($sheet, count($hb));

            // Create writer and save to temporary file
            $writer = new Xlsx($spreadsheet);
            $filename = 'data-hemoglobin-' . date('Y-m-d') . '.xlsx';
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export Excel: ' . $e->getMessage());
        }
    }

    // public function exportExcel(Request $request)
    // {
    //     try {
    //         $query = Hb::with(['user', 'user.divisi'])
    //             ->whereHas('user', function($q) {
    //                 $q->where('level', 'Member')
    //                   ->whereNotNull('instansi_id')
    //                   ->where('instansi_id', Auth::user()->instansi_id);
    //             });

    //         if ($request->has('divisi') && $request->divisi) {
    //             $query->where('id_divisi', $request->divisi);
    //         }

    //         if ($request->has('from') && $request->from) {
    //             $query->whereDate('tgl', '>=', $request->from);
    //         }

    //         if ($request->has('to') && $request->to) {
    //             $query->whereDate('tgl', '<=', $request->to);
    //         }

    //         $hb = $query->orderBy('created_at', 'desc')->get();

    //         $spreadsheet = new Spreadsheet();
    //         $sheet = $spreadsheet->getActiveSheet();

    //         // Set judul
    //         $sheet->setTitle('Data Hemoglobin');
            
    //         // Header
    //         $headers = [
    //             'No',
    //             'Nama Member',
    //             'Nomor Induk', 
    //             'Jenis Kelamin',
    //             'Divisi',
    //             'Tanggal Pemeriksaan',
    //             'HB (g/dL)',
    //             'Status',
    //             'Pesan'
    //         ];

    //         // Set header
    //         $sheet->fromArray($headers, null, 'A1');

    //         // Data rows
    //         $row = 2;
    //         $no = 1;
            
    //         foreach ($hb as $item) {
    //             $sheet->setCellValue('A' . $row, $no);
    //             $sheet->setCellValue('B' . $row, $item->user->nama ?? '-');
    //             $sheet->setCellValue('C' . $row, $item->user->nomor_induk ?? '-');
    //             $sheet->setCellValue('D' . $row, $item->user->jk === 'L' ? 'Laki-laki' : 'Perempuan');
    //             $sheet->setCellValue('E' . $row, $item->user->divisi->divisi_name ?? '-');
    //             $sheet->setCellValue('F' . $row, $item->tgl->format('d/m/Y'));
    //             $sheet->setCellValue('G' . $row, $item->hb);
    //             $sheet->setCellValue('H' . $row, $item->status);
    //             $sheet->setCellValue('I' . $row, $item->pesan);
                
    //             $row++;
    //             $no++;
    //         }

    //         // Styling
    //         $this->applyExcelStyles($sheet, count($hb));

    //         // Create writer and save to temporary file
    //         $writer = new Xlsx($spreadsheet);
    //         $filename = 'data-hemoglobin-' . date('Y-m-d') . '.xlsx';
            
    //         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //         header('Content-Disposition: attachment;filename="' . $filename . '"');
    //         header('Cache-Control: max-age=0');
            
    //         $writer->save('php://output');
    //         exit;

    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat export Excel: ' . $e->getMessage());
    //     }
    // }

    private function applyExcelStyles($sheet, $dataCount)
    {
        // Style untuk header
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '1B5E20']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E9']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'C8E6C9']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];

        // Style untuk data
        $dataStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E0E0E0']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];

        // Apply header style
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Apply data style
        if ($dataCount > 0) {
            $sheet->getStyle('A2:I' . ($dataCount + 1))->applyFromArray($dataStyle);
        }

        // Auto size columns
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set alignment untuk kolom tertentu
        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D:D')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set wrap text untuk kolom pesan
        $sheet->getStyle('I:I')->getAlignment()->setWrapText(true);

        // Set row height untuk header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Set row height untuk data rows dengan pesan
        for ($i = 2; $i <= $dataCount + 1; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $filterType = $request->input('filter_type', 'all');

            $query = Hb::with(['user', 'user.divisi'])
                ->select('hb.*')
                ->join('users', 'hb.id_user', '=', 'users.id')
                ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
                ->where('users.level', 'Member')
                ->whereNotNull('users.instansi_id')
                ->where('users.instansi_id', Auth::user()->instansi_id)
                ->where('hb.tgl', '!=', null)
                ->orderBy('divisi.divisi_name', 'asc')
                ->orderBy('users.nama', 'asc');

            if (in_array($filterType, ['divisi', 'divisi_tanggal']) && $request->filled('divisi')) {
                $query->where('users.id_divisi', $request->divisi);
            }

            if (in_array($filterType, ['tanggal', 'divisi_tanggal'])) {
                if ($request->filled('from')) {
                    $query->whereDate('hb.tgl', '>=', $request->from);
                }
                if ($request->filled('to')) {
                    $query->whereDate('hb.tgl', '<=', $request->to);
                }
                
                if (!$request->filled('from') && !$request->filled('to')) {
                    $query->whereYear('hb.tgl', now()->year);
                }
            }

            if (in_array($filterType, ['all', 'divisi'])) {
                $query->whereYear('hb.tgl', now()->year);
            }

            $hb = $query->orderBy('hb.tgl', 'desc')->get();
            
            $namaInstansi = Auth::user()->instansi->nama_instansi ?? 'Instansi Tidak Diketahui';
            $ikonPath = public_path('src/aku_sehat_icon.png');
            $raadeveloperz_cr = public_path('src/raadeveloperz_crc.png');

            $pdf = PDF::loadView('admin.hb.export-pdf', compact('hb', 'namaInstansi', 'ikonPath', 'raadeveloperz_cr'));
            $pdf->setPaper('A4', 'portrait');
            
            
            return $pdf->download('data-hemoglobin-' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF: ' . $e->getMessage());
        }
    }

    // public function exportPdf(Request $request)
    // {
    //     try {
    //         $query = Hb::with(['user', 'user.divisi'])
    //             ->whereHas('user', function($q) {
    //                 $q->where('level', '!=', 'Admin Instansi')
    //                   ->whereNotNull('instansi_id')
    //                   ->where('instansi_id', Auth::user()->instansi_id);
    //             });

    //         if ($request->has('divisi') && $request->divisi) {
    //             $query->where('id_divisi', $request->divisi);
    //         }

    //         if ($request->has('from') && $request->from) {
    //             $query->whereDate('tgl', '>=', $request->from);
    //         }

    //         if ($request->has('to') && $request->to) {
    //             $query->whereDate('tgl', '<=', $request->to);
    //         }

    //         $hb = $query->orderBy('created_at', 'desc')->get();
            
    //         $namaInstansi = Auth::user()->instansi->nama_instansi ?? 'Instansi Tidak Diketahui';
    //         $ikonPath = public_path('src/aku_sehat_icon.png');
    //         $raadeveloperz_cr = public_path('src/raadeveloperz_crc.png');

    //         $pdf = PDF::loadView('admin.hb.export-pdf', compact('hb', 'namaInstansi', 'ikonPath', 'raadeveloperz_cr'));
    //         $pdf->setPaper('A4', 'portrait');
            
            
    //         return $pdf->download('data-hemoglobin-' . date('Y-m-d') . '.pdf');
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF: ' . $e->getMessage());
    //     }
    // }
}