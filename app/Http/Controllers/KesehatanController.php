<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kesehatan;
use App\Models\User;
use App\Models\Kelas;

class KesehatanController extends Controller
{
    public function index()
    {
        $kesehatan = Kesehatan::with(['user', 'kelas'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.kesehatan.index', compact('kesehatan'));
    }

    public function create()
    {
        $siswa = User::where('level', 'Siswa')->get();
        $kelas = Kelas::all();
        return view('admin.kesehatan.create', compact('siswa', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'tgl' => 'required|date',
            'bb' => 'required|numeric',
            'tb' => 'required|numeric',
            'sistol' => 'required|numeric',
            'diastol' => 'required|numeric',
            'kondisi_telinga' => 'nullable|string',
            'kondisi_gigi' => 'nullable|string',
            'perilaku_beresiko' => 'nullable|string',
            'gangguan_reproduksi' => 'nullable|string',
        ],[
            'id_user.required' => 'Siswa wajib dipilih.',
            'id_user.exists' => 'Siswa tidak ditemukan.',
            'id_kelas.required' => 'Kelas wajib dipilih.',
            'id_kelas.exists' => 'Kelas tidak ditemukan.',
            'tgl.required' => 'Tanggal pemeriksaan wajib diisi.',
            'tgl.date' => 'Tanggal pemeriksaan tidak valid.',
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

        $bb = floatval($request->bb);
        $tb = floatval($request->tb) / 100;
        $imt = round($bb / ($tb * $tb), 2);

        $status_imt = 'Normal';
        if ($imt < 18.5) $status_imt = 'Kurus';
        elseif ($imt >= 25 && $imt < 30) $status_imt = 'Overweight';
        elseif ($imt >= 30) $status_imt = 'Obesitas';

        $sistol = intval($request->sistol);
        $diastol = intval($request->diastol);
        $status_darah = 'Normal';
        if ($sistol >= 140 || $diastol >= 90) $status_darah = 'Tinggi';
        elseif ($sistol < 90 || $diastol < 60) $status_darah = 'Rendah';

        Kesehatan::create([
            'id_user' => $request->id_user,
            'id_kelas' => User::find($request->id_user)->id_kelas,
            'tgl' => $request->tgl,
            'bb' => $request->bb,
            'tb' => $request->tb,
            'sistol' => $request->sistol,
            'diastol' => $request->diastol,
            'status_darah' => $status_darah,
            'imt' => $imt,
            'status' => $status_imt,
            'pesan_imt' => $this->generatePesanIMT($status_imt),
            'pesan_tkd' => $this->generatePesanTekananDarah($status_darah),
            'kondisi_telinga' => $request->kondisi_telinga,
            'kondisi_gigi' => $request->kondisi_gigi,
            'perilaku_beresiko' => $request->perilaku_beresiko,
            'gangguan_reproduksi' => $request->gangguan_reproduksi,
        ]);

        return redirect()->route('admin.kesehatan.index')
            ->with('success', 'Data kesehatan berhasil ditambahkan.');
    }

    private function generatePesanIMT($status)
    {
        switch ($status) {
            case 'Kurus':
                return 'Berat badan kurang. Disarankan untuk meningkatkan asupan nutrisi yang seimbang.';
            case 'Overweight':
                return 'Berat badan berlebih. Disarankan untuk mengatur pola makan dan olahraga teratur.';
            case 'Obesitas':
                return 'Obesitas. Sangat disarankan untuk konsultasi dengan ahli gizi dan program penurunan berat badan.';
            default:
                return 'Berat badan normal. Pertahankan pola hidup sehat.';
        }
    }

    private function generatePesanTekananDarah($status)
    {
        switch ($status) {
            case 'Tinggi':
                return 'Tekanan darah tinggi. Disarankan untuk mengurangi konsumsi garam dan konsultasi dengan dokter.';
            case 'Rendah':
                return 'Tekanan darah rendah. Pastikan asupan cairan cukup dan istirahat yang adequate.';
            default:
                return 'Tekanan darah normal. Pertahankan pola hidup sehat.';
        }
    }

    public function edit($id)
    {
        $kesehatan = Kesehatan::findOrFail($id);
        $siswa = User::where('level', 'Siswa')->get();
        $kelas = Kelas::all();
        return view('admin.kesehatan.edit', compact('kesehatan', 'siswa', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $kesehatan = Kesehatan::findOrFail($id);
        
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'tgl' => 'required|date',
            'bb' => 'required|numeric',
            'tb' => 'required|numeric',
            'sistol' => 'required|numeric',
            'diastol' => 'required|numeric',
            'kondisi_telinga' => 'nullable|string',
            'kondisi_gigi' => 'nullable|string',
            'perilaku_beresiko' => 'nullable|string',
            'gangguan_reproduksi' => 'nullable|string',
        ],[
            'id_user.required' => 'Siswa wajib dipilih.',
            'id_user.exists' => 'Siswa tidak ditemukan.',
            'id_kelas.required' => 'Kelas wajib dipilih.',
            'id_kelas.exists' => 'Kelas tidak ditemukan.',
            'tgl.required' => 'Tanggal pemeriksaan wajib diisi.',
            'tgl.date' => 'Tanggal pemeriksaan tidak valid.',
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

        $bb = floatval($request->bb);
        $tb = floatval($request->tb) / 100;
        $imt = round($bb / ($tb * $tb), 2);

        $status_imt = 'Normal';
        if ($imt < 18.5) $status_imt = 'Kurus';
        elseif ($imt >= 25 && $imt < 30) $status_imt = 'Overweight';
        elseif ($imt >= 30) $status_imt = 'Obesitas';

        $sistol = intval($request->sistol);
        $diastol = intval($request->diastol);
        $status_darah = 'Normal';
        if ($sistol >= 140 || $diastol >= 90) $status_darah = 'Tinggi';
        elseif ($sistol < 90 || $diastol < 60) $status_darah = 'Rendah';

        $kesehatan->update([
            'id_user' => $request->id_user,
            'id_kelas' => $kesehatan->id_kelas ?? User::find($request->id_user)->id_kelas,
            'tgl' => $request->tgl,
            'bb' => $request->bb,
            'tb' => $request->tb,
            'sistol' => $request->sistol,
            'diastol' => $request->diastol,
            'status_darah' => $status_darah,
            'imt' => $imt,
            'status' => $status_imt,
            'pesan_imt' => $this->generatePesanIMT($status_imt),
            'pesan_tkd' => $this->generatePesanTekananDarah($status_darah),
            'kondisi_telinga' => $request->kondisi_telinga,
            'kondisi_gigi' => $request->kondisi_gigi,
            'perilaku_beresiko' => $request->perilaku_beresiko,
            'gangguan_reproduksi' => $request->gangguan_reproduksi,
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
}