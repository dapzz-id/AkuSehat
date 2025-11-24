<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PeminjamanPita;
use App\Models\User;
use App\Models\DataHaid;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HealthMonitorApiController extends Controller
{
    /**
     * GET /api/peminjaman-pita
     * List semua peminjaman pita
     */
    public function index()
    {
        $peminjaman = PeminjamanPita::with('user')
            ->whereHas('user', function ($query) {
                $query->where('jk', 'P')
                      ->where('sekolah_id', Auth::user()->sekolah_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $menungguVerifikasi = PeminjamanPita::where('verified', 0)
            ->whereHas('user', function($query) {
                $query->where('sekolah_id', Auth::user()->sekolah_id);
            })
            ->count();

        $totalSiswi = User::where('level', 'Member')
            ->where('jk', 'P')
            ->where('sekolah_id', Auth::user()->sekolah_id)
            ->count();

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman pita berhasil dimuat.',
            'data' => $peminjaman,
            'menunggu_verifikasi' => $menungguVerifikasi,
            'total_siswi' => $totalSiswi,
        ]);
    }

    /**
     * PUT /api/peminjaman-pita/{id}/accept-verifikasi
     * Terima/verifikasi peminjaman pita
     */
    public function acceptVerifikasi($id)
    {
        $peminjaman = PeminjamanPita::findOrFail($id);
        $peminjaman->update(['verified' => 1]);

        return response()->json([
            'status' => true,
            'message' => 'Peminjaman pita berhasil diverifikasi.',
            'data' => $peminjaman->load('user')
        ]);
    }

    /**
     * PUT /api/peminjaman-pita/{id}/reject-verifikasi
     * Tolak peminjaman pita
     */
    public function rejectVerifikasi($id)
    {
        $peminjaman = PeminjamanPita::findOrFail($id);
        $peminjaman->update(['verified' => 0]);

        return response()->json([
            'status' => true,
            'message' => 'Peminjaman pita berhasil ditolak.',
            'data' => $peminjaman->load('user')
        ]);
    }

    /**
     * POST /api/peminjaman-pita
     * Tambah data peminjaman pita
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_user' => 'required|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'jumlah_pita' => 'required|integer|min:1',
            'estimasi_selesai_haid' => 'required|date|after:tanggal_pinjam',
            'keterangan' => 'nullable|string',
        ], [
            'id_user.exists' => 'Data member tidak ditemukan.',
            'estimasi_selesai_haid.after' => 'Estimasi selesai haid harus setelah tanggal pinjam.',
            'jumlah_pita.min' => 'Jumlah pita minimal 1 peminjaman.',
        ]);

        $dataUser = User::with('dataHaid')->find($request->id_user);

        $peminjaman = PeminjamanPita::create([
            'id_user' => $dataUser->id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'jumlah_pita' => $request->jumlah_pita,
            'status' => 'dipinjam',
            'verified' => $dataUser->level === 'Health Monitor' ? 1 : 0,
            'estimasi_selesai_haid' => $request->estimasi_selesai_haid ?? now()->addDays(7),
            'keterangan' => $request->keterangan,
        ]);

        // Update catatan di data_haid
        DataHaid::updateOrCreate(
            ['id_user' => $dataUser->id],
            ['catatan' => 'Peminjaman pita: ' . $request->jumlah_pita . ' buah']
        );

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman pita berhasil ditambahkan.',
            'data' => $peminjaman
        ], 201);
    }

    public function getSiswiSedangHaid()
    {
        // Ambil siswi perempuan yang memiliki data haid aktif
        // Data haid dianggap aktif jika tanggal_mulai dalam 2 hari terakhir
        $siswi = User::with(['dataHaid', 'peminjamanPita'])
            ->where('level', 'Member')
            ->where('jk', 'P')
            ->whereHas('dataHaid', function($query) {
                $query->where('tanggal_mulai', '<=', Carbon::now())
                      ->whereDate('tanggal_selesai', '>=', Carbon::now());
            })
            ->whereDoesntHave('peminjamanPita', function($query) {
                $query->whereNull('tanggal_kembali')
                    ->where('status', 'dipinjam');
            })
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data siswi berhasil dimuat.',
            'data' => $siswi
        ]);
    }

    /**
     * PUT /api/peminjaman-pita/{id}
     * Update (misalnya untuk pengembalian)
     */
    public function update(Request $request, $id)
    {
        $peminjaman = PeminjamanPita::findOrFail($id);

        // Jika ada tanggal_kembali, berarti proses pengembalian
        if ($request->has('tanggal_kembali')) {
            $request->validate([
                'tanggal_kembali' => 'required|date',
            ]);

            $peminjaman->update([
                'tanggal_kembali' => $request->tanggal_kembali,
                'status' => 'dikembalikan',
            ]);

            $dataUser = User::with('dataHaid')->find($peminjaman->id_user);

            // Reset data haid
            DataHaid::updateOrCreate(
                ['id_user' => $dataUser->id],
                [
                    'catatan' => null,
                    'tanggal_selesai' => $request->tanggal_kembali,
                    'status' => 'selesai',
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Pita berhasil dikembalikan.',
                'data' => $peminjaman->load('user')
            ]);
        }

        // Jika tidak ada tanggal_kembali, berarti edit data
        $request->validate([
            'jumlah_pita' => 'sometimes|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $updateData = [];
        if ($request->has('jumlah_pita')) {
            $updateData['jumlah_pita'] = $request->jumlah_pita;
        }
        if ($request->has('keterangan')) {
            $updateData['keterangan'] = $request->keterangan;
        }

        $peminjaman->update($updateData);

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman berhasil diupdate.',
            'data' => $peminjaman->load('user')
        ]);
    }

    /**
     * DELETE /api/peminjaman-pita/{id}
     * Hapus data peminjaman
     */
    public function destroy($id)
    {
        $peminjaman = PeminjamanPita::findOrFail($id);
        $peminjaman->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman pita berhasil dihapus.'
        ]);
    }

    /**
     * GET /api/peminjaman-pita/warning
     * Ambil data yang terlambat mengembalikan pita
     */
    public function warningTerlambat()
    {
        // Update status "terlambat" jika sudah lewat estimasi
        $terlambatBaru = PeminjamanPita::with('user')
            ->where('status', 'dipinjam')
            ->where('estimasi_selesai_haid', '<', Carbon::now())
            ->get();

        foreach ($terlambatBaru as $item) {
            $item->update(['status' => 'terlambat']);
        }

        // Ambil semua yang sudah berstatus "terlambat"
        $terlambat = PeminjamanPita::with('user')
            ->where('status', 'terlambat')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data siswi yang terlambat berhasil dimuat.',
            'data' => $terlambat
        ]);
    }

    /**
     * GET /api/peminjaman-pita/{id}
     * Ambil detail satu data peminjaman
     */
    public function show($id)
    {
        $data = PeminjamanPita::with('user')->findOrFail($id);
        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }
}
