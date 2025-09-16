<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hb;
use App\Models\User;
use App\Models\Kelas;

class HbController extends Controller
{
    public function index()
    {
        $hb = Hb::with(['user', 'kelas'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.hb.index', compact('hb'));
    }

    public function create()
    {
        $siswa = User::where('level', 'Siswa')->get();
        $kelas = Kelas::all();
        return view('admin.hb.create', compact('siswa', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'tgl' => 'required|date',
            'hb' => 'required|numeric',
        ],[
            'hb.numeric' => 'Kadar HB harus berupa angka.',
            'hb.required' => 'Kadar HB wajib diisi.',
            'hb.min' => 'Kadar HB minimal 0.',
            'hb.max' => 'Kadar HB maksimal 25.',
            'tgl.required' => 'Tanggal pemeriksaan wajib diisi.',
            'tgl.date' => 'Tanggal pemeriksaan tidak valid.',
            'id_user.required' => 'Siswa wajib dipilih.',
            'id_user.exists' => 'Siswa tidak ditemukan.',
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
            'tgl' => $request->tgl,
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
        $siswa = User::where('level', 'Siswa')->get();
        $kelas = Kelas::all();
        return view('admin.hb.edit', compact('hb', 'siswa', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $hb = Hb::findOrFail($id);
        
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'tgl' => 'required|date',
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
            'tgl' => $request->tgl,
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
}