<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount(['users as jumlah_siswa' => function($query) {
            $query->where('level', 'Siswa');
        }])->paginate(10);

        return view('admin.kelas.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string|max:35',
            'jurusan' => 'required|string|max:20',
        ]);

        Kelas::create([
            'tgl' => now()->format('Y-m-d'),
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'count_kesehatan' => 0,
        ]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }
}