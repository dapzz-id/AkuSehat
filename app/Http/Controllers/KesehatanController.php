<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kesehatan;
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

class KesehatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Kesehatan::with(['user', 'user.divisi'])
            ->whereHas('user', function($query) {
                $query->where('level', 'Member')->whereNotNull('instansi_id')->where('instansi_id', Auth::user()->instansi_id);
            });

        if (!empty($request->search)) {
            $search = $request->search;

            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nomor_induk', 'like', '%' . $search . '%')
                    ->orWhereHas('divisi', function($d) use ($search) {
                        $d->where('divisi_name', 'like', '%' . $search . '%');
                    });
                })->orWhere('status', 'like', '%' . $search . '%')
                ->orWhere('status_darah', 'like', '%' . $search . '%');
            });
        }

        if (!empty($request->year)) {
            if ($request->year !== 'all') {
                $query->whereYear('tgl', $request->year);
            }
        }else{
            $query->whereYear('tgl', Carbon::now()->year);
        }

        $kesehatan = $query->where('tgl', '!=', null)
                    ->orderBy('tgl', 'desc')
                    ->paginate(15)
                    ->withQueryString();

        $years = Kesehatan::whereHas('user', function($query) {
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

        return view('admin.kesehatan.index', compact('kesehatan', 'divisi', 'years'));
    }

    public function exportExcel(Request $request)
    {
        try {
            $filterType = $request->input('filter_type', 'all');

            $query = Kesehatan::with(['user', 'user.divisi'])
                ->select('kesehatan.*')
                ->join('users', 'kesehatan.id_user', '=', 'users.id')
                ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
                ->where('users.level', 'Member')
                ->whereNotNull('users.instansi_id')
                ->where('users.instansi_id', Auth::user()->instansi_id)
                ->where('kesehatan.tgl', '!=', null)
                ->orderBy('divisi.divisi_name', 'asc')
                ->orderBy('users.nama', 'asc');

            if (in_array($filterType, ['divisi', 'divisi_tanggal']) && $request->filled('divisi')) {
                $query->where('users.id_divisi', $request->divisi);
            }

            if (in_array($filterType, ['tanggal', 'divisi_tanggal'])) {
                if ($request->filled('from')) {
                    $query->whereDate('kesehatan.tgl', '>=', $request->from);
                }
                if ($request->filled('to')) {
                    $query->whereDate('kesehatan.tgl', '<=', $request->to);
                }
                
                if (!$request->filled('from') && !$request->filled('to')) {
                    $query->whereYear('kesehatan.tgl', now()->year);
                }
            }

            if (in_array($filterType, ['all', 'divisi'])) {
                $query->whereYear('kesehatan.tgl', now()->year);
            }

            $kesehatan = $query->orderBy('kesehatan.tgl', 'desc')->get();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Data Kesehatan');
            
            $headers = [
                'No',
                'Nama Member',
                'Nomor Induk',
                'Divisi', 
                'Tanggal Pemeriksaan',
                'Berat Badan (kg)',
                'Tinggi Badan (cm)', 
                'IMT',
                'Status BMI',
                'Tekanan Darah Sistol',
                'Tekanan Darah Diastol',
                'Status Tekanan Darah',
                'Kondisi Telinga',
                'Kondisi Gigi',
                'Perilaku Berisiko',
                'Gangguan Reproduksi'
            ];

            $sheet->fromArray($headers, null, 'A1');

            $row = 2;
            $no = 1;
            
            foreach ($kesehatan as $item) {
                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $item->user->nama ?? '-');
                $sheet->setCellValue('C' . $row, $item->user->nomor_induk ?? '-');
                $sheet->setCellValue('D' . $row, $item->user->divisi->divisi_name ?? '-');
                $sheet->setCellValue('E' . $row, $item->tgl->format('d/m/Y'));
                $sheet->setCellValue('F' . $row, $item->bb);
                $sheet->setCellValue('G' . $row, $item->tb);
                $sheet->setCellValue('H' . $row, $item->imt);
                $sheet->setCellValue('I' . $row, $item->status);
                $sheet->setCellValue('J' . $row, $item->sistol);
                $sheet->setCellValue('K' . $row, $item->diastol);
                $sheet->setCellValue('L' . $row, $item->status_darah);
                $sheet->setCellValue('M' . $row, $item->kondisi_telinga ?? '-');
                $sheet->setCellValue('N' . $row, $item->kondisi_gigi ?? '-');
                $sheet->setCellValue('O' . $row, $item->perilaku_beresiko ?? '-');
                $sheet->setCellValue('P' . $row, $item->gangguan_reproduksi ?? '-');
                
                $row++;
                $no++;
            }

            $this->applyExcelStyles($sheet, count($kesehatan));

            $writer = new Xlsx($spreadsheet);
            $filename = 'data-kesehatan-' . date('Y-m-d') . '.xlsx';
            
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
    //         $query = Kesehatan::with(['user', 'user.divisi'])
    //             ->whereHas('user', function($q) {
    //                 $q->where('level', 'Member')
    //                   ->whereNotNull('instansi_id')
    //                   ->where('instansi_id', Auth::user()->instansi_id);
    //             });

    //         if ($request->has('divisi') && $request->divisi) {
    //             $query->whereHas('user', function($q) use ($request) {
    //                 $q->where('id_divisi', $request->divisi);
    //             });
    //         }

    //         if ($request->has('from') && $request->from) {
    //             $query->whereDate('tgl', '>=', $request->from);
    //         }

    //         if ($request->has('to') && $request->to) {
    //             $query->whereDate('tgl', '<=', $request->to);
    //         }

    //         $kesehatan = $query->orderBy('created_at', 'desc')->get();

    //         $spreadsheet = new Spreadsheet();
    //         $sheet = $spreadsheet->getActiveSheet();
    //         $sheet->setTitle('Data Kesehatan');
            
    //         $headers = [
    //             'No',
    //             'Nama Member',
    //             'Nomor Induk',
    //             'Divisi', 
    //             'Tanggal Pemeriksaan',
    //             'Berat Badan (kg)',
    //             'Tinggi Badan (cm)', 
    //             'IMT',
    //             'Status Gizi',
    //             'Tekanan Darah Sistol',
    //             'Tekanan Darah Diastol',
    //             'Status Tekanan Darah',
    //             'Kondisi Telinga',
    //             'Kondisi Gigi',
    //             'Perilaku Berisiko',
    //             'Gangguan Reproduksi'
    //         ];

    //         $sheet->fromArray($headers, null, 'A1');

    //         $row = 2;
    //         $no = 1;
            
    //         foreach ($kesehatan as $item) {
    //             $sheet->setCellValue('A' . $row, $no);
    //             $sheet->setCellValue('B' . $row, $item->user->nama ?? '-');
    //             $sheet->setCellValue('C' . $row, $item->user->nomor_induk ?? '-');
    //             $sheet->setCellValue('D' . $row, $item->user->divisi->divisi_name ?? '-');
    //             $sheet->setCellValue('E' . $row, $item->tgl->format('d/m/Y'));
    //             $sheet->setCellValue('F' . $row, $item->bb);
    //             $sheet->setCellValue('G' . $row, $item->tb);
    //             $sheet->setCellValue('H' . $row, $item->imt);
    //             $sheet->setCellValue('I' . $row, $item->status);
    //             $sheet->setCellValue('J' . $row, $item->sistol);
    //             $sheet->setCellValue('K' . $row, $item->diastol);
    //             $sheet->setCellValue('L' . $row, $item->status_darah);
    //             $sheet->setCellValue('M' . $row, $item->kondisi_telinga ?? '-');
    //             $sheet->setCellValue('N' . $row, $item->kondisi_gigi ?? '-');
    //             $sheet->setCellValue('O' . $row, $item->perilaku_beresiko ?? '-');
    //             $sheet->setCellValue('P' . $row, $item->gangguan_reproduksi ?? '-');
                
    //             $row++;
    //             $no++;
    //         }

    //         $this->applyExcelStyles($sheet, count($kesehatan));

    //         $writer = new Xlsx($spreadsheet);
    //         $filename = 'data-kesehatan-' . date('Y-m-d') . '.xlsx';
            
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

        $sheet->getStyle('A1:P1')->applyFromArray($headerStyle);

        if ($dataCount > 0) {
            $sheet->getStyle('A2:P' . ($dataCount + 1))->applyFromArray($dataStyle);
        }

        // Auto size columns
        foreach (range('A', 'P') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $sheet->getStyle('A:A')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F:F')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G:G')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H:H')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J:J')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K:K')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('M:P')->getAlignment()->setWrapText(true);
        $sheet->getRowDimension(1)->setRowHeight(25);
    }

    public function exportPdf(Request $request)
    {
        $filterType = $request->input('filter_type', 'all');

        $query = Kesehatan::with(['user', 'user.divisi'])
            ->select('kesehatan.*')
            ->join('users', 'kesehatan.id_user', '=', 'users.id')
            ->join('divisi', 'users.id_divisi', '=', 'divisi.id')
            ->where('users.level', 'Member')
            ->whereNotNull('users.instansi_id')
            ->where('users.instansi_id', Auth::user()->instansi_id)
            ->where('kesehatan.tgl', '!=', null)
            ->orderBy('divisi.divisi_name', 'asc')
            ->orderBy('users.nama', 'asc');

        if (in_array($filterType, ['divisi', 'divisi_tanggal']) && $request->filled('divisi')) {
            $query->where('users.id_divisi', $request->divisi);
        }

        if (in_array($filterType, ['tanggal', 'divisi_tanggal'])) {
            if ($request->filled('from')) {
                $query->whereDate('kesehatan.tgl', '>=', $request->from);
            }
            if ($request->filled('to')) {
                $query->whereDate('kesehatan.tgl', '<=', $request->to);
            }
            
            if (!$request->filled('from') && !$request->filled('to')) {
                $query->whereYear('kesehatan.tgl', now()->year);
            }
        }

        if (in_array($filterType, ['all', 'divisi'])) {
            $query->whereYear('kesehatan.tgl', now()->year);
        }

        $kesehatan = $query->orderBy('kesehatan.tgl', 'desc')->get();

        $namaInstansi = Auth::user()->instansi->nama_instansi ?? 'Instansi Tidak Diketahui';
        $ikonPath = public_path('src/aku_sehat_icon.png');
        $raadeveloperz_cr = public_path('src/raadeveloperz_crc.png');

        $pdf = PDF::loadView('admin.kesehatan.export-pdf', compact('kesehatan', 'namaInstansi', 'ikonPath', 'raadeveloperz_cr'));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('data-kesehatan-' . date('Y-m-d') . '.pdf');
    }

    // public function exportPdf(Request $request)
    // {
    //     $filterType = $request->input('filter_type', 'all'); // Default 'all' jika kosong

    //     $query = Kesehatan::with(['user', 'user.divisi'])
    //         ->whereHas('user', function($q) {
    //             $q->where('level', 'Member')
    //             ->whereNotNull('instansi_id')
    //             ->where('instansi_id', Auth::user()->instansi_id)
    //             ->whereHas('divisi', function($q2) {
    //                 $q2->orderBy('divisi_name', 'asc');
    //             })
    //             ->orderBy('nama', 'asc');
    //         })
    //         ->where('tgl', '!=', null);

    //     // Filter divisi hanya jika relevan
    //     if (in_array($filterType, ['divisi', 'divisi_tanggal']) && $request->filled('divisi')) {
    //         $query->whereHas('user', function($q) use ($request) {
    //             $q->where('id_divisi', $request->divisi);
    //         });
    //     }

    //     // Filter tanggal hanya jika relevan
    //     if (in_array($filterType, ['tanggal', 'divisi_tanggal'])) {
    //         if ($request->filled('from')) {
    //             $query->whereDate('tgl', '>=', $request->from);
    //         }
    //         if ($request->filled('to')) {
    //             $query->whereDate('tgl', '<=', $request->to);
    //         }
            
    //         if (!$request->filled('from') && !$request->filled('to')) {
    //             $query->whereYear('tgl', now()->year);
    //         }
    //     }

    //     // Untuk 'all' atau 'divisi' tanpa tanggal spesifik, bisa tambah default tahun jika perlu
    //     if (in_array($filterType, ['all', 'divisi'])) {
    //         $query->whereYear('tgl', now()->year); // Opsional, sesuaikan kebutuhan
    //     }

    //     $kesehatan = $query->orderBy('tgl', 'desc')->get();

    //     $namaInstansi = Auth::user()->instansi->nama_instansi ?? 'Instansi Tidak Diketahui';
    //     $ikonPath = public_path('src/aku_sehat_icon.png');
    //     $raadeveloperz_cr = public_path('src/raadeveloperz_crc.png');

    //     $pdf = PDF::loadView('admin.kesehatan.export-pdf', compact('kesehatan', 'namaInstansi', 'ikonPath', 'raadeveloperz_cr'));
    //     $pdf->setPaper('A4', 'landscape');
        
    //     return $pdf->download('data-kesehatan-' . date('Y-m-d') . '.pdf');
    // }

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
        return view('admin.kesehatan.create', compact('member', 'divisi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'bb' => 'required|numeric',
            'tb' => 'required|numeric',
            'sistol' => 'required|numeric',
            'diastol' => 'required|numeric',
            'kondisi_telinga' => 'nullable|string',
            'kondisi_gigi' => 'nullable|string',
            'perilaku_beresiko' => 'nullable|string',
            'gangguan_reproduksi' => 'nullable|string',
        ]);

        /* --------------------------
        HITUNG BMI
        ---------------------------*/
        $bb = floatval($request->bb);
        $tb = floatval($request->tb) / 100; // konversi ke meter
        $imt = round($bb / ($tb * $tb), 2);

        // Kategori BMI WHO
        if ($imt < 18.5) {
            $status_imt = 'Underweight';
        } elseif ($imt < 25) {
            $status_imt = 'Normal';
        } elseif ($imt < 30) {
            $status_imt = 'Overweight';
        } elseif ($imt < 35) {
            $status_imt = 'Obesitas Level 1';
        } elseif ($imt < 40) {
            $status_imt = 'Obesitas Level 2';
        } else {
            $status_imt = 'Obesitas Level 3';
        }

        /* --------------------------
        STATUS TEKANAN DARAH WHO
        ---------------------------*/
        $sistol = intval($request->sistol);
        $diastol = intval($request->diastol);

        if ($sistol < 90 || $diastol < 60) {
            $status_darah = 'Hipotensi';
        } elseif ($sistol < 120 && $diastol < 80) {
            $status_darah = 'Normal';
        } elseif ($sistol >= 120 && $sistol < 130 && $diastol < 80) {
            $status_darah = 'Elevasi';
        } elseif (($sistol >= 130 && $sistol < 140) || ($diastol >= 80 && $diastol < 90)) {
            $status_darah = 'Hipertensi Tahap 1';
        } elseif (($sistol >= 140 && $sistol < 180) || ($diastol >= 90 && $diastol < 120)) {
            $status_darah = 'Hipertensi Tahap 2';
        } else {
            $status_darah = 'Krisis';
        }

        Kesehatan::create([
            'id_user' => $request->id_user,
            'tgl' => now()->format('Y-m-d'),
            'bb' => $request->bb,
            'tb' => $request->tb,
            'sistol' => $request->sistol,
            'diastol' => $request->diastol,
            'status_darah' => $status_darah,
            'imt' => $imt,
            'status' => $status_imt,
            'pesan_imt' => $this->generatePesanIMT($status_imt),
            'pesan_tkd' => $this->generatePesanTekananDarah($status_darah),
            'kondisi_telinga' => $request->kondisi_telinga ?? null,
            'kondisi_gigi' => $request->kondisi_gigi ?? null,
            'perilaku_beresiko' => $request->perilaku_beresiko ?? null,
            'gangguan_reproduksi' => $request->gangguan_reproduksi ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.kesehatan.index')
            ->with('success', 'Data kesehatan berhasil ditambahkan.');
    }

    private function generatePesanIMT($status)
    {
        return match ($status) {
            'Underweight' =>
                'Berat badan kurang. Tingkatkan asupan nutrisi secara seimbang.',
            'Overweight' =>
                'Berat badan berlebih. Disarankan mengatur pola makan dan olahraga rutin.',
            'Obesitas Level 1', 'Obesitas Level 2', 'Obesitas Level 3' =>
                'Obesitas. Disarankan konsultasi dengan ahli gizi untuk penanganan lebih lanjut.',
            default =>
                'Berat badan normal. Pertahankan pola hidup sehat.',
        };
    }

    private function generatePesanTekananDarah($status)
    {
        return match ($status) {
            'Hipotensi' =>
                'Tekanan darah rendah. Pastikan hidrasi cukup dan istirahat memadai.',
            'Elevasi' =>
                'Tekanan darah sedikit meningkat. Harap perhatikan gaya hidup sehat.',
            'Hipertensi Tahap 1' =>
                'Hipertensi tahap 1. Mulai lakukan pengurangan garam dan olahraga teratur.',
            'Hipertensi Tahap 2' =>
                'Hipertensi tahap 2. Perlu konsultasi lebih lanjut dengan tenaga medis.',
            'Krisis' =>
                'Kondisi darurat! Segera hubungi tenaga medis.',
            default =>
                'Tekanan darah normal. Pertahankan gaya hidup sehat.',
        };
    }

    public function edit($id)
    {
        $kesehatan = Kesehatan::findOrFail($id);
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

        return view('admin.kesehatan.edit', compact('kesehatan', 'member', 'divisi'));
    }

    public function update(Request $request, $id)
    {
        $kesehatan = Kesehatan::findOrFail($id);

        $request->validate([
            'id_user' => 'required|exists:users,id',
            'bb' => 'required|numeric',
            'tb' => 'required|numeric',
            'sistol' => 'required|numeric',
            'diastol' => 'required|numeric',
            'kondisi_telinga' => 'nullable|string',
            'kondisi_gigi' => 'nullable|string',
            'perilaku_beresiko' => 'nullable|string',
            'gangguan_reproduksi' => 'nullable|string',
        ],[
            'id_user.required' => 'Member wajib dipilih.',
            'id_user.exists' => 'Member tidak ditemukan.',
            'bb.required' => 'Berat badan wajib diisi.',
            'bb.numeric' => 'Berat badan harus berupa angka.',
            'tb.required' => 'Tinggi badan wajib diisi.',
            'tb.numeric' => 'Tinggi badan harus berupa angka.',
            'sistol.required' => 'Tekanan darah sistol wajib diisi.',
            'sistol.numeric' => 'Tekanan darah sistol harus berupa angka.',
            'diastol.required' => 'Tekanan darah diastol wajib diisi.',
            'diastol.numeric' => 'Tekanan darah diastol harus berupa angka.',
            'kondisi_telinga.string' => 'Kondisi telinga harus berupa teks.',
            'kondisi_gigi.string' => 'Kondisi gigi harus berupa teks.',
            'perilaku_beresiko.string' => 'Perilaku berisiko harus berupa teks.',
            'gangguan_reproduksi.string' => 'Gangguan reproduksi harus berupa teks.',
        ]);

        /* --------------------------
        HITUNG BMI
        ---------------------------*/
        $bb = floatval($request->bb);
        $tb = floatval($request->tb) / 100; // konversi cm ke meter
        $imt = round($bb / ($tb * $tb), 2);

        if ($imt < 18.5) {
            $status_imt = 'Underweight';
        } elseif ($imt < 25) {
            $status_imt = 'Normal';
        } elseif ($imt < 30) {
            $status_imt = 'Overweight';
        } elseif ($imt < 35) {
            $status_imt = 'Obesitas Level 1';
        } elseif ($imt < 40) {
            $status_imt = 'Obesitas Level 2';
        } else {
            $status_imt = 'Obesitas Level 3';
        }

        /* --------------------------
        STATUS TEKANAN DARAH WHO
        ---------------------------*/
        $sistol = intval($request->sistol);
        $diastol = intval($request->diastol);

        if ($sistol < 90 || $diastol < 60) {
            $status_darah = 'Hipotensi';
        } elseif ($sistol < 120 && $diastol < 80) {
            $status_darah = 'Normal';
        } elseif ($sistol >= 120 && $sistol < 130 && $diastol < 80) {
            $status_darah = 'Elevasi';
        } elseif (($sistol >= 130 && $sistol < 140) || ($diastol >= 80 && $diastol < 90)) {
            $status_darah = 'Hipertensi Tahap 1';
        } elseif (($sistol >= 140 && $sistol < 180) || ($diastol >= 90 && $diastol < 120)) {
            $status_darah = 'Hipertensi Tahap 2';
        } else {
            $status_darah = 'Krisis';
        }

        $kesehatan->update([
            'id_user' => $request->id_user,
            'tgl' => $kesehatan->tgl,
            'bb' => $request->bb,
            'tb' => $request->tb,
            'sistol' => $request->sistol,
            'diastol' => $request->diastol,
            'status_darah' => $status_darah,
            'imt' => $imt,
            'status' => $status_imt,
            'pesan_imt' => $this->generatePesanIMT($status_imt),
            'pesan_tkd' => $this->generatePesanTekananDarah($status_darah),
            'kondisi_telinga' => $request->kondisi_telinga ?? null,
            'kondisi_gigi' => $request->kondisi_gigi ?? null,
            'perilaku_beresiko' => $request->perilaku_beresiko ?? null,
            'gangguan_reproduksi' => $request->gangguan_reproduksi ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.kesehatan.index')
            ->with('success', 'Data kesehatan berhasil diupdate.');
    }

    public function destroy($id)
    {
        $kesehatan = Kesehatan::findOrFail($id);
        $kesehatan->delete();

        return redirect()->route('admin.kesehatan.index')
            ->with('success', 'Data kesehatan berhasil dihapus.');
    }

    public function mass_destroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:kesehatan,id_kesehatan',
        ]);

        $deletedCount = 0;

        try {
            foreach ($request->ids as $id) {
                $kesehatan = Kesehatan::find($id);
                if ($kesehatan && $kesehatan->user->instansi_id === Auth::user()->instansi_id) {
                    $kesehatan->delete();
                    $deletedCount++;
                }
            }

            return redirect()->route('admin.kesehatan.index')
                ->with('success', "$deletedCount data kesehatan berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error saat menghapus: ' . $e->getMessage());
        }
    }
}