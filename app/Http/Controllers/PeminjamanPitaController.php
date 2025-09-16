<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeminjamanPita;
use App\Models\User;
use App\Models\DataHaid;
use Carbon\Carbon;

class PeminjamanPitaController extends Controller
{
    public function index()
    {
        $peminjaman = PeminjamanPita::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('guru-bk.peminjaman.index', compact('peminjaman'));
    }

    public function create()
    {
        $siswi = User::where('level', 'Siswa')
            ->where('jk', 'P')
            ->get();
        
        return view('guru-bk.peminjaman.create', compact('siswi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'jumlah_pita' => 'required|integer|min:1',
            'estimasi_selesai_haid' => 'required|date|after:tanggal_pinjam',
            'keterangan' => 'nullable|string',
        ]);

        // Buat data peminjaman pita
        PeminjamanPita::create([
            'id_user' => $request->id_user,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'jumlah_pita' => $request->jumlah_pita,
            'status' => 'dipinjam',
            'estimasi_selesai_haid' => $request->estimasi_selesai_haid,
            'keterangan' => $request->keterangan,
        ]);

        // Buat data haid
        DataHaid::create([
            'id_user' => $request->id_user,
            'tanggal_mulai' => $request->tanggal_pinjam,
            'status' => 'berlangsung',
            'catatan' => 'Peminjaman pita: ' . $request->jumlah_pita . ' buah',
        ]);

        return redirect()->route('guru-bk.peminjaman.index')
            ->with('success', 'Data peminjaman pita berhasil ditambahkan.');
    }

    public function kembalikan(Request $request, $id)
    {
        $peminjaman = PeminjamanPita::findOrFail($id);
        
        $request->validate([
            'tanggal_kembali' => 'required|date',
        ]);

        $peminjaman->update([
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => 'dikembalikan',
        ]);

        // Update data haid
        $dataHaid = DataHaid::where('id_user', $peminjaman->id_user)
            ->where('status', 'berlangsung')
            ->latest()
            ->first();

        if ($dataHaid) {
            $durasi = Carbon::parse($dataHaid->tanggal_mulai)
                ->diffInDays(Carbon::parse($request->tanggal_kembali)) + 1;
                
            $dataHaid->update([
                'tanggal_selesai' => $request->tanggal_kembali,
                'durasi_hari' => $durasi,
                'status' => 'selesai',
            ]);
        }

        return redirect()->route('guru-bk.peminjaman.index')
            ->with('success', 'Pita berhasil dikembalikan.');
    }

    public function warningTerlambat()
    {
        $terlambat = PeminjamanPita::with('user')
            ->where('status', 'dipinjam')
            ->where('estimasi_selesai_haid', '<', Carbon::now())
            ->get();

        // Update status menjadi terlambat
        foreach ($terlambat as $item) {
            $item->update(['status' => 'terlambat']);
        }

        return view('guru-bk.warning', compact('terlambat'));
    }
}