<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Hb;
use App\Models\User;
use App\Models\Kelas;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PDF;

class HbController extends Controller
{
    public function index()
    {
        $hb = Hb::with(['user', 'kelas'])
            ->whereHas('user', function($query) {
                $query->where('level', 'Member')->whereNotNull('sekolah_id')->where('sekolah_id', Auth::user()->sekolah_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.hb.index', compact('hb'));
    }

    public function create()
    {
        $member = User::where('level', 'Member')->whereNotNull('sekolah_id')->where('sekolah_id', Auth::user()->sekolah_id)->get();
        $kelas = Kelas::where('sekolah_id', Auth::user()->sekolah_id)->get();
        return view('admin.hb.create', compact('member', 'kelas'));
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
            'id_kelas' => User::find($request->id_user)->id_kelas,
            'tgl' => now()->format('Y-m-d'),
            'hb' => $request->hb,
            'status' => $status,
            'pesan' => $pesan,
        ]);

        return redirect()->route('admin.hb.index')
            ->with('success', 'Data HB berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $hb = Hb::findOrFail($id);
        $member = User::where('level', 'Member')->whereNotNull('sekolah_id')->where('sekolah_id', Auth::user()->sekolah_id)->get();
        $kelas = Kelas::where('sekolah_id', Auth::user()->sekolah_id)->get();
        return view('admin.hb.edit', compact('hb', 'member', 'kelas'));
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
            'id_kelas' => $hb->id_kelas ?? User::find($request->id_user)->id_kelas,
            'tgl' => $hb->tgl,
            'hb' => $request->hb,
            'status' => $status,
            'pesan' => $pesan,
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

    public function exportExcel()
    {
        try {
            $hb = Hb::with(['user', 'kelas'])
                ->whereHas('user', function($query) {
                    $query->where('level', 'Member')
                          ->whereNotNull('sekolah_id')
                          ->where('sekolah_id', Auth::user()->sekolah_id);
                })
                ->orderBy('created_at', 'desc')
                ->get();

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
                $sheet->setCellValue('C' . $row, $item->user->nis ?? '-');
                $sheet->setCellValue('D' . $row, $item->user->jk === 'L' ? 'Laki-laki' : 'Perempuan');
                $sheet->setCellValue('E' . $row, $item->kelas->kelas ?? '-');
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

    public function exportPdf()
    {
        try {
            $hb = Hb::with(['user', 'kelas'])
                ->whereHas('user', function($query) {
                    $query->where('level', 'Member')
                          ->whereNotNull('sekolah_id')
                          ->where('sekolah_id', Auth::user()->sekolah_id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
            
            $namaSekolah = Auth::user()->sekolah->nama_sekolah ?? 'Sekolah Tidak Diketahui';
            $ikonPath = public_path('src/raadeveloperz_crc.png');

            $pdf = PDF::loadView('admin.hb.export-pdf', compact('hb', 'namaSekolah', 'ikonPath'));
            $pdf->setPaper('A4', 'landscape');
            
            
            return $pdf->download('data-hemoglobin-' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF: ' . $e->getMessage());
        }
    }
}