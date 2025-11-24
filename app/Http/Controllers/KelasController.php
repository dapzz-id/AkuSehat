<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kelas;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::withCount(['users as jumlah_member' => function($query) {
            $query->where('level', 'Member')->whereNotNull('sekolah_id')->where('sekolah_id', Auth::user()->sekolah_id);
        }])->where('sekolah_id', Auth::user()->sekolah_id)->paginate(10);

        return view('admin.kelas.index', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string|max:35',
            'jurusan' => 'required|string|max:20',
        ]);

        // Cek apakah user sudah memiliki sekolah terkait
        if (!Auth::user()->sekolah_id) {
            return redirect()->route('admin.kelas.index')
                ->with('error', 'Anda tidak memiliki sekolah terkait. Silakan hubungi administrator.');
        }

        Kelas::create([
            'sekolah_id' => Auth::user()->sekolah_id,
            'tgl' => now()->format('Y-m-d'),
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'count_kesehatan' => 0,
        ]);

        return redirect()->route('admin.kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas)
    {
        try {
            $validated = $request->validate([
                'kelas' => 'required|string|max:35|unique:kelas,kelas,' . $kelas->id,
                'jurusan' => 'required|string|max:20',
            ], [
                'kelas.unique' => 'Nama kelas sudah ada.',
                'jurusan.required' => 'Jurusan harus diisi.',
                'jurusan.max' => 'Jurusan maksimal 20 karakter.',
                'kelas.required' => 'Kelas harus diisi.',
                'kelas.max' => 'Kelas maksimal 35 karakter.'
            ]);

            // Cek apakah user sudah memiliki sekolah terkait
            if (!Auth::user()->sekolah_id) {
                return redirect()->route('admin.kelas.index')
                    ->with('error', 'Anda tidak memiliki sekolah terkait. Silakan hubungi administrator.');
            }

            Kelas::updateOrCreate([
                'id' => $kelas->id,
                'sekolah_id' => Auth::user()->sekolah_id,
            ], [
                'kelas' => $request->kelas,
                'jurusan' => $request->jurusan,
            ]);

            return redirect()->route('admin.kelas.index')
                ->with('success', 'Kelas berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('admin.kelas.index')
                ->with('error', implode(', ', collect($e->errors())->flatten()->toArray()));
        } catch (\Exception $e) {
            return redirect()->route('admin.kelas.index')
                ->with('error', 'Terjadi kesalahan saat memperbarui kelas: ' . $e->getMessage());
        }
    }
}