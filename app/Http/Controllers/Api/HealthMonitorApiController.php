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
                      ->where('instansi_id', Auth::user()->instansi_id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $menungguVerifikasi = PeminjamanPita::where('verified', 0)
            ->whereHas('user', function($query) {
                $query->where('instansi_id', Auth::user()->instansi_id);
            })
            ->count();

        $totalMember = User::where('level', 'Member')
            ->where('jk', 'P')
            ->where('instansi_id', Auth::user()->instansi_id)
            ->count();

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman pita berhasil dimuat.',
            'data' => $peminjaman,
            'menunggu_verifikasi' => $menungguVerifikasi,
            'total_member' => $totalMember,
        ]);
    }

    /**
     * PUT /api/peminjaman-pita/{id}/accept-verifikasi
     * Terima/verifikasi peminjaman pita
     */
    public function acceptVerifikasi($id)
    {
        $peminjaman = PeminjamanPita::findOrFail($id);
        $peminjaman->update(['verified' => 1, 'status' => 'dipinjam']);

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
    public function rejectVerifikasi($id, Request $request)
    {
        $request->validate([
            'alasan_penolakan' => 'nullable|string|min:12',
        ],[
            'alasan_penolakan.string' => 'Alasan penolakan harus berupa teks.',
            'alasan_penolakan.min' => 'Alasan penolakan minimal 12 karakter.',
        ]);

        $peminjaman = PeminjamanPita::findOrFail($id);
        $peminjaman->update(['verified' => 1, 'status' => 'ditolak', 'keterangan' => $request->alasan_penolakan ?? $peminjaman->keterangan]);

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
            'keterangan' => 'nullable|string',
        ], [
            'id_user.exists' => 'Data member tidak ditemukan.',
            'id_user.required' => 'Member wajib dipilih.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_pinjam.date' => 'Tanggal pinjam tidak valid.',
        ]);

        $dataUser = User::with('dataHaid')->find($request->id_user);

        $peminjaman = PeminjamanPita::create([
            'id_user' => $dataUser->id,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'jumlah_pita' => 1,
            'status' => 'dipinjam',
            'verified' => $dataUser->level === 'Health Monitor' ? 1 : 0,
            'estimasi_selesai_haid' => $dataHaid = DataHaid::where('id_user', $dataUser->id)->latest()->first() ? $dataHaid->estimasi_mulai->addDays(7)->format('Y-m-d') : Carbon::parse(now())->addDays(7)->format('Y-m-d'),
            'keterangan' => $request->keterangan,
        ]);

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

    public function getMemberSedangHaid()
    {
        // Ambil member perempuan yang memiliki data haid aktif
        // Data haid dianggap aktif jika tanggal_mulai dalam 2 hari terakhir
        $member = User::with(['dataHaid', 'peminjamanPita'])
            ->where('level', 'Member')
            ->where('jk', 'P')
            ->whereHas('dataHaid', function($query) {
                $query->where('tanggal_mulai', '<=', Carbon::now())
                      ->whereDate('tanggal_selesai', '>=', Carbon::now());
            })
            ->whereDoesntHave('peminjamanPita', function($query) {
                $query->whereNull('tanggal_kembali')
                    ->whereIn('status', ['dipinjam', 'menunggu', 'terlambat']);
            })
            ->orderBy('nama', 'asc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data member berhasil dimuat.',
            'data' => $member
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
            'message' => 'Data member yang terlambat berhasil dimuat.',
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
